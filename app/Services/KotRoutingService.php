<?php

namespace App\Services;

use App\Enums\Ask;
use App\Enums\OrderType;
use App\Enums\PrintFormat;
use App\Enums\PrintingChoice;
use App\Enums\Source;
use App\Enums\Status;
use App\Libraries\AppLibrary;
use App\Models\KitchenStation;
use App\Models\KitchenTicket;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Printer;
use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

/**
 * Category → kitchen → printer routing for POS (and reusable by waiter).
 */
class KotRoutingService
{
    public function __construct(
        protected EscPosPrintService $escPosPrintService
    ) {
    }

    /**
     * Assign stations, generate per-kitchen KOTs, print invoice.
     *
     * @return array{jobs: array<int, array>, kot_count: int}
     */
    public function processPosOrder(Order $order): array
    {
        $order->loadMissing([
            'orderItems.orderItem.category',
            'diningTable',
            'waiter',
            'user',
            'restaurant',
            'posDetail',
        ]);

        $groups = $this->groupItemsByKitchen($order);
        $this->persistItemStations($groups);

        $jobs = [];

        foreach ($groups as $group) {
            /** @var KitchenStation|null $station */
            $station = $group['station'];
            $items   = $group['items'];
            if ($items->isEmpty()) {
                continue;
            }

            // Kitchen explicitly set to "No Auto KOT" — skip kitchen slip
            if ($station?->printer && $station->printer->skipsAutoKot()) {
                continue;
            }

            $printer = $this->resolveKotPrinter($order, $station);
            if ($printer && $printer->skipsAutoKot()) {
                continue;
            }

            $payload = $this->buildKotPayload($order, $items, $station);
            $ticket  = $this->storeTicket($order, $station, $printer, $payload);
            $jobs[]  = $this->dispatchPrintJob('kot', $printer, $payload, $ticket);
        }

        $invoiceJob = $this->processInvoice($order);
        if ($invoiceJob) {
            $jobs[] = $invoiceJob;
        }

        return [
            'jobs'      => $jobs,
            'kot_count' => collect($jobs)->where('type', 'kot')->count(),
        ];
    }

    /**
     * @return array<int, array{station: ?KitchenStation, items: Collection<int, OrderItem>}>
     */
    public function groupItemsByKitchen(Order $order): array
    {
        $stations = KitchenStation::query()
            ->with(['categories', 'printer'])
            ->where('restaurant_id', $order->restaurant_id)
            ->where('status', Status::ACTIVE)
            ->orderBy('sort_order')
            ->get();

        $categoryMap = [];
        foreach ($stations as $station) {
            foreach ($station->categories as $category) {
                $categoryMap[(int) $category->id] = $station;
            }
        }

        $default = $stations->first(fn (KitchenStation $s) => strtolower((string) $s->code) === 'default')
            ?: $stations->first();

        $buckets = [];
        foreach ($order->orderItems as $orderItem) {
            $categoryId = (int) ($orderItem->orderItem?->item_category_id ?? 0);
            $station    = $categoryMap[$categoryId] ?? $default;
            $key        = $station?->id ?? 0;

            if (!isset($buckets[$key])) {
                $buckets[$key] = [
                    'station' => $station,
                    'items'   => collect(),
                ];
            }
            $buckets[$key]['items']->push($orderItem);
        }

        return array_values($buckets);
    }

    protected function persistItemStations(array $groups): void
    {
        foreach ($groups as $group) {
            $stationId = $group['station']?->id;
            /** @var Collection $items */
            $items = $group['items'];
            if ($items->isEmpty()) {
                continue;
            }
            OrderItem::query()
                ->whereIn('id', $items->pluck('id')->all())
                ->update(['kitchen_station_id' => $stationId]);
        }
    }

    protected function storeTicket(Order $order, ?KitchenStation $station, ?Printer $printer, array $payload): KitchenTicket
    {
        $ticketNo = 'KOT-' . $order->id . '-' . ($station?->id ?: 'x');

        return KitchenTicket::create([
            'restaurant_id'      => $order->restaurant_id,
            'order_id'           => $order->id,
            'kitchen_station_id' => $station?->id,
            'printer_id'         => $printer?->id,
            'ticket_no'          => $ticketNo,
            'print_count'        => 1,
            'printed_at'         => now(),
            'printed_by'         => Auth::id(),
            'payload'            => $payload,
        ]);
    }

    protected function buildKotPayload(Order $order, Collection $items, ?KitchenStation $station): array
    {
        $mappedItems = $items->map(function (OrderItem $item) {
            $variations = json_decode($item->item_variations, true);
            $extras     = json_decode($item->item_extras, true);

            return [
                'name'            => $item->orderItem?->name,
                'quantity'        => $item->quantity,
                'instruction'     => $item->instruction,
                'item_variations' => $variations,
                'item_extras'     => $extras,
                'variation_lines' => $this->variationLines(is_array($variations) ? $variations : null),
                'extra_lines'     => $this->extraLines(is_array($extras) ? $extras : null),
                'kitchen_status'  => $item->kitchen_status,
            ];
        })->values()->all();

        $orderTypeLabel = match ((int) $order->order_type) {
            OrderType::DINING_TABLE => 'Dine In',
            OrderType::DELIVERY => 'Delivery',
            OrderType::TAKEAWAY => 'Take Away',
            default => 'POS',
        };

        return [
            'copy'             => 'KITCHEN KOT',
            'ticket_no'        => 'KOT - ' . $order->id . ($station ? ' / ' . $station->name : ''),
            'kot_no'           => (string) $order->id,
            'restaurant'       => $order->restaurant?->name,
            'order_serial_no'  => $order->order_serial_no,
            'order_type'       => $order->order_type,
            'order_type_label' => $orderTypeLabel,
            'source'           => $order->source,
            'counter'          => ((int) $order->source === Source::POS) ? 'POS' : null,
            'biller'           => Auth::user()?->name ?: 'Cashier',
            'table'            => $order->diningTable ? [
                'number' => $order->diningTable->table_number,
                'name'   => $order->diningTable->name,
                'zone'   => $order->diningTable->zone,
            ] : null,
            'table_no'         => $order->diningTable?->table_number,
            'waiter'           => $order->waiter?->name,
            'customer'         => $order->user?->name,
            'order_note'       => $order->order_note,
            'special_note'     => $order->order_note,
            'order_datetime'   => AppLibrary::datetime($order->order_datetime),
            'order_date'       => AppLibrary::date($order->order_datetime),
            'order_time'       => AppLibrary::time($order->order_datetime),
            'preparation_time' => $order->preparation_time,
            'status'           => $order->status,
            'station'          => $station?->name,
            'station_id'       => $station?->id,
            'items'            => $mappedItems,
            'total_qty'        => (int) $items->sum('quantity'),
            'printed_at'       => AppLibrary::datetime(now()),
            'reprint'          => false,
        ];
    }

    protected function processInvoice(Order $order): ?array
    {
        $printer = $this->resolveInvoicePrinter($order);
        $payload = $this->buildInvoicePayload($order, $printer);

        return $this->dispatchPrintJob('invoice', $printer, $payload, null);
    }

    /**
     * Prefer kitchen-station printer; otherwise any active restaurant KOT printer.
     */
    protected function resolveKotPrinter(Order $order, ?KitchenStation $station): ?Printer
    {
        $linked = $station?->printer;
        if ($linked && $linked->isActive() && $linked->isKotFormat()) {
            return $linked;
        }

        return Printer::query()
            ->where('restaurant_id', $order->restaurant_id)
            ->where('status', Status::ACTIVE)
            ->where('print_format', PrintFormat::KOT)
            ->orderBy('id')
            ->first();
    }

    /**
     * Prefer dedicated invoice/POS printer; fall back to any direct-print printer
     * (same thermal often prints both KOT and bill).
     */
    protected function resolveInvoicePrinter(Order $order): ?Printer
    {
        $invoice = Printer::query()
            ->where('restaurant_id', $order->restaurant_id)
            ->where('status', Status::ACTIVE)
            ->where('print_format', PrintFormat::INVOICE)
            ->orderBy('id')
            ->first();

        if ($invoice) {
            return $invoice;
        }

        return Printer::query()
            ->where('restaurant_id', $order->restaurant_id)
            ->where('status', Status::ACTIVE)
            ->where('printing_choice', PrintingChoice::DIRECT_PRINT)
            ->where('print_format', '!=', PrintFormat::NO_AUTO_KOT)
            ->orderBy('id')
            ->first();
    }

    protected function buildInvoicePayload(Order $order, ?Printer $printer = null): array
    {
        $orderTypeLabel = match ((int) $order->order_type) {
            OrderType::DINING_TABLE => 'Dine In',
            OrderType::DELIVERY => 'Delivery',
            OrderType::TAKEAWAY => 'Take Away',
            default => 'POS',
        };

        $items = $order->orderItems->map(function (OrderItem $item) {
            return [
                'name'        => $item->orderItem?->name,
                'quantity'    => $item->quantity,
                'price'       => AppLibrary::currencyAmountFormat($item->price),
                'total_price' => AppLibrary::currencyAmountFormat($item->total_price),
            ];
        })->values()->all();

        return [
            'restaurant'       => $order->restaurant?->name,
            'order_serial_no'  => $order->order_serial_no,
            'order_type_label' => $orderTypeLabel,
            'table_no'         => $order->diningTable?->table_number,
            'biller'           => Auth::user()?->name ?: 'Cashier',
            'order_datetime'   => AppLibrary::datetime($order->order_datetime),
            'items'            => $items,
            'subtotal'         => AppLibrary::currencyAmountFormat($order->subtotal),
            'tax'              => AppLibrary::currencyAmountFormat($order->total_tax),
            'total'            => AppLibrary::currencyAmountFormat($order->total),
            'invoice_qr'       => $printer ? ((int) $printer->invoice_qr_status === Ask::YES) : false,
        ];
    }

    protected function dispatchPrintJob(string $type, ?Printer $printer, array $payload, ?KitchenTicket $ticket): array
    {
        $mode = (!$printer || $printer->isBrowserPopup())
            ? 'browser_popup'
            : 'direct_print';

        $job = [
            'type'           => $type,
            'mode'           => $mode,
            'status'         => $mode === 'browser_popup' ? 'pending_browser' : 'pending_local',
            'printer_id'     => $printer?->id,
            'printer'        => $printer?->name,
            'ticket_id'      => $ticket?->id,
            'payload'        => $payload,
            'message'        => null,
            'raw_base64'     => null,
            'computer_ipv4'  => $printer?->computer_ipv4,
            'printer_ip'     => $printer?->printer_ip,
            'printer_port'   => (int) ($printer?->printer_port ?: 9100),
            'bridge_port'    => 1811,
        ];

        if ($mode === 'direct_print' && $printer) {
            // Always prepare raw bytes so the POS PC can print via local agent
            // when the cloud server cannot reach the restaurant LAN.
            try {
                $job['raw_base64'] = $type === 'invoice'
                    ? $this->escPosPrintService->invoiceRawBase64($printer, $payload)
                    : $this->escPosPrintService->kotRawBase64($printer, $payload);
            } catch (Exception $exception) {
                Log::info('ESC/POS build failed: ' . $exception->getMessage());
                $job['message'] = $exception->getMessage();
            }

            try {
                $result = $type === 'invoice'
                    ? $this->escPosPrintService->printInvoice($printer, $payload)
                    : $this->escPosPrintService->printKot($printer, $payload);

                if ($result['success'] ?? false) {
                    $job['status']  = 'printed';
                    $job['message'] = $result['message'] ?? null;
                } else {
                    // Keep local_bridge path — never open Chrome print dialog for Direct Print
                    $job['mode']    = 'local_bridge';
                    $job['status']  = 'pending_local';
                    $job['message'] = $result['message'] ?? null;
                }
            } catch (Exception $exception) {
                Log::info('Direct print failed (will try local agent): ' . $exception->getMessage());
                $job['mode']    = 'local_bridge';
                $job['status']  = 'pending_local';
                $job['message'] = $exception->getMessage();
            }
        }

        return $job;
    }

    protected function variationLines(?array $variations): array
    {
        if (!$variations) {
            return [];
        }
        if (isset($variations['names']) && is_array($variations['names'])) {
            return array_values(array_filter($variations['names']));
        }
        if (array_is_list($variations)) {
            return array_values(array_filter(array_map(
                fn ($v) => $v['name'] ?? $v['variation_name'] ?? null,
                $variations
            )));
        }

        return [];
    }

    protected function extraLines(?array $extras): array
    {
        if (!$extras) {
            return [];
        }
        if (isset($extras['names']) && is_array($extras['names'])) {
            return array_values(array_filter($extras['names']));
        }
        if (array_is_list($extras)) {
            return array_values(array_filter(array_map(
                fn ($e) => $e['name'] ?? null,
                $extras
            )));
        }

        return [];
    }
}
