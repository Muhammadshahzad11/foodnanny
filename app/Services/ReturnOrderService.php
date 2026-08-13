<?php

namespace App\Services;

use App\Enums\OrderType;
use App\Http\Requests\PaginateRequest;
use Exception;
use App\Models\Order;
use App\Enums\OrderStatus;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Libraries\QueryExceptionLibrary;
use App\Http\Requests\ReturnOrderRequest;

class ReturnOrderService
{
    public object $order;

    /**
     * @throws Exception
     */
    public function list(PaginateRequest $request)
    { 
        try {
            $requests    = $request->all();
            $method      = $request->get('paginate', 0) == 1 ? 'paginate' : 'get';
            $methodValue = $request->get('paginate', 0) == 1 ? $request->get('per_page', 10) : '*';
            $orderColumn = $request->get('order_column') ?? 'id';
            $orderType   = $request->get('order_by') ?? 'desc';

            return Order::with('transaction', 'orderItems')->where(['order_type' => OrderType::DELIVERY, 'status' => OrderStatus::RETURNED])->where(function ($query) use ($requests) {
                foreach ($requests as $key => $request) {
                    if ($key === 'order_serial_no' && !empty($request)) {
                        $query->where('order_serial_no', 'like', '%' . $request . '%');
                    }
                }
                if (isset($requests['from_date']) && isset($requests['to_date'])) {
                    $first_date = Date('Y-m-d', strtotime($requests['from_date']));
                    $last_date  = Date('Y-m-d', strtotime($requests['to_date']));
                    $query->whereDate('order_datetime', '>=', $first_date)->whereDate('order_datetime', '<=', $last_date);
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
    public function store(ReturnOrderRequest $request): object
    {
        try {
            DB::transaction(function () use ($request) {
                $returnOrder         = Order::where(['order_serial_no' => $request->order_serial_no])->first();
                $this->order         = $returnOrder;
                $this->order->status = OrderStatus::RETURNED;
                $this->order->reason = $request->reason;
                $this->order->save();

                if ($request->hasFile('images')) {
                    foreach ($request->file('images') as $image) {
                        $this->order->addMedia($image)->toMediaCollection('return-image');
                    }
                }
            });
            return $this->order;
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }

    /**
     * @throws Exception
     */
    public function destroy(Order $order): void
    {
        try {
            DB::transaction(function () use ($order) {
                $order->clearMediaCollection('return-image');
                $order->status = OrderStatus::OUT_FOR_DELIVERY;
                $order->reason = null;
                $order->save();
                app(DeliveryOtpService::class)->ensure($order, true);
            });
        } catch (Exception $exception) {
            Log::error($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }

    /**
     * @throws Exception
     */
    public function show(Order $order): Order
    {
        try {
            return $order->load('media', 'user', 'address', 'restaurant', 'deliveryBoy', 'coupon', 'transaction', 'orderItems', 'posDetail');
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }
}
