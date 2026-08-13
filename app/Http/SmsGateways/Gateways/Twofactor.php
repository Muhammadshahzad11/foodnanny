<?php

namespace App\Http\SmsGateways\Gateways;

use Exception;
use GuzzleHttp\Client;
use App\Enums\Activity;
use App\Models\SmsGateway;
use App\Services\SmsAbstract;
use Illuminate\Support\Facades\Log;

class Twofactor extends SmsAbstract
{
    public string $apiKey = '';
    public string $baseUrl;
    public string $otpUrl;
    public string $module = 'TRANS_SMS';
    public string $from = '';

    public function __construct()
    {
        parent::__construct();
        $this->baseUrl = 'https://2factor.in/API/R1/';
        $this->otpUrl  = 'https://2factor.in/API/V1/';
        $this->smsGateway = SmsGateway::with('gatewayOptions')->where(['slug' => 'twofactor'])->first();
        if (!blank($this->smsGateway)) {
            $this->smsGatewayOption = $this->smsGateway->gatewayOptions->pluck('value', 'option');
            $this->gateway          = new Client([
                'timeout' => 15,
                'verify'  => config('services.http.verify_ssl'),
            ]);
            $this->apiKey = (string) ($this->smsGatewayOption['twofactor_api_key'] ?? '');
            $this->module = (string) ($this->smsGatewayOption['twofactor_module'] ?? 'TRANS_SMS');
            $this->from   = (string) ($this->smsGatewayOption['twofactor_from'] ?? '');
        }
    }

    public function status(): bool
    {
        return SmsGateway::where(['slug' => 'twofactor', 'status' => Activity::ENABLE])->exists()
            && $this->apiKey !== '';
    }

    public function send($code, $phone, $message): void
    {
        try {
            if ($this->apiKey === '') {
                Log::warning('2Factor SMS skipped: API key is empty');
                return;
            }

            $to  = $this->normalizePhone($code, $phone);
            $otp = $this->extractOtp((string) $message);

            if ($otp !== null) {
                $this->sendOtp($to, $otp);
                return;
            }

            $this->sendTransactional($to, (string) $message);
        } catch (Exception $exception) {
            Log::warning('2Factor SMS failed', ['error' => $exception->getMessage()]);
        }
    }

    protected function sendOtp(string $to, string $otp): void
    {
        $url = $this->otpUrl . rawurlencode($this->apiKey) . '/SMS/' . rawurlencode($to) . '/' . rawurlencode($otp);
        $response = $this->gateway->get($url);
        $body = (string) $response->getBody();
        Log::info('2Factor OTP dispatched', ['to_suffix' => substr($to, -4), 'http' => $response->getStatusCode()]);

        if (!str_contains(strtolower($body), 'success')) {
            Log::warning('2Factor OTP unexpected response', ['body' => $body]);
        }
    }

    protected function sendTransactional(string $to, string $message): void
    {
        $module = $this->module !== '' ? $this->module : 'TRANS_SMS';
        // OTP/order alerts are transactional; promo module often cannot deliver them.
        if (strcasecmp($module, 'PROMO_SMS') === 0) {
            $module = 'TRANS_SMS';
        }

        $response = $this->gateway->post($this->baseUrl, [
            'form_params' => [
                'module' => $module,
                'apikey' => $this->apiKey,
                'to'     => $to,
                'from'   => $this->senderId(),
                'msg'    => $message,
            ],
        ]);

        $body = (string) $response->getBody();
        Log::info('2Factor SMS dispatched', ['to_suffix' => substr($to, -4), 'http' => $response->getStatusCode()]);

        if (!str_contains(strtolower($body), 'success')) {
            Log::warning('2Factor SMS unexpected response', ['body' => $body]);
        }
    }

    protected function senderId(): string
    {
        $from = preg_replace('/[^A-Za-z0-9]/', '', $this->from) ?? '';
        if ($from === '') {
            return 'NOTICE';
        }
        return strtoupper(substr($from, 0, 6));
    }

    protected function normalizePhone($code, $phone): string
    {
        $digits = preg_replace('/\D+/', '', (string) $code . (string) $phone) ?? '';
        return ltrim($digits, '0');
    }

    protected function extractOtp(string $message): ?string
    {
        if (preg_match('/\b(\d{4,8})\b/', $message, $matches)) {
            return $matches[1];
        }
        return null;
    }
}
