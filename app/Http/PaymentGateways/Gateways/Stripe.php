<?php

namespace App\Http\PaymentGateways\Gateways;

use App\Enums\Activity;
use App\Models\CapturePaymentNotification;
use App\Models\Currency;
use App\Models\PaymentGateway;
use App\Services\PaymentAbstract;
use App\Services\PaymentService;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Dipokhalder\Settings\Facades\Settings;
use Stripe as StripeClient;

class Stripe extends PaymentAbstract
{

    public bool $response = false;

    public function __construct()
    {
        $paymentService = new PaymentService();
        parent::__construct($paymentService);

        $this->paymentGateway = PaymentGateway::with('gatewayOptions')->where(['slug' => 'stripe'])->first();
        if (!blank($this->paymentGateway)) {
            $this->paymentGatewayOption = $this->paymentGateway->gatewayOptions->pluck('value', 'option');
            $this->gateway              = new StripeClient\StripeClient($this->paymentGatewayOption['stripe_secret']);
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

            $response = $this->gateway->charges->create([
                'amount'      => (int)$frontendOrder->total * 100,
                'currency'    => $currencyCode,
                'source'      => $request->stripeToken,
                'description' => 'Food order payment',
            ]);

            if (isset($response->status) && $response->status == 'succeeded') {
                $capturePaymentNotification = DB::table('capture_payment_notifications')->where([
                    ['order_id', $frontendOrder->id]
                ]);
                $capturePaymentNotification?->delete();
                $token = $response->balance_transaction;
                CapturePaymentNotification::create([
                    'order_id'   => $frontendOrder->id,
                    'token'      => $token,
                    'created_at' => now()
                ]);
                return redirect()->away(
                    route('payment.success', ['paymentGateway' => 'stripe', 'frontendOrder' => $frontendOrder, 'token' => $token])
                );
            } else {
                return redirect()->route('payment.index', ['frontendOrder' => $frontendOrder, 'paymentGateway' => 'stripe'])->with(
                    'error',
                    trans('all.message.something_wrong')
                );
            }
        } catch (Exception $e) {
            Log::info($e->getMessage());
            return redirect()->route('payment.index', ['frontendOrder' => $frontendOrder, 'paymentGateway' => 'stripe'])->with(
                'error',
                $e->getMessage()
            );
        }
    }

    public function status(): bool
    {
        $paymentGateways = PaymentGateway::where(['slug' => 'stripe', 'status' => Activity::ENABLE])->first();
        if ($paymentGateways) {
            return true;
        }
        return false;
    }

    public function success($frontendOrder, $request): \Illuminate\Http\RedirectResponse
    {
        try {
            DB::transaction(function () use ($frontendOrder, $request) {
                if ($request->token) {
                    $capturePaymentNotification = DB::table('capture_payment_notifications')->where([
                        ['token', $request->token]
                    ]);
                    $token                      = $capturePaymentNotification->first();
                    if (!blank($token) && $frontendOrder->id == $token->order_id) {
                        $this->paymentService->payment($frontendOrder, 'stripe', $token->token);
                        $capturePaymentNotification->delete();
                        $this->response = true;
                    }
                }
            });

            if ($this->response) {
                return redirect()->route('payment.successful', ['frontendOrder' => $frontendOrder])->with(
                    'success',
                    trans('all.message.payment_successful')
                );
            }
            return redirect()->route('payment.fail', ['frontendOrder' => $frontendOrder, 'paymentGateway' => 'stripe'])->with(
                'error',
                trans('all.message.something_wrong')
            );
        } catch (Exception $e) {
            Log::info($e->getMessage());
            DB::rollBack();
            return redirect()->route('payment.fail', ['frontendOrder' => $frontendOrder, 'paymentGateway' => 'stripe'])->with('error', $e->getMessage());
        }
    }

    public function fail($frontendOrder, $request): \Illuminate\Http\RedirectResponse
    {
        return redirect()->route('payment.index', ['frontendOrder' => $frontendOrder, 'paymentGateway' => 'stripe'])->with('error', trans('all.message.something_wrong'));
    }

    public function cancel($frontendOrder, $request): \Illuminate\Http\RedirectResponse
    {
        return redirect('/checkout');
    }
}
