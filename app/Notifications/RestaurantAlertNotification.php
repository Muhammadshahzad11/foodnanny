<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class RestaurantAlertNotification extends Notification
{
    use Queueable;

    public function __construct(
        public string $type,
        public string $title,
        public string $message,
        public array $payload = []
    ) {
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return array_merge([
            'type'    => $this->type,
            'title'   => $this->title,
            'message' => $this->message,
            'icon'    => $this->payload['icon'] ?? 'lab-line-notification',
            'color'   => $this->payload['color'] ?? 'primary',
            'url'     => $this->payload['url'] ?? null,
        ], $this->payload);
    }

    public function toDatabase(object $notifiable): array
    {
        return $this->toArray($notifiable);
    }
}
