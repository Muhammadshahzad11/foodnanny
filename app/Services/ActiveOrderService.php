<?php

namespace App\Services;

use Exception;
use App\Enums\Ask;
use App\Models\Order;
use App\Enums\OrderType;
use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Events\OrderPlacedSMS;
use App\Events\OrderPlacedEmail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\PaginateRequest;
use App\Libraries\QueryExceptionLibrary;
use App\Events\OrderPlacedPushNotification;
use App\Events\RestaurantDeliveryBoyOrderPickedSMS;
use App\Events\RestaurantDeliveryBoyOrderPickedEmail;
use App\Events\RestaurantDeliveryBoyOrderPickedPushNotification;


class ActiveOrderService
{
    public object $order;
    public StatementCalculationService $statementCalculationService;
    protected array $orderFilter = [
        'order_serial_no',
        'status'
    ];

    public function __construct(StatementCalculationService $statementCalculationService)
    {
        $this->statementCalculationService = $statementCalculationService;
    }

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

            return Order::where(['delivery_boy_id' => Auth::user()->id, 'order_type' => OrderType::DELIVERY])->where(function ($query) use ($requests) {
                if (isset($requests['from_date']) && isset($requests['to_date'])) {
                    $first_date = Date('Y-m-d', strtotime($requests['from_date']));
                    $last_date  = Date('Y-m-d', strtotime($requests['to_date']));
                    $query->whereDate('order_datetime', '>=', $first_date)->whereDate('order_datetime', '<=', $last_date);
                }

                foreach ($requests as $key => $request) {
                    if (in_array($key, $this->orderFilter)) {
                        if ($key === "status") {
                            $query->where($key, (int)$request);
                        } else {
                            $query->where($key, 'like', '%' . $request . '%');
                        }
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
    public function show(Order $order): Order
    {
        try {
            if ($order->delivery_boy_id == Auth::user()->id) {
                if ($order->status === OrderStatus::OUT_FOR_DELIVERY) {
                    app(DeliveryOtpService::class)->ensure($order);
                }
                return $order;
            } else {
                Log::info(trans('all.message.something_wrong'));
                throw new Exception(trans('all.message.something_wrong'), 422);
            }
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }

    /**
     * @throws Exception
     */
    public function receivedStatus(Order $order): Order
    {
        try {
            if ($order->delivery_boy_id == Auth::user()->id && $order->is_received == Ask::NO) {
                $order->is_received = Ask::YES;
                $order->status      = OrderStatus::OUT_FOR_DELIVERY;
                $order->save();
                app(DeliveryOtpService::class)->ensure($order, true);
                $this->statementCalculationService->restaurant($order);

                OrderPlacedEmail::dispatch(['order_id' => $order->id, 'status' => OrderStatus::OUT_FOR_DELIVERY]);
                OrderPlacedSMS::dispatch(['order_id' => $order->id, 'status' => OrderStatus::OUT_FOR_DELIVERY]);
                OrderPlacedPushNotification::dispatch(['order_id' => $order->id, 'status' => OrderStatus::OUT_FOR_DELIVERY]);

                RestaurantDeliveryBoyOrderPickedEmail::dispatch(['order_id' => $order->id]);
                RestaurantDeliveryBoyOrderPickedSMS::dispatch(['order_id' => $order->id]);
                RestaurantDeliveryBoyOrderPickedPushNotification::dispatch(['order_id' => $order->id]);

                try {
                    app(RealtimePublisher::class)->customerOrderStatus(
                        $order->fresh(),
                        'out_for_delivery',
                        OrderStatus::PREPARED
                    );
                } catch (\Throwable $e) {
                    Log::info('ActiveOrder receivedStatus realtime: ' . $e->getMessage());
                }
            }
            return $order;
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }

    /**
     * @throws Exception
     */
    public function changeStatus(Order $order, ?string $deliveryOtp = null): Order
    {
        try {
            if ($order->delivery_boy_id == Auth::user()->id && $order->is_received == Ask::YES && $order->status === OrderStatus::OUT_FOR_DELIVERY) {
                $otpService = app(DeliveryOtpService::class);
                $otpService->assertValid($order, $deliveryOtp);
                $otpService->markVerified($order);
                $order->payment_status = PaymentStatus::PAID;
                $order->status         = OrderStatus::DELIVERED;
                $order->save();
                $this->statementCalculationService->deliveryBoy($order);
                $this->statementCalculationService->ownerRevenueFromDeliveryBoy($order);

                OrderPlacedEmail::dispatch(['order_id' => $order->id, 'status' => OrderStatus::DELIVERED]);
                OrderPlacedSMS::dispatch(['order_id' => $order->id, 'status' => OrderStatus::DELIVERED]);
                OrderPlacedPushNotification::dispatch(['order_id' => $order->id, 'status' => OrderStatus::DELIVERED]);

                try {
                    app(RealtimePublisher::class)->customerOrderStatus(
                        $order->fresh(),
                        'complete',
                        OrderStatus::OUT_FOR_DELIVERY
                    );
                } catch (\Throwable $e) {
                    Log::info('ActiveOrder changeStatus realtime: ' . $e->getMessage());
                }
            }
            return $order;
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }
}
