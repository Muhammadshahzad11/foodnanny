<?php

namespace App\Services;

use App\Enums\Ask;
use App\Enums\IsAdvance;
use App\Enums\OrderStatus;
use App\Enums\OrderType;
use App\Enums\PaymentGateway;
use App\Enums\PaymentStatus;
use App\Enums\Source;
use App\Enums\Status;
use App\Enums\TableStatus;
use App\Http\Requests\PaginateRequest;
use App\Http\Requests\WaiterOrderRequest;
use App\Http\Requests\WaiterOrderUpdateRequest;
use App\Libraries\QueryExceptionLibrary;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderSetup;
use App\Models\RestaurantTable;
use App\Traits\DefaultAccessModelTrait;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class WaiterOrderService
{
    use DefaultAccessModelTrait;

    public object $order;

    public function __construct(
        protected DineInOrderGuard $dineInOrderGuard,
        protected RestaurantTableService $restaurantTableService
    ) {
    }

    /**
     * @throws Exception
     */
    public function dashboard(): array
    {
        try {
            $base = Order::query()
                ->where('order_type', OrderType::DINING_TABLE)
                ->where(function ($query) {
                    $query->where(function ($draft) {
                        $draft->where('status', OrderStatus::PENDING)
                            ->where('active', Ask::NO);
                    })->orWhere(function ($open) {
                        $open->where('active', Ask::YES)
                            ->whereNotIn('status', [
                                OrderStatus::DELIVERED,
                                OrderStatus::CANCELED,
                                OrderStatus::REJECTED,
                                OrderStatus::RETURNED,
                            ]);
                    });
                });

            $tables = RestaurantTable::query()
                ->whereNotIn('status', [TableStatus::INACTIVE, TableStatus::OUT_OF_SERVICE])
                ->get(['id', 'status']);

            return [
                'tables_total'     => $tables->count(),
                'tables_available' => $tables->where('status', TableStatus::AVAILABLE)->count(),
                'tables_occupied'  => $tables->where('status', TableStatus::OCCUPIED)->count(),
                'draft_orders'     => (clone $base)->where('status', OrderStatus::PENDING)->where('active', Ask::NO)->count(),
                'kitchen_orders'   => (clone $base)->where('active', Ask::YES)->whereIn('status', [
                    OrderStatus::ACCEPT,
                    OrderStatus::PREPARING,
                    OrderStatus::PREPARED,
                ])->count(),
                'ready_orders'     => (clone $base)->where('status', OrderStatus::PREPARED)->where('active', Ask::YES)->count(),
            ];
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }

    /**
     * @throws Exception
     */
    public function tables(PaginateRequest $request)
    {
        try {
            $requests    = $request->all();
            $method      = $request->get('paginate', 0) == 1 ? 'paginate' : 'get';
            $methodValue = $request->get('paginate', 0) == 1 ? $request->get('per_page', 50) : '*';
            $orderColumn = $request->get('order_column') ?? 'table_number';
            $orderType   = $request->get('order_type') ?? 'asc';
            $search      = trim((string) $request->get('search', ''));

            $openStatuses = [
                OrderStatus::PENDING,
                OrderStatus::ACCEPT,
                OrderStatus::PREPARING,
                OrderStatus::PREPARED,
            ];

            return RestaurantTable::query()
                ->with(['restaurant:id,name'])
                ->with(['orders' => function ($query) use ($openStatuses) {
                    $query->where('order_type', OrderType::DINING_TABLE)
                        ->whereIn('status', $openStatuses)
                        ->where(function ($inner) {
                            $inner->where(function ($draft) {
                                $draft->where('status', OrderStatus::PENDING)->where('active', Ask::NO);
                            })->orWhere('active', Ask::YES);
                        })
                        ->with(['waiter:id,name', 'orderItems.orderItem'])
                        ->latest('id')
                        ->limit(1);
                }])
                ->whereNotIn('status', [TableStatus::INACTIVE])
                ->where(function ($query) use ($requests, $search) {
                    if (!empty($requests['status'])) {
                        $query->where('status', $requests['status']);
                    }
                    if (!empty($requests['zone'])) {
                        $query->where('zone', 'like', '%' . $requests['zone'] . '%');
                    }
                    if ($search !== '') {
                        $query->where(function ($inner) use ($search) {
                            $inner->where('name', 'like', '%' . $search . '%')
                                ->orWhere('table_number', 'like', '%' . $search . '%')
                                ->orWhere('zone', 'like', '%' . $search . '%');
                        });
                    }
                })
                ->orderBy($orderColumn, $orderType)
                ->$method($methodValue);
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }

    /**
     * @throws Exception
     */
    public function tableShow(RestaurantTable $restaurantTable): RestaurantTable
    {
        try {
            $this->restaurantTableService->assertCanManage($restaurantTable);

            return $restaurantTable->load([
                'restaurant:id,name',
                'orders' => function ($query) {
                    $query->where('order_type', OrderType::DINING_TABLE)
                        ->whereIn('status', [
                            OrderStatus::PENDING,
                            OrderStatus::ACCEPT,
                            OrderStatus::PREPARING,
                            OrderStatus::PREPARED,
                        ])
                        ->where(function ($inner) {
                            $inner->where(function ($draft) {
                                $draft->where('status', OrderStatus::PENDING)->where('active', Ask::NO);
                            })->orWhere('active', Ask::YES);
                        })
                        ->with(['waiter:id,name', 'orderItems.orderItem', 'diningTable'])
                        ->latest('id');
                },
            ]);
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception(
                $exception->getCode() === 422 ? $exception->getMessage() : QueryExceptionLibrary::message($exception),
                422
            );
        }
    }

    /**
     * @throws Exception
     */
    public function list(PaginateRequest $request)
    {
        try {
            $requests    = $request->all();
            $method      = $request->get('paginate', 0) == 1 ? 'paginate' : 'get';
            $methodValue = $request->get('paginate', 0) == 1 ? $request->get('per_page', 20) : '*';
            $orderColumn = $request->get('order_column') ?? 'id';
            $orderType   = $request->get('order_by') ?? 'desc';

            return Order::with(['diningTable', 'waiter:id,name', 'orderItems.orderItem', 'restaurant:id,name'])
                ->where('order_type', OrderType::DINING_TABLE)
                ->where(function ($query) use ($requests) {
                    if (!empty($requests['table_id'])) {
                        $query->where('table_id', $requests['table_id']);
                    }
                    if (!empty($requests['status'])) {
                        $query->where('status', $requests['status']);
                    }
                    if (isset($requests['active']) && $requests['active'] !== '') {
                        $query->where('active', $requests['active']);
                    }
                    if (!empty($requests['waiter_id'])) {
                        $query->where('waiter_id', $requests['waiter_id']);
                    }
                })
                ->orderBy($orderColumn, $orderType)
                ->$method($methodValue);
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }

    /**
     * @throws Exception
     */
    public function show(Order $order): Order
    {
        try {
            $this->assertWaiterOrder($order);

            return $order->load([
                'diningTable',
                'waiter:id,name',
                'orderItems.orderItem',
                'restaurant:id,name',
                'user:id,name,email,phone',
            ]);
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception(
                $exception->getCode() === 422 ? $exception->getMessage() : QueryExceptionLibrary::message($exception),
                422
            );
        }
    }

    /**
     * @throws Exception
     */
    public function store(WaiterOrderRequest $request): Order
    {
        try {
            DB::transaction(function () use ($request) {
                $restaurantId = (int) $this->restaurant();
                if ($restaurantId <= 0) {
                    throw new Exception(trans('all.message.restaurant_required_for_table'), 422);
                }

                $table = $this->dineInOrderGuard->assertStaffOrderableTable($restaurantId, (int) $request->table_id);

                $existing = $this->openOrderForTable($table->id);
                if ($existing) {
                    throw new Exception(trans('all.message.waiter_table_has_open_order'), 422);
                }

                $orderSetup  = OrderSetup::select('food_preparation_time', 'schedule_order_slot_duration')->first();
                $currentTime = Carbon::now();
                $endTime     = $currentTime->copy()->addMinutes($orderSetup?->schedule_order_slot_duration ?? 30);
                $start       = $currentTime->format('H:i');
                $end         = $endTime->format('H:i');
                $sendKitchen = (bool) $request->boolean('send_to_kitchen');

                $this->order = Order::create([
                    'user_id'          => 2,
                    'table_id'         => $table->id,
                    'waiter_id'        => Auth::id(),
                    'subtotal'         => $request->subtotal,
                    'discount'         => $request->discount ?? 0,
                    'total'            => $request->total,
                    'total_tax'        => $request->tax,
                    'token'            => $request->token,
                    'order_note'       => $request->order_note,
                    'status'           => $sendKitchen ? OrderStatus::ACCEPT : OrderStatus::PENDING,
                    'payment_status'   => PaymentStatus::UNPAID,
                    'order_datetime'   => date('Y-m-d H:i:s'),
                    'payment_method'   => PaymentGateway::CASH_ON_DELIVERY,
                    'preparation_time' => $orderSetup?->food_preparation_time ?? 30,
                    'is_advance_order' => IsAdvance::NO,
                    'order_type'       => OrderType::DINING_TABLE,
                    'source'           => Source::WAITER,
                    'delivery_time'    => "$start - $end",
                    'delivery_fee'     => 0,
                    'active'           => $sendKitchen ? Ask::YES : Ask::NO,
                ]);

                $this->syncOrderItems($this->order, json_decode($request->items));

                $this->order->order_serial_no = date('dmy') . $this->order->id;
                $this->order->save();

                $table->update(['status' => TableStatus::OCCUPIED]);
            });

            return $this->show($this->order->fresh());
        } catch (Exception $exception) {
            DB::rollBack();
            Log::info($exception->getMessage());
            throw new Exception(
                $exception->getCode() === 422 ? $exception->getMessage() : QueryExceptionLibrary::message($exception),
                422
            );
        }
    }

    /**
     * @throws Exception
     */
    public function update(WaiterOrderUpdateRequest $request, Order $order): Order
    {
        try {
            DB::transaction(function () use ($request, $order) {
                $this->assertWaiterOrder($order);
                $this->assertEditable($order);
                $this->assertOptimisticLock($order, $request->input('updated_at'));

                $payload = [
                    'subtotal'   => $request->subtotal,
                    'discount'   => $request->discount ?? 0,
                    'total'      => $request->total,
                    'total_tax'  => $request->tax,
                    'waiter_id'  => Auth::id(),
                ];

                if ($request->filled('token') || $request->has('token')) {
                    $payload['token'] = $request->token;
                }
                if ($request->has('order_note')) {
                    $payload['order_note'] = $request->order_note;
                }

                $order->update($payload);
                $this->syncOrderItems($order, json_decode($request->items));
                $this->order = $order->fresh();
            });

            return $this->show($this->order);
        } catch (Exception $exception) {
            DB::rollBack();
            Log::info($exception->getMessage());
            throw new Exception(
                $exception->getCode() === 422 ? $exception->getMessage() : QueryExceptionLibrary::message($exception),
                422
            );
        }
    }

    /**
     * @throws Exception
     */
    public function sendToKitchen(Order $order, ?string $updatedAt = null): Order
    {
        try {
            DB::transaction(function () use ($order, $updatedAt) {
                $this->assertWaiterOrder($order);
                $this->assertEditable($order);
                $this->assertOptimisticLock($order, $updatedAt);

                if ($order->orderItems()->count() === 0) {
                    throw new Exception(trans('all.message.waiter_order_requires_items'), 422);
                }

                $order->status = OrderStatus::ACCEPT;
                $order->active = Ask::YES;
                $order->waiter_id = Auth::id();
                $order->save();

                if ($order->diningTable) {
                    $order->diningTable->update(['status' => TableStatus::OCCUPIED]);
                }

                $this->order = $order->fresh();
            });

            return $this->show($this->order);
        } catch (Exception $exception) {
            DB::rollBack();
            Log::info($exception->getMessage());
            throw new Exception(
                $exception->getCode() === 422 ? $exception->getMessage() : QueryExceptionLibrary::message($exception),
                422
            );
        }
    }

    /**
     * @throws Exception
     */
    public function cancelDraft(Order $order): void
    {
        try {
            DB::transaction(function () use ($order) {
                $this->assertWaiterOrder($order);

                if ((int) $order->status !== OrderStatus::PENDING || (int) $order->active !== Ask::NO) {
                    throw new Exception(trans('all.message.waiter_only_draft_cancel'), 422);
                }

                $tableId = $order->table_id;
                $order->orderItems()->delete();
                $order->delete();

                $this->releaseTableIfIdle($tableId);
            });
        } catch (Exception $exception) {
            DB::rollBack();
            Log::info($exception->getMessage());
            throw new Exception(
                $exception->getCode() === 422 ? $exception->getMessage() : QueryExceptionLibrary::message($exception),
                422
            );
        }
    }

    /**
     * Mark table AVAILABLE when no open dine-in orders remain.
     */
    public function releaseTableIfIdle(?int $tableId): void
    {
        if (!$tableId) {
            return;
        }

        $hasOpen = $this->openOrderForTable($tableId) !== null;
        if ($hasOpen) {
            return;
        }

        $table = RestaurantTable::query()->find($tableId);
        if ($table && (int) $table->status === TableStatus::OCCUPIED) {
            $table->update(['status' => TableStatus::AVAILABLE]);
        }
    }

    protected function openOrderForTable(int $tableId): ?Order
    {
        return Order::query()
            ->where('table_id', $tableId)
            ->where('order_type', OrderType::DINING_TABLE)
            ->whereIn('status', [
                OrderStatus::PENDING,
                OrderStatus::ACCEPT,
                OrderStatus::PREPARING,
                OrderStatus::PREPARED,
            ])
            ->where(function ($query) {
                $query->where(function ($draft) {
                    $draft->where('status', OrderStatus::PENDING)->where('active', Ask::NO);
                })->orWhere('active', Ask::YES);
            })
            ->latest('id')
            ->first();
    }

    /**
     * @throws Exception
     */
    protected function assertWaiterOrder(Order $order): void
    {
        if ((int) $order->order_type !== OrderType::DINING_TABLE) {
            throw new Exception(trans('all.message.permission_denied'), 422);
        }

        $scoped = (int) $this->restaurant();
        if ($scoped > 0 && (int) $order->restaurant_id !== $scoped) {
            throw new Exception(trans('all.message.permission_denied'), 422);
        }
    }

    /**
     * @throws Exception
     */
    protected function assertEditable(Order $order): void
    {
        $locked = [
            OrderStatus::PREPARED,
            OrderStatus::DELIVERED,
            OrderStatus::CANCELED,
            OrderStatus::REJECTED,
            OrderStatus::RETURNED,
        ];

        if (in_array((int) $order->status, $locked, true)) {
            throw new Exception(trans('all.message.waiter_order_locked'), 422);
        }
    }

    /**
     * @throws Exception
     */
    protected function assertOptimisticLock(Order $order, ?string $updatedAt): void
    {
        if ($updatedAt === null || $updatedAt === '') {
            return;
        }

        $incoming = Carbon::parse($updatedAt)->timestamp;
        $current  = optional($order->updated_at)->timestamp;

        if ($current && $incoming !== $current) {
            throw new Exception(trans('all.message.waiter_order_stale'), 422);
        }
    }

    protected function syncOrderItems(Order $order, $requestItems): void
    {
        OrderItem::where('order_id', $order->id)->delete();

        $itemsArray = [];
        $i          = 0;

        if (!blank($requestItems)) {
            foreach ($requestItems as $item) {
                $itemsArray[$i] = [
                    'order_id'             => $order->id,
                    'restaurant_id'        => $order->restaurant_id,
                    'item_id'              => $item->item_id,
                    'quantity'             => $item->quantity,
                    'discount'             => (float) $item->discount,
                    'tax_name'             => $item->tax_name,
                    'tax_rate'             => $item->tax_rate,
                    'tax_type'             => $item->tax_type,
                    'tax_amount'           => $item->tax_amount,
                    'price'                => $item->item_price,
                    'item_variations'      => json_encode($item->item_variations),
                    'item_extras'          => json_encode($item->item_extras),
                    'instruction'          => $item->instruction ?? null,
                    'item_variation_total' => $item->item_variation_total,
                    'item_extra_total'     => $item->item_extra_total,
                    'total_price'          => $item->total_price,
                    'status'               => Status::ACTIVE,
                    'created_at'           => now(),
                    'updated_at'           => now(),
                ];
                $i++;
            }
        }

        if (!blank($itemsArray)) {
            OrderItem::insert($itemsArray);
        }
    }
}
