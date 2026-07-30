<?php

namespace App\Listeners;




use App\Events\RestaurantDeliveryBoyOrderAcceptPushNotification;
use App\Events\RestaurantOrderReceivedPushNotification;
use App\Services\RestaurantDeliveryBoyOrderAcceptPushNotificationBuilder;
use Exception;
use Illuminate\Support\Facades\Log;


class SendRestaurantDeliveryBoyOrderAcceptPushNotification
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
    }


    public function handle(RestaurantDeliveryBoyOrderAcceptPushNotification $event): void
    {
        try {
            $restaurantDeliveryBoyOrderAcceptPushNotificationBuilder = new RestaurantDeliveryBoyOrderAcceptPushNotificationBuilder($event->info['order_id']);
            $restaurantDeliveryBoyOrderAcceptPushNotificationBuilder->send();
        } catch (Exception $e) {
            Log::info($e->getMessage());
        }
    }
}
