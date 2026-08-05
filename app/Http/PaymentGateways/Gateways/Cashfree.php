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
use Illuminate\Support\Facades\Config;
use LoveyCom\CashFree\PaymentGateway\Order as CashfreeClient;

class Cashfree extends PaymentAbstract
{

    /**
     * @throws \Exception
     */
    public function __construct()
    {
        $paymentService = new PaymentService();
        parent::__construct($paymentService);
        $this->paymentGateway = PaymentGateway::with('gatewayOptions')->where(['slug' => 'cashfree'])->first();
        if (!blank($this->paymentGateway)) {
            $this->paymentGatewayOption = $this->paymentGateway->gatewayOptions->pluck('value', 'option');
            Config::set('cashfree.appID', $this->paymentGatewayOption['cashfree_app_id']);
            Config::set('cashfree.secretKey', $this->paymentGatewayOption['cashfree_secret_key']);
            Config::set('cashfree.PG.appID', $this->paymentGatewayOption['cashfree_app_id']);
            Config::set('cashfree.PG.secretKey', $this->paymentGatewayOption['cashfree_secret_key']);
            Config::set('cashfree.PG.isLive', !($this->paymentGatewayOption['cashfree_mode'] == GatewayMode::SANDBOX));
        }
    }

    public function payment($frontendOrder, $request): \Illuminate\Http\RedirectResponse
    {
        try {
            $cashfree = new CashfreeClient();
            $data["orderId"] = $frontendOrder->order_serial_no;
            $data["orderAmount"] = floatval($frontendOrder->total);
            $data["orderNote"] = "";
            $data["customerPhone"] = $frontendOrder->user?->phone;
            $data["customerName"] = $frontendOrder->user?->name;
            $data["customerEmail"] = $frontendOrder->user?->email;
            $data["returnUrl"] = route('payment.success', ['frontendOrder' => $frontendOrder, 'paymentGateway' => 'cashfree']);
            $data["notifyUrl"] = route('payment.success', ['frontendOrder' => $frontendOrder, 'paymentGateway' => 'cashfree']);
            $cashfree->create($data);
            $link = $cashfree->getLink($frontendOrder->order_serial_no);

            if (isset($link->paymentLink) && $link->status == "OK") {
                return redirect()->away($link->paymentLink);
            } else {
                return redirect()->route('payment.index', ['frontendOrder' => $frontendOrder, 'paymentGateway' => 'cashfree'])->with(
                    'error',
                    $response['message'] ?? trans('all.message.something_wrong')
                );
            }
        } catch (Exception $e) {
            Log::info($e->getMessage());
            return redirect()->route('payment.index', ['frontendOrder' => $frontendOrder, 'paymentGateway' => 'cashfree'])->with(
                'error',
                $e->getMessage()
            );
        }
    }

    public function status(): bool
    {
        $paymentGateways = PaymentGateway::where(['slug' => 'cashfree', 'status' => Activity::ENABLE])->first();
        if ($paymentGateways) {
            return true;
        }
        return false;
    }

    public function success($frontendOrder, $request): \Illuminate\Http\RedirectResponse
    {
        try {
            if (isset($request['referenceId']) && $request['txStatus'] == 'SUCCESS') {
                $this->paymentService->payment($frontendOrder, 'cashfree', $request['referenceId']);
                return redirect()->route('payment.successful', ['frontendOrder' => $frontendOrder])->with(
                    'success',
                    trans('all.message.payment_successful')
                );
            } else {
                return redirect()->route('payment.fail', ['frontendOrder' => $frontendOrder, 'paymentGateway' => 'cashfree'])->with(
                    'error',
                    $request['txMsg']
                );
            }
        } catch (Exception $e) {
            Log::info($e->getMessage());
            DB::rollBack();
            return redirect()->route('payment.fail', ['frontendOrder' => $frontendOrder, 'paymentGateway' => 'cashfree'])->with(
                'error',
                $e->getMessage()
            );
        }
    }

    public function fail($frontendOrder, $request): \Illuminate\Http\RedirectResponse
    {
        return redirect()->route('payment.index', ['frontendOrder' => $frontendOrder, 'paymentGateway' => 'cashfree'])->with('error', trans('all.message.something_wrong'));
    }

    public function cancel($frontendOrder, $request): \Illuminate\Http\RedirectResponse
    {
        return redirect('/checkout');
    }
}
