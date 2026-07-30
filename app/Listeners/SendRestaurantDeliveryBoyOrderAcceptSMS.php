<?php

namespace App\Listeners;


use App\Events\RestaurantDeliveryBoyOrderAcceptSMS;
use App\Services\RestaurantDeliveryBoyOrderAcceptSMSBuilder;
use Exception;
use Illuminate\Support\Facades\Log;


class SendRestaurantDeliveryBoyOrderAcceptSMS
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
    }


    public function handle(RestaurantDeliveryBoyOrderAcceptSMS $event): void
    {
        try {
            $restaurantDeliveryBoyOrderAcceptSMSBuilder = new RestaurantDeliveryBoyOrderAcceptSMSBuilder($event->info['order_id']);
            $restaurantDeliveryBoyOrderAcceptSMSBuilder->send();
        } catch (Exception $e) {
            Log::info($e->getMessage());
        }
    }
}
