<?php

namespace App\Services;

use App\Enums\AppNotificationType;
use App\Enums\OrderStatus;
use App\Enums\Role as EnumRole;
use App\Enums\TableStatus;
use App\Events\AppNotificationCreated;
use App\Events\CustomerOrderStatusUpdated;
use App\Events\EmployeeUpdated;
use App\Events\KitchenOrderUpdated;
use App\Events\TableStatusUpdated;
use App\Models\Order;
use App\Models\RestaurantTable;
use App\Models\User;
use App\Notifications\RestaurantAlertNotification;
use App\Support\PusherAvailability;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Spatie\Permission\Models\Role;

class RealtimePublisher
{
    public function __construct(protected AppNotificationService $appNotificationService)
    {
    }

    public function kitchenOrder(Order $order, string $action, ?int $previousStatus = null, array $meta = []): void
    {
        try {
            $order->loadMissing(['diningTable', 'waiter', 'user', 'restaurant']);
        } catch (\Throwable $e) {
            Log::info('Realtime kitchen load failed: ' . $e->getMessage());
        }

        try {
            event(new KitchenOrderUpdated($order, $action, $previousStatus, $meta));
        } catch (\Throwable $e) {
            Log::info('Realtime kitchen broadcast failed: ' . $e->getMessage());
        }

        try {
            $this->customerOrderStatus($order, $action, $previousStatus, $meta);
        } catch (\Throwable $e) {
            Log::info('Realtime customer order broadcast failed: ' . $e->getMessage());
        }

        try {
            $this->notifyKitchenStakeholders($order, $action);
        } catch (\Throwable $e) {
            Log::info('Realtime kitchen notify failed: ' . $e->getMessage());
        }
    }

    /**
     * Notify the ordering customer of status changes (QR + marketplace).
     */
    public function customerOrderStatus(Order $order, string $action, ?int $previousStatus = null, array $meta = []): void
    {
        if ((int) $order->user_id <= 0) {
            return;
        }

        $customerActions = [
            'created',
            'accept',
            'preparing',
            'ready',
            'complete',
            'reject',
            'cancel',
            'delivered',
            'completed',
            'out_for_delivery',
            'status',
        ];

        if (!in_array($action, $customerActions, true)) {
            return;
        }

        if ($previousStatus !== null && (int) $previousStatus === (int) $order->status) {
            return;
        }

        try {
            $order->loadMissing(['diningTable', 'restaurant', 'user']);
        } catch (\Throwable $e) {
            Log::info('Realtime customer order load failed: ' . $e->getMessage());
        }

        try {
            event(new CustomerOrderStatusUpdated($order, $action, $previousStatus, $meta));
        } catch (\Throwable $e) {
            Log::info('Realtime customer order event failed: ' . $e->getMessage());
        }
    }

    public function table(RestaurantTable $table, string $action = 'updated', ?int $previousStatus = null): void
    {
        try {
            event(new TableStatusUpdated($table, $action, $previousStatus));
        } catch (\Throwable $e) {
            Log::info('Realtime table broadcast failed: ' . $e->getMessage());
        }

        try {
            $type = match ((int) $table->status) {
                TableStatus::OCCUPIED => AppNotificationType::TABLE_OCCUPIED,
                TableStatus::AVAILABLE => AppNotificationType::TABLE_AVAILABLE,
                default => AppNotificationType::TABLE_UPDATED,
            };

            $this->appNotificationService->notifyUsers(
                $this->restaurantStaff($table->restaurant_id, [
                    EnumRole::RESTAURANT_OWNER,
                    EnumRole::MANAGER,
                    EnumRole::WAITER,
                ]),
                $type,
                'Table ' . $table->table_number,
                sprintf('%s is now %s', $table->name ?: $table->table_number, $this->tableStatusLabel((int) $table->status)),
                [
                    'restaurant_id' => (int) $table->restaurant_id,
                    'table_id'      => (int) $table->id,
                    'table_number'  => $table->table_number,
                    'status'        => (int) $table->status,
                    'icon'          => 'lab-line-table',
                    'color'         => (int) $table->status === TableStatus::OCCUPIED ? 'amber' : 'emerald',
                    'url'           => '/admin/waiter/tables',
                    'play_sound'    => true,
                ]
            );
        } catch (\Throwable $e) {
            Log::info('Realtime table notify failed: ' . $e->getMessage());
        }
    }

    public function employee(User $employee, string $action = 'updated'): void
    {
        try {
            event(new EmployeeUpdated($employee, $action));
        } catch (\Throwable $e) {
            Log::info('Realtime employee broadcast failed: ' . $e->getMessage());
        }

        try {
            $type = $action === 'created'
                ? AppNotificationType::EMPLOYEE_CREATED
                : AppNotificationType::EMPLOYEE_UPDATED;

            $this->appNotificationService->notifyUsers(
                $this->restaurantStaff($employee->restaurant_id, [
                    EnumRole::RESTAURANT_OWNER,
                    EnumRole::MANAGER,
                    EnumRole::ADMIN,
                ]),
                $type,
                $action === 'created' ? 'Employee created' : 'Employee updated',
                $employee->name . ' (' . ($employee->email ?? '') . ')',
                [
                    'restaurant_id' => (int) $employee->restaurant_id,
                    'employee_id'   => (int) $employee->id,
                    'icon'          => 'lab-line-profile',
                    'color'         => 'sky',
                    'url'           => '/admin/employees',
                ]
            );
        } catch (\Throwable $e) {
            Log::info('Realtime employee notify failed: ' . $e->getMessage());
        }
    }

    protected function notifyKitchenStakeholders(Order $order, string $action): void
    {
        [$type, $title, $message, $color, $url] = $this->kitchenNotificationCopy($order, $action);

        $recipients = collect();

        // Kitchen roles for every ticket lifecycle update
        if (in_array($action, ['created', 'accept', 'preparing', 'ready', 'reject', 'cancel', 'priority', 'print', 'reprint'], true)) {
            $recipients = $recipients->merge($this->restaurantStaff($order->restaurant_id, [
                EnumRole::CHEF,
                EnumRole::MANAGER,
                EnumRole::RESTAURANT_OWNER,
            ]));
        }

        // Assigned waiter gets every status change for their order
        if ($order->waiter_id && in_array($action, [
            'created', 'accept', 'preparing', 'ready', 'reject', 'cancel', 'priority',
        ], true)) {
            $waiter = User::query()->find($order->waiter_id);
            if ($waiter) {
                $recipients->push($waiter);
            }
        }

        // All restaurant waiters also see ready / reject / cancel (floor awareness)
        if (in_array($action, ['ready', 'reject', 'cancel', 'created'], true)) {
            $recipients = $recipients->merge($this->restaurantStaff($order->restaurant_id, [
                EnumRole::WAITER,
            ]));
        }

        // Always notify owners/managers for lifecycle
        $recipients = $recipients->merge($this->restaurantStaff($order->restaurant_id, [
            EnumRole::RESTAURANT_OWNER,
            EnumRole::MANAGER,
        ]));

        // Platform admins watch new orders and failures, not every kitchen step
        if (in_array($action, ['created', 'reject', 'cancel'], true)) {
            $recipients = $recipients->merge($this->platformAdmins());
        }

        $recipients = $recipients->unique('id')->values();

        $tableId = $order->table_id ? (int) $order->table_id : null;

        foreach ($recipients as $user) {
            $userUrl = $this->urlForRecipient($user, $order, $action, $url);
            $this->appNotificationService->notifyUsers(collect([$user]), $type, $title, $message, [
                'restaurant_id'   => (int) $order->restaurant_id,
                'order_id'        => (int) $order->id,
                'table_id'        => $tableId,
                'order_serial_no' => $order->order_serial_no,
                'table_number'    => $order->diningTable?->table_number,
                'action'          => $action,
                'status'          => (int) $order->status,
                'icon'            => 'lab-line-flame',
                'color'           => $color,
                'url'             => $userUrl,
                'play_sound'      => in_array($action, ['created', 'ready', 'accept', 'preparing', 'reject', 'cancel'], true),
            ]);
        }
    }

    protected function urlForRecipient(User $user, Order $order, string $action, string $fallback): string
    {
        $user->loadMissing('roles');
        $roleIds = $user->roles->pluck('id')->map(fn ($id) => (int) $id)->all();

        if (in_array(EnumRole::WAITER, $roleIds, true) && !in_array(EnumRole::CHEF, $roleIds, true)) {
            return $order->id
                ? '/admin/waiter/orders/' . $order->id
                : '/admin/waiter/tables';
        }

        if (in_array(EnumRole::CHEF, $roleIds, true)) {
            return $order->id
                ? '/admin/kitchen/orders/' . $order->id
                : '/admin/kitchen/queue';
        }

        return $fallback;
    }

    protected function kitchenNotificationCopy(Order $order, string $action): array
    {
        $serial = '#' . $order->order_serial_no;
        $table  = $order->diningTable?->table_number
            ? ' · Table ' . $order->diningTable->table_number
            : '';

        return match ($action) {
            'created' => [
                AppNotificationType::ORDER_CREATED,
                'New kitchen order',
                "Order {$serial}{$table} sent to kitchen",
                'sky',
                '/admin/kitchen/queue',
            ],
            'accept' => [
                AppNotificationType::KITCHEN_ACCEPTED,
                'Order accepted',
                "Kitchen accepted {$serial}{$table}",
                'sky',
                '/admin/kitchen/queue',
            ],
            'preparing' => [
                AppNotificationType::KITCHEN_PREPARING,
                'Preparing',
                "Kitchen is preparing {$serial}{$table}",
                'amber',
                '/admin/kitchen/queue',
            ],
            'ready' => [
                AppNotificationType::KITCHEN_READY,
                'Order ready',
                "{$serial}{$table} is ready for service",
                'emerald',
                '/admin/waiter',
            ],
            'reject' => [
                AppNotificationType::ORDER_REJECTED,
                'Order rejected',
                "Kitchen rejected {$serial}{$table}",
                'rose',
                '/admin/waiter',
            ],
            'cancel' => [
                AppNotificationType::ORDER_CANCELLED,
                'Order cancelled',
                "{$serial}{$table} was cancelled",
                'rose',
                '/admin/kitchen/queue',
            ],
            default => [
                AppNotificationType::ORDER_UPDATED,
                'Order updated',
                "Order {$serial}{$table} · {$action}",
                'primary',
                '/admin/kitchen/queue',
            ],
        };
    }

    protected function platformAdmins(): Collection
    {
        return User::query()
            ->whereHas('roles', fn ($q) => $q->where('id', EnumRole::ADMIN))
            ->get();
    }

    protected function restaurantStaff(int $restaurantId, array $roleIds): Collection
    {
        if ($restaurantId <= 0) {
            return collect();
        }

        $restaurantRoleIds = array_filter($roleIds, fn ($id) => (int) $id !== EnumRole::ADMIN);
        $query = User::query()->where('restaurant_id', $restaurantId);

        $roleNames = Role::query()->whereIn('id', $restaurantRoleIds)->pluck('name');
        if ($roleNames->isNotEmpty()) {
            $query->whereHas('roles', function ($q) use ($roleNames) {
                $q->whereIn('name', $roleNames);
            });
        }

        // Asking only for admins must not sweep in every user of the restaurant.
        $users = empty($restaurantRoleIds) ? collect() : $query->get();

        if (in_array(EnumRole::ADMIN, $roleIds, true)) {
            $adminRole = Role::query()->find(EnumRole::ADMIN);
            if ($adminRole) {
                $admins = User::query()->whereHas('roles', fn ($q) => $q->where('name', $adminRole->name))->get();
                $users  = $users->merge($admins);
            }
        }

        return $users->unique('id')->values();
    }

    protected function tableStatusLabel(int $status): string
    {
        return match ($status) {
            TableStatus::AVAILABLE => 'Available',
            TableStatus::OCCUPIED => 'Occupied',
            TableStatus::RESERVED => 'Reserved',
            TableStatus::CLEANING => 'Cleaning',
            TableStatus::OUT_OF_SERVICE => 'Out of service',
            default => 'Updated',
        };
    }
}
