<?php

namespace App\Listeners;



use App\Events\RestaurantDeliveryBoyOrderAcceptEmail;
use App\Services\RestaurantDeliveryBoyOrderAcceptEmailBuilder;
use App\Services\RestaurantOrderEmailBuilder;
use Exception;
use Illuminate\Support\Facades\Log;


class SendRestaurantDeliveryBoyOrderAcceptEmail
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
    }


    public function handle(RestaurantDeliveryBoyOrderAcceptEmail $event): void
    {
        try {
            $restaurantDeliveryBoyOrderAcceptEmailBuilder = new RestaurantDeliveryBoyOrderAcceptEmailBuilder($event->info['order_id']);
            $restaurantDeliveryBoyOrderAcceptEmailBuilder->send();
        } catch (Exception $e) {
            Log::info($e->getMessage());
        }
    }
}
