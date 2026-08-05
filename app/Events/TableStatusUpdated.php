<?php

namespace App\Events;

use App\Models\RestaurantTable;
use App\Support\PusherAvailability;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TableStatusUpdated implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public RestaurantTable $table,
        public string $action = 'updated',
        public ?int $previousStatus = null
    ) {
    }

    public function broadcastWhen(): bool
    {
        return PusherAvailability::ready();
    }

    public function broadcastAs(): string
    {
        return 'table.status.updated';
    }

    public function broadcastOn(): array
    {
        $restaurantId = (int) $this->table->restaurant_id;

        return [
            new PrivateChannel("restaurant.{$restaurantId}.waiter"),
            new PrivateChannel("restaurant.{$restaurantId}.owner"),
            new PrivateChannel("restaurant.{$restaurantId}.kitchen"),
            new PrivateChannel('admin.ops'),
        ];
    }

    public function broadcastWith(): array
    {
        return [
            'restaurant_id' => (int) $this->table->restaurant_id,
            'table_id'      => (int) $this->table->id,
            'table_number'  => $this->table->table_number,
            'name'          => $this->table->name,
            'zone'          => $this->table->zone,
            'status'        => (int) $this->table->status,
            'previous_status' => $this->previousStatus,
            'action'        => $this->action,
            'updated_at'    => optional($this->table->updated_at)?->toIso8601String(),
        ];
    }
}
