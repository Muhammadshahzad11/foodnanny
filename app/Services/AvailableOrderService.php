<?php

namespace App\Services;

use Exception;
use App\Enums\Ask;
use App\Enums\Role;
use App\Models\Order;
use App\Enums\OrderType;
use App\Enums\OrderStatus;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\PaginateRequest;
use App\Libraries\QueryExceptionLibrary;
use Dipokhalder\Settings\Facades\Settings;
use App\Events\RestaurantDeliveryBoyOrderAcceptSMS;
use App\Events\RestaurantDeliveryBoyOrderAcceptEmail;
use App\Events\RestaurantDeliveryBoyOrderAcceptPushNotification;

class AvailableOrderService
{
    protected array $orderFilter = [
        'order_serial_no',
        'order_datetime',
        'status'
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
            $orderColumn = $request->get('order_column') ?? 'id';
            $orderType   = $request->get('order_by') ?? 'desc';

            $deliveryLocation = Auth::user()?->deliveryLocation;
            if ($deliveryLocation) {
                $riderZoneId = (int) (Auth::user()?->zone_id ?? 0);

                return Order::whereIn('status', [OrderStatus::PREPARING, OrderStatus::PREPARED])->where(['delivery_boy_id' => null, 'order_type' => OrderType::DELIVERY, 'delivery_boy_request' => Ask::YES])->when($riderZoneId > 0, function ($query) use ($riderZoneId) {
                    $query->where('zone_id', $riderZoneId);
                })->where(function ($query) use ($requests) {
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
                })->whereHas('address', function ($q) use ($deliveryLocation) {
                    if ($deliveryLocation) {
                        $q->withinDistanceOf($deliveryLocation->latitude, $deliveryLocation->longitude, Settings::group('site')->get('site_delivery_boy_order_radius', 1));
                    }
                })->orderBy($orderColumn, $orderType)->$method($methodValue);
            } else {
                return collect([]);
            }
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }

    /**
     * @throws Exception
     */
    public function changeStatus(Order $order): void
    {
        try {
            $acceptedOrder =  Order::where(['delivery_boy_id' => Auth::user()->id])->whereNotIn('status', [OrderStatus::DELIVERED, OrderStatus::RETURNED])->get();
            if(count($acceptedOrder) < (int) Settings::group('site')->get('site_same_time_delivery_boy_maximum_orders_accept_limit')) {
                if($order->delivery_boy_id == null && ($order->status == OrderStatus::ACCEPT || $order->status == OrderStatus::PREPARING || $order->status == OrderStatus::PREPARED)) {
                    if (Auth::user()->myRole == Role::DELIVERY_BOY) {
                        $order->delivery_boy_id = Auth::user()->id;
                        $order->save();

                        RestaurantDeliveryBoyOrderAcceptEmail::dispatch(['order_id' => $order->id]);
                        RestaurantDeliveryBoyOrderAcceptSMS::dispatch(['order_id' => $order->id]);
                        RestaurantDeliveryBoyOrderAcceptPushNotification::dispatch(['order_id' => $order->id]);
                    }
                } else {
                    Log::info(trans('all.message.order_already_accepted'));
                    throw new Exception(trans('all.message.order_already_accepted'), 422);
                }
            } else {
                Log::info(trans('all.message.maximum_order_accepted'));
                throw new Exception(trans('all.message.maximum_order_accepted'), 422);
            }
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }
}
