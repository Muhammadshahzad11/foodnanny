<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class RestaurantDeliveryBoyAcceptOrder extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */

    public int $orderId;
    public mixed $message;

    public function __construct($orderId, $message)
    {
        $this->orderId = $orderId;
        $this->message = $message;
    }

    public function build(): RestaurantDeliveryBoyAcceptOrder
    {
        return $this->subject("Delivery boy accept order confirmation")->markdown('emails.restaurantDeliveryBoyAcceptOrder', [
            'orderId' => $this->orderId,
            'message' => $this->message
        ]);
    }
}
