<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class RestaurantDeliveryBoyPickedOrder extends Mailable
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

    public function build(): RestaurantDeliveryBoyPickedOrder
    {
        return $this->subject("Delivery boy order pick confirmation")->markdown('emails.restaurantDeliveryBoyPickedOrder', [
            'orderId' => $this->orderId,
            'message' => $this->message
        ]);
    }
}
