<?php

namespace App\Services;


use App\Enums\Role;
use App\Enums\SwitchBox;
use App\Mail\RestaurantDeliveryBoyAcceptOrder;
use App\Models\FrontendOrder;
use App\Models\NotificationAlert;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class RestaurantDeliveryBoyOrderAcceptEmailBuilder
{
    public int $orderId;
    public object $order;

    public function __construct($orderId)
    {
        $this->orderId = $orderId;
        $this->order = FrontendOrder::find($orderId);
    }

    public function send(): void
    {
        if (!blank($this->order)) {
            $restaurantOwner = User::role(Role::RESTAURANT_OWNER)->where(['restaurant_id' => $this->order->restaurant_id])->whereNotNull('email')->first();

            $i = 0;
            $emailArray = [];
            if (!blank($restaurantOwner)) {
                $emailArray[$i] = $restaurantOwner->email;
                $i++;
            }

            if (count($emailArray) > 0) {
                try {
                    $notificationAlert = NotificationAlert::where(['language' => 'delivery_boy_order_accepted_message'])->first();
                    if ($notificationAlert && $notificationAlert->mail == SwitchBox::ON) {
                        try {
                            Mail::to($emailArray[0])->cc($emailArray)->send(new RestaurantDeliveryBoyAcceptOrder($this->order->order_serial_no, $notificationAlert->mail_message));
                        } catch (Exception $e) {
                            Log::info($e->getMessage());
                        }
                    }
                } catch (Exception $e) {
                    Log::info($e->getMessage());
                }
            }
        }
    }
}
