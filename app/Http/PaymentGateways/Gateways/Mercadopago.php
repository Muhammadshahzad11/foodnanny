<?php

namespace App\Http\PaymentGateways\Gateways;


use Exception;
use App\Enums\Activity;
use App\Enums\GatewayMode;
use App\Models\Currency;
use App\Models\PaymentGateway;
use App\Services\PaymentService;
use App\Services\PaymentAbstract;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use SantiGraviano\LaravelMercadoPago\MP;
use Dipokhalder\Settings\Facades\Settings;

class Mercadopago extends PaymentAbstract
{
    public mixed $response;

    /**
     * @throws \Exception
     */
    public function __construct()
    {
        $paymentService = new PaymentService();
        parent::__construct($paymentService);
        $this->paymentGateway = PaymentGateway::with('gatewayOptions')->where(['slug' => 'mercadopago'])->first();
        if (!blank($this->paymentGateway)) {
            $this->paymentGatewayOption = $this->paymentGateway->gatewayOptions->pluck('value', 'option');
            $this->gateway = new MP($this->paymentGatewayOption['mercadopago_client_id'], $this->paymentGatewayOption['mercadopago_client_secret']);
        }
    }

    public function payment($frontendOrder, $request): \Illuminate\Http\RedirectResponse
    {
        try {
            $currencyCode = 'ARS';
            $currencyId = Settings::group('site')->get('site_default_currency');
            if (!blank($currencyId)) {
                $currency = Currency::find($currencyId);
                if ($currency) {
                    $currencyCode = $currency->code;
                }
            }
            $data = [
                'items' => [
                    [
                        'id' => $frontendOrder->order_serial_no,
                        'title' => $frontendOrder->order_serial_no,
                        'quantity' => 1,
                        'currency_id' => $currencyCode,
                        'unit_price' => floatval($frontendOrder->total),
                    ]
                ],
                'auto_return' => 'approved',
                'back_urls' => (object)[
                    'success' => route('payment.success', ['frontendOrder' => $frontendOrder, 'paymentGateway' => 'mercadopago']),
                    'failure' => route('payment.fail', ['frontendOrder' => $frontendOrder, 'paymentGateway' => 'mercadopago']),
                    'pending' => route('payment.index', ['frontendOrder' => $frontendOrder, 'paymentGateway' => 'mercadopago'])
                ]
            ];

            $response = $this->gateway->create_preference($data);

            if ($response['response']['client_id']) {
                if ($this->paymentGatewayOption['mercadopago_mode'] == GatewayMode::SANDBOX) {
                    return redirect()->to($response['response']['sandbox_init_point']);
                } else {
                    return redirect()->to($response['response']['init_point']);
                }
            } else {
                return redirect()->route('payment.index', ['frontendOrder' => $frontendOrder, 'paymentGateway' => 'mercadopago'])->with(
                    'error',
                    trans('all.message.something_wrong')
                );
            }
        } catch (Exception $e) {
            Log::info($e->getMessage());
            return redirect()->route('payment.index', ['frontendOrder' => $frontendOrder, 'paymentGateway' => 'mercadopago'])->with(
                'error',
                $e->getMessage()
            );
        }
    }

    public function status(): bool
    {
        $paymentGateways = PaymentGateway::where(['slug' => 'mercadopago', 'status' => Activity::ENABLE])->first();
        if ($paymentGateways) {
            return true;
        }
        return false;
    }

    public function success($frontendOrder, $request): \Illuminate\Http\RedirectResponse
    {
        try {
            if (isset($request['status']) && $request['status'] == 'approved') {
                $this->paymentService->payment($frontendOrder, 'mercadopago', $request['payment_id']);
                return redirect()->route('payment.successful', ['frontendOrder' => $frontendOrder])->with(
                    'success',
                    trans('all.message.payment_successful')
                );
            } else {
                return redirect()->route('payment.fail', ['frontendOrder' => $frontendOrder, 'paymentGateway' => 'mercadopago'])->with(
                    'error',
                    $this->response['message'] ?? trans('all.message.something_wrong')
                );
            }
        } catch (Exception $e) {
            Log::info($e->getMessage());
            DB::rollBack();
            return redirect()->route('payment.fail', ['frontendOrder' => $frontendOrder, 'paymentGateway' => 'mercadopago'])->with(
                'error',
                $e->getMessage()
            );
        }
    }

    public function fail($frontendOrder, $request): \Illuminate\Http\RedirectResponse
    {
        return redirect()->route('payment.index', ['frontendOrder' => $frontendOrder, 'paymentGateway' => 'mercadopago'])->with('error', trans('all.message.something_wrong'));
    }

    public function cancel($frontendOrder, $request): \Illuminate\Http\RedirectResponse
    {
        return redirect('/checkout');
    }
}
