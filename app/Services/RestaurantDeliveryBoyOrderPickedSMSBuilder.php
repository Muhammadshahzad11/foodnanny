<?php

namespace App\Services;


use App\Enums\Role;
use App\Enums\SwitchBox;
use App\Models\FrontendOrder;
use App\Models\NotificationAlert;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Log;


class RestaurantDeliveryBoyOrderPickedSMSBuilder
{
    public int $orderId;
    public object $order;

    public function __construct($orderId)
    {
        $this->orderId = $orderId;
        $this->order   = FrontendOrder::find($orderId);
    }

    public function send(): void
    {
        if (!blank($this->order)) {
            $restaurantOwner = User::role(Role::RESTAURANT_OWNER)->where(['restaurant_id' => $this->order->restaurant_id])->whereNotNull('phone')->first();

            $i           = 0;
            $phoneArrays = [];
            if (!blank($restaurantOwner)) {
                $phoneArrays[$i] = [
                    'code'  => $restaurantOwner->country_code,
                    'phone' => $restaurantOwner->phone
                ];
                $i++;
            }

            if (count($phoneArrays) > 0) {
                try {
                    $notificationAlert = NotificationAlert::where(['language' => 'delivery_boy_order_received_message'])->first();
                    if ($notificationAlert && $notificationAlert->sms == SwitchBox::ON) {
                        $message = trans('all.message.order_received_sms_message', [
                            'orderId' => $this->order->order_serial_no,
                            'message' => $notificationAlert->sms_message
                        ]);
                        foreach ($phoneArrays as $phoneArray) {
                            $this->sms($phoneArray['code'], $phoneArray['phone'], $message);
                        }
                    }
                } catch (Exception $e) {
                    Log::info($e->getMessage());
                }
            }
        }
    }

    private function sms($code, $phone, $message): void
    {
        try {
            $smsManagerService = new SmsManagerService();
            $smsService        = new SmsService();
            if ($smsService->gateway() && $smsManagerService->gateway($smsService->gateway())->status()) {
                $smsManagerService->gateway($smsService->gateway())->send($code, $phone, $message);
            }
        } catch (Exception $e) {
            Log::info($e->getMessage());
        }
    }

}
