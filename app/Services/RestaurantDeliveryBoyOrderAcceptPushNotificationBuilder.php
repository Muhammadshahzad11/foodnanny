<?php

namespace App\Services;


use App\Enums\Role;
use App\Enums\SwitchBox;
use App\Models\FrontendOrder;
use App\Models\NotificationAlert;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Log;


class RestaurantDeliveryBoyOrderAcceptPushNotificationBuilder
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
            $restaurantOwner = User::role(Role::RESTAURANT_OWNER)->where(['restaurant_id' => $this->order->restaurant_id])->first();

            $i           = 0;
            $fcmTokenArrays = [];
            if (!blank($restaurantOwner)) {
                if(!blank($restaurantOwner->web_token)) {
                    $fcmTokenArrays[$i] = $restaurantOwner->web_token;
                    $i++;
                }

                if(!blank($restaurantOwner->device_token)) {
                    $fcmTokenArrays[$i] = $restaurantOwner->device_token;
                    $i++;
                }
            }

            if (count($fcmTokenArrays) > 0) {
                try {
                    $notificationAlert = NotificationAlert::where(['language' => 'delivery_boy_order_accepted_message'])->first();
                    if ($notificationAlert && $notificationAlert->push_notification == SwitchBox::ON) {
                        $pushNotification = (object)[
                            'title'       => 'Delivery boy accept order confirmation',
                            'description' => $notificationAlert->push_notification_message,
                            'order_id'    => $this->orderId
                        ];
                        $firebase         = new FirebaseService();
                        $firebase->sendNotification($pushNotification, $fcmTokenArrays, 'delivery-boy-order', 'accept', "/admin/online-orders/show/{$this->orderId}");
                    }
                } catch (Exception $e) {
                    Log::info($e->getMessage());
                }
            }
        }
    }

}
