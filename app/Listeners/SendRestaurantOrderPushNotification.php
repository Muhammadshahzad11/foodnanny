<?php

namespace App\Listeners;




use App\Events\RestaurantOrderReceivedPushNotification;
use App\Events\RestaurantOrderReceivedSMS;
use App\Services\RestaurantOrderPushNotificationBuilder;
use App\Services\RestaurantOrderSMSBuilder;
use Exception;
use Illuminate\Support\Facades\Log;


class SendRestaurantOrderPushNotification
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
    }


    public function handle(RestaurantOrderReceivedPushNotification $event): void
    {
        try {
            $restaurantOrderPushNotificationBuilder = new RestaurantOrderPushNotificationBuilder($event->info['order_id']);
            $restaurantOrderPushNotificationBuilder->send();
        } catch (Exception $e) {
            Log::info($e->getMessage());
        }
    }
}
