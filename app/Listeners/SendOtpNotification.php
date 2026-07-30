<?php

namespace App\Listeners;


use App\Events\OtpRequested;
use App\Services\SmsManagerService;
use App\Services\SmsService;
use Exception;
use Illuminate\Support\Facades\Log;


class SendOtpNotification
{
    public SmsManagerService $smsManagerService;
    public string $gateway;

    public function __construct(SmsManagerService $smsManagerService, SmsService $smsService)
    {
        $this->smsManagerService = $smsManagerService;
        $this->gateway           = $smsService->gateway();
    }


    public function handle(OtpRequested $event): void
    {
        try {
            if ($this->smsManagerService->gateway($this->gateway)->status()) {
                $this->smsManagerService->gateway($this->gateway)->send(
                    $event->info['code'],
                    $event->info['phone'],
                    trans("all.message.your_code", ['number' => $event->info['token']])
                );
            }
        } catch (Exception $e) {
            Log::info($e->getMessage());
        }
    }
}
