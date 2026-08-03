<?php

namespace App\Http\Controllers\Frontend;


use App\Enums\Activity;
use App\Enums\OrderStatus;
use App\Enums\OrderType;
use App\Enums\PaymentStatus;
use App\Events\OrderPlacedEmail;
use App\Events\OrderPlacedPushNotification;
use App\Events\OrderPlacedSMS;
use App\Events\RestaurantOrderReceivedEmail;
use App\Events\RestaurantOrderReceivedPushNotification;
use App\Events\RestaurantOrderReceivedSMS;
use App\Http\Requests\PaymentRequest;
use App\Libraries\AppLibrary;
use App\Models\Currency;
use App\Models\FrontendOrder;
use App\Models\PaymentGateway;
use App\Models\ThemeSetting;
use App\Services\PaymentManagerService;
use Exception;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Dipokhalder\Settings\Facades\Settings;

class PaymentController extends Controller
{
    private PaymentManagerService $paymentManagerService;

    public function __construct(PaymentManagerService $paymentManagerService)
    {
        $this->paymentManagerService = $paymentManagerService;
    }

    public function index(PaymentGateway $paymentGateway, FrontendOrder $frontendOrder): \Illuminate\Contracts\View\Factory|\Illuminate\Foundation\Application|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse
    {
        $credit          = false;
        $cashOnDelivery  = false;
        $paymentGateways = PaymentGateway::with('gatewayOptions')->where(['status' => Activity::ENABLE])->get();
        $company         = Settings::group('company')->all();
        $site            = Settings::group('site')->all();
        $logo            = ThemeSetting::where(['key' => 'theme_logo'])->first();
        $faviconLogo     = ThemeSetting::where(['key' => 'theme_favicon_logo'])->first();
        $currency        = Currency::findOrFail(Settings::group('site')->get('site_default_currency'));
        if ($frontendOrder?->user?->balance >= $frontendOrder->total) {
            $credit = true;
        }

        if ($site['site_cash_on_delivery'] == Activity::ENABLE) {
            $cashOnDelivery = true;
        }

        $frontendOrder->load('restaurant');
        if (blank($frontendOrder->transaction) && $frontendOrder->payment_status === PaymentStatus::UNPAID) {
            return view('payment', [
                'company'         => $company,
                'logo'            => $logo,
                'currency'        => $currency,
                'faviconLogo'     => $faviconLogo,
                'paymentGateways' => $paymentGateways,
                'frontendOrder'   => $frontendOrder,
                'creditAmount'    => AppLibrary::currencyAmountFormat($frontendOrder->user?->balance),
                'credit'          => $credit,
                'cashOnDelivery'  => $cashOnDelivery,
                'paymentMethod'   => $paymentGateway
            ]);
        }
        return redirect()->route('home')->with('error', trans('all.message.payment_canceled'));
    }

    public function payment(FrontendOrder $frontendOrder, PaymentRequest $paymentRequest)
    {
        if ($this->paymentManagerService->gateway($paymentRequest->paymentMethod)->status()) {
            $className = 'App\\Http\\PaymentGateways\\PaymentRequests\\' . ucfirst($paymentRequest->paymentMethod);
            $gateway   = new $className;
            $paymentRequest->validate($gateway->rules());
            return $this->paymentManagerService->gateway($paymentRequest->paymentMethod)->payment($frontendOrder, $paymentRequest);
        } else {
            return redirect()->route('payment.index', ['paymentGateway' => $paymentRequest->paymentMethod, 'frontendOrder' => $frontendOrder])->with('error', trans('all.message.payment_gateway_disable'));
        }
    }

    public function success(PaymentGateway $paymentGateway, FrontendOrder $frontendOrder, Request $request)
    {
        return $this->paymentManagerService->gateway($paymentGateway->slug)->success($frontendOrder, $request);
    }

    public function fail(PaymentGateway $paymentGateway, FrontendOrder $frontendOrder, Request $request)
    {
        return $this->paymentManagerService->gateway($paymentGateway->slug)->fail($frontendOrder, $request);
    }

    public function cancel(PaymentGateway $paymentGateway, FrontendOrder $frontendOrder, Request $request)
    {
        return $this->paymentManagerService->gateway($paymentGateway->slug)->cancel($frontendOrder, $request);
    }

    public function successful(FrontendOrder $frontendOrder): \Illuminate\Foundation\Application|\Illuminate\Routing\Redirector|\Illuminate\Http\RedirectResponse
    {
        try {
            if (in_array((int) $frontendOrder->order_type, [
                OrderType::DELIVERY,
                OrderType::TAKEAWAY,
                OrderType::DINING_TABLE,
            ], true)) {
                OrderPlacedEmail::dispatch(['order_id' => $frontendOrder->id, 'status' => OrderStatus::PENDING]);
                OrderPlacedSMS::dispatch(['order_id' => $frontendOrder->id, 'status' => OrderStatus::PENDING]);
                OrderPlacedPushNotification::dispatch(['order_id' => $frontendOrder->id, 'status' => OrderStatus::PENDING]);
            }

            RestaurantOrderReceivedEmail::dispatch(['order_id' => $frontendOrder->id]);
            RestaurantOrderReceivedSMS::dispatch(['order_id' => $frontendOrder->id]);
            RestaurantOrderReceivedPushNotification::dispatch(['order_id' => $frontendOrder->id]);
        } catch (Exception $e) {

        }

        $frontendOrder->load('restaurant');
        if (in_array((int) $frontendOrder->order_type, [
            OrderType::DELIVERY,
            OrderType::TAKEAWAY,
            OrderType::DINING_TABLE,
        ], true)) {
            return redirect('/my-orders/?id=' . $frontendOrder->id);
        }

        return redirect()->route('home');
    }
}
