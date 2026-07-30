<?php

namespace App\Http\Controllers\Admin;

use App\Http\Resources\RevenueChartResource;
use Exception;
use App\Services\DashboardService;
use App\Http\Requests\DateRequest;
use App\Http\Resources\CollectionBalanceResource;
use App\Http\Resources\DashboardActiveOrdersResource;
use App\Http\Resources\DeliveryBoyOverviewResource;
use App\Http\Resources\MostPopularItemResource;
use App\Http\Resources\OtherOverviewResource;
use App\Http\Resources\PayoutBalanceResource;
use App\Http\Resources\OrderSummaryResource;
use App\Http\Resources\SalesSummaryResource;
use App\Http\Resources\TopCustomersResource;
use App\Http\Resources\AdminOverviewResource;
use App\Http\Resources\CustomerStatsResource;
use Illuminate\Routing\Controllers\Middleware;
use App\Http\Resources\TopDeliveryBoysResource;
use App\Http\Resources\MostPopularRestaurantResource;
use App\Http\Resources\RestaurantOwnerOverviewResource;

class DashboardController extends AdminController
{
    private DashboardService $dashboardService;

    public function __construct(DashboardService $dashboardService)
    {
        parent::__construct();
        $this->dashboardService = $dashboardService;
    }

    public static function middleware(): array
    {
        return [
            new Middleware('permission:dashboard', only: ['adminOverview'])
        ];
    }

    public function adminOverview(DateRequest $request): \Illuminate\Foundation\Application|\Illuminate\Http\Response|AdminOverviewResource|\Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return new AdminOverviewResource($this->dashboardService->adminOverview($request));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function adminSalesSummary(DateRequest $request): \Illuminate\Foundation\Application|\Illuminate\Http\Response|SalesSummaryResource|\Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return new SalesSummaryResource($this->dashboardService->adminSalesSummary($request));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function adminOrdersSummary(DateRequest $request): \Illuminate\Foundation\Application|\Illuminate\Http\Response|OrderSummaryResource|\Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return new OrderSummaryResource($this->dashboardService->adminOrdersSummary($request));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function adminRevenue(): \Illuminate\Foundation\Application|\Illuminate\Http\Response|RevenueChartResource|\Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return new RevenueChartResource($this->dashboardService->adminRevenue());
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function adminTopCustomers(): \Illuminate\Foundation\Application|\Illuminate\Http\Response|\Illuminate\Http\Resources\Json\AnonymousResourceCollection|\Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return TopCustomersResource::collection($this->dashboardService->adminTopCustomers());
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function adminTopDeliveryBoys(): \Illuminate\Foundation\Application|\Illuminate\Http\Response|\Illuminate\Http\Resources\Json\AnonymousResourceCollection|\Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return TopDeliveryBoysResource::collection($this->dashboardService->adminTopDeliveryBoys());
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function adminMostPopularRestaurants(): \Illuminate\Foundation\Application|\Illuminate\Http\Response|\Illuminate\Http\Resources\Json\AnonymousResourceCollection|\Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return MostPopularRestaurantResource::collection($this->dashboardService->adminMostPopularRestaurants());
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function restaurantOwnerOverview(DateRequest $request): \Illuminate\Foundation\Application|\Illuminate\Http\Response|RestaurantOwnerOverviewResource|\Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return new RestaurantOwnerOverviewResource($this->dashboardService->restaurantOwnerOverview($request));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function restaurantOwnerCustomerStats(DateRequest $request): \Illuminate\Foundation\Application|\Illuminate\Http\Response|CustomerStatsResource|\Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return new CustomerStatsResource($this->dashboardService->restaurantOwnerCustomerStats($request));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function restaurantOwnerMostPopularItems(): \Illuminate\Foundation\Application|\Illuminate\Http\Response|\Illuminate\Http\Resources\Json\AnonymousResourceCollection|\Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return MostPopularItemResource::collection($this->dashboardService->restaurantOwnerMostPopularItems());
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function deliveryBoyOverview(DateRequest $request): DeliveryBoyOverviewResource|\Illuminate\Foundation\Application|\Illuminate\Http\Response|\Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return new DeliveryBoyOverviewResource($this->dashboardService->deliveryBoyOverview($request));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function deliveryBoyPayoutBalance(): \Illuminate\Foundation\Application|\Illuminate\Http\Response|\Illuminate\Contracts\Routing\ResponseFactory|PayoutBalanceResource
    {
        try {
            return new PayoutBalanceResource($this->dashboardService->deliveryBoyPayoutBalance());
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function deliveryBoyCollectionBalance(): CollectionBalanceResource|\Illuminate\Foundation\Application|\Illuminate\Http\Response|\Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return new CollectionBalanceResource($this->dashboardService->deliveryBoyCollectionBalance());
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function deliveryBoyActiveOrders(): \Illuminate\Foundation\Application|\Illuminate\Http\Response|\Illuminate\Http\Resources\Json\AnonymousResourceCollection|\Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return DashboardActiveOrdersResource::collection($this->dashboardService->deliveryBoyActiveOrders());
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function otherOverview(DateRequest $request): \Illuminate\Foundation\Application|\Illuminate\Http\Response|OtherOverviewResource|\Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return new OtherOverviewResource($this->dashboardService->otherOverview($request));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }
}
