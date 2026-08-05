<?php

namespace App\Events;

use App\Support\PusherAvailability;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Queue\SerializesModels;

class AppNotificationCreated implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public int $userId,
        public DatabaseNotification $notification
    ) {
    }

    public function broadcastWhen(): bool
    {
        return PusherAvailability::ready();
    }

    public function broadcastAs(): string
    {
        return 'notification.created';
    }

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel("user.{$this->userId}"),
        ];
    }

    public function broadcastWith(): array
    {
        $data = $this->notification->data;

        return [
            'id'         => $this->notification->id,
            'type'       => $data['type'] ?? 'system',
            'title'      => $data['title'] ?? '',
            'message'    => $data['message'] ?? '',
            'icon'       => $data['icon'] ?? 'lab-line-notification',
            'color'      => $data['color'] ?? 'primary',
            'url'        => $data['url'] ?? null,
            'data'       => $data,
            'read_at'    => null,
            'created_at' => optional($this->notification->created_at)?->toIso8601String(),
        ];
    }
}
