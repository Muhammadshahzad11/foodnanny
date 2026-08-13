<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\PaginateRequest;
use App\Http\Requests\ZoneRequest;
use App\Http\Resources\ZoneResource;
use App\Models\Zone;
use App\Services\ZoneService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class ZoneController extends AdminController implements HasMiddleware
{
    public function __construct(protected ZoneService $zoneService)
    {
        parent::__construct();
    }

    public static function middleware(): array
    {
        return [
            new Middleware('permission:delivery-zones', only: [
                'index', 'show', 'store', 'update', 'destroy', 'deactivate',
                'assignRestaurants', 'assignAdmin', 'assignDeliveryBoys',
            ]),
        ];
    }

    public function index(PaginateRequest $request)
    {
        try {
            return ZoneResource::collection($this->zoneService->list($request));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function show(Zone $zone)
    {
        try {
            return new ZoneResource(
                $zone->loadCount('restaurants')->load([
                    'admins' => fn ($q) => $q->role(\App\Enums\Role::ZONE_ADMIN),
                    'restaurants' => fn ($q) => $q->withoutGlobalScopes()->select('id', 'name', 'zone_id'),
                    'deliveryBoys' => fn ($q) => $q->withoutGlobalScopes()->role(\App\Enums\Role::DELIVERY_BOY),
                ])
            );
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function store(ZoneRequest $request)
    {
        try {
            return new ZoneResource($this->zoneService->store($request));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function update(ZoneRequest $request, Zone $zone)
    {
        try {
            return new ZoneResource($this->zoneService->update($request, $zone));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function destroy(Zone $zone)
    {
        try {
            $this->zoneService->destroy($zone);
            return response('', 202);
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function deactivate(Zone $zone)
    {
        try {
            return new ZoneResource($this->zoneService->deactivate($zone));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function assignRestaurants(Request $request, Zone $zone)
    {
        try {
            $ids = $request->input('restaurant_ids', []);
            if (!is_array($ids)) {
                $ids = [];
            }
            return new ZoneResource($this->zoneService->assignRestaurants($zone, $ids));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function assignAdmin(Request $request, Zone $zone)
    {
        try {
            $request->validate([
                'user_id'  => ['nullable', 'integer', 'exists:users,id'],
                'name'     => ['required_without:user_id', 'nullable', 'string', 'max:190'],
                'email'    => ['required_without:user_id', 'nullable', 'email', 'unique:users,email'],
                'phone'    => ['nullable', 'string', 'max:20'],
                'password' => ['required_without:user_id', 'nullable', 'string', 'min:6'],
                'country_code' => ['nullable', 'string', 'max:10'],
            ]);

            return new ZoneResource($this->zoneService->assignAdmin($zone, $request->all()));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function assignDeliveryBoys(Request $request, Zone $zone)
    {
        try {
            $ids = $request->input('user_ids', []);
            if (!is_array($ids)) {
                $ids = [];
            }
            return new ZoneResource($this->zoneService->assignDeliveryBoys($zone, $ids));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }
}
