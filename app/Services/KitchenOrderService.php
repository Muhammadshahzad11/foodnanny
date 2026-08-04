<?php

namespace App\Services;

use App\Enums\Ask;
use App\Enums\KitchenItemStatus;
use App\Enums\KitchenPriority;
use App\Enums\OrderStatus;
use App\Enums\OrderType;
use App\Http\Requests\PaginateRequest;
use App\Libraries\AppLibrary;
use App\Libraries\QueryExceptionLibrary;
use App\Models\KitchenStatusLog;
use App\Models\KitchenTicket;
use App\Models\Order;
use App\Models\OrderItem;
use App\Traits\DefaultAccessModelTrait;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class KitchenOrderService
{
    use DefaultAccessModelTrait;

    /** Kitchen-eligible order types (not pure delivery routing). */
    public const ELIGIBLE_TYPES = [
        OrderType::DINING_TABLE,
        OrderType::POS,
        OrderType::TAKEAWAY,
    ];

    public function __construct(protected OrderService $orderService)
    {
    }

    /**
     * @throws Exception
     */
    public function dashboard(): array
    {
        try {
            $today = Carbon::today()->toDateString();
            $base  = $this->kitchenBaseQuery()->whereDate('order_datetime', $today);

            $counts = (clone $base)
                ->selectRaw('status, COUNT(*) as aggregate')
                ->groupBy('status')
                ->pluck('aggregate', 'status');

            $queue = (clone $base)->whereIn('status', [
                OrderStatus::PENDING,
                OrderStatus::ACCEPT,
                OrderStatus::PREPARING,
                OrderStatus::PREPARED,
            ])->count();

            $readyAvgSeconds = KitchenStatusLog::query()
                ->whereDate('created_at', $today)
                ->where('action', 'ready')
                ->whereNotNull('meta')
                ->get()
                ->avg(fn ($log) => (int) data_get($log->meta, 'elapsed_seconds', 0));

            return [
                'today_orders'      => (clone $base)->count(),
                'pending_orders'    => (int) ($counts[OrderStatus::PENDING] ?? 0),
                'accepted_orders'   => (int) ($counts[OrderStatus::ACCEPT] ?? 0),
                'preparing_orders'  => (int) ($counts[OrderStatus::PREPARING] ?? 0),
                'ready_orders'      => (int) ($counts[OrderStatus::PREPARED] ?? 0),
                'completed_orders'  => (int) ($counts[OrderStatus::DELIVERED] ?? 0),
                'cancelled_orders'  => (int) (($counts[OrderStatus::CANCELED] ?? 0) + ($counts[OrderStatus::REJECTED] ?? 0)),
                'preparation_queue' => $queue,
                'avg_ready_seconds' => $readyAvgSeconds ? (int) round($readyAvgSeconds) : 0,
            ];
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }

    /**
     * @throws Exception
     */
    public function queue(PaginateRequest $request)
    {
        try {
            $requests    = $request->all();
            $method      = $request->get('paginate', 0) == 1 ? 'paginate' : 'get';
            $methodValue = $request->get('paginate', 0) == 1 ? $request->get('per_page', 50) : '*';
            $sort        = $request->get('sort', 'order_time');
            $search      = trim((string) $request->get('search', ''));
            $period      = $request->get('period', 'today');

            $query = $this->kitchenBaseQuery()
                ->with([
                    'diningTable:id,name,table_number,zone',
                    'waiter:id,name',
                    'user:id,name',
                    'restaurant:id,name',
                    'kitchenStation:id,name,code',
                    'kitchenAcceptedBy:id,name',
                    'kitchenPreparingBy:id,name',
                    'kitchenReadyBy:id,name',
                    'orderItems.orderItem:id,name',
                ]);

            if (!empty($requests['from_date']) || !empty($requests['to_date'])) {
                // explicit date range — skip default today filter
            } elseif ($period === 'today' || empty($period)) {
                $query->whereDate('order_datetime', Carbon::today()->toDateString());
            }

            if (!empty($requests['status'])) {
                $status = (int) $requests['status'];
                if ($status === OrderStatus::CANCELED) {
                    $query->whereIn('status', [OrderStatus::CANCELED, OrderStatus::REJECTED]);
                } else {
                    $query->where('status', $status);
                }
            } else {
                // Default active kitchen board
                $query->whereIn('status', [
                    OrderStatus::PENDING,
                    OrderStatus::ACCEPT,
                    OrderStatus::PREPARING,
                    OrderStatus::PREPARED,
                ]);
            }

            if (!empty($requests['table_id'])) {
                $query->where('table_id', $requests['table_id']);
            }
            if (!empty($requests['waiter_id'])) {
                $query->where('waiter_id', $requests['waiter_id']);
            }
            if (!empty($requests['kitchen_station_id'])) {
                $query->where('kitchen_station_id', $requests['kitchen_station_id']);
            }
            if (isset($requests['kitchen_priority']) && $requests['kitchen_priority'] !== '') {
                $query->where('kitchen_priority', (int) $requests['kitchen_priority']);
            }
            if (!empty($requests['from_date'])) {
                $query->whereDate('order_datetime', '>=', $requests['from_date']);
            }
            if (!empty($requests['to_date'])) {
                $query->whereDate('order_datetime', '<=', $requests['to_date']);
            }

            if ($search !== '') {
                $query->where(function ($inner) use ($search) {
                    $inner->where('order_serial_no', 'like', '%' . $search . '%')
                        ->orWhereHas('diningTable', function ($table) use ($search) {
                            $table->where('table_number', 'like', '%' . $search . '%')
                                ->orWhere('name', 'like', '%' . $search . '%');
                        })
                        ->orWhereHas('waiter', function ($waiter) use ($search) {
                            $waiter->where('name', 'like', '%' . $search . '%');
                        })
                        ->orWhereHas('user', function ($user) use ($search) {
                            $user->where('name', 'like', '%' . $search . '%');
                        });
                });
            }

            match ($sort) {
                'priority' => $query->orderByDesc('kitchen_priority')->orderBy('order_datetime')->orderBy('id'),
                'oldest' => $query->orderBy('order_datetime')->orderBy('id'),
                'preparation_time' => $query->orderBy('preparation_time')->orderBy('order_datetime'),
                'table' => $query->orderBy('table_id')->orderBy('order_datetime'),
                'waiter' => $query->orderBy('waiter_id')->orderBy('order_datetime'),
                'order_number' => $query->orderBy('order_serial_no'),
                // Highest priority, then oldest waiting
                default => $query->orderByDesc('kitchen_priority')->orderBy('order_datetime')->orderBy('id'),
            };

            return $query->$method($methodValue);
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
            $this->assertKitchenOrder($order);

            return $order->load([
                'diningTable:id,name,table_number,zone',
                'waiter:id,name',
                'user:id,name,email,phone',
                'restaurant:id,name',
                'kitchenStation:id,name,code',
                'kitchenAcceptedBy:id,name',
                'kitchenPreparingBy:id,name',
                'kitchenReadyBy:id,name',
                'orderItems.orderItem:id,name',
                'kitchenTickets',
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
     * Called when waiter/POS activates an order for the kitchen board.
     * Module 7 will broadcast from afterKitchenTransition.
     */
    public function notifyNewKitchenOrder(Order $order): void
    {
        try {
            $order = $order->loadMissing(['diningTable', 'waiter', 'user', 'restaurant']);
            $this->afterKitchenTransition($order, 'created', (int) $order->status);
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
        }
    }

    /**
     * @throws Exception
     */
    public function accept(Order $order, ?string $updatedAt = null): Order
    {
        return $this->transition($order, OrderStatus::ACCEPT, 'accept', $updatedAt, function (Order $order) {
            $this->assertMutable($order);

            if (!in_array((int) $order->status, [OrderStatus::PENDING, OrderStatus::ACCEPT], true)) {
                throw new Exception(trans('all.message.kitchen_invalid_transition'), 422);
            }

            if (
                (int) $order->status === OrderStatus::ACCEPT
                && $order->kitchen_accepted_by
                && (int) $order->kitchen_accepted_by !== (int) Auth::id()
            ) {
                throw new Exception(trans('all.message.kitchen_order_locked_by_other'), 422);
            }

            $order->status              = OrderStatus::ACCEPT;
            $order->active              = Ask::YES;
            $order->kitchen_accepted_by = Auth::id();
            $order->kitchen_accepted_at = now();
            $order->save();

            $this->bumpItemKitchenStatus($order, KitchenItemStatus::PENDING);
        });
    }

    /**
     * @throws Exception
     */
    public function preparing(Order $order, ?string $updatedAt = null): Order
    {
        return $this->transition($order, OrderStatus::PREPARING, 'preparing', $updatedAt, function (Order $order) {
            $this->assertMutable($order);

            if (!in_array((int) $order->status, [OrderStatus::ACCEPT, OrderStatus::PREPARING], true)) {
                throw new Exception(trans('all.message.kitchen_invalid_transition'), 422);
            }

            $this->assertAcceptedLock($order);

            $order->status               = OrderStatus::PREPARING;
            $order->kitchen_preparing_by = Auth::id();
            if (!$order->kitchen_accepted_by) {
                $order->kitchen_accepted_by = Auth::id();
                $order->kitchen_accepted_at = now();
            }
            $order->save();

            $this->bumpItemKitchenStatus($order, KitchenItemStatus::PREPARING);
        });
    }

    /**
     * @throws Exception
     */
    public function ready(Order $order, ?string $updatedAt = null): Order
    {
        return $this->transition($order, OrderStatus::PREPARED, 'ready', $updatedAt, function (Order $order) {
            $this->assertMutable($order);

            if (!in_array((int) $order->status, [OrderStatus::PREPARING, OrderStatus::PREPARED], true)) {
                throw new Exception(trans('all.message.kitchen_invalid_transition'), 422);
            }

            $this->assertAcceptedLock($order);

            $elapsed = $order->kitchen_accepted_at
                ? (int) $order->kitchen_accepted_at->diffInSeconds(now())
                : null;

            $order->status           = OrderStatus::PREPARED;
            $order->kitchen_ready_by = Auth::id();
            $order->save();

            $this->bumpItemKitchenStatus($order, KitchenItemStatus::READY);

            return ['elapsed_seconds' => $elapsed];
        });
    }

    /**
     * @throws Exception
     */
    public function reject(Order $order, ?string $reason = null, ?string $updatedAt = null): Order
    {
        return $this->transition($order, OrderStatus::REJECTED, 'reject', $updatedAt, function (Order $order) use ($reason) {
            $this->assertMutable($order);

            if (!in_array((int) $order->status, [
                OrderStatus::PENDING,
                OrderStatus::ACCEPT,
                OrderStatus::PREPARING,
            ], true)) {
                throw new Exception(trans('all.message.kitchen_invalid_transition'), 422);
            }

            $order->status = OrderStatus::REJECTED;
            $order->reason = $reason ?: $order->reason;
            $order->save();

            return ['reason' => $reason];
        });
    }

    /**
     * @throws Exception
     */
    public function cancel(Order $order, ?string $reason = null, ?string $updatedAt = null): Order
    {
        return $this->transition($order, OrderStatus::CANCELED, 'cancel', $updatedAt, function (Order $order) use ($reason) {
            $this->assertMutable($order);

            if (!in_array((int) $order->status, [
                OrderStatus::PENDING,
                OrderStatus::ACCEPT,
                OrderStatus::PREPARING,
                OrderStatus::PREPARED,
            ], true)) {
                throw new Exception(trans('all.message.kitchen_invalid_transition'), 422);
            }

            $order->status = OrderStatus::CANCELED;
            $order->reason = $reason ?: $order->reason;
            $order->save();

            return ['reason' => $reason];
        });
    }

    /**
     * @throws Exception
     */
    public function updatePriority(Order $order, int $priority): Order
    {
        try {
            $this->assertKitchenOrder($order);
            $this->assertMutable($order);

            $allowed = [
                KitchenPriority::NORMAL,
                KitchenPriority::HIGH,
                KitchenPriority::URGENT,
                KitchenPriority::VIP,
            ];
            if (!in_array($priority, $allowed, true)) {
                throw new Exception(trans('all.message.kitchen_invalid_priority'), 422);
            }

            $order->kitchen_priority = $priority;
            $order->save();

            $fresh = $this->show($order->fresh());
            $this->afterKitchenTransition($fresh, 'priority', (int) $order->status);

            return $fresh;
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception(
                $exception->getCode() === 422 ? $exception->getMessage() : QueryExceptionLibrary::message($exception),
                422
            );
        }
    }

    /**
     * Build / refresh printable KOT payload (browser print; driver-agnostic).
     *
     * @throws Exception
     */
    public function printData(Order $order): array
    {
        try {
            $order = $this->show($order);

            $ticket = KitchenTicket::query()
                ->where('order_id', $order->id)
                ->latest('id')
                ->first();

            $isReprint = (bool) $ticket;
            $payload   = $this->buildTicketPayload($order, $isReprint);

            if (!$ticket) {
                $ticket = KitchenTicket::create([
                    'restaurant_id' => $order->restaurant_id,
                    'order_id'      => $order->id,
                    'ticket_no'     => 'KOT-' . $order->order_serial_no,
                    'print_count'   => 1,
                    'printed_at'    => now(),
                    'printed_by'    => Auth::id(),
                    'payload'       => $payload,
                ]);
            } else {
                $ticket->update([
                    'print_count' => $ticket->print_count + 1,
                    'printed_at'  => now(),
                    'printed_by'  => Auth::id(),
                    'payload'     => $payload,
                ]);
                $ticket->refresh();
            }

            $this->logStatus($order, (int) $order->status, (int) $order->status, $isReprint ? 'reprint' : 'print', [
                'ticket_id'   => $ticket->id,
                'print_count' => $ticket->print_count,
            ]);

            $this->afterKitchenTransition($order, $isReprint ? 'reprint' : 'print', (int) $order->status);

            return [
                'ticket'  => $ticket,
                'payload' => $payload,
            ];
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception(
                $exception->getCode() === 422 ? $exception->getMessage() : QueryExceptionLibrary::message($exception),
                422
            );
        }
    }

    protected function kitchenBaseQuery()
    {
        return Order::query()
            ->where('active', Ask::YES)
            ->whereIn('order_type', self::ELIGIBLE_TYPES);
    }

    /**
     * @throws Exception
     */
    protected function assertKitchenOrder(Order $order): void
    {
        if ((int) $order->active !== Ask::YES) {
            throw new Exception(trans('all.message.kitchen_order_not_active'), 422);
        }

        if (!in_array((int) $order->order_type, self::ELIGIBLE_TYPES, true)) {
            throw new Exception(trans('all.message.kitchen_order_not_eligible'), 422);
        }

        $scoped = (int) $this->restaurant();
        if ($scoped > 0 && (int) $order->restaurant_id !== $scoped) {
            throw new Exception(trans('all.message.permission_denied'), 422);
        }
    }

    /**
     * @throws Exception
     */
    protected function assertAcceptedLock(Order $order): void
    {
        if (
            $order->kitchen_accepted_by
            && (int) $order->kitchen_accepted_by !== (int) Auth::id()
        ) {
            // Allow other chefs to continue after accept (shared station), but surface lock info.
            // Strict single-chef lock only blocks steal on accept; prepare/ready allowed by any chef with permission.
            return;
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

        if ($current && abs($incoming - $current) > 1) {
            throw new Exception(trans('all.message.kitchen_order_stale'), 422);
        }
    }

    /**
     * @throws Exception
     */
    protected function transition(Order $order, int $toStatus, string $action, ?string $updatedAt, callable $mutator): Order
    {
        $fromStatus = null;
        try {
            DB::transaction(function () use ($order, $toStatus, $action, $updatedAt, $mutator, &$fromStatus) {
                $locked = Order::query()->whereKey($order->id)->lockForUpdate()->first();
                $this->assertKitchenOrder($locked);
                $this->assertOptimisticLock($locked, $updatedAt);

                $from = (int) $locked->status;
                $fromStatus = $from;
                $meta = $mutator($locked);
                if (!is_array($meta)) {
                    $meta = [];
                }

                $this->logStatus($locked->fresh(), $from, $toStatus, $action, $meta);
                $this->order = $locked->fresh();
            });

            $shown = $this->show($this->order);
            $this->afterKitchenTransition($shown, $action, $fromStatus);

            return $shown;
        } catch (Exception $exception) {
            DB::rollBack();
            Log::info($exception->getMessage());
            throw new Exception(
                $exception->getCode() === 422 ? $exception->getMessage() : QueryExceptionLibrary::message($exception),
                422
            );
        }
    }

    protected object $order;

    /**
     * Terminal kitchen states cannot be mutated.
     *
     * @throws Exception
     */
    protected function assertMutable(Order $order): void
    {
        if (in_array((int) $order->status, [
            OrderStatus::DELIVERED,
            OrderStatus::CANCELED,
            OrderStatus::REJECTED,
            OrderStatus::RETURNED,
        ], true)) {
            throw new Exception(trans('all.message.kitchen_order_locked_terminal'), 422);
        }
    }

    /**
     * Module 7 seam: realtime broadcast / notifications hook.
     * Intentionally empty in Module 6 — keep all mutations here so Module 7
     * can broadcast without changing controllers or Vue action flow.
     */
    protected function afterKitchenTransition(Order $order, string $action, ?int $previousStatus = null): void
    {
        try {
            app(\App\Services\RealtimePublisher::class)->kitchenOrder($order, $action, $previousStatus, [
                'status' => (int) $order->status,
            ]);
        } catch (\Throwable $e) {
            Log::info('afterKitchenTransition: ' . $e->getMessage());
        }
    }

    protected function bumpItemKitchenStatus(Order $order, int $kitchenStatus): void
    {
        OrderItem::query()
            ->where('order_id', $order->id)
            ->update(['kitchen_status' => $kitchenStatus]);
    }

    protected function logStatus(Order $order, int $from, int $to, string $action, array $meta = []): void
    {
        KitchenStatusLog::create([
            'restaurant_id' => $order->restaurant_id,
            'order_id'      => $order->id,
            'from_status'   => $from,
            'to_status'     => $to,
            'user_id'       => Auth::id(),
            'action'        => $action,
            'meta'          => $meta,
        ]);
    }

    protected function buildTicketPayload(Order $order, bool $reprint = false): array
    {
        $items = $order->orderItems->map(function ($item) {
            $variations = json_decode($item->item_variations, true);
            $extras     = json_decode($item->item_extras, true);

            return [
                'name'             => $item->orderItem?->name,
                'quantity'         => $item->quantity,
                'instruction'      => $item->instruction,
                'item_variations'  => $variations,
                'item_extras'      => $extras,
                'variation_lines'  => $this->variationLines(is_array($variations) ? $variations : null),
                'extra_lines'      => $this->extraLines(is_array($extras) ? $extras : null),
                'kitchen_status'   => $item->kitchen_status,
            ];
        })->values()->all();

        return [
            'copy'              => 'KITCHEN',
            'restaurant'        => $order->restaurant?->name,
            'order_serial_no'   => $order->order_serial_no,
            'order_type'        => $order->order_type,
            'table'             => $order->diningTable ? [
                'number' => $order->diningTable->table_number,
                'name'   => $order->diningTable->name,
                'zone'   => $order->diningTable->zone,
            ] : null,
            'waiter'            => $order->waiter?->name,
            'customer'          => $order->user?->name,
            'order_note'        => $order->order_note,
            'order_datetime'    => AppLibrary::datetime($order->order_datetime),
            'preparation_time'  => $order->preparation_time,
            'status'            => $order->status,
            'status_name'       => trans('order_status.' . $order->status),
            'priority'          => $order->kitchen_priority,
            'priority_label'    => KitchenPriority::LABELS[$order->kitchen_priority] ?? 'normal',
            'station'           => $order->kitchenStation?->name,
            'items'             => $items,
            'printed_at'        => AppLibrary::datetime(now()),
            'reprint'           => $reprint,
        ];
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
