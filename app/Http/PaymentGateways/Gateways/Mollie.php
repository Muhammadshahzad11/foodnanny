<?php

namespace App\Http\PaymentGateways\Gateways;


use App\Models\CapturePaymentNotification;
use Exception;
use App\Enums\Activity;
use App\Models\Currency;
use App\Models\PaymentGateway;
use App\Services\PaymentService;
use App\Services\PaymentAbstract;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Dipokhalder\Settings\Facades\Settings;
use Mollie\Laravel\Facades\Mollie as MollieClient;

class Mollie extends PaymentAbstract
{
    public bool $response = false;

    public function __construct()
    {
        $paymentService = new PaymentService();
        parent::__construct($paymentService);
        $this->paymentGateway = PaymentGateway::with('gatewayOptions')->where(['slug' => 'mollie'])->first();
        if (!blank($this->paymentGateway)) {
            $this->paymentGatewayOption = $this->paymentGateway->gatewayOptions->pluck('value', 'option');
            MollieClient::api()->setApiKey($this->paymentGatewayOption['mollie_api_key']);
        }
    }

    public function payment($frontendOrder, $request): \Illuminate\Http\RedirectResponse
    {
        try {
            $currencyCode = 'USD';
            $currencyId   = Settings::group('site')->get('site_default_currency');
            if (!blank($currencyId)) {
                $currency = Currency::find($currencyId);
                if ($currency) {
                    $currencyCode = $currency->code;
                }
            }

            $payment = MollieClient::api()->payments->create([
                "amount"      => [
                    "currency" => $currencyCode,
                    "value"    => number_format((float)$frontendOrder->total, 2, '.', '')
                ],
                "description" => $frontendOrder->order_serial_no,
                "redirectUrl" => route('payment.success', ['frontendOrder' => $frontendOrder, 'paymentGateway' => 'mollie']),
                "cancelUrl"   => route('payment.cancel', ['frontendOrder' => $frontendOrder, 'paymentGateway' => 'mollie'])
            ]);

            if (isset($payment->id) && $payment->id) {
                $capturePaymentNotification = DB::table('capture_payment_notifications')->where([
                    ['order_id', $frontendOrder->id]
                ]);

                $capturePaymentNotification?->delete();
                $token = $payment->id;
                CapturePaymentNotification::create([
                    'order_id'   => $frontendOrder->id,
                    'token'      => $token,
                    'created_at' => now()
                ]);
                $payment = MollieClient::api()->payments->get($token);
                return redirect()->away($payment->getCheckoutUrl());
            } else {
                return redirect()->route('payment.index', [
                    'frontendOrder'          => $frontendOrder,
                    'paymentGateway' => 'mollie'
                ])->with('error', trans('all.message.something_wrong'));
            }
        } catch (Exception $e) {
            Log::info($e->getMessage());
            return redirect()->route('payment.index', [
                'frontendOrder'          => $frontendOrder,
                'paymentGateway' => 'mollie'
            ])->with('error', $e->getMessage());
        }
    }

    public function status(): bool
    {
        $paymentGateways = PaymentGateway::where(['slug' => 'mollie', 'status' => Activity::ENABLE])->first();
        if ($paymentGateways) {
            return true;
        }
        return false;
    }

    public function success($frontendOrder, $request): \Illuminate\Http\RedirectResponse
    {
        try {
            $capturePaymentNotification = DB::table('capture_payment_notifications')->where([
                ['order_id', $frontendOrder->id]
            ]);
            $token                      = $capturePaymentNotification->first();
            if (!blank($token) && $frontendOrder->id == $token->order_id) {
                $response = MollieClient::api()->payments->get($token->token);
                if ($response->isPaid()) {
                    DB::transaction(function () use ($frontendOrder, $request, $token, $capturePaymentNotification) {
                        $this->paymentService->payment($frontendOrder, 'mollie', $token->token);
                        $capturePaymentNotification->delete();
                        $this->response = true;
                    });
                }
            }

            if ($this->response) {
                return redirect()->route('payment.successful', ['frontendOrder' => $frontendOrder])->with(
                    'success',
                    trans('all.message.payment_successful')
                );
            }
            return redirect()->route('payment.fail', ['frontendOrder' => $frontendOrder, 'paymentGateway' => 'mollie'])->with(
                'error',
                trans('all.message.something_wrong')
            );
        } catch (Exception $e) {
            Log::info($e->getMessage());
            return redirect()->route('payment.fail', ['frontendOrder' => $frontendOrder, 'paymentGateway' => 'mollie'])->with('error', $e->getMessage());
        }
    }

    public function fail($frontendOrder, $request): \Illuminate\Http\RedirectResponse
    {
        return redirect()->route('payment.index', ['frontendOrder' => $frontendOrder, 'paymentGateway' => 'mollie'])->with('error', trans('all.message.something_wrong'));
    }

    public function cancel($frontendOrder, $request): \Illuminate\Http\RedirectResponse
    {
        return redirect('/checkout');
    }
}
