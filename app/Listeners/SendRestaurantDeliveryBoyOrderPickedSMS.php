<?php

namespace App\Listeners;



use App\Events\RestaurantDeliveryBoyOrderPickedSMS;
use App\Services\RestaurantDeliveryBoyOrderPickedSMSBuilder;
use Exception;
use Illuminate\Support\Facades\Log;


class SendRestaurantDeliveryBoyOrderPickedSMS
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
    }


    public function handle(RestaurantDeliveryBoyOrderPickedSMS $event): void
    {
        try {
            $restaurantDeliveryBoyOrderPickedSMSBuilder = new RestaurantDeliveryBoyOrderPickedSMSBuilder($event->info['order_id']);
            $restaurantDeliveryBoyOrderPickedSMSBuilder->send();
        } catch (Exception $e) {
            Log::info($e->getMessage());
        }
    }
}
