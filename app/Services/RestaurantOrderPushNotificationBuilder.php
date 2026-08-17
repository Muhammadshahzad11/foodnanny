<?php

namespace App\Services;


use App\Enums\OrderStatus;
use App\Enums\OrderType;
use App\Enums\Role;
use App\Enums\SwitchBox;
use App\Models\FrontendOrder;
use App\Models\NotificationAlert;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Log;


class RestaurantOrderPushNotificationBuilder
{
    public int $orderId;
    public object $order;

    public function __construct($orderId)
    {
        $this->orderId = $orderId;
        $this->order   = FrontendOrder::find($orderId);
    }

    public function send(): void
    {
        if (!blank($this->order)) {
            $fcmTokenArrays = $this->recipientTokens();

            if (count($fcmTokenArrays) > 0) {
                try {
                    $notificationAlert = NotificationAlert::where(['language' => 'restaurant_owner_new_order_message'])->first();
                    if ($notificationAlert && $notificationAlert->push_notification == SwitchBox::ON) {
                        $pushNotification = (object)[
                            'title'       => 'New order ' . $this->orderReference(),
                            'description' => $notificationAlert->push_notification_message,
                            'order_id'    => $this->orderId
                        ];
                        $firebase         = new FirebaseService();
                        $firebase->sendNotification($pushNotification, $fcmTokenArrays, "new-order", (string) OrderStatus::PENDING, '/admin/online-orders');
                    }
                } catch (Exception $e) {
                    Log::info($e->getMessage());
                }
            }
        }
    }

    protected function orderReference(): string
    {
        $serial = trim((string) ($this->order->order_serial_no ?? ''));

        return $serial !== '' ? '#' . $serial : '';
    }

    /**
     * Everyone who runs the counter needs the alert, not just the first owner:
     * owners and managers of the restaurant plus platform admins.
     *
     * @return string[]
     */
    protected function recipientTokens(): array
    {
        $staff = User::query()
            ->whereHas('roles', fn ($q) => $q->whereIn('id', [Role::RESTAURANT_OWNER, Role::MANAGER]))
            ->where('restaurant_id', $this->order->restaurant_id)
            ->get();

        $admins = User::query()
            ->whereHas('roles', fn ($q) => $q->where('id', Role::ADMIN))
            ->get();

        return $staff->merge($admins)
            ->unique('id')
            ->flatMap(fn (User $user) => [$user->web_token, $user->device_token])
            ->filter(fn ($token) => filled($token))
            ->unique()
            ->values()
            ->all();
    }

}
