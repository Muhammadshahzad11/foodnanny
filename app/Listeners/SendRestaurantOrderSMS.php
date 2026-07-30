<?php

namespace App\Listeners;




use App\Events\RestaurantOrderReceivedSMS;
use App\Services\RestaurantOrderSMSBuilder;
use Exception;
use Illuminate\Support\Facades\Log;


class SendRestaurantOrderSMS
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
    }


    public function handle(RestaurantOrderReceivedSMS $event): void
    {
        try {
            $restaurantOrderSMSBuilder = new RestaurantOrderSMSBuilder($event->info['order_id']);
            $restaurantOrderSMSBuilder->send();
        } catch (Exception $e) {
            Log::info($e->getMessage());
        }
    }
}
