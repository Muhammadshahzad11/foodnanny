<?php

namespace App\Services;

use App\Enums\Ask;
use App\Enums\IsAdvance;
use App\Enums\KitchenItemStatus;
use App\Enums\OrderItemChangeAction;
use App\Enums\OrderStatus;
use App\Enums\OrderType;
use App\Enums\PaymentGateway;
use App\Enums\PaymentStatus;
use App\Enums\Source;
use App\Enums\Status;
use App\Enums\TableStatus;
use App\Http\Requests\PosOrderRequest;
use App\Libraries\QueryExceptionLibrary;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderItemChange;
use App\Models\OrderPosDetail;
use App\Models\OrderSetup;
use App\Models\RestaurantTable;
use App\Traits\DefaultAccessModelTrait;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * POS running dine-in orders: place (KOT only), modify + history, bill, pay/close.
 */
class PosRunningOrderService
{
    use DefaultAccessModelTrait;

    public function __construct(
        protected KotRoutingService $kotRoutingService,
        protected WaiterOrderService $waiterOrderService,
        protected KitchenOrderService $kitchenOrderService
    ) {
    }

    /**
     * Open/running POS dine-in (and unpaid delivery) orders for the current restaurant.
     */
    public function listOpenOrders(): Collection
    {
        $restaurantId = (int) $this->restaurant();

        return Order::query()
            ->with(['diningTable', 'waiter:id,name', 'orderItems.orderItem', 'user:id,name,phone'])
            ->withCount('orderItems')
            ->when($restaurantId > 0, fn ($q) => $q->where('restaurant_id', $restaurantId))
            ->where(function ($q) {
                $q->where('source', Source::POS)
                    ->orWhere(function ($inner) {
                        $inner->where('order_type', OrderType::DINING_TABLE)
                            ->whereIn('source', [Source::WEB, Source::APP, (string) Source::WEB, (string) Source::APP]);
                    });
            })
            ->where('payment_status', PaymentStatus::UNPAID)
            ->where('active', Ask::YES)
            ->whereNotIn('status', [
                OrderStatus::DELIVERED,
                OrderStatus::CANCELED,
                OrderStatus::REJECTED,
                OrderStatus::RETURNED,
            ])
            ->latest('id')
            ->get();
    }

    public function openOrderForTable(int $tableId): ?Order
    {
        $restaurantId = (int) $this->restaurant();

        return Order::query()
            ->with(['diningTable', 'waiter:id,name', 'orderItems.orderItem', 'itemChanges.user:id,name'])
            ->when($restaurantId > 0, fn ($q) => $q->where('restaurant_id', $restaurantId))
            ->where('table_id', $tableId)
            ->where('order_type', OrderType::DINING_TABLE)
            ->where('payment_status', PaymentStatus::UNPAID)
            ->where('active', Ask::YES)
            ->whereNotIn('status', [
                OrderStatus::DELIVERED,
                OrderStatus::CANCELED,
                OrderStatus::REJECTED,
                OrderStatus::RETURNED,
            ])
            ->latest('id')
            ->first();
    }

    /**
     * Place dine-in: save order unpaid/open, print KOT only, occupy table.
     *
     * @return array{order: Order, print: array}
     * @throws Exception
     */
    public function placeDineIn(PosOrderRequest $request): array
    {
        $restaurantId = (int) $this->restaurant();
        if ($restaurantId <= 0 && Auth::check()) {
            $restaurantId = (int) (Auth::user()->restaurant_id ?? 0);
        }
        if ($restaurantId <= 0) {
            throw new Exception(trans('all.message.restaurant_required_for_pos'), 422);
        }

        $order = null;

        DB::transaction(function () use ($request, $restaurantId, &$order) {
            $tableId = (int) $request->input('table_id');
            $table   = RestaurantTable::query()
                ->where('id', $tableId)
                ->where('restaurant_id', $restaurantId)
                ->first();
            if (!$table) {
                throw new Exception(trans('all.message.table_required_for_dine_in') ?: 'Please select a table.', 422);
            }

            $existing = $this->openOrderForTable($tableId);
            if ($existing) {
                throw new Exception(
                    trans('all.message.waiter_table_has_open_order') ?: 'This table already has an open order. Open it instead of creating a new one.',
                    422
                );
            }

            $orderSetup  = OrderSetup::query()
                ->where('restaurant_id', $restaurantId)
                ->select('food_preparation_time', 'schedule_order_slot_duration')
                ->first()
                ?: OrderSetup::query()->select('food_preparation_time', 'schedule_order_slot_duration')->first();
            $currentTime = Carbon::now();
            $endTime     = $currentTime->copy()->addMinutes($orderSetup?->schedule_order_slot_duration ?? 30);
            $start       = $currentTime->format('H:i');
            $end         = $endTime->format('H:i');

            $order = Order::create([
                'user_id'          => 2,
                'restaurant_id'    => $restaurantId,
                'table_id'         => $tableId,
                'waiter_id'        => Auth::id(),
                'subtotal'         => $request->subtotal,
                'discount'         => $request->discount ?? 0,
                'total'            => $request->total,
                'total_tax'        => $request->tax,
                'token'            => $request->token,
                'status'           => OrderStatus::ACCEPT,
                'payment_status'   => PaymentStatus::UNPAID,
                'order_datetime'   => date('Y-m-d H:i:s'),
                'payment_method'   => PaymentGateway::CASH_ON_DELIVERY,
                'preparation_time' => $orderSetup?->food_preparation_time ?? 30,
                'is_advance_order' => IsAdvance::NO,
                'order_type'       => OrderType::DINING_TABLE,
                'order_note'       => $request->input('order_note'),
                'customer_name'    => $request->input('customer_name'),
                'source'           => Source::POS,
                'delivery_time'    => "$start - $end",
                'delivery_fee'     => 0,
                'active'           => Ask::YES,
            ]);

            $this->insertItemsFromRequest($order, json_decode($request->items));

            $order->order_serial_no = date('dmy') . $order->id;
            $order->save();

            // Record initial ADD history (before kitchen print)
            foreach ($order->orderItems()->with('orderItem')->get() as $oi) {
                $this->recordChange($order, $oi, OrderItemChangeAction::ADD, 0, (float) $oi->quantity, false);
            }

            $table->update(['status' => TableStatus::OCCUPIED]);
        });

        $order = $order->fresh([
            'orderItems.orderItem.category',
            'diningTable',
            'waiter',
            'user',
            'restaurant',
            'itemChanges.user:id,name',
        ]);

        $this->kitchenOrderService->publishIfEligible($order);

        $print = $this->kotRoutingService->processPosOrder($order, [
            'print_invoice' => false,
            'print_kot'     => true,
        ]);

        // Mark initial changes as kot_printed
        OrderItemChange::query()
            ->where('order_id', $order->id)
            ->where('kot_printed', false)
            ->update(['kot_printed' => true]);

        return ['order' => $order, 'print' => $print];
    }

    /**
     * Sync cart onto an existing open order; print modification KOT for deltas only.
     *
     * @return array{order: Order, print: array, changes: Collection}
     * @throws Exception
     */
    public function updateOpenOrder(Order $order, PosOrderRequest $request): array
    {
        $this->assertPosOpenOrder($order);

        $pendingChanges = collect();
        $print          = ['jobs' => [], 'kot_count' => 0];

        DB::transaction(function () use ($order, $request, &$pendingChanges) {
            $order->load(['orderItems.orderItem']);
            $before = $this->indexItemsBySignature($order->orderItems);
            $incoming = collect(json_decode($request->items) ?: []);

            $afterBySig = [];
            foreach ($incoming as $item) {
                $sig = $this->itemSignatureFromRequest($item);
                if (!isset($afterBySig[$sig])) {
                    $afterBySig[$sig] = $item;
                } else {
                    $afterBySig[$sig]->quantity = (float) $afterBySig[$sig]->quantity + (float) $item->quantity;
                    $afterBySig[$sig]->total_price = (float) $afterBySig[$sig]->total_price + (float) $item->total_price;
                    $afterBySig[$sig]->tax_amount = (float) $afterBySig[$sig]->tax_amount + (float) $item->tax_amount;
                }
            }

            // Apply: update existing, insert new, remove missing
            $keepIds = [];
            foreach ($afterBySig as $sig => $item) {
                $newQty = (float) $item->quantity;
                if (isset($before[$sig])) {
                    /** @var OrderItem $existing */
                    $existing = $before[$sig];
                    $oldQty   = (float) $existing->quantity;
                    $keepIds[] = $existing->id;

                    if (abs($oldQty - $newQty) > 0.0001) {
                        $existing->update([
                            'quantity'             => $newQty,
                            'discount'             => (float) $item->discount,
                            'tax_name'             => $item->tax_name,
                            'tax_rate'             => $item->tax_rate,
                            'tax_type'             => $this->sanitizeTaxType($item->tax_type ?? null, $existing->tax_type),
                            'tax_amount'           => $item->tax_amount,
                            'price'                => $item->item_price,
                            'item_variations'      => json_encode($item->item_variations),
                            'item_extras'          => json_encode($item->item_extras),
                            'instruction'          => $item->instruction ?? null,
                            'item_variation_total' => $item->item_variation_total,
                            'item_extra_total'     => $item->item_extra_total,
                            'total_price'          => $item->total_price,
                        ]);
                        $action = $newQty < $oldQty
                            ? ($newQty <= 0 ? OrderItemChangeAction::VOID : OrderItemChangeAction::QUANTITY_CHANGE)
                            : OrderItemChangeAction::QUANTITY_CHANGE;
                        if ($newQty <= 0) {
                            $pendingChanges->push($this->recordChange($order, $existing, OrderItemChangeAction::VOID, $oldQty, 0, false));
                            $existing->delete();
                            array_pop($keepIds);
                        } else {
                            $pendingChanges->push($this->recordChange($order, $existing, $action, $oldQty, $newQty, false));
                        }
                    }
                } else {
                    $oi = OrderItem::create([
                        'order_id'             => $order->id,
                        'restaurant_id'        => $order->restaurant_id,
                        'item_id'              => $item->item_id,
                        'quantity'             => $newQty,
                        'discount'             => (float) $item->discount,
                        'tax_name'             => $item->tax_name,
                        'tax_rate'             => $item->tax_rate,
                        'tax_type'             => $this->sanitizeTaxType($item->tax_type ?? null),
                        'tax_amount'           => $item->tax_amount,
                        'price'                => $item->item_price,
                        'item_variations'      => json_encode($item->item_variations),
                        'item_extras'          => json_encode($item->item_extras),
                        'instruction'          => $item->instruction ?? null,
                        'item_variation_total' => $item->item_variation_total,
                        'item_extra_total'     => $item->item_extra_total,
                        'total_price'          => $item->total_price,
                        'status'               => Status::ACTIVE,
                        'kitchen_status'       => KitchenItemStatus::PENDING,
                    ]);
                    $oi->load('orderItem');
                    $keepIds[] = $oi->id;
                    $pendingChanges->push($this->recordChange($order, $oi, OrderItemChangeAction::ADD, 0, $newQty, false));
                }
            }

            foreach ($before as $sig => $existing) {
                if (!isset($afterBySig[$sig])) {
                    $oldQty = (float) $existing->quantity;
                    $pendingChanges->push($this->recordChange($order, $existing, OrderItemChangeAction::VOID, $oldQty, 0, false));
                    $existing->delete();
                }
            }

            $order->update([
                'subtotal'   => $request->subtotal,
                'discount'   => $request->discount ?? 0,
                'total'      => $request->total,
                'total_tax'  => $request->tax,
                'order_note' => $request->input('order_note', $order->order_note),
                'waiter_id'  => Auth::id(),
                'token'      => $request->filled('token') ? $request->token : $order->token,
            ]);
        });

        $order = $order->fresh([
            'orderItems.orderItem.category',
            'diningTable',
            'waiter',
            'user',
            'restaurant',
            'itemChanges.user:id,name',
        ]);

        if ($pendingChanges->isNotEmpty()) {
            $print = $this->kotRoutingService->processOrderModification($order, $pendingChanges);
            OrderItemChange::query()
                ->whereIn('id', $pendingChanges->pluck('id')->all())
                ->update(['kot_printed' => true]);
            $this->kitchenOrderService->publishIfEligible($order);
        }

        return [
            'order'   => $order,
            'print'   => $print,
            'changes' => $pendingChanges,
        ];
    }

    /**
     * Print final bill/invoice only (does not close order).
     *
     * @return array{order: Order, print: array}
     * @throws Exception
     */
    public function printBill(Order $order): array
    {
        $this->assertPosOpenOrder($order);

        $order->billing_requested_at = now();
        $order->save();

        return $this->reprintInvoice($order);
    }

    /**
     * On-demand invoice reprint from POS order view (open or closed).
     *
     * @return array{order: Order, print: array}
     * @throws Exception
     */
    public function reprintInvoice(Order $order): array
    {
        $this->assertPosOrderAccess($order);

        if ((int) $order->payment_status === PaymentStatus::UNPAID
            && !in_array((int) $order->status, [OrderStatus::CANCELED, OrderStatus::REJECTED], true)
        ) {
            $order->billing_requested_at = now();
            $order->save();
        }

        $order = $order->fresh([
            'orderItems.orderItem',
            'diningTable',
            'waiter',
            'user',
            'restaurant',
            'posDetail',
        ]);

        $print = $this->kotRoutingService->processInvoiceOnly($order);

        return ['order' => $order, 'print' => $print];
    }

    /**
     * Take payment and close order / free table.
     *
     * @return array{order: Order, print: array}
     * @throws Exception
     */
    public function payAndClose(Order $order, PosOrderRequest $request, bool $printInvoice = true): array
    {
        $this->assertPosOpenOrder($order);

        $tableId = $order->table_id;

        DB::transaction(function () use ($order, $request) {
            $order->update([
                'subtotal'       => $request->subtotal,
                'discount'       => $request->discount ?? 0,
                'total'          => $request->total,
                'total_tax'      => $request->tax,
                'payment_status' => PaymentStatus::PAID,
                'status'         => OrderStatus::DELIVERED,
                'payment_method' => 1,
                'waiter_id'      => Auth::id(),
            ]);

            OrderPosDetail::updateOrCreate(
                ['order_id' => $order->id],
                [
                    'payment_method'  => $request->payment_method,
                    'payment_note'    => $request->payment_note,
                    'received_amount' => $request->received_amount,
                ]
            );
        });

        $order = $order->fresh([
            'orderItems.orderItem',
            'diningTable',
            'waiter',
            'user',
            'restaurant',
            'posDetail',
        ]);

        if ($tableId) {
            // Always free the table after payment — do not leave OCCUPIED/RESERVED
            $table = RestaurantTable::query()->find((int) $tableId);
            if ($table) {
                $previous = (int) $table->status;
                $table->update(['status' => TableStatus::AVAILABLE]);
                try {
                    app(RealtimePublisher::class)->table($table->fresh(), 'status', $previous);
                } catch (\Throwable $e) {
                    Log::info('payAndClose table free realtime: ' . $e->getMessage());
                }
            } else {
                $this->waiterOrderService->releaseTableIfIdle((int) $tableId);
            }
        }

        $print = ['jobs' => [], 'kot_count' => 0];
        if ($printInvoice) {
            $print = $this->kotRoutingService->processInvoiceOnly($order);
        }

        return ['order' => $order, 'print' => $print];
    }

    public function history(Order $order): Collection
    {
        $this->assertPosOrderAccess($order);

        return OrderItemChange::query()
            ->with('user:id,name')
            ->where('order_id', $order->id)
            ->orderBy('id')
            ->get();
    }

    /**
     * POS staff can set table Available / Occupied / Reserved (etc.).
     *
     * @throws Exception
     */
    public function updateTableStatus(int $tableId, int $status): RestaurantTable
    {
        $restaurantId = (int) $this->restaurant();
        if ($restaurantId <= 0 && Auth::check()) {
            $restaurantId = (int) (Auth::user()->restaurant_id ?? 0);
        }

        $allowed = [
            TableStatus::AVAILABLE,
            TableStatus::OCCUPIED,
            TableStatus::RESERVED,
            TableStatus::CLEANING,
            TableStatus::OUT_OF_SERVICE,
        ];
        if (!in_array($status, $allowed, true)) {
            throw new Exception('Invalid table status.', 422);
        }

        $table = RestaurantTable::query()
            ->when($restaurantId > 0, fn ($q) => $q->where('restaurant_id', $restaurantId))
            ->find($tableId);
        if (!$table) {
            throw new Exception(trans('all.message.permission_denied') ?: 'Table not found.', 422);
        }

        // Do not mark Available while an unpaid open dine-in order still exists
        if ($status === TableStatus::AVAILABLE) {
            $open = $this->openOrderForTable($tableId);
            if ($open) {
                throw new Exception(
                    'This table has an open order. Pay/close the order first, or free the table after payment.',
                    422
                );
            }
        }

        $previous = (int) $table->status;
        $table->update(['status' => $status]);
        try {
            app(RealtimePublisher::class)->table($table->fresh(), 'status', $previous);
        } catch (\Throwable $e) {
            Log::info('POS updateTableStatus realtime: ' . $e->getMessage());
        }

        return $table->fresh();
    }

    /**
     * @throws Exception
     */
    public function assertPosOpenOrder(Order $order): void
    {
        $this->assertPosOrderAccess($order);

        if ((int) $order->payment_status === PaymentStatus::PAID) {
            throw new Exception('This order is already paid/closed.', 422);
        }
        if (in_array((int) $order->status, [OrderStatus::DELIVERED, OrderStatus::CANCELED, OrderStatus::REJECTED], true)) {
            throw new Exception('This order can no longer be modified.', 422);
        }
    }

    /**
     * @throws Exception
     */
    protected function assertPosOrderAccess(Order $order): void
    {
        $scoped = (int) $this->restaurant();
        if ($scoped > 0 && (int) $order->restaurant_id !== $scoped) {
            throw new Exception(trans('all.message.permission_denied'), 422);
        }
        if ((int) $order->source !== Source::POS && (int) $order->order_type !== OrderType::DINING_TABLE) {
            // Allow dine-in from waiter opened via POS too when unpaid
            if ((int) $order->payment_status !== PaymentStatus::UNPAID) {
                throw new Exception(trans('all.message.permission_denied'), 422);
            }
        }
    }

    protected function insertItemsFromRequest(Order $order, $requestItems): void
    {
        $rows = [];
        if (!blank($requestItems)) {
            foreach ($requestItems as $item) {
                $rows[] = [
                    'order_id'             => $order->id,
                    'restaurant_id'        => $order->restaurant_id,
                    'item_id'              => $item->item_id,
                    'quantity'             => $item->quantity,
                    'discount'             => (float) $item->discount,
                    'tax_name'             => $item->tax_name,
                    'tax_rate'             => $item->tax_rate,
                    'tax_type'             => $this->sanitizeTaxType($item->tax_type ?? null),
                    'tax_amount'           => $item->tax_amount,
                    'price'                => $item->item_price,
                    'item_variations'      => json_encode($item->item_variations),
                    'item_extras'          => json_encode($item->item_extras),
                    'instruction'          => $item->instruction ?? null,
                    'item_variation_total' => $item->item_variation_total,
                    'item_extra_total'     => $item->item_extra_total,
                    'total_price'          => $item->total_price,
                    'status'               => Status::ACTIVE,
                    'kitchen_status'       => KitchenItemStatus::PENDING,
                    'created_at'           => now(),
                    'updated_at'           => now(),
                ];
            }
        }
        if (!blank($rows)) {
            OrderItem::insert($rows);
        }
    }

    protected function recordChange(
        Order $order,
        OrderItem $orderItem,
        string $action,
        float $previousQty,
        float $newQty,
        bool $kotPrinted
    ): OrderItemChange {
        $diff = $newQty - $previousQty;

        return OrderItemChange::create([
            'restaurant_id'     => $order->restaurant_id,
            'order_id'          => $order->id,
            'order_item_id'     => $orderItem->id,
            'item_id'           => $orderItem->item_id,
            'item_name'         => $orderItem->orderItem?->name,
            'action'            => $action,
            'previous_quantity' => $previousQty,
            'new_quantity'      => $newQty,
            'difference'        => $diff,
            'kot_printed'       => $kotPrinted,
            'user_id'           => Auth::id(),
            'meta'              => [
                'item_variations' => json_decode($orderItem->item_variations, true),
                'item_extras'     => json_decode($orderItem->item_extras, true),
            ],
        ]);
    }

    /**
     * @param Collection<int, OrderItem> $items
     * @return array<string, OrderItem>
     */
    protected function indexItemsBySignature(Collection $items): array
    {
        $map = [];
        foreach ($items as $item) {
            $map[$this->itemSignatureFromOrderItem($item)] = $item;
        }

        return $map;
    }

    protected function itemSignatureFromOrderItem(OrderItem $item): string
    {
        return $this->buildSignature(
            (int) $item->item_id,
            json_decode($item->item_variations, true),
            json_decode($item->item_extras, true),
            (string) ($item->instruction ?? '')
        );
    }

    protected function itemSignatureFromRequest(object $item): string
    {
        return $this->buildSignature(
            (int) $item->item_id,
            json_decode(json_encode($item->item_variations), true),
            json_decode(json_encode($item->item_extras), true),
            (string) ($item->instruction ?? '')
        );
    }

    /**
     * Order APIs sometimes expose tax_type as "%" / currency code for display.
     * Persist only TaxType integers (FIXED=5, PERCENTAGE=10).
     */
    protected function sanitizeTaxType(mixed $value, mixed $fallback = null): ?int
    {
        // Guard against infinite recursion when both value and fallback are empty
        if ($value === null || $value === '') {
            if ($fallback === null || $fallback === '' || $fallback === $value) {
                return \App\Enums\TaxType::PERCENTAGE;
            }

            return $this->sanitizeTaxType($fallback, null);
        }

        if (is_numeric($value)) {
            $int = (int) $value;
            if (in_array($int, [\App\Enums\TaxType::FIXED, \App\Enums\TaxType::PERCENTAGE], true)) {
                return $int;
            }
        }

        $str = trim((string) $value);
        if ($str === '%' || str_contains($str, '%')) {
            return \App\Enums\TaxType::PERCENTAGE;
        }
        if ($str !== '' && !is_numeric($str)) {
            // Currency code / symbol from OrderItemResource display field
            return \App\Enums\TaxType::FIXED;
        }

        if ($fallback !== null && $fallback !== '' && $fallback !== $value) {
            return $this->sanitizeTaxType($fallback, null);
        }

        return \App\Enums\TaxType::PERCENTAGE;
    }

    protected function buildSignature(int $itemId, $variations, $extras, string $instruction): string
    {
        $norm = function ($v) {
            if (!is_array($v)) {
                return [];
            }
            // Sort keys for stable JSON
            ksort($v);

            return $v;
        };

        return md5(json_encode([
            $itemId,
            $norm($variations),
            $norm($extras),
            trim($instruction),
        ]));
    }
}
