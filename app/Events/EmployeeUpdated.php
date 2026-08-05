<?php

namespace App\Events;

use App\Models\User;
use App\Support\PusherAvailability;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class EmployeeUpdated implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public User $employee,
        public string $action = 'updated'
    ) {
    }

    public function broadcastWhen(): bool
    {
        return PusherAvailability::ready();
    }

    public function broadcastAs(): string
    {
        return 'employee.updated';
    }

    public function broadcastOn(): array
    {
        $restaurantId = (int) $this->employee->restaurant_id;

        return [
            new PrivateChannel("restaurant.{$restaurantId}.owner"),
            new PrivateChannel('admin.ops'),
        ];
    }

    public function broadcastWith(): array
    {
        return [
            'restaurant_id' => (int) $this->employee->restaurant_id,
            'employee_id'   => (int) $this->employee->id,
            'name'          => $this->employee->name,
            'email'         => $this->employee->email,
            'role_id'       => (int) ($this->employee->myrole ?? 0),
            'action'        => $this->action,
            'updated_at'    => optional($this->employee->updated_at)?->toIso8601String(),
        ];
    }
}
