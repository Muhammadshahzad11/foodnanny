<?php

namespace App\Services;

use Exception;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Order;
use App\Models\Refund;
use App\Enums\ModelType;
use App\Models\Restaurant;
use App\Enums\PaymentStatus;
use App\Libraries\AppLibrary;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Requests\RefundRequest;
use App\Http\Requests\PaginateRequest;
use App\Libraries\QueryExceptionLibrary;

class RefundService
{
    public object $refund;
    protected array $allowedColumns = ['id', 'order_serial_no', 'created_at'];
    protected array $allowedTypes = ['asc', 'desc'];
    protected array $refundFilter = [
        'order_serial_no',
        'refund_amount',
        'deduction_amount'
    ];

    /**
     * @throws Exception
     */
    public function list(PaginateRequest $request)
    {
        try {
            $requests    = $request->all();
            $method      = $request->get('paginate', 0) == 1 ? 'paginate' : 'get';
            $methodValue = $request->get('paginate', 0) == 1 ? $request->get('per_page', 10) : '*';
            $orderColumn = $request->get('order_column', 'id');
            $orderType   = $request->get('order_by', 'desc');

            if (!in_array($orderColumn, $this->allowedColumns) || !in_array($orderType, $this->allowedTypes)) {
                throw new Exception("Invalid order column or type: {$orderColumn}/{$orderType}", 422);
            }

            return Refund::where(function ($query) use ($requests) {
                foreach ($requests as $key => $request) {
                    if (in_array($key, $this->refundFilter)) {
                        $query->where($key, 'like', '%' . $request . '%');
                    }
                }
            })->orderBy($orderColumn, $orderType)->$method($methodValue);
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }

    /**
     * @throws Exception
     */
    public function store(RefundRequest $request): object
    {
        try {
            DB::transaction(function () use ($request) {
                $order = Order::where('order_serial_no', $request->order_serial_no)->first();
                $info  = [
                    'order_id'          => $order->order_serial_no,
                    'order_date'        => AppLibrary::datetime($order->order_datetime),
                    'restaurant_name'   => $order->restaurant?->name,
                    'order_amount'      => AppLibrary::flatAmountFormat($order->total),
                    'refund_date'       => AppLibrary::datetime(Carbon::now()),
                    'refund_amount'     => AppLibrary::flatAmountFormat($request->refund_amount),
                    'deducted_amount'   => AppLibrary::flatAmountFormat($request->deduction_amount),
                    'deducted_type'     => $request->responsible === ModelType::RESTAURANT ? trans('all.label.restaurant') : trans('all.label.delivery_boy'),
                    'deducted_name'     => $request->responsible === ModelType::RESTAURANT ? $order->restaurant?->name : $order->deliveryBoy?->name,
                    'deducted_phone'    => $request->responsible === ModelType::RESTAURANT ? ($order->restaurant?->phone ? $order->restaurant?->country_code . $order->restaurant?->phone : 'N/A') : ($order->deliveryBoy?->phone ? $order->deliveryBoy?->country_code . $order->deliveryBoy?->phone : 'N/A'),
                    'deducted_email'    => $request->responsible === ModelType::RESTAURANT ? ($order->restaurant?->email ?: 'N/A') : ($order->deliveryBoy?->email ?: 'N/A'),
                    'refunded_to_name'  => $order->payment_status == PaymentStatus::PAID ? $order->user?->name : 'N/A',
                    'refunded_to_phone' => $order->payment_status == PaymentStatus::PAID ? ($order->user?->phone ? $order->user?->country_code . $order->user?->phone : 'N/A') : 'N/A',
                    'refunded_to_email' => $order->payment_status == PaymentStatus::PAID ? ($order->user?->email ?: 'N/A') : 'N/A'
                ];

                $this->refund = Refund::create([
                    'order_serial_no'  => $request->order_serial_no,
                    'refund_amount'    => $request->refund_amount,
                    'deduction_amount' => $request->deduction_amount,
                    'info'             => json_encode($info),
                    'responsible_type' => $request->responsible === ModelType::RESTAURANT ? Restaurant::class : User::class,
                    'responsible_id'   => $request->responsible === ModelType::RESTAURANT ? $order->restaurant_id : $order->delivery_boy_id
                ]);

                $statementCalculationService = new StatementCalculationService();
                if ($request->responsible === ModelType::RESTAURANT) {
                    $statementCalculationService->restaurantRefund($order, $request->refund_amount, $request->deduction_amount);
                } elseif ($request->responsible === ModelType::DELIVERY_BOY) {
                    $statementCalculationService->deliveryBoyRefund($order, $request->refund_amount, $request->deduction_amount);
                }
            });
            return $this->refund;
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            DB::rollBack();
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }

    /**
     * @throws Exception
     */
    public function destroy(Refund $refund): void
    {
        try {
            DB::transaction(function () use ($refund) {
                $statementCalculationService = new StatementCalculationService();
                $statementCalculationService->reverseRefund($refund);
                $refund->delete();
            });
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            DB::rollBack();
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }
}
