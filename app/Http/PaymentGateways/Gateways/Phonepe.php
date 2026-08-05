<?php

namespace App\Http\PaymentGateways\Gateways;


use Exception;
use App\Enums\Activity;
use App\Enums\GatewayMode;
use App\Models\PaymentGateway;
use App\Services\PaymentService;
use App\Services\PaymentAbstract;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Dipesh79\LaravelPhonePe\LaravelPhonePe;
use Illuminate\Support\Facades\Config;

class Phonepe extends PaymentAbstract
{
    public mixed $response;

    public function __construct()
    {
        $paymentService = new PaymentService();
        parent::__construct($paymentService);
        $this->paymentGateway       = PaymentGateway::with('gatewayOptions')->where(['slug' => 'phonepe'])->first();
        $this->paymentGatewayOption = $this->paymentGateway->gatewayOptions->pluck('value', 'option');
        Config::set('phonepe.merchantId', $this->paymentGatewayOption['phonepe_merchant_id']);
        Config::set('phonepe.merchantUserId', $this->paymentGatewayOption['phonepe_merchant_user_id']);
        Config::set('phonepe.env', $this->paymentGatewayOption['phonepe_mode'] == GatewayMode::SANDBOX ? "staging" : "production");
        Config::set('phonepe.saltIndex', $this->paymentGatewayOption['phonepe_key_index']);
        Config::set('phonepe.saltKey', $this->paymentGatewayOption['phonepe_key']);
    }

    public function payment($frontendOrder, $request): \Illuminate\Routing\Redirector|\Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse
    {
        try {

            Config::set('phonepe.merchantTransactionId', "PHONEPE" . uniqid());
            Config::set('phonepe.redirectUrl', route('payment.success', ['frontendOrder' => $frontendOrder, 'paymentGateway' => 'phonepe']));
            Config::set('phonepe.callBackUrl', route('payment.success', ['frontendOrder' => $frontendOrder, 'paymentGateway' => 'phonepe']));

            $merchantTransactionId = Config::get('phonepe.merchantTransactionId');
            $phonepe = new LaravelPhonePe();
            $url = $phonepe->makePayment(floatval($frontendOrder->total), $frontendOrder->user?->phone, route('payment.success', ['frontendOrder' => $frontendOrder, 'paymentGateway' => 'phonepe']), $merchantTransactionId);

            if ($url) {
                return redirect()->away($url);
            } else {
                return redirect()->route('payment.index', [
                    'frontendOrder'          => $frontendOrder,
                    'paymentGateway' => 'phonepe'
                ])->with('error', "JSON Data parsing error!");
            }
        } catch (Exception $e) {
            Log::info($e->getMessage());
            return redirect()->route('payment.index', [
                'frontendOrder'          => $frontendOrder,
                'paymentGateway' => 'phonepe'
            ])->with('error', $e->getMessage());
        }
    }

    public function status(): bool
    {
        $paymentGateways = PaymentGateway::where(['slug' => 'phonepe', 'status' => Activity::ENABLE])->first();
        if ($paymentGateways) {
            return true;
        }
        return false;
    }

    public function success($frontendOrder, $request): \Illuminate\Http\RedirectResponse
    {
        try {
            if (isset($request['transactionId']) && $request['code'] == "PAYMENT_SUCCESS") {
                $paymentService = new PaymentService;
                $paymentService->payment($frontendOrder, 'phonepe', $request['transactionId']);
                return redirect()->route('payment.successful', ['frontendOrder' => $frontendOrder])->with('success', trans('all.message.payment_successful'));
            } else {
                return redirect()->route('payment.fail', [
                    'frontendOrder'          => $frontendOrder,
                    'paymentGateway' => 'phonepe'
                ])->with('error', $this->response['message'] ?? trans('all.message.something_wrong'));
            }
        } catch (Exception $e) {
            Log::info($e->getMessage());
            DB::rollBack();
            return redirect()->route('payment.fail', [
                'frontendOrder'          => $frontendOrder,
                'paymentGateway' => 'phonepe'
            ])->with('error', $e->getMessage());
        }
    }

    public function fail($frontendOrder, $request): \Illuminate\Http\RedirectResponse
    {
        return redirect()->route('payment.index', ['frontendOrder' => $frontendOrder, 'paymentGateway' => 'phonepe'])->with('error', trans('all.message.something_wrong'));
    }

    public function cancel($frontendOrder, $request): \Illuminate\Http\RedirectResponse
    {
        return redirect('/checkout');
    }
}
