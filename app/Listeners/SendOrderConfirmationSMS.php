<?php

namespace App\Listeners;


use App\Events\OrderPlacedSMS;
use App\Services\OrderConfirmationSMSBuilder;
use Exception;
use Illuminate\Support\Facades\Log;


class SendOrderConfirmationSMS
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
    }


    public function handle(OrderPlacedSMS $event): void
    {
        try {
            $orderConfirmationSMSBuilder = new OrderConfirmationSMSBuilder($event->info['order_id'], $event->info['status']);
            $orderConfirmationSMSBuilder->send();
        } catch (Exception $e) {
            Log::info($e->getMessage());
        }
    }
}
