<?php

namespace App\Services;


use App\Models\Revenue;
use Exception;
use Carbon\Carbon;
use App\Models\Item;
use App\Models\User;
use App\Enums\Status;
use App\Models\Order;
use App\Enums\OrderType;
use App\Models\Statement;
use App\Enums\OrderStatus;
use App\Models\Collection;
use App\Models\Restaurant;
use App\Enums\PaymentStatus;
use App\Libraries\AppLibrary;
use App\Enums\Role as EnumRole;
use App\Http\Requests\DateRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Traits\DefaultAccessModelTrait;
use App\Libraries\QueryExceptionLibrary;

class DashboardService
{
    use DefaultAccessModelTrait;

    /**
     * @throws Exception
     */
    public function adminOverview(DateRequest $request): array
    {
        if (Auth::user()->myRole != EnumRole::ADMIN) {
            throw new Exception(trans('all.message.you_have_not_right_permission'), 422);
        }

        try {
            $order = new Order;
            if ($request->start_date && $request->end_date) {
                $startDate = Date('Y-m-d', strtotime($request->start_date));
                $endDate   = Date('Y-m-d', strtotime($request->end_date));
            } else {
                $startDate = Date('Y-m-d', strtotime(Carbon::now()->startOfMonth()));
                $endDate   = Date('Y-m-d', strtotime(Carbon::now()->endOfMonth()));
            }

            $adminOverviewArray['total_orders']      = $order->where(['active' => Status::ACTIVE])->whereDate('order_datetime', '>=', $startDate)->whereDate('order_datetime', '<=', $endDate)->count();
            $adminOverviewArray['sales_volume']      = $order->where(['active' => Status::ACTIVE])->whereDate('order_datetime', '>=', $startDate)->whereDate('order_datetime', '<=', $endDate)->sum('total');
            $adminOverviewArray['no_of_restaurants'] = Restaurant::count();
            $adminOverviewArray['commission']        = Revenue::whereDate('date', '>=', $startDate)->whereDate('date', '<=', $endDate)->sum('revenue_amount');
            $adminOverviewArray['delivered_orders']  = $order->delivered()->where(['active' => Status::ACTIVE])->whereDate('order_datetime', '>=', $startDate)->whereDate('order_datetime', '<=', $endDate)->count();
            $adminOverviewArray['canceled_orders']   = $order->canceled()->where(['active' => Status::ACTIVE])->whereDate('order_datetime', '>=', $startDate)->whereDate('order_datetime', '<=', $endDate)->count();
            $adminOverviewArray['returned_orders']   = $order->returned()->where(['active' => Status::ACTIVE])->whereDate('order_datetime', '>=', $startDate)->whereDate('order_datetime', '<=', $endDate)->count();
            $adminOverviewArray['rejected_orders']   = $order->rejected()->where(['active' => Status::ACTIVE])->whereDate('order_datetime', '>=', $startDate)->whereDate('order_datetime', '<=', $endDate)->count();
            return $adminOverviewArray;
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }

    /**
     * @throws Exception
     */
    public function adminSalesSummary(DateRequest $request): array
    {
        if (Auth::user()->myRole != EnumRole::ADMIN && Auth::user()->myRole != EnumRole::RESTAURANT_OWNER) {
            throw new Exception(trans('all.message.you_have_not_right_permission'), 422);
        }

        try {
            $order = new Order;
            if ($request->start_date && $request->end_date) {
                $startDate = Date('Y-m-d', strtotime($request->start_date));
                $endDate   = Date('Y-m-d', strtotime($request->end_date));
            } else {
                $startDate = Date('Y-m-d', strtotime(Carbon::now()->startOfMonth()));
                $endDate   = Date('Y-m-d', strtotime(Carbon::now()->endOfMonth()));
            }

            $date           = date_diff(date_create($startDate), date_create($endDate), false);
            $dateDiff       = (int)$date->format("%a");
            $sumOfTotalSale = AppLibrary::flatAmountFormat($order->where(['active' => Status::ACTIVE])->whereDate('order_datetime', '>=', $startDate)->whereDate('order_datetime', '<=', $endDate)->where('payment_status', PaymentStatus::PAID)->sum('total'));
            $totalSales     = $order->where(['active' => Status::ACTIVE])->whereDate('order_datetime', '>=', $startDate)->whereDate('order_datetime', '<=', $endDate)->where('payment_status', PaymentStatus::PAID)->get();

            $totalSalesArrays = [];
            foreach ($totalSales as $key => $totalSale) {
                $day = (int)date('j', strtotime($totalSale->order_datetime)) - 1;
                if (!isset($totalSalesArrays[$day])) {
                    $totalSalesArrays[$day] = AppLibrary::convertAmountFormat($totalSale->total);
                } else {
                    $totalSalesArrays[$day] += AppLibrary::convertAmountFormat($totalSale->total);
                }
            }

            $dateRangeArray = [];
            for ($currentDate = strtotime($startDate); $currentDate <= strtotime($endDate); $currentDate += (86400)) {
                $date             = date('Y-m-d', $currentDate);
                $dateRangeArray[] = $date;
            }

            $dateRangeValueArray = [];
            for ($i = 0; $i <= count($dateRangeArray) - 1; $i++) {
                $dateRangeValueArray[$i] = $totalSalesArrays[$i] ?? 0;
            }

            return [
                'total_sales'   => AppLibrary::currencyAmountFormat($sumOfTotalSale),
                'avg_per_day'   => AppLibrary::currencyAmountFormat($dateDiff > 0 ? ($sumOfTotalSale / $dateDiff) : $sumOfTotalSale),
                'per_day_sales' => $dateRangeValueArray
            ];
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }

    /**
     * @throws Exception
     */
    public function adminOrdersSummary(DateRequest $request): array
    {
        if (Auth::user()->myRole != EnumRole::ADMIN && Auth::user()->myRole != EnumRole::RESTAURANT_OWNER) {
            throw new Exception(trans('all.message.you_have_not_right_permission'), 422);
        }

        try {
            $order = new Order;
            if ($request->start_date && $request->end_date) {
                $startDate = Carbon::parse($request->start_date)->format('Y-m-d');
                $endDate   = Carbon::parse($request->end_date)->format('Y-m-d');
            } else {
                $startDate = Carbon::today()->subDays(6)->format('Y-m-d');
                $endDate   = Carbon::today()->format('Y-m-d');
            }

            $totalOrder = $order->where(['active' => Status::ACTIVE])->whereDate('order_datetime', '>=', $startDate)->whereDate('order_datetime', '<=', $endDate)->count();
            $delivered  = $order->where(['active' => Status::ACTIVE])->delivered()->whereDate('order_datetime', '>=', $startDate)->whereDate('order_datetime', '<=', $endDate)->count();
            $canceled   = $order->where(['active' => Status::ACTIVE])->canceled()->whereDate('order_datetime', '>=', $startDate)->whereDate('order_datetime', '<=', $endDate)->count();
            $returned   = $order->where(['active' => Status::ACTIVE])->returned()->whereDate('order_datetime', '>=', $startDate)->whereDate('order_datetime', '<=', $endDate)->count();
            $rejected   = $order->where(['active' => Status::ACTIVE])->rejected()->whereDate('order_datetime', '>=', $startDate)->whereDate('order_datetime', '<=', $endDate)->count();

            return [
                'delivered' => $this->adminOrderCounter($totalOrder, $delivered),
                'canceled'  => $this->adminOrderCounter($totalOrder, $canceled),
                'returned'  => $this->adminOrderCounter($totalOrder, $returned),
                'rejected'  => $this->adminOrderCounter($totalOrder, $rejected),
            ];
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }

    private function adminOrderCounter($totalOrder, $count): int
    {
        if ($totalOrder > 0) {
            return (int)round(($count * 100) / $totalOrder);
        }
        return 0;
    }

    /**
     * @throws Exception
     */
    public function adminRevenue(): array
    {
        if (Auth::user()->myRole != EnumRole::ADMIN) {
            throw new Exception(trans('all.message.you_have_not_right_permission'), 422);
        }

        try {
            $year            = Carbon::now()->year;
            $revenues        = DB::table('revenues')->selectRaw('MONTH(`date`) as month, SUM(order_amount) as total_sale, SUM(revenue_amount) as admin_commission')->whereYear('date', $year)->groupBy(DB::raw('MONTH(`date`)'))->orderBy(DB::raw('MONTH(`date`)'))->get();
            $totalSale       = array_fill(1, 12, 0);
            $adminCommission = array_fill(1, 12, 0);

            if (!blank($revenues)) {
                foreach ($revenues as $revenue) {
                    $totalSale[$revenue->month]       = round((float)$revenue->total_sale, AppLibrary::currencySettings()['decimals']);
                    $adminCommission[$revenue->month] = round((float)$revenue->admin_commission, AppLibrary::currencySettings()['decimals']);
                }
            }

            return [
                'total_sale'       => $totalSale,
                'admin_commission' => $adminCommission
            ];
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }

    /**
     * @throws Exception
     */
    public function adminTopCustomers()
    {
        if (Auth::user()->myRole != EnumRole::ADMIN) {
            throw new Exception(trans('all.message.you_have_not_right_permission'), 422);
        }
        try {
            return User::where('id', '!=', 2)->withCount(['orders' => function ($query) {
                $query->where(['active' => Status::ACTIVE]);
            }])->having('orders_count', '>=', 1)->orderBy('orders_count', 'desc')->limit(8)->get();
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }

    /**
     * @throws Exception
     */
    public function adminTopDeliveryBoys()
    {
        if (Auth::user()->myRole != EnumRole::ADMIN && Auth::user()->myRole <= 6) {
            throw new Exception(trans('all.message.you_have_not_right_permission'), 422);
        }

        try {
            return User::withCount('deliveryBoyOrders')->having('delivery_boy_orders_count', '>=', 1)->role(EnumRole::DELIVERY_BOY)->orderBy('delivery_boy_orders_count', 'desc')->limit(8)->get();
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }

    /**
     * @throws Exception
     */
    public function adminMostPopularRestaurants(): \Illuminate\Database\Eloquent\Collection
    {
        if (Auth::user()->myRole != EnumRole::ADMIN && Auth::user()->myRole <= 6) {
            throw new Exception(trans('all.message.you_have_not_right_permission'), 422);
        }

        try {
            return Restaurant::with('cuisinesWithCuisineRelation')->withCount(['orders' => function ($query) {
                $query->where(['active' => Status::ACTIVE]);
            }])->having('orders_count', '>=', 1)->orderBy('orders_count', 'desc')->limit(6)->get();
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }

    /**
     * @throws Exception
     */
    public function restaurantOwnerOverview(DateRequest $request): array
    {
        if (Auth::user()->myRole != EnumRole::RESTAURANT_OWNER && (Auth::user()->myRole != EnumRole::ADMIN && $this->restaurant() == 0)) {
            throw new Exception(trans('all.message.you_have_not_right_permission'), 422);
        }

        try {
            $order = new Order;
            if ($request->start_date && $request->end_date) {
                $startDate = Carbon::parse($request->start_date)->format('Y-m-d');
                $endDate   = Carbon::parse($request->end_date)->format('Y-m-d');
            } else {
                $startDate = Carbon::today()->format('Y-m-d');
                $endDate   = Carbon::today()->format('Y-m-d');
            }

            // POS orders are PAID at counter but stay ACCEPT until kitchen completes.
            // Sales must count all paid orders except canceled/rejected/returned.
            $salesQuery = $order->where(['active' => Status::ACTIVE])
                ->where('payment_status', PaymentStatus::PAID)
                ->whereNotIn('status', [
                    OrderStatus::CANCELED,
                    OrderStatus::REJECTED,
                    OrderStatus::RETURNED,
                ]);

            $restaurantOwnerOverviewArray['total_sales'] = AppLibrary::currencyAmountFormat((clone $salesQuery)->sum('total'));
            $restaurantOwnerOverviewArray['filter_total_sales'] = AppLibrary::currencyAmountFormat(
                (clone $salesQuery)
                    ->whereDate('order_datetime', '>=', $startDate)
                    ->whereDate('order_datetime', '<=', $endDate)
                    ->sum('total')
            );
            $restaurantOwnerOverviewArray['total_orders']                  = $order->where(['active' => Status::ACTIVE])->count();
            $restaurantOwnerOverviewArray['available_balance']             = Restaurant::find($this->restaurant());
            $restaurantOwnerOverviewArray['total_menu_items']              = Item::count();
            $restaurantOwnerOverviewArray['filter_total_orders']           = $order->where(['active' => Status::ACTIVE])->whereDate('order_datetime', '>=', $startDate)->whereDate('order_datetime', '<=', $endDate)->count();
            $restaurantOwnerOverviewArray['filter_total_pending']          = $order->pending()->where(['active' => Status::ACTIVE])->whereDate('order_datetime', '>=', $startDate)->whereDate('order_datetime', '<=', $endDate)->count();
            $restaurantOwnerOverviewArray['filter_total_preparing']        = $order->preparing()->where(['active' => Status::ACTIVE])->whereDate('order_datetime', '>=', $startDate)->whereDate('order_datetime', '<=', $endDate)->count();
            $restaurantOwnerOverviewArray['filter_total_out_for_delivery'] = $order->outForDelivery()->where(['active' => Status::ACTIVE])->whereDate('order_datetime', '>=', $startDate)->whereDate('order_datetime', '<=', $endDate)->count();
            $restaurantOwnerOverviewArray['filter_total_delivered']        = $order->delivered()->where(['active' => Status::ACTIVE])->whereDate('order_datetime', '>=', $startDate)->whereDate('order_datetime', '<=', $endDate)->count();
            $restaurantOwnerOverviewArray['filter_total_canceled']         = $order->canceled()->where(['active' => Status::ACTIVE])->whereDate('order_datetime', '>=', $startDate)->whereDate('order_datetime', '<=', $endDate)->count();
            $restaurantOwnerOverviewArray['filter_total_returned']         = $order->returned()->where(['active' => Status::ACTIVE])->whereDate('order_datetime', '>=', $startDate)->whereDate('order_datetime', '<=', $endDate)->count();
            $restaurantOwnerOverviewArray['filter_total_rejected']         = $order->rejected()->where(['active' => Status::ACTIVE])->whereDate('order_datetime', '>=', $startDate)->whereDate('order_datetime', '<=', $endDate)->count();
            // POS accepted/prepared counts (kitchen pipeline)
            $restaurantOwnerOverviewArray['filter_total_accepted']         = $order->where(['active' => Status::ACTIVE, 'status' => OrderStatus::ACCEPT])->whereDate('order_datetime', '>=', $startDate)->whereDate('order_datetime', '<=', $endDate)->count();
            $restaurantOwnerOverviewArray['filter_total_prepared']         = $order->where(['active' => Status::ACTIVE, 'status' => OrderStatus::PREPARED])->whereDate('order_datetime', '>=', $startDate)->whereDate('order_datetime', '<=', $endDate)->count();

            return $restaurantOwnerOverviewArray;
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }

    /**
     * @throws Exception
     */
    public function restaurantOwnerCustomerStats(DateRequest $request): array
    {
        if (Auth::user()->myRole != EnumRole::RESTAURANT_OWNER && (Auth::user()->myRole != EnumRole::ADMIN && $this->restaurant() == 0)) {
            throw new Exception(trans('all.message.you_have_not_right_permission'), 422);
        }

        try {
            if ($request->start_date && $request->end_date) {
                $startDate = Date('Y-m-d', strtotime($request->start_date));
                $endDate   = Date('Y-m-d', strtotime($request->end_date));
            } else {
                $startDate = Date('Y-m-d', strtotime(Carbon::now()->startOfMonth()));
                $endDate   = Date('Y-m-d', strtotime(Carbon::now()->endOfMonth()));
            }

            $totalCustomerArray = [];
            $orders             = Order::where(['active' => Status::ACTIVE])->whereDate('order_datetime', '>=', $startDate)->whereDate('order_datetime', '<=', $endDate)->get();
            $timeSlots          = collect(range(6, 23))->map(function ($hour) {
                return sprintf('%02d:00', $hour);
            });

            foreach ($timeSlots as $slot) {
                $start = Carbon::parse($slot);
                $end   = (clone $start)->addMinutes(59);
                $count = $orders->filter(function ($order) use ($start, $end) {
                    $orderTime = Carbon::parse($order->order_datetime)->format('H:i');
                    return $orderTime >= $start->format('H:i') && $orderTime <= $end->format('H:i');
                })->count();

                $totalCustomerArray[] = $count;
            }

            return [
                'total_customers' => $totalCustomerArray,
                'times'           => $timeSlots->toArray(),
            ];
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }

    /**
     * @throws Exception
     */
    public function restaurantOwnerMostPopularItems(): \Illuminate\Database\Eloquent\Collection
    {
        if (Auth::user()->myRole != EnumRole::RESTAURANT_OWNER && (Auth::user()->myRole != EnumRole::ADMIN && $this->restaurant() == 0)) {
            throw new Exception(trans('all.message.you_have_not_right_permission'), 422);
        }

        try {
            return Item::with('media', 'category')->withCount('orders')->where(['status' => Status::ACTIVE])->orderBy('orders_count', 'desc')->limit(6)->get();
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }

    /**
     * @throws Exception
     */
    public function deliveryBoyOverview(DateRequest $request): array
    {
        if (Auth::user()->myRole != EnumRole::DELIVERY_BOY) {
            throw new Exception(trans('all.message.you_have_not_right_permission'), 422);
        }

        try {
            $order = new Order;
            if ($request->start_date && $request->end_date) {
                $startDate = Carbon::parse($request->start_date)->format('Y-m-d');
                $endDate   = Carbon::parse($request->end_date)->format('Y-m-d');
            } else {
                $startDate = Carbon::today()->format('Y-m-d');
                $endDate   = Carbon::today()->format('Y-m-d');
            }

            $deliveryBoyOverviewArray['total_earnings']        = $order->where(['active' => Status::ACTIVE])->whereDate('order_datetime', '>=', $startDate)->whereDate('order_datetime', '<=', $endDate)->where(['delivery_boy_id' => Auth::User()->id, 'order_type' => OrderType::DELIVERY])->where('status', OrderStatus::DELIVERED)->select(DB::raw('SUM(COALESCE(delivery_fee,0) + COALESCE(rider_tip,0) + COALESCE(extra_delivery_fee,0)) as total_earnings'))->value('total_earnings');
            $deliveryBoyOverviewArray['total_accepted_orders'] = $order->where(['active' => Status::ACTIVE])->whereDate('order_datetime', '>=', $startDate)->whereDate('order_datetime', '<=', $endDate)->where(['delivery_boy_id' => Auth::User()->id, 'order_type' => OrderType::DELIVERY])->where('status', '!=', OrderStatus::CANCELED)->where('status', '!=', OrderStatus::REJECTED)->where('status', '!=', OrderStatus::RETURNED)->where('status', '!=', OrderStatus::DELIVERED)->count();
            $deliveryBoyOverviewArray['completed_delivery']    = $order->where(['active' => Status::ACTIVE])->whereDate('order_datetime', '>=', $startDate)->whereDate('order_datetime', '<=', $endDate)->where(['delivery_boy_id' => Auth::User()->id, 'order_type' => OrderType::DELIVERY])->where('status', OrderStatus::DELIVERED)->count();
            $deliveryBoyOverviewArray['return_delivery']       = $order->where(['active' => Status::ACTIVE])->whereDate('order_datetime', '>=', $startDate)->whereDate('order_datetime', '<=', $endDate)->where(['delivery_boy_id' => Auth::User()->id, 'order_type' => OrderType::DELIVERY])->where('status', OrderStatus::RETURNED)->count();

            return $deliveryBoyOverviewArray;
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }

    /**
     * @throws Exception
     */
    public function deliveryBoyPayoutBalance(): array
    {
        if (Auth::user()->myRole != EnumRole::DELIVERY_BOY) {
            throw new Exception(trans('all.message.you_have_not_right_permission'), 422);
        }

        try {
            $deliveryBoyOverviewArray['total_payout_balance']      = Auth::user()?->balance;
            $deliveryBoyOverviewArray['today_payout_balance']      = Statement::whereBetween('date', [
                Carbon::today()->startOfDay(),
                Carbon::today()->endOfDay()
            ])->where(['model_type' => User::class, 'model_id' => Auth::user()->id])->sum('amount');
            $deliveryBoyOverviewArray['this_week_payout_balance']  = Statement::whereBetween('date', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])->where(['model_type' => User::class, 'model_id' => Auth::user()->id])->sum('amount');
            $deliveryBoyOverviewArray['this_month_payout_balance'] = Statement::whereBetween('date', [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()])->where(['model_type' => User::class, 'model_id' => Auth::user()->id])->sum('amount');

            return $deliveryBoyOverviewArray;
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }

    /**
     * @throws Exception
     */
    public function deliveryBoyCollectionBalance(): array
    {
        if (Auth::user()->myRole != EnumRole::DELIVERY_BOY) {
            throw new Exception(trans('all.message.you_have_not_right_permission'), 422);
        }

        try {
            $deliveryBoyOverviewArray['total_collection_balance']      = Auth::user()?->collection;
            $deliveryBoyOverviewArray['today_collection_balance']      = Collection::whereBetween('date', [
                Carbon::today()->startOfDay(),
                Carbon::today()->endOfDay()
            ])->where(['source_user_id' => Auth::user()->id])->sum('amount');
            $deliveryBoyOverviewArray['last_week_collection_balance']  = Collection::whereBetween('date', [Carbon::now()->subWeek()->startOfWeek(), Carbon::now()->subWeek()->endOfWeek()])->where(['source_user_id' => Auth::user()->id])->sum('amount');
            $deliveryBoyOverviewArray['this_month_collection_balance'] = Collection::whereBetween('date', [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()])->where(['source_user_id' => Auth::user()->id])->sum('amount');

            return $deliveryBoyOverviewArray;
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }

    /**
     * @throws Exception
     */
    public function deliveryBoyActiveOrders(): \Illuminate\Database\Eloquent\Collection
    {
        if (Auth::user()->myRole != EnumRole::DELIVERY_BOY) {
            throw new Exception(trans('all.message.you_have_not_right_permission'), 422);
        }

        try {
            return Order::with('restaurant')->where(['active' => Status::ACTIVE])->where(['delivery_boy_id' => Auth::user()->id, 'order_type' => OrderType::DELIVERY])->where(function ($query) {
                $query->where('status', '!=', OrderStatus::DELIVERED)->where('status', '!=', OrderStatus::RETURNED);
            })->orderBy('id', 'desc')->get();
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }


    /**
     * @throws Exception
     */
    public function otherOverview(DateRequest $request): array
    {
        if (Auth::user()->myRole == EnumRole::ADMIN || Auth::user()->myRole == EnumRole::RESTAURANT_OWNER || Auth::user()->myRole == EnumRole::DELIVERY_BOY || Auth::user()->myRole == EnumRole::CUSTOMER) {
            throw new Exception(trans('all.message.you_have_not_right_permission'), 422);
        }

        try {
            $order = new Order;
            if ($request->start_date && $request->end_date) {
                $startDate = Date('Y-m-d', strtotime($request->start_date));
                $endDate   = Date('Y-m-d', strtotime($request->end_date));
            } else {
                $startDate = Date('Y-m-d', strtotime(Carbon::now()->startOfMonth()));
                $endDate   = Date('Y-m-d', strtotime(Carbon::now()->endOfMonth()));
            }

            $adminOverviewArray['delivered_orders'] = $order->delivered()->where(['active' => Status::ACTIVE])->whereDate('order_datetime', '>=', $startDate)->whereDate('order_datetime', '<=', $endDate)->count();
            $adminOverviewArray['canceled_orders']  = $order->canceled()->where(['active' => Status::ACTIVE])->whereDate('order_datetime', '>=', $startDate)->whereDate('order_datetime', '<=', $endDate)->count();
            $adminOverviewArray['returned_orders']  = $order->returned()->where(['active' => Status::ACTIVE])->whereDate('order_datetime', '>=', $startDate)->whereDate('order_datetime', '<=', $endDate)->count();
            $adminOverviewArray['rejected_orders']  = $order->rejected()->where(['active' => Status::ACTIVE])->whereDate('order_datetime', '>=', $startDate)->whereDate('order_datetime', '<=', $endDate)->count();
            return $adminOverviewArray;
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }
}
