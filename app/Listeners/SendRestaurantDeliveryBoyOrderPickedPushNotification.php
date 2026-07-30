<?php

namespace App\Listeners;



use App\Events\RestaurantDeliveryBoyOrderPickedPushNotification;
use App\Services\RestaurantDeliveryBoyOrderPickedPushNotificationBuilder;
use Exception;
use Illuminate\Support\Facades\Log;


class SendRestaurantDeliveryBoyOrderPickedPushNotification
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
    }


    public function handle(RestaurantDeliveryBoyOrderPickedPushNotification $event): void
    {
        try {
            $restaurantDeliveryBoyOrderPickedPushNotificationBuilder = new RestaurantDeliveryBoyOrderPickedPushNotificationBuilder($event->info['order_id']);
            $restaurantDeliveryBoyOrderPickedPushNotificationBuilder->send();
        } catch (Exception $e) {
            Log::info($e->getMessage());
        }
    }
}
