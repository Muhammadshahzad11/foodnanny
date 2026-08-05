<?php

namespace App\Events;

use App\Models\Order;
use App\Support\PusherAvailability;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class KitchenOrderUpdated implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public Order $order,
        public string $action,
        public ?int $previousStatus = null,
        public array $meta = []
    ) {
        $this->order->loadMissing(['diningTable:id,table_number,name', 'waiter:id,name', 'user:id,name', 'restaurant:id,name']);
    }

    public function broadcastWhen(): bool
    {
        return PusherAvailability::ready();
    }

    public function broadcastAs(): string
    {
        return 'kitchen.order.updated';
    }

    public function broadcastOn(): array
    {
        $restaurantId = (int) $this->order->restaurant_id;

        return [
            new PrivateChannel("restaurant.{$restaurantId}.kitchen"),
            new PrivateChannel("restaurant.{$restaurantId}.waiter"),
            new PrivateChannel("restaurant.{$restaurantId}.owner"),
            new PrivateChannel('admin.ops'),
        ];
    }

    public function broadcastWith(): array
    {
        return [
            'restaurant_id'    => (int) $this->order->restaurant_id,
            'order_id'         => (int) $this->order->id,
            'order_serial_no'  => $this->order->order_serial_no,
            'status'           => (int) $this->order->status,
            'previous_status'  => $this->previousStatus,
            'kitchen_priority' => (int) $this->order->kitchen_priority,
            'table_id'         => $this->order->table_id ? (int) $this->order->table_id : null,
            'table_number'     => $this->order->diningTable?->table_number,
            'table_name'       => $this->order->diningTable?->name,
            'waiter_id'        => $this->order->waiter_id ? (int) $this->order->waiter_id : null,
            'waiter_name'      => $this->order->waiter?->name,
            'customer_name'    => $this->order->user?->name,
            'action'           => $this->action,
            'updated_at'       => optional($this->order->updated_at)?->toIso8601String(),
            'meta'             => $this->meta,
        ];
    }
}
