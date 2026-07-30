<?php

namespace App\Http\PaymentGateways\Gateways;

use App\Enums\Activity;
use App\Models\CapturePaymentNotification;
use App\Models\PaymentGateway;
use App\Models\User;
use App\Services\PaymentAbstract;
use App\Services\PaymentService;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class Credit extends PaymentAbstract
{
    public bool $response = false;

    public function __construct()
    {
        $paymentService = new PaymentService();
        parent::__construct($paymentService);
    }

    public function payment($frontendOrder, $request) : \Illuminate\Http\RedirectResponse
    {
        try {
            if ($frontendOrder?->user?->balance >= $frontendOrder->total) {
                $capturePaymentNotification = DB::table('capture_payment_notifications')->where([
                    ['order_id', $frontendOrder->id]
                ]);
                $capturePaymentNotification?->delete();
                $token = rand(111111111, 999999999);
                CapturePaymentNotification::create([
                    'order_id'   => $frontendOrder->id,
                    'token'      => $token,
                    'created_at' => now()
                ]);

                return redirect()->away(route('payment.success', ['frontendOrder' => $frontendOrder, 'paymentGateway' => 'credit', 'token' => $token]));
            } else {
                return redirect()->route('payment.index', ['paymentGateway' => 'credit', 'frontendOrder' => $frontendOrder])->with('error', trans('all.message.something_wrong'));
            }
        } catch (Exception $e) {
            Log::info($e->getMessage());
            return redirect()->route('payment.index', ['paymentGateway' => 'credit', 'frontendOrder' => $frontendOrder])->with('error', $e->getMessage());
        }
    }

    public function status() : bool
    {
        $paymentGateways = PaymentGateway::where(['slug' => 'credit', 'status' => Activity::ENABLE])->first();
        if ($paymentGateways) {
            return true;
        }
        return false;
    }

    public function success($frontendOrder, $request) : \Illuminate\Http\RedirectResponse
    {
        try {
            DB::transaction(function () use ($frontendOrder, $request) {
                if ($request->token) {
                    $capturePaymentNotification = DB::table('capture_payment_notifications')->where([['token', $request->token]]);
                    $token                      = $capturePaymentNotification->first();
                    if (!blank($token) && $frontendOrder->id == $token->order_id) {
                        $user = User::find($frontendOrder->user_id);
                        if ($user) {
                            $user->balance = ($user->balance - $frontendOrder->total);
                            $user->save();
                            $this->paymentService->payment($frontendOrder, 'credit', $token->token);
                            $capturePaymentNotification->delete();
                            $this->response = true;
                        }
                    }
                }
            });

            if ($this->response) {
                return redirect()->route('payment.successful', ['frontendOrder' => $frontendOrder])->with('success', trans('all.message.payment_successful'));
            }
            return redirect()->route('payment.fail', ['frontendOrder' => $frontendOrder, 'paymentGateway' => 'credit'])->with('error', trans('all.message.something_wrong'));
        } catch (Exception $e) {
            Log::info($e->getMessage());
            DB::rollBack();
            return redirect()->route('payment.fail', ['frontendOrder' => $frontendOrder, 'paymentGateway' => 'credit'])->with('error', $e->getMessage());
        }
    }

    public function fail($frontendOrder, $request) : \Illuminate\Http\RedirectResponse
    {
        return redirect()->route('payment.index', ['frontendOrder' => $frontendOrder])->with('error', trans('all.message.something_wrong'));
    }

    public function cancel($frontendOrder, $request) : \Illuminate\Http\RedirectResponse
    {
        return redirect('/checkout');
    }
}
