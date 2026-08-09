<?php

namespace App\Services;

use App\Enums\Ask;
use App\Enums\OrderType;
use App\Enums\PrintFormat;
use App\Enums\Source;
use App\Enums\Status;
use App\Libraries\AppLibrary;
use App\Models\KitchenStation;
use App\Models\KitchenTicket;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Printer;
use Dipokhalder\Settings\Facades\Settings;
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
            'logo_url'         => $order->restaurant?->logo,
            'tagline'          => $this->restaurantTagline($order),
            'phone'            => $this->restaurantPhone($order),
            'powered_by'       => $this->poweredByName(),
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

    protected function restaurantTagline(Order $order): ?string
    {
        $restaurant = $order->restaurant;
        $address = trim((string) ($restaurant?->address ?? ''));
        if ($address !== '') {
            return $address;
        }
        try {
            $companyAddress = trim((string) (Settings::group('company')->get('company_address') ?? ''));
            if ($companyAddress !== '') {
                return $companyAddress;
            }
        } catch (\Throwable $e) {
        }

        return null;
    }

    protected function restaurantPhone(Order $order): ?string
    {
        $restaurant = $order->restaurant;
        $phone = trim((string) (($restaurant?->country_code ?? '') . ($restaurant?->phone ?? '')));
        if ($phone !== '') {
            return $phone;
        }
        try {
            $companyPhone = trim((string) (Settings::group('company')->get('company_phone') ?? ''));

            return $companyPhone !== '' ? $companyPhone : null;
        } catch (\Throwable $e) {
            return null;
        }
    }

    protected function poweredByName(): string
    {
        try {
            $name = trim((string) (Settings::group('company')->get('company_name') ?? ''));
            if ($name !== '') {
                return $name;
            }
        } catch (\Throwable $e) {
        }

        return 'FoodNanny';
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
     * Bill/invoice must use a dedicated Invoice printer — never the KOT machine.
     */
    protected function resolveInvoicePrinter(Order $order): ?Printer
    {
        return Printer::query()
            ->where('restaurant_id', $order->restaurant_id)
            ->where('status', Status::ACTIVE)
            ->where('print_format', PrintFormat::INVOICE)
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
                'price'       => (float) $item->price,
                'total_price' => (float) $item->total_price,
            ];
        })->values()->all();

        return [
            'restaurant'       => $order->restaurant?->name,
            'logo_url'         => $order->restaurant?->logo,
            'tagline'          => $this->restaurantTagline($order),
            'phone'            => $this->restaurantPhone($order),
            'powered_by'       => $this->poweredByName(),
            'order_serial_no'  => $order->order_serial_no,
            'order_type_label' => $orderTypeLabel,
            'table_no'         => $order->diningTable?->table_number,
            'biller'           => Auth::user()?->name ?: 'Cashier',
            'order_datetime'   => AppLibrary::datetime($order->order_datetime),
            'items'            => $items,
            'subtotal'         => (float) $order->subtotal,
            'tax'              => (float) $order->total_tax,
            'discount'         => (float) $order->discount,
            'total'            => (float) $order->total,
            'invoice_qr'       => $printer ? ((int) $printer->invoice_qr_status === Ask::YES) : false,
        ];
    }

    protected function dispatchPrintJob(string $type, ?Printer $printer, array $payload, ?KitchenTicket $ticket): array
    {
        $hasNetworkTarget = $printer && $printer->isNetwork() && filled($printer->printer_ip);
        $hasWindowsTarget = $printer && filled($printer->windows_printer_name);
        $wantsDirect      = $printer && $printer->isDirectPrint() && ($hasNetworkTarget || $hasWindowsTarget);

        // Direct Print (network IP or Windows USB name) → Local Agent automatic path (no Chrome popup)
        $mode = $wantsDirect ? 'direct_print' : 'browser_popup';

        $job = [
            'type'                 => $type,
            'mode'                 => $mode,
            'status'               => $mode === 'browser_popup' ? 'pending_browser' : 'pending_local',
            'printer_id'           => $printer?->id,
            'printer'              => $printer?->name,
            'print_format'         => $printer?->print_format,
            'ticket_id'            => $ticket?->id,
            'payload'              => $payload,
            'message'              => $printer
                ? null
                : ($type === 'invoice'
                    ? 'No Invoice/Bill printer configured.'
                    : 'No KOT printer configured.'),
            'raw_base64'           => null,
            'computer_ipv4'        => $printer?->computer_ipv4,
            'printer_ip'           => $printer?->printer_ip,
            'printer_port'         => (int) ($printer?->printer_port ?: 9100),
            'windows_printer_name' => $printer?->windows_printer_name,
            'bridge_port'          => 1811,
        ];

        if ($mode === 'direct_print' && $printer) {
            try {
                $job['raw_base64'] = $type === 'invoice'
                    ? $this->escPosPrintService->invoiceRawBase64($printer, $payload)
                    : $this->escPosPrintService->kotRawBase64($printer, $payload);
            } catch (Exception $exception) {
                Log::info('ESC/POS build failed: ' . $exception->getMessage());
                $job['message'] = $exception->getMessage();
            }

            // Network IP: try cloud TCP first; USB Windows name can only print via Local Agent
            if ($hasNetworkTarget) {
                try {
                    $result = $type === 'invoice'
                        ? $this->escPosPrintService->printInvoice($printer, $payload)
                        : $this->escPosPrintService->printKot($printer, $payload);

                    if ($result['success'] ?? false) {
                        $job['status']  = 'printed';
                        $job['message'] = $result['message'] ?? null;

                        return $job;
                    }
                } catch (Exception $exception) {
                    Log::info('Direct network print failed (local agent next): ' . $exception->getMessage());
                    $job['message'] = $exception->getMessage();
                }
            }

            // Always keep local_bridge for POS browser → Local Agent (no Chrome popup)
            $job['mode']   = 'local_bridge';
            $job['status'] = 'pending_local';
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
