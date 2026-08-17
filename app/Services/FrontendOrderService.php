<?php

namespace App\Services;


use Exception;
use App\Enums\Ask;
use App\Enums\Source;
use App\Enums\Status;
use App\Enums\OrderStatus;
use App\Enums\OrderType;
use App\Enums\PaymentGateway;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Restaurant;
use App\Models\FrontendOrder;
use App\Events\OrderPlacedSMS;
use App\Models\FrontendAddress;
use App\Events\OrderPlacedEmail;
use App\Models\FrontendOrderItem;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\OrderRequest;
use App\Models\FrontendOrderCoupon;
use Illuminate\Support\Facades\Log;
use App\Models\FrontendOrderAddress;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\PaginateRequest;
use App\Libraries\QueryExceptionLibrary;
use App\Http\Requests\OrderStatusRequest;
use App\Events\OrderPlacedPushNotification;
use App\Events\RestaurantOrderReceivedEmail;
use App\Events\RestaurantOrderReceivedSMS;
use App\Events\RestaurantOrderReceivedPushNotification;
use App\Services\KitchenOrderService;

class FrontendOrderService
{

    public object $frontendOrder;
    protected array $frontendOrderFilter = [
        'order_serial_no',
        'user_id',
        'total',
        'order_type',
        'order_datetime',
        'payment_method',
        'payment_status',
        'status',
        'delivery_boy_id'
    ];

    protected array $exceptFilter = [
        'excepts'
    ];

    /**
     * @throws Exception
     */
    public function myOrder(PaginateRequest $request)
    {
        try {
            $requests            = $request->all();
            $method              = $request->get('paginate', 0) == 1 ? 'paginate' : 'get';
            $methodValue         = $request->get('paginate', 0) == 1 ? $request->get('per_page', 10) : '*';
            $frontendOrderColumn = $request->get('order_column') ?? 'id';
            $frontendOrderType   = $request->get('order_by') ?? 'desc';

            return FrontendOrder::where(['active' => Status::ACTIVE])->where(function ($query) use ($requests) {
                $query->where('user_id', auth()->user()->id);
                foreach ($requests as $key => $request) {
                    if (in_array($key, $this->frontendOrderFilter)) {
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
                                $query->where('status', '!=', $explode);
                            }
                        }
                    }
                }
            })->orderBy($frontendOrderColumn, $frontendOrderType)->$method($methodValue);
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }

    /**
     * @throws Exception
     */
    public function myOrderStore(OrderRequest $request): object
    {
        try {
            DB::transaction(function () use ($request) {
                $oldOrder = FrontendOrder::where(['user_id' => Auth::user()->id, 'active' => Status::INACTIVE]);
                if (!blank($oldOrder->get())) {
                    $ids = $oldOrder->pluck('id');
                    FrontendOrderItem::whereIn('order_id', $ids)->where(['status' => Status::INACTIVE])->delete();
                    FrontendOrderAddress::whereIn('order_id', $ids)->where(['user_id' => Auth::user()->id])?->delete();
                    FrontendOrderCoupon::whereIn('order_id', $ids)->where(['user_id' => Auth::user()->id])?->delete();
                    $oldOrder->delete();
                }

                $restaurant          = Restaurant::with('orderSetup')->where(['id' => $request->restaurant_id])->first();
                $payload             = $request->validated();
                unset($payload['qr_token']);

                if ((int) ($payload['order_type'] ?? 0) !== \App\Enums\OrderType::DINING_TABLE) {
                    unset($payload['table_id']);
                } else {
                    $payload['delivery_fee'] = 0;
                    $payload['rider_tip']    = 0;
                    $payload['address_id']   = null;
                }

                // Apply zone-resolved fee/zone id from OrderRequest::after (request bag), not only validated().
                if ((int) ($payload['order_type'] ?? 0) === \App\Enums\OrderType::DELIVERY) {
                    if ($request->has('delivery_fee')) {
                        $payload['delivery_fee'] = $request->input('delivery_fee');
                    }
                    if ($request->has('extra_delivery_fee')) {
                        $payload['extra_delivery_fee'] = $request->input('extra_delivery_fee');
                    }
                    if ($request->has('total')) {
                        $payload['total'] = $request->input('total');
                    }
                    $payload['delivery_zone_id'] = $request->input('delivery_zone_id');
                    if ($request->has('zone_id')) {
                        $payload['zone_id'] = $request->input('zone_id');
                    } elseif ($restaurant?->zone_id) {
                        $payload['zone_id'] = $restaurant->zone_id;
                    }
                }

                $this->frontendOrder = FrontendOrder::create(
                    $payload + [
                        'user_id'          => Auth::user()->id,
                        'status'           => OrderStatus::PENDING,
                        'order_datetime'   => date('Y-m-d H:i:s'),
                        'preparation_time' => $restaurant->orderSetup?->food_preparation_time,
                    ]
                );

                $i            = 0;
                $itemsArray   = [];
                $requestItems = $this->decodeOrderItems($request->items);

                if (!blank($requestItems)) {
                    foreach ($requestItems as $item) {
                        $item = is_array($item) ? (object) $item : $item;
                        $itemsArray[$i] = [
                            'order_id'             => $this->frontendOrder->id,
                            'restaurant_id'        => $request->restaurant_id,
                            'item_id'              => $item->item_id ?? null,
                            'quantity'             => $item->quantity ?? 1,
                            'discount'             => (float) ($item->discount ?? 0),
                            'tax_name'             => $item->tax_name ?? null,
                            'tax_rate'             => $item->tax_rate ?? 0,
                            'tax_type'             => $item->tax_type ?? null,
                            'tax_amount'           => $item->tax_amount ?? 0,
                            'price'                => $item->item_price ?? $item->price ?? 0,
                            'item_variations'      => json_encode($item->item_variations ?? []),
                            'item_extras'          => json_encode($item->item_extras ?? []),
                            'instruction'          => $item->instruction ?? null,
                            'item_variation_total' => $item->item_variation_total ?? 0,
                            'item_extra_total'     => $item->item_extra_total ?? 0,
                            'total_price'          => $item->total_price ?? 0,
                            'status'               => Status::INACTIVE,
                            'created_at'           => now(),
                            'updated_at'           => now()
                        ];
                        $i++;
                    }
                }

                if (!blank($itemsArray)) {
                    FrontendOrderItem::insert($itemsArray);
                }

                $this->frontendOrder->order_serial_no = date('dmy') . $this->frontendOrder->id;
                $this->frontendOrder->total_tax       = $request->tax;
                $this->frontendOrder->save();

                if ($request->address_id) {
                    $address = FrontendAddress::find($request->address_id);
                    if ($address) {
                        FrontendOrderAddress::create([
                            'restaurant_id' => $request->restaurant_id,
                            'order_id'      => $this->frontendOrder->id,
                            'user_id'       => Auth::user()->id,
                            'label'         => $address->label,
                            'address'       => $address->address,
                            'apartment'     => $address->apartment,
                            'latitude'      => $address->latitude,
                            'longitude'     => $address->longitude
                        ]);
                    }
                }

                if ($request->coupon_id > 0) {
                    FrontendOrderCoupon::create([
                        'restaurant_id' => $request->restaurant_id,
                        'order_id'      => $this->frontendOrder->id,
                        'coupon_id'     => $request->coupon_id,
                        'user_id'       => Auth::user()->id,
                        'discount'      => $request->discount
                    ]);
                }
            });

            // COD (web, PWA, app, QR) never reliably hits the payment-success URL from the mobile/app checkout.
            // Activate immediately so restaurant Online Orders / kitchen / POS tables see it.
            // Web payment success stays idempotent if it also runs later.
            try {
                $this->activateAppCashOnDeliveryOrder($this->frontendOrder);
            } catch (\Throwable $e) {
                Log::info('COD activate after place: ' . $e->getMessage());
            }

            try {
                return $this->frontendOrder->fresh([
                    'orderItems', 'user', 'address', 'restaurant', 'deliveryBoy', 'coupon', 'transaction', 'diningTable'
                ]) ?? $this->frontendOrder;
            } catch (\Throwable $e) {
                Log::info('Order store fresh load: ' . $e->getMessage());
                return $this->frontendOrder;
            }
        } catch (Exception $exception) {
            DB::rollBack();
            Log::info($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }

    /**
     * Activate a placed WEB/APP + COD order (mirrors Cashondelivery payment success).
     */
    public function activateAppCashOnDeliveryOrder(FrontendOrder $frontendOrder): FrontendOrder
    {
        $source    = (int) $frontendOrder->source;
        $payMethod = (int) $frontendOrder->payment_method;

        if (!in_array($source, [Source::WEB, Source::APP], true) || $payMethod !== PaymentGateway::CASH_ON_DELIVERY) {
            return $frontendOrder;
        }

        if ((int) $frontendOrder->active === Ask::YES || (int) $frontendOrder->active === Status::ACTIVE) {
            return $frontendOrder;
        }

        return $this->activatePlacedCustomerOrder($frontendOrder);
    }

    /**
     * Mark order + items active, notify kitchen/restaurant/customer (same as payment.successful).
     */
    public function activatePlacedCustomerOrder(FrontendOrder $frontendOrder): FrontendOrder
    {
        DB::transaction(function () use ($frontendOrder) {
            $frontendOrder->refresh();
            if ((int) $frontendOrder->active === Ask::YES || (int) $frontendOrder->active === Status::ACTIVE) {
                return;
            }

            $frontendOrder->active = Ask::YES;
            $frontendOrder->save();

            OrderItem::where(['order_id' => $frontendOrder->id, 'status' => Status::INACTIVE])
                ->update(['status' => Status::ACTIVE]);
        });

        $frontendOrder->refresh();

        try {
            $order = Order::query()->find($frontendOrder->id);
            if ($order) {
                app(KitchenOrderService::class)->publishIfEligible($order);
            }
        } catch (\Throwable $e) {
            Log::info('App COD kitchen notify: ' . $e->getMessage());
        }

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
        } catch (\Throwable $e) {
            Log::info('App COD order notify: ' . $e->getMessage());
        }

        return $frontendOrder;
    }

    /**
     * Explicit confirm for mobile (COD). Idempotent if already active.
     *
     * @throws Exception
     */
    public function confirmCashOnDelivery(FrontendOrder $frontendOrder): FrontendOrder
    {
        if ((int) $frontendOrder->user_id !== (int) Auth::id()) {
            throw new Exception(trans('all.message.permission_denied'), 403);
        }

        if ((int) $frontendOrder->payment_method !== PaymentGateway::CASH_ON_DELIVERY) {
            throw new Exception(trans('all.message.something_wrong'), 422);
        }

        if ((int) $frontendOrder->active === Ask::YES || (int) $frontendOrder->active === Status::ACTIVE) {
            return $frontendOrder->load('orderItems', 'user', 'address', 'restaurant', 'deliveryBoy', 'coupon', 'transaction', 'diningTable');
        }

        return $this->activatePlacedCustomerOrder($frontendOrder)->load(
            'orderItems', 'user', 'address', 'restaurant', 'deliveryBoy', 'coupon', 'transaction', 'diningTable'
        );
    }

    /**
     * Accept items as JSON string (web) or array (Flutter / application/json).
     */
    private function decodeOrderItems(mixed $items): array
    {
        if (is_array($items)) {
            return array_values($items);
        }

        if (is_string($items) && $items !== '') {
            $decoded = json_decode($items);
            if (is_array($decoded)) {
                return $decoded;
            }
        }

        return [];
    }

    /**
     * @throws Exception
     */
    public function show(FrontendOrder $frontendOrder): FrontendOrder|array
    {
        try {
            if ($frontendOrder->user_id == Auth::user()->id) {
                if (in_array((int) $frontendOrder->status, [OrderStatus::PREPARED, OrderStatus::OUT_FOR_DELIVERY], true)
                    && (int) $frontendOrder->order_type === OrderType::DELIVERY) {
                    app(DeliveryOtpService::class)->ensure($frontendOrder);
                }
                return $frontendOrder->load('orderItems', 'user', 'address', 'restaurant', 'deliveryBoy', 'coupon', 'transaction', 'diningTable');
            }
            return [];
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }

    /**
     * @throws Exception
     */
    public function cancel(FrontendOrder $frontendOrder, OrderStatusRequest $request): FrontendOrder
    {
        try {
            if ((int) $frontendOrder->user_id !== (int) Auth::id()) {
                throw new Exception(trans('all.message.permission_denied'), 403);
            }

            if (!$frontendOrder->customerCancelSettingEnabled()) {
                throw new Exception(trans('all.message.order_cannot_be_canceled'), 422);
            }

            if (!$frontendOrder->customerCanCancel()) {
                throw new Exception(trans('all.message.order_cancel_window_expired'), 422);
            }

            $previousStatus = (int) $frontendOrder->status;

            DB::transaction(function () use ($frontendOrder, $request) {
                $frontendOrder->status = OrderStatus::CANCELED;
                if ($request->filled('reason')) {
                    $frontendOrder->reason = $request->reason;
                }
                $frontendOrder->save();
            });

            $frontendOrder->refresh();

            $order = Order::withoutGlobalScopes()->find($frontendOrder->id);
            if ($order) {
                if ((int) $order->table_id > 0) {
                    try {
                        app(WaiterOrderService::class)->releaseTableIfIdle((int) $order->table_id);
                    } catch (\Throwable $e) {
                        Log::info('Customer cancel table release: ' . $e->getMessage());
                    }
                }

                try {
                    app(RealtimePublisher::class)->kitchenOrder($order, 'cancel', $previousStatus);
                } catch (\Throwable $e) {
                    Log::info('Customer cancel realtime: ' . $e->getMessage());
                }
            }

            return $frontendOrder->load(
                'orderItems',
                'user',
                'address',
                'restaurant',
                'deliveryBoy',
                'coupon',
                'transaction',
                'diningTable'
            );
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception(
                $exception->getCode() === 422 || $exception->getCode() === 403
                    ? $exception->getMessage()
                    : QueryExceptionLibrary::message($exception),
                $exception->getCode() === 403 ? 403 : 422
            );
        }
    }
}
