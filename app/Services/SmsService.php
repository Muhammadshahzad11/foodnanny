<?php

namespace App\Services;

use App\Enums\Activity;
use App\Models\SmsGateway;
use Dipokhalder\Settings\Facades\Settings;

class SmsService
{
    public string $gateway;

    public function gateway(): string
    {
        $this->gateway = 'twofactor';
        $gatewayId     = Settings::group('site')->get('site_default_sms_gateway');
        if ($gatewayId) {
            $gateway = SmsGateway::find($gatewayId);
            if ($gateway) {
                $this->gateway = $gateway->slug;
                return $this->gateway;
            }
        }

        $twofactor = SmsGateway::where(['slug' => 'twofactor', 'status' => Activity::ENABLE])->first();
        if ($twofactor) {
            $this->gateway = 'twofactor';
        }

        return $this->gateway;
    }
}
