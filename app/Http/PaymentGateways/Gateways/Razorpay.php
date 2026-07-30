<?php

namespace App\Http\PaymentGateways\Gateways;

use Exception;
use Razorpay\Api\Api as RazorPayClient;
use App\Enums\Activity;
use App\Models\PaymentGateway;
use App\Services\PaymentService;
use App\Services\PaymentAbstract;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class Razorpay extends PaymentAbstract
{
    public mixed $response;

    /**
     * @throws \Exception
     */
    public function __construct()
    {
        $paymentService = new PaymentService();
        parent::__construct($paymentService);
        $this->paymentGateway = PaymentGateway::with('gatewayOptions')->where(['slug' => 'razorpay'])->first();
        if (!blank($this->paymentGateway)) {
            $this->paymentGatewayOption = $this->paymentGateway->gatewayOptions->pluck('value', 'option');
            $this->gateway = new RazorPayClient($this->paymentGatewayOption['razorpay_key'], $this->paymentGatewayOption['razorpay_secret']);
        }
    }

    public function payment($frontendOrder, $request): \Illuminate\Http\RedirectResponse
    {
        if (!empty($request->razorpayPaymentId)) {
            try {
                $payment = $this->gateway->payment->fetch($request->razorpayPaymentId)->capture(['amount' => (int)($frontendOrder->total * 100)]);
                if ($payment['status'] == 'captured') {
                    return redirect()->away(route('payment.success', [
                        'frontendOrder' => $frontendOrder,
                        'paymentGateway' => 'razorpay',
                        'token' => $request->razorpayPaymentId
                    ]));
                } else {
                    return redirect()->route('payment.index', ['frontendOrder' => $frontendOrder, 'paymentGateway' => 'razorpay'])->with(
                        'error',
                        trans('all.message.something_wrong')
                    );
                }
            } catch (Exception $e) {
                return redirect()->route('payment.index', ['frontendOrder' => $frontendOrder, 'paymentGateway' => 'razorpay'])->with(
                    'error',
                    $e->getMessage()
                );
            }
        } else {
            return redirect()->route('payment.index', [
                'frontendOrder' => $frontendOrder,
                'paymentGateway' => 'razorpay'
            ])->with('error', trans('all.message.something_wrong'));
        }
    }

    public function status(): bool
    {
        $paymentGateways = PaymentGateway::where(['slug' => 'razorpay', 'status' => Activity::ENABLE])->first();
        if ($paymentGateways) {
            return true;
        }
        return false;
    }

    public function success($frontendOrder, $request): \Illuminate\Http\RedirectResponse
    {
        try {
            if (isset($request->token)) {
                $paymentService = new PaymentService;
                $paymentService->payment($frontendOrder, 'razorpay', $request->token);
                return redirect()->route('payment.successful', ['frontendOrder' => $frontendOrder])->with('success', trans('all.message.payment_successful'));
            } else {
                return redirect()->route('payment.fail', [
                    'frontendOrder' => $frontendOrder,
                    'paymentGateway' => 'razorpay'
                ])->with('error', $this->response['message'] ?? trans('all.message.something_wrong'));
            }
        } catch (Exception $e) {
            Log::info($e->getMessage());
            DB::rollBack();
            return redirect()->route('payment.fail', [
                'frontendOrder' => $frontendOrder,
                'paymentGateway' => 'razorpay'
            ])->with('error', $e->getMessage());
        }
    }

    public function fail($frontendOrder, $request): \Illuminate\Http\RedirectResponse
    {
        return redirect()->route('payment.index', ['frontendOrder' => $frontendOrder, 'paymentGateway' => 'razorpay'])->with('error', trans('all.message.something_wrong'));
    }

    public function cancel($frontendOrder, $request): \Illuminate\Http\RedirectResponse
    {
        return redirect('/checkout');
    }
}
