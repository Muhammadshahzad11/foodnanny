<?php

namespace App\Events;

use App\Models\Order;
use App\Support\PusherAvailability;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CustomerOrderStatusUpdated implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public Order $order,
        public string $action,
        public ?int $previousStatus = null,
        public array $meta = []
    ) {
        $this->order->loadMissing(['diningTable:id,table_number,name', 'restaurant:id,name', 'user:id,name']);
    }

    public function broadcastWhen(): bool
    {
        return PusherAvailability::ready() && (int) $this->order->user_id > 0;
    }

    public function broadcastAs(): string
    {
        return 'order.status.updated';
    }

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('user.' . (int) $this->order->user_id),
        ];
    }

    public function broadcastWith(): array
    {
        return [
            'order_id'         => (int) $this->order->id,
            'order_serial_no'  => $this->order->order_serial_no,
            'status'           => (int) $this->order->status,
            'previous_status'  => $this->previousStatus,
            'order_type'       => (int) $this->order->order_type,
            'table_id'         => $this->order->table_id ? (int) $this->order->table_id : null,
            'table_number'     => $this->order->diningTable?->table_number,
            'restaurant_id'    => (int) $this->order->restaurant_id,
            'restaurant_name'  => $this->order->restaurant?->name,
            'action'           => $this->action,
            'is_scan_menu'     => $this->order->isScanMenuOrder(),
            'preparation_time' => (int) ($this->order->preparation_time ?? 0),
            'updated_at'       => optional($this->order->updated_at)?->toIso8601String(),
            'meta'             => $this->meta,
        ];
    }
}
