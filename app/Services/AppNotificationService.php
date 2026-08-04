<?php

namespace App\Services;

use App\Events\AppNotificationCreated;
use App\Models\User;
use App\Notifications\RestaurantAlertNotification;
use App\Support\PusherAvailability;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AppNotificationService
{
    public function notifyUsers(Collection $users, string $type, string $title, string $message, array $payload = []): void
    {
        foreach ($users as $user) {
            if (!$user instanceof User) {
                continue;
            }

            try {
                $user->notify(new RestaurantAlertNotification($type, $title, $message, $payload));

                $notification = $user->notifications()->latest()->first();
                if ($notification && PusherAvailability::ready()) {
                    try {
                        event(new AppNotificationCreated((int) $user->id, $notification));
                    } catch (\Throwable $broadcastError) {
                        Log::info('Notification broadcast failed: ' . $broadcastError->getMessage());
                    }
                }
            } catch (\Throwable $e) {
                Log::info('Notify user failed: ' . $e->getMessage());
            }
        }
    }

    public function list(User $user, int $perPage = 20): LengthAwarePaginator
    {
        return $user->notifications()->latest()->paginate($perPage);
    }

    public function unreadCount(User $user): int
    {
        return $user->unreadNotifications()->count();
    }

    public function recent(User $user, int $limit = 10): Collection
    {
        return $user->notifications()->latest()->limit($limit)->get();
    }

    public function markRead(User $user, string $notificationId): ?DatabaseNotification
    {
        $notification = $user->notifications()->where('id', $notificationId)->first();
        if ($notification && is_null($notification->read_at)) {
            $notification->markAsRead();
        }

        return $notification?->fresh();
    }

    public function markAllRead(User $user): int
    {
        $count = $user->unreadNotifications()->count();
        $user->unreadNotifications->markAsRead();

        return $count;
    }

    public function destroy(User $user, string $notificationId): bool
    {
        $notification = $user->notifications()->where('id', $notificationId)->first();
        if (!$notification) {
            return false;
        }
        $notification->delete();

        return true;
    }

    public function format(DatabaseNotification $notification): array
    {
        $data = $notification->data;

        return [
            'id'         => $notification->id,
            'type'       => $data['type'] ?? 'system',
            'title'      => $data['title'] ?? '',
            'message'    => $data['message'] ?? '',
            'icon'       => $data['icon'] ?? 'lab-line-notification',
            'color'      => $data['color'] ?? 'primary',
            'url'        => $data['url'] ?? null,
            'data'       => $data,
            'read_at'    => optional($notification->read_at)?->toIso8601String(),
            'created_at' => optional($notification->created_at)?->toIso8601String(),
            'is_unread'  => is_null($notification->read_at),
        ];
    }
}
