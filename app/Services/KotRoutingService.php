<?php

namespace App\Services;

use App\Enums\Ask;
use App\Enums\OrderType;
use App\Enums\PrintFormat;
use App\Enums\Source;
use App\Enums\Status;
use App\Libraries\AppLibrary;
use App\Enums\OrderItemChangeAction;
use App\Models\KitchenStation;
use App\Models\KitchenTicket;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderItemChange;
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
     * Assign stations, generate per-kitchen KOTs, optionally print invoice.
     *
     * Options:
     * - print_kot (bool, default true)
     * - print_invoice (bool, default false) — customer bill is Print Bill only
     *
     * @return array{jobs: array<int, array>, kot_count: int, warnings: array<int, string>}
     */
    public function processPosOrder(Order $order, array $options = []): array
    {
        $printKot     = array_key_exists('print_kot', $options) ? (bool) $options['print_kot'] : true;
        $printInvoice = array_key_exists('print_invoice', $options) ? (bool) $options['print_invoice'] : false;

        $order->loadMissing([
            'orderItems.orderItem.category',
            'diningTable',
            'waiter',
            'user',
            'restaurant',
            'posDetail',
        ]);

        $groups   = $this->groupItemsByKitchen($order);
        $this->persistItemStations($groups);

        $jobs          = [];
        $warnings      = [];
        $kotPrinterIds = [];

        if ($printKot) {
            $kotJobs = 0;
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
                if ($printer?->id) {
                    $kotPrinterIds[] = (int) $printer->id;
                }
                $kotJobs++;
            }

            if ($kotJobs === 0 && $order->orderItems->isNotEmpty()) {
                $warnings[] = 'No KOT printer configured. Order was saved; kitchen slip was not printed.';
                $jobs[]     = [
                    'type'    => 'kot',
                    'mode'    => 'none',
                    'status'  => 'skipped',
                    'message' => $warnings[0],
                    'payload' => null,
                ];
            }
        }

        if ($printInvoice) {
            $invoicePrinter = $this->resolveInvoicePrinter($order);
            // One kitchen printer set to BOTH was also dumping the customer bill
            // after every KOT. Bill only prints on a dedicated invoice printer,
            // or when the caller asks for invoice-only (Print Bill / pay).
            if ($invoicePrinter && in_array((int) $invoicePrinter->id, $kotPrinterIds, true)) {
                $printInvoice = false;
            }
        }

        if ($printInvoice) {
            $invoiceJob = $this->processInvoice($order);
            if ($invoiceJob) {
                $jobs[] = $invoiceJob;
            }
        }

        return [
            'jobs'      => $jobs,
            'kot_count' => collect($jobs)->where('type', 'kot')->where('status', '!=', 'skipped')->count(),
            'warnings'  => $warnings,
        ];
    }

    /**
     * Print invoice/bill only (dine-in final bill, or on-demand reprint).
     *
     * @return array{jobs: array<int, array>, kot_count: int, warnings: array<int, string>}
     */
    public function processInvoiceOnly(Order $order): array
    {
        $order->loadMissing([
            'orderItems.orderItem',
            'diningTable',
            'waiter',
            'user',
            'restaurant',
            'posDetail',
        ]);

        $jobs     = [];
        $warnings = [];
        $invoiceJob = $this->processInvoice($order);
        if ($invoiceJob) {
            $jobs[] = $invoiceJob;
            if (!empty($invoiceJob['message']) && empty($invoiceJob['printer_id'])) {
                $warnings[] = $invoiceJob['message'];
            }
        }

        return [
            'jobs'      => $jobs,
            'kot_count' => 0,
            'warnings'  => $warnings,
        ];
    }

    /**
     * Print modification KOT showing only deltas (ADD / REMOVE).
     *
     * @param Collection<int, OrderItemChange>|iterable $changes
     * @return array{jobs: array<int, array>, kot_count: int, warnings: array<int, string>}
     */
    public function processOrderModification(Order $order, $changes): array
    {
        $order->loadMissing([
            'orderItems.orderItem.category',
            'diningTable',
            'waiter',
            'user',
            'restaurant',
        ]);

        $changeList = collect($changes)->filter(function ($c) {
            return abs((float) $c->difference) > 0.0001
                || in_array($c->action, [OrderItemChangeAction::VOID, OrderItemChangeAction::REMOVE], true);
        });

        if ($changeList->isEmpty()) {
            return ['jobs' => [], 'kot_count' => 0, 'warnings' => []];
        }

        // Build synthetic OrderItem-like rows for kitchen grouping by item_id category
        $deltaItems = collect();
        foreach ($changeList as $change) {
            $orderItem = $order->orderItems->firstWhere('id', $change->order_item_id)
                ?: OrderItem::with('orderItem.category')->find($change->order_item_id);

            if (!$orderItem && $change->item_id) {
                $orderItem = new OrderItem([
                    'id'         => 0,
                    'item_id'    => $change->item_id,
                    'quantity'   => abs((float) $change->difference) ?: abs((float) $change->previous_quantity),
                    'order_id'   => $order->id,
                ]);
                $orderItem->setRelation('orderItem', \App\Models\Item::with('category')->find($change->item_id));
            }

            if (!$orderItem) {
                continue;
            }

            $qty = abs((float) $change->difference);
            if ($qty < 0.0001) {
                $qty = abs((float) $change->previous_quantity);
            }

            $deltaItems->push([
                'order_item' => $orderItem,
                'change'     => $change,
                'qty'        => $qty,
                'direction'  => ((float) $change->difference) < 0
                    || in_array($change->action, [OrderItemChangeAction::VOID, OrderItemChangeAction::REMOVE], true)
                    ? 'REMOVE'
                    : 'ADD',
            ]);
        }

        // Group by kitchen station using the underlying order item
        $fakeOrderItems = $deltaItems->map(fn ($row) => $row['order_item']);
        $tempOrder = clone $order;
        $tempOrder->setRelation('orderItems', $fakeOrderItems->values());
        $groups = $this->groupItemsByKitchen($tempOrder);

        $jobs     = [];
        $warnings = [];
        $kotJobs  = 0;

        foreach ($groups as $group) {
            $station = $group['station'];
            $groupItemIds = $group['items']->pluck('id')->filter()->all();

            $rows = $deltaItems->filter(function ($row) use ($groupItemIds, $group) {
                $oi = $row['order_item'];
                if ($oi->id && in_array($oi->id, $groupItemIds, true)) {
                    return true;
                }
                // new items may share station via category match
                return $group['items']->contains(fn ($gi) => (int) $gi->item_id === (int) $oi->item_id);
            });

            if ($rows->isEmpty()) {
                continue;
            }

            if ($station?->printer && $station->printer->skipsAutoKot()) {
                continue;
            }

            $printer = $this->resolveKotPrinter($order, $station);
            if ($printer && $printer->skipsAutoKot()) {
                continue;
            }

            $payload = $this->buildModificationKotPayload($order, $rows, $station);
            $ticket  = $this->storeTicket($order, $station, $printer, $payload);
            $jobs[]  = $this->dispatchPrintJob('kot', $printer, $payload, $ticket);
            $kotJobs++;
        }

        if ($kotJobs === 0) {
            $warnings[] = 'No KOT printer configured. Order changes were saved; modification slip was not printed.';
            $jobs[]     = [
                'type'    => 'kot',
                'mode'    => 'none',
                'status'  => 'skipped',
                'message' => $warnings[0],
                'payload' => null,
            ];
        }

        return [
            'jobs'      => $jobs,
            'kot_count' => $kotJobs,
            'warnings'  => $warnings,
        ];
    }

    /**
     * @param Collection<int, array{order_item: OrderItem, change: OrderItemChange, qty: float, direction: string}> $rows
     */
    protected function buildModificationKotPayload(Order $order, Collection $rows, ?KitchenStation $station): array
    {
        $mappedItems = $rows->map(function (array $row) {
            /** @var OrderItem $item */
            $item   = $row['order_item'];
            $change = $row['change'];
            $variations = is_string($item->item_variations)
                ? json_decode($item->item_variations, true)
                : ($item->item_variations ?? ($change->meta['item_variations'] ?? null));
            $extras = is_string($item->item_extras)
                ? json_decode($item->item_extras, true)
                : ($item->item_extras ?? ($change->meta['item_extras'] ?? null));

            $name = $item->orderItem?->name ?: $change->item_name;

            return [
                'name'            => $name,
                'quantity'        => $row['qty'],
                'change_label'    => $row['direction'] . ': ' . (int) $row['qty'],
                'direction'       => $row['direction'],
                'instruction'     => $item->instruction,
                'item_variations' => $variations,
                'item_extras'     => $extras,
                'variation_lines' => $this->variationLines(is_array($variations) ? $variations : null),
                'extra_lines'     => $this->extraLines(is_array($extras) ? $extras : null),
                'kitchen_status'  => $item->kitchen_status,
            ];
        })->values()->all();

        $base = $this->buildKotPayload($order, collect(), $station);
        $base['copy']            = 'ORDER UPDATE';
        $base['ticket_no']       = 'CHG - ' . $order->id . ($station ? ' / ' . $station->name : '');
        $base['kot_no']          = 'CHG-' . $order->id;
        $base['items']           = $mappedItems;
        $base['total_qty']       = (int) $rows->sum('qty');
        $base['is_modification'] = true;
        $base['modification']    = true;

        return $base;
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
        $isMod = !empty($payload['is_modification']) || !empty($payload['modification']);
        $prefix = $isMod ? 'CHG' : 'KOT';
        // Unique per print event (DB unique on order_id + ticket_no)
        $ticketNo = $prefix . '-' . $order->id . '-' . ($station?->id ?: 'x') . '-' . now()->format('Hisv') . '-' . substr((string) microtime(true), -4);

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
            'copy'             => 'KOT',
            'ticket_no'        => 'KOT - ' . $order->id . ($station ? ' / ' . $station->name : ''),
            'kot_no'           => (string) $order->id,
            // No logo_url on KOT — logo is for customer invoice only
            'restaurant'       => $order->restaurant?->name,
            'tagline'          => null,
            'phone'            => null,
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
            'customer'         => $this->invoiceCustomerName($order),
            'customer_phone'   => $this->invoiceCustomerPhone($order),
            'customer_address' => $this->invoiceCustomerAddress($order),
            'delivery_note'    => $order->delivery_note,
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

    protected function isPlaceholderCustomer(?string $name): bool
    {
        $name = trim((string) $name);
        if ($name === '') {
            return true;
        }

        return (bool) preg_match('/walking\s*customer/i', $name)
            || (bool) preg_match('/^walk-?in$/i', $name)
            || strcasecmp($name, 'guest') === 0;
    }

    protected function invoiceCustomerName(Order $order): string
    {
        $name = trim((string) ($order->customer_name ?: ''));
        if (!$this->isPlaceholderCustomer($name)) {
            return $name;
        }

        $userName = trim((string) ($order->user?->name ?: ''));
        if (!$this->isPlaceholderCustomer($userName)) {
            return $userName;
        }

        return '';
    }

    protected function invoiceCustomerPhone(Order $order): string
    {
        $phone = trim((string) ($order->customer_phone ?: ''));
        if ($phone !== '') {
            return $phone;
        }

        if ($this->isPlaceholderCustomer($order->user?->name)) {
            return '';
        }

        return trim((string) (($order->user?->country_code ?: '') . ($order->user?->phone ?: '')));
    }

    protected function invoiceCustomerAddress(Order $order): string
    {
        $address = trim((string) ($order->customer_address ?: ''));
        if ($address !== '') {
            return $address;
        }

        $apartment = $order->address?->apartment ? $order->address->apartment . ', ' : '';

        return trim($apartment . (string) ($order->address?->address ?: ''));
    }

    /**
     * POS payments are stored on the POS detail; online orders use the gateway.
     */
    protected function paymentLabel(Order $order): string
    {
        $posMethod = $order->posDetail?->payment_method;
        if ($posMethod !== null && $posMethod !== '') {
            $label = trans('pos_payment_method.' . (int) $posMethod);
            if (is_string($label) && !str_starts_with($label, 'pos_payment_method.')) {
                return $label;
            }
        }

        $label = trans('payment_gateway.' . (int) $order->payment_method);

        return is_string($label) && !str_starts_with($label, 'payment_gateway.') ? $label : '';
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
     * Prefer kitchen-station printer; otherwise any active KOT or BOTH printer.
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
            ->whereIn('print_format', [PrintFormat::KOT, PrintFormat::BOTH])
            ->orderByRaw('CASE WHEN print_format = ? THEN 0 ELSE 1 END', [PrintFormat::KOT])
            ->orderBy('id')
            ->first();
    }

    /**
     * Prefer dedicated Invoice printer; fall back to BOTH (same physical printer, separate print event).
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
            ->where('print_format', PrintFormat::BOTH)
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

        // Single GST rate is the norm; used only to label the CGST/SGST halves.
        $gstRate = (float) ($order->orderItems
            ->pluck('tax_rate')
            ->filter(fn ($rate) => (float) $rate > 0)
            ->first() ?? 0);

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
            'customer'         => $this->invoiceCustomerName($order),
            'customer_phone'   => $this->invoiceCustomerPhone($order),
            'customer_address' => $this->invoiceCustomerAddress($order),
            'delivery_note'    => $order->delivery_note,
            'order_datetime'   => AppLibrary::datetime($order->order_datetime),
            'order_date'       => AppLibrary::date($order->order_datetime),
            'order_time'       => AppLibrary::time($order->order_datetime),
            'payment_label'    => $this->paymentLabel($order),
            'items'            => $items,
            'total_qty'        => (int) $order->orderItems->sum('quantity'),
            'subtotal'         => (float) $order->subtotal,
            'tax'              => (float) $order->total_tax,
            'gst_rate'         => $gstRate,
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
