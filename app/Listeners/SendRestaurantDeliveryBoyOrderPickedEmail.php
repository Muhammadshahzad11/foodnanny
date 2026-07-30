<?php

namespace App\Listeners;



use App\Events\RestaurantDeliveryBoyOrderPickedEmail;
use App\Services\RestaurantDeliveryBoyOrderPickedEmailBuilder;
use Exception;
use Illuminate\Support\Facades\Log;


class SendRestaurantDeliveryBoyOrderPickedEmail
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
    }


    public function handle(RestaurantDeliveryBoyOrderPickedEmail $event): void
    {
        try {
            $restaurantDeliveryBoyOrderPickedEmailBuilder = new RestaurantDeliveryBoyOrderPickedEmailBuilder($event->info['order_id']);
            $restaurantDeliveryBoyOrderPickedEmailBuilder->send();
        } catch (Exception $e) {
            Log::info($e->getMessage());
        }
    }
}
