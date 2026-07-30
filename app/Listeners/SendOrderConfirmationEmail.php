<?php

namespace App\Listeners;


use App\Events\OrderPlacedEmail;
use App\Services\OrderConfirmationEmailBuilder;
use Exception;
use Illuminate\Support\Facades\Log;


class SendOrderConfirmationEmail
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
    }


    public function handle(OrderPlacedEmail $event): void
    {
        try {
            $orderConfirmationEmailBuilder = new OrderConfirmationEmailBuilder($event->info['order_id'], $event->info['status']);
            $orderConfirmationEmailBuilder->send();
        } catch (Exception $e) {
            Log::info($e->getMessage());
        }
    }
}
