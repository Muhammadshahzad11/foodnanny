<?php

namespace App\Listeners;



use App\Events\RestaurantOrderReceivedEmail;
use App\Services\RestaurantOrderEmailBuilder;
use Exception;
use Illuminate\Support\Facades\Log;


class SendRestaurantOrderEmail
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
    }


    public function handle(RestaurantOrderReceivedEmail $event): void
    {
        try {
            $restaurantOrderEmailBuilder = new RestaurantOrderEmailBuilder($event->info['order_id']);
            $restaurantOrderEmailBuilder->send();
        } catch (Exception $e) {
            Log::info($e->getMessage());
        }
    }
}
