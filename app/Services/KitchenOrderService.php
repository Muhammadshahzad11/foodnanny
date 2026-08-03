<?php

namespace App\Services;

use App\Enums\Ask;
use App\Enums\KitchenItemStatus;
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

            if ($period === 'today' || empty($requests['from_date'])) {
                $query->whereDate('order_datetime', Carbon::today()->toDateString());
            }

            if (!empty($requests['status'])) {
                $query->where('status', (int) $requests['status']);
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
                'priority' => $query->orderByDesc('kitchen_priority')->orderBy('order_datetime'),
                'table' => $query->orderBy('table_id')->orderBy('order_datetime'),
                'waiter' => $query->orderBy('waiter_id')->orderBy('order_datetime'),
                'order_number' => $query->orderBy('order_serial_no'),
                default => $query->orderBy('order_datetime')->orderBy('id'),
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
     * @throws Exception
     */
    public function accept(Order $order, ?string $updatedAt = null): Order
    {
        return $this->transition($order, OrderStatus::ACCEPT, 'accept', $updatedAt, function (Order $order) {
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
    public function updatePriority(Order $order, int $priority): Order
    {
        try {
            $this->assertKitchenOrder($order);
            $order->kitchen_priority = max(0, min(100, $priority));
            $order->save();

            return $this->show($order->fresh());
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

            $payload = $this->buildTicketPayload($order);

            $ticket = KitchenTicket::query()
                ->where('order_id', $order->id)
                ->latest('id')
                ->first();

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

            $this->logStatus($order, (int) $order->status, (int) $order->status, 'print', [
                'ticket_id'   => $ticket->id,
                'print_count' => $ticket->print_count,
            ]);

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
        try {
            DB::transaction(function () use ($order, $toStatus, $action, $updatedAt, $mutator) {
                $locked = Order::query()->whereKey($order->id)->lockForUpdate()->first();
                $this->assertKitchenOrder($locked);
                $this->assertOptimisticLock($locked, $updatedAt);

                $from = (int) $locked->status;
                $meta = $mutator($locked);
                if (!is_array($meta)) {
                    $meta = [];
                }

                $this->logStatus($locked->fresh(), $from, $toStatus, $action, $meta);
                $this->order = $locked->fresh();
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

    protected object $order;

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

    protected function buildTicketPayload(Order $order): array
    {
        $items = $order->orderItems->map(function ($item) {
            return [
                'name'            => $item->orderItem?->name,
                'quantity'        => $item->quantity,
                'instruction'     => $item->instruction,
                'item_variations' => json_decode($item->item_variations, true),
                'item_extras'     => json_decode($item->item_extras, true),
                'kitchen_status'  => $item->kitchen_status,
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
            'station'           => $order->kitchenStation?->name,
            'items'             => $items,
            'printed_at'        => AppLibrary::datetime(now()),
        ];
    }
}
