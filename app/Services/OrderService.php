<?php

namespace App\Services;



use Exception;
use App\Enums\Ask;
use Carbon\Carbon;
use App\Models\User;
use App\Enums\Source;
use App\Enums\Status;
use App\Models\Order;
use App\Enums\IsAdvance;
use App\Enums\OrderType;
use App\Models\OrderItem;
use App\Enums\OrderStatus;
use App\Models\OrderSetup;
use App\Enums\PaymentStatus;
use App\Enums\PaymentGateway;
use App\Events\OrderPlacedSMS;
use App\Models\OrderPosDetail;
use App\Events\OrderPlacedEmail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\PaginateRequest;
use App\Http\Requests\PosOrderRequest;
use App\Http\Requests\OrderTokenRequest;
use App\Libraries\QueryExceptionLibrary;
use App\Http\Requests\OrderStatusRequest;
use App\Http\Requests\OrderTrackerRequest;
use App\Events\OrderPlacedPushNotification;

class OrderService
{
    public object $order;
    protected array $orderFilter = [
        'order_serial_no',
        'user_id',
        'restaurant_id',
        'total',
        'order_type',
        'order_datetime',
        'payment_method',
        'payment_status',
        'status',
        'delivery_boy_id',
        'source'
    ];

    protected array $exceptFilter = [
        'excepts'
    ];

    public StatementCalculationService $statementCalculationService;

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

            return Order::with('transaction', 'orderItems')->where(['active' => Status::ACTIVE])->where(function ($query) use ($requests) {
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

                    if (in_array($key, $this->exceptFilter)) {
                        $explodes = explode('|', $request);
                        if (is_array($explodes)) {
                            foreach ($explodes as $explode) {
                                $query->where('order_type', '!=', $explode);
                            }
                        }
                    }
                }
            })->orderBy($orderColumn, $orderType)->$method(
                $methodValue
            );
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }

    /**
     * @throws Exception
     */
    public function userOrder(PaginateRequest $request, User $user)
    {
        try {
            $requests    = $request->all();
            $method      = $request->get('paginate', 0) == 1 ? 'paginate' : 'get';
            $methodValue = $request->get('paginate', 0) == 1 ? $request->get('per_page', 10) : '*';
            $orderColumn = $request->get('order_column') ?? 'id';
            $orderType   = $request->get('order_by') ?? 'desc';

            return Order::where(['active' => Status::ACTIVE])->where(['user_id' => $user->id])->where(function ($query) use ($requests) {
                foreach ($requests as $key => $request) {
                    if (in_array($key, $this->orderFilter)) {
                        $query->where($key, 'like', '%' . $request . '%');
                    }
                }
            })->orderBy($orderColumn, $orderType)->$method(
                $methodValue
            );
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }

    /**
     * @throws Exception
     */
    public function deliveredOrder(PaginateRequest $request, User $user)
    {
        try {
            $requests    = $request->all();
            $method      = $request->get('paginate', 0) == 1 ? 'paginate' : 'get';
            $methodValue = $request->get('paginate', 0) == 1 ? $request->get('per_page', 10) : '*';
            $orderColumn = $request->get('order_column') ?? 'id';
            $orderType   = $request->get('order_by') ?? 'desc';

            return Order::where(['active' => Status::ACTIVE])->where(['delivery_boy_id' => $user->id, 'status' => OrderStatus::DELIVERED])->where(
                function ($query) use ($requests) {
                    foreach ($requests as $key => $request) {
                        if (in_array($key, $this->orderFilter)) {
                            $query->where($key, 'like', '%' . $request . '%');
                        }
                    }
                }
            )->orderBy($orderColumn, $orderType)->$method(
                $methodValue
            );
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }

    /**
     * @throws Exception
     */
    public function posOrderStore(PosOrderRequest $request): object
    {
        try {
            DB::transaction(function () use ($request) {
                $orderSetup  = OrderSetup::select('food_preparation_time', 'schedule_order_slot_duration')->first();
                $currentTime = Carbon::now();
                $endTime     = $currentTime->copy()->addMinutes($orderSetup?->schedule_order_slot_duration ?? 30);
                $start       = $currentTime->format('H:i');
                $end         = $endTime->format('H:i');

                $this->order = Order::create(
                    $request->validated() + [
                        'user_id'          => 2,
                        'status'           => OrderStatus::ACCEPT,
                        'token'            => $request->token,
                        'payment_status'   => PaymentStatus::PAID,
                        'order_datetime'   => date('Y-m-d H:i:s'),
                        'payment_method'   => 1,
                        'preparation_time' => $orderSetup?->food_preparation_time ?? 30,
                        'is_advance_order' => IsAdvance::NO,
                        'order_type'       => OrderType::POS,
                        'source'           => Source::POS,
                        'delivery_time'    => "$start - $end",
                        'delivery_fee'     => 0,
                        'active'           => Ask::YES
                    ]
                );

                $i            = 0;
                $itemsArray   = [];
                $requestItems = json_decode($request->items);

                if (!blank($requestItems)) {
                    foreach ($requestItems as $item) {
                        $itemsArray[$i] = [
                            'order_id'             => $this->order->id,
                            'restaurant_id'        => $this->order->restaurant_id,
                            'item_id'              => $item->item_id,
                            'quantity'             => $item->quantity,
                            'discount'             => (float)$item->discount,
                            'tax_name'             => $item->tax_name,
                            'tax_rate'             => $item->tax_rate,
                            'tax_type'             => $item->tax_type,
                            'tax_amount'           => $item->tax_amount,
                            'price'                => $item->item_price,
                            'item_variations'      => json_encode($item->item_variations),
                            'item_extras'          => json_encode($item->item_extras),
                            'instruction'          => $item->instruction,
                            'item_variation_total' => $item->item_variation_total,
                            'item_extra_total'     => $item->item_extra_total,
                            'total_price'          => $item->total_price,
                            'status'               => Status::ACTIVE,
                            'kitchen_status'       => \App\Enums\KitchenItemStatus::PENDING,
                            'created_at'           => now(),
                            'updated_at'           => now()
                        ];
                        $i++;
                    }
                }

                if (!blank($itemsArray)) {
                    OrderItem::insert($itemsArray);
                }

                $this->order->order_serial_no = date('dmy') . $this->order->id;
                $this->order->total_tax       = $request->tax;
                $this->order->save();

                if ($this->order) {
                    OrderPosDetail::create([
                        'order_id'        => $this->order->id,
                        'payment_method'  => $request->payment_method,
                        'payment_note'    => $request->payment_note,
                        'received_amount' => $request->received_amount
                    ]);
                }
            });
            $order = $this->order->fresh();
            if ($order) {
                app(KitchenOrderService::class)->notifyNewKitchenOrder($order);
            }

            return $order;
        } catch (Exception $exception) {
            DB::rollBack();
            Log::info($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }

    /**
     * @throws Exception
     */
    public function show(Order $order, $auth = false): Order|array
    {
        try {
            if ($auth) {
                if ($order->user_id == Auth::user()->id) {
                    return $order->load('user', 'address', 'restaurant', 'deliveryBoy', 'coupon', 'transaction', 'orderItems', 'posDetail', 'media', 'diningTable', 'waiter');
                } else {
                    return [];
                }
            } else {
                return $order->load('user', 'address', 'restaurant', 'deliveryBoy', 'coupon', 'transaction', 'orderItems', 'posDetail', 'media', 'diningTable', 'waiter');
            }
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }

    /**
     * @throws Exception
     */
    public function fetchByOrderSerialNo(OrderTrackerRequest $orderTrackerRequest)
    {
        try {
            $order = Order::where(['active' => Status::ACTIVE])->where('order_serial_no', $orderTrackerRequest->order_id)->with(['orderItems', 'restaurant', 'user', 'address', 'deliveryBoy', 'coupon', 'transaction', 'posDetail', 'diningTable'])->first();
            if ($order) {
                return $order;
            } else {
                return [];
            }
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }

    /**
     * @throws Exception
     */
    public function orderDetails(User $user, Order $order): Order|array
    {
        try {
            if ($order->user_id == $user->id) {
                return $order->load('user', 'address', 'restaurant', 'deliveryBoy', 'coupon', 'transaction', 'orderItems', 'posDetail');
            } else {
                return [];
            }
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }

    /**
     * @throws Exception
     */
    public function deliveryBoyDeliveredOrderDetails(User $user, Order $order): Order|array
    {
        try {
            if ($order->delivery_boy_id == $user->id) {
                return $order->load('user', 'address', 'restaurant', 'deliveryBoy', 'coupon', 'transaction', 'orderItems', 'posDetail');
            } else {
                return [];
            }
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }

    /**
     * @throws Exception
     */
    public function changeStatus(Order $order, OrderStatusRequest $request): Order
    {
        try {
            $order->load(['orderItems', 'user', 'address', 'deliveryBoy', 'coupon', 'transaction', 'posDetail', 'diningTable', 'restaurant' => fn($query) => $query->with('cuisines')]);
            $status = [OrderStatus::ACCEPT, OrderStatus::REJECTED, OrderStatus::PREPARING, OrderStatus::PREPARED];
            if ($order->order_type == OrderType::TAKEAWAY || $order->order_type == OrderType::POS || $order->order_type == OrderType::DINING_TABLE) {
                $status[] = OrderStatus::DELIVERED;
            }

            if (in_array($request->status, $status)) {
                if ($request->status == OrderStatus::REJECTED) {
                    $request->validate([
                        'reason' => 'required|max:700',
                    ]);

                    if ($request->reason) {
                        $order->reason = $request->reason;
                    }

                    if ($order->transaction) {
                        app(PaymentService::class)->cashBack($order, 'credit', rand(111111111111111, 999999999999999));
                    }
                }

                if ($order->order_type == OrderType::TAKEAWAY && $request->status == OrderStatus::DELIVERED) {
                    if ($order->payment_method == PaymentGateway::CASH_ON_DELIVERY) {
                        $order->payment_status = PaymentStatus::PAID;
                        $this->statementCalculationService->restaurantTakeaway($order);
                    } else {
                        $this->statementCalculationService->restaurant($order);
                        $this->statementCalculationService->ownerRevenueFromRestaurant($order);
                    }
                } elseif ($order->order_type == OrderType::POS && $request->status == OrderStatus::DELIVERED) {
                    $this->statementCalculationService->restaurantPOS($order);
                } elseif ($order->order_type == OrderType::DINING_TABLE && $request->status == OrderStatus::DELIVERED) {
                    if ($order->payment_method == PaymentGateway::CASH_ON_DELIVERY) {
                        $order->payment_status = PaymentStatus::PAID;
                    }
                    $this->statementCalculationService->restaurantPOS($order);
                }

                $order->status = $request->status;
                $order->save();

                if (
                    $order->order_type == OrderType::DINING_TABLE
                    && in_array((int) $request->status, [OrderStatus::DELIVERED, OrderStatus::CANCELED, OrderStatus::REJECTED], true)
                    && $order->table_id
                ) {
                    app(WaiterOrderService::class)->releaseTableIfIdle((int) $order->table_id);
                }

                OrderPlacedEmail::dispatch(['order_id' => $order->id, 'status' => $request->status]);
                OrderPlacedSMS::dispatch(['order_id' => $order->id, 'status' => $request->status]);
                OrderPlacedPushNotification::dispatch(['order_id' => $order->id, 'status' => $request->status]);
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
    public function addToken(Order $order, OrderTokenRequest $request): Order
    {
        try {
            $order->load(['orderItems', 'restaurant', 'transaction']);
            $order->token = $request->token;
            $order->save();
            return $order;
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
                $order->address()?->delete();
                $order->coupon()?->delete();
                $order->orderItems()?->delete();
                $order->delete();
            });
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            DB::rollBack();
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }
}
