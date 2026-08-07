<?php

namespace App\Http\PaymentGateways\Gateways;


use App\Models\OrderItem;
use Exception;
use App\Enums\Ask;
use App\Enums\Status;
use App\Enums\Activity;
use App\Models\PaymentGateway;
use App\Services\PaymentService;
use App\Services\PaymentAbstract;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Dipokhalder\Settings\Facades\Settings;
use App\Models\CapturePaymentNotification;

class Cashondelivery extends PaymentAbstract
{
    public bool $response = false;

    public function __construct()
    {
        $paymentService = new PaymentService();
        parent::__construct($paymentService);
    }

    public function payment($frontendOrder, $request): \Illuminate\Http\RedirectResponse
    {
        try {
            $site = Settings::group('site')->all();
            if ($site['site_cash_on_delivery'] == Activity::ENABLE) {
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
                return redirect()->away(route('payment.success', ['paymentGateway' => 'cashondelivery', 'frontendOrder' => $frontendOrder, 'token' => $token]));
            } else {
                return redirect()->route('payment.index', ['frontendOrder' => $frontendOrder, 'paymentGateway' => 'cashondelivery'])->with('error', trans('all.message.something_wrong'));
            }
        } catch (Exception $e) {
            Log::info($e->getMessage());
            return redirect()->route('payment.index', ['frontendOrder' => $frontendOrder, 'paymentGateway' => 'cashondelivery'])->with('error', $e->getMessage()
            );
        }
    }

    public function status(): bool
    {
        $paymentGateways = PaymentGateway::where(['slug' => 'cashondelivery', 'status' => Activity::ENABLE])->first();
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
                        $frontendOrder->active = Ask::YES;
                        $frontendOrder->save();
                        OrderItem::where(['order_id' => $frontendOrder->id, 'status' => Status::INACTIVE])?->update(['status' => Status::ACTIVE]);
                        $capturePaymentNotification->delete();
                        $this->response = true;
                    }
                }
            });

            if ($this->response) {
                try {
                    // Kitchen service is typed to Order (same orders table as FrontendOrder).
                    $order = \App\Models\Order::query()->find($frontendOrder->id);
                    if ($order) {
                        app(\App\Services\KitchenOrderService::class)->publishIfEligible($order);
                    }
                } catch (\Throwable $e) {
                    Log::info('COD kitchen notify: ' . $e->getMessage());
                }

                return redirect()->route('payment.successful', ['frontendOrder' => $frontendOrder])->with('success', trans('all.message.payment_successful'));
            }
            return redirect()->route('payment.fail', ['frontendOrder' => $frontendOrder, 'paymentGateway' => 'cashondelivery'])->with('error', trans('all.message.something_wrong'));
        } catch (Exception $e) {
            Log::info($e->getMessage());
            DB::rollBack();
            return redirect()->route('payment.fail', ['frontendOrder' => $frontendOrder, 'paymentGateway' => 'cashondelivery'])->with('error', $e->getMessage());
        }
    }

    public function fail($frontendOrder, $request): \Illuminate\Http\RedirectResponse
    {
        return redirect()->route('payment.index', ['frontendOrder' => $frontendOrder, 'paymentGateway' => 'cashondelivery'])->with('error', trans('all.message.something_wrong'));
    }

    public function cancel($frontendOrder, $request): \Illuminate\Foundation\Application|\Illuminate\Routing\Redirector|\Illuminate\Http\RedirectResponse|\Illuminate\Contracts\Foundation\Application
    {
        return redirect('/checkout');
    }
}
