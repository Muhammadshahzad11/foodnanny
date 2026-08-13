<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Requests\RestaurantByLatLongRadiusRequest;
use App\Http\Resources\FrontendRestaurantDetailsResource;
use App\Http\Resources\SimpleRestaurantResource;
use App\Models\Restaurant;
use App\Services\ZoneService;
use Exception;
use App\Services\RestaurantService;
use App\Http\Requests\PaginateRequest;
use App\Http\Resources\RestaurantResource;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class RestaurantController extends Controller
{
    public RestaurantService $restaurantService;

    public function __construct(RestaurantService $restaurantService)
    {
        $this->restaurantService = $restaurantService;
    }

    public function index(PaginateRequest $request): \Illuminate\Http\Response | \Illuminate\Http\Resources\Json\AnonymousResourceCollection | \Illuminate\Contracts\Foundation\Application | \Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return RestaurantResource::collection($this->restaurantService->list($request));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function show(Restaurant $restaurant, Request $request): FrontendRestaurantDetailsResource|\Illuminate\Foundation\Application|\Illuminate\Http\Response|\Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return new FrontendRestaurantDetailsResource($this->restaurantService->showWithDetails($restaurant, $request));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function restaurantByLatLongRadius(RestaurantByLatLongRadiusRequest $request): \Illuminate\Http\Response | \Illuminate\Http\Resources\Json\AnonymousResourceCollection | \Illuminate\Contracts\Foundation\Application | \Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            $results = $this->restaurantService->restaurantByLatLongRadius($request);

            $outOfService = false;
            $message      = null;
            $zoneService  = app(ZoneService::class);
            $area         = [
                'city'     => (string) $request->input('city', ''),
                'district' => (string) $request->input('district', ''),
                'state'    => (string) $request->input('state', ''),
            ];
            if ($zoneService->hasActiveZones() && !$zoneService->isServiceable((float) $request->latitude, (float) $request->longitude, $area)) {
                $outOfService = true;
                $message      = trans('all.message.not_available_in_your_location');
            }

            return SimpleRestaurantResource::collection($results)->additional([
                'out_of_service_area' => $outOfService,
                'message'             => $message,
            ]);
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function favorite(PaginateRequest $request): \Illuminate\Http\Response | \Illuminate\Http\Resources\Json\AnonymousResourceCollection | \Illuminate\Contracts\Foundation\Application | \Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return SimpleRestaurantResource::collection($this->restaurantService->favorite($request));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }
}
