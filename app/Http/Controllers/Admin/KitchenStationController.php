<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\KitchenStationRequest;
use App\Http\Requests\PaginateRequest;
use App\Http\Resources\KitchenStationResource;
use App\Models\KitchenStation;
use App\Services\KitchenStationService;
use Exception;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class KitchenStationController extends AdminController implements HasMiddleware
{
    public function __construct(protected KitchenStationService $kitchenStationService)
    {
        parent::__construct();
    }

    public static function middleware(): array
    {
        return [
            new Middleware('permission:restaurant-settings', only: ['index', 'store', 'update', 'destroy']),
        ];
    }

    public function index(PaginateRequest $request)
    {
        try {
            return KitchenStationResource::collection($this->kitchenStationService->list($request));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function store(KitchenStationRequest $request)
    {
        try {
            return new KitchenStationResource($this->kitchenStationService->store($request));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function update(KitchenStationRequest $request, KitchenStation $kitchenStation)
    {
        try {
            return new KitchenStationResource($this->kitchenStationService->update($request, $kitchenStation));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function destroy(KitchenStation $kitchenStation)
    {
        try {
            $this->kitchenStationService->destroy($kitchenStation);

            return response('', 202);
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }
}
