<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\PaginateRequest;
use App\Http\Requests\RestaurantDeliveryZoneRequest;
use App\Http\Resources\RestaurantDeliveryZoneResource;
use App\Models\RestaurantDeliveryZone;
use App\Services\RestaurantDeliveryZoneService;
use Exception;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class RestaurantDeliveryZoneController extends AdminController implements HasMiddleware
{
    public RestaurantDeliveryZoneService $zoneService;

    public function __construct(RestaurantDeliveryZoneService $zoneService)
    {
        parent::__construct();
        $this->zoneService = $zoneService;
    }

    public static function middleware(): array
    {
        return [
            new Middleware('permission:delivery-zones', only: ['index', 'show', 'store', 'update', 'destroy', 'deactivate']),
        ];
    }

    public function index(PaginateRequest $request)
    {
        try {
            return RestaurantDeliveryZoneResource::collection($this->zoneService->list($request));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function show(RestaurantDeliveryZone $deliveryZone)
    {
        try {
            return new RestaurantDeliveryZoneResource($deliveryZone->load('restaurant:id,name,latitude,longitude'));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function store(RestaurantDeliveryZoneRequest $request)
    {
        try {
            return new RestaurantDeliveryZoneResource($this->zoneService->store($request));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function update(RestaurantDeliveryZoneRequest $request, RestaurantDeliveryZone $deliveryZone)
    {
        try {
            return new RestaurantDeliveryZoneResource($this->zoneService->update($request, $deliveryZone));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function destroy(RestaurantDeliveryZone $deliveryZone)
    {
        try {
            $this->zoneService->destroy($deliveryZone);
            return response('', 202);
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function deactivate(RestaurantDeliveryZone $deliveryZone)
    {
        try {
            return new RestaurantDeliveryZoneResource($this->zoneService->deactivate($deliveryZone));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }
}
