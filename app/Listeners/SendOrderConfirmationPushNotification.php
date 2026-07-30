<?php

namespace App\Listeners;


use App\Events\OrderPlacedPushNotification;
use App\Services\OrderConfirmationPushNotificationBuilder;
use Exception;
use Illuminate\Support\Facades\Log;


class SendOrderConfirmationPushNotification
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
    }


    public function handle(OrderPlacedPushNotification $event): void
    {
        try {
            $orderConfirmationPushNotificationBuilder = new OrderConfirmationPushNotificationBuilder($event->info['order_id'], $event->info['status']);
            $orderConfirmationPushNotificationBuilder->send();
        } catch (Exception $e) {
            Log::info($e->getMessage());
        }
    }
}
