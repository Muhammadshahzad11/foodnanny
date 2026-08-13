<?php

namespace App\Services;

use App\Enums\DeliveryChargeType;
use App\Enums\OrderStatus;
use App\Enums\Status;
use App\Http\Requests\PaginateRequest;
use App\Http\Requests\RestaurantDeliveryZoneRequest;
use App\Libraries\GeoPolygon;
use App\Libraries\QueryExceptionLibrary;
use App\Models\Order;
use App\Models\Restaurant;
use App\Models\RestaurantDeliveryZone;
use App\Traits\DefaultAccessModelTrait;
use Dipokhalder\Settings\Facades\Settings;
use Exception;
use Illuminate\Support\Facades\Log;

class RestaurantDeliveryZoneService
{
    use DefaultAccessModelTrait;

    protected array $filter = [
        'name',
        'display_name',
        'status',
        'charge_type',
        'restaurant_id',
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
            $orderType   = $request->get('order_type') ?? 'desc';

            return RestaurantDeliveryZone::query()
                ->with('restaurant:id,name,latitude,longitude')
                ->where(function ($query) use ($requests) {
                    foreach ($requests as $key => $value) {
                        if (!in_array($key, $this->filter, true) || $value === '' || $value === null) {
                            continue;
                        }
                        if (in_array($key, ['status', 'charge_type', 'restaurant_id'], true)) {
                            $query->where($key, (int) $value);
                        } elseif ($key === 'name' || $key === 'display_name') {
                            $query->where($key, 'like', '%' . $value . '%');
                        } else {
                            $query->where($key, 'like', '%' . $value . '%');
                        }
                    }

                    if (!empty($requests['search'])) {
                        $search = $requests['search'];
                        $query->where(function ($q) use ($search) {
                            $q->where('name', 'like', '%' . $search . '%')
                                ->orWhere('display_name', 'like', '%' . $search . '%')
                                ->orWhereHas('restaurant', function ($restaurantQuery) use ($search) {
                                    $restaurantQuery->where('name', 'like', '%' . $search . '%');
                                });
                        });
                    }

                    if (!empty($requests['created_from'])) {
                        $query->whereDate('created_at', '>=', $requests['created_from']);
                    }
                    if (!empty($requests['created_to'])) {
                        $query->whereDate('created_at', '<=', $requests['created_to']);
                    }
                    if (!empty($requests['updated_from'])) {
                        $query->whereDate('updated_at', '>=', $requests['updated_from']);
                    }
                    if (!empty($requests['updated_to'])) {
                        $query->whereDate('updated_at', '<=', $requests['updated_to']);
                    }
                })
                ->orderBy($orderColumn, $orderType)
                ->$method($methodValue);
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }

    /**
     * @throws Exception
     */
    public function store(RestaurantDeliveryZoneRequest $request): RestaurantDeliveryZone
    {
        try {
            $data    = $request->validated();
            $polygon = GeoPolygon::normalize($data['polygon']);

            return RestaurantDeliveryZone::create([
                'restaurant_id'              => (int) $data['restaurant_id'],
                'name'                       => $data['name'],
                'display_name'               => $data['display_name'],
                'polygon'                    => $polygon,
                'status'                     => (int) ($data['status'] ?? Status::ACTIVE),
                'charge_type'                => (int) ($data['charge_type'] ?? DeliveryChargeType::PER_KM),
                'min_delivery_charge'        => $data['min_delivery_charge'] ?? null,
                'max_delivery_charge'        => $data['max_delivery_charge'] ?? null,
                'charge_per_km'              => $data['charge_per_km'] ?? null,
                'max_cod_amount'             => $data['max_cod_amount'] ?? null,
                'additional_delivery_charge' => $data['additional_delivery_charge'] ?? 0,
            ])->load('restaurant:id,name,latitude,longitude');
        } catch (\InvalidArgumentException $exception) {
            Log::warning('Delivery zone create validation failed', ['error' => $exception->getMessage()]);
            throw new Exception($exception->getMessage(), 422);
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception(
                $exception->getCode() === 422 ? $exception->getMessage() : QueryExceptionLibrary::message($exception),
                422
            );
        }
    }

    /**
     * @throws Exception
     */
    public function update(RestaurantDeliveryZoneRequest $request, RestaurantDeliveryZone $zone): RestaurantDeliveryZone
    {
        try {
            $data    = $request->validated();
            $polygon = GeoPolygon::normalize($data['polygon']);

            $zone->update([
                'name'                       => $data['name'],
                'display_name'               => $data['display_name'],
                'polygon'                    => $polygon,
                'status'                     => (int) ($data['status'] ?? $zone->status),
                'charge_type'                => (int) ($data['charge_type'] ?? $zone->charge_type),
                'min_delivery_charge'        => $data['min_delivery_charge'] ?? null,
                'max_delivery_charge'        => $data['max_delivery_charge'] ?? null,
                'charge_per_km'              => $data['charge_per_km'] ?? null,
                'max_cod_amount'             => $data['max_cod_amount'] ?? null,
                'additional_delivery_charge' => $data['additional_delivery_charge'] ?? 0,
            ]);

            return $zone->fresh(['restaurant:id,name,latitude,longitude']);
        } catch (\InvalidArgumentException $exception) {
            Log::warning('Delivery zone update validation failed', ['zone_id' => $zone->id, 'error' => $exception->getMessage()]);
            throw new Exception($exception->getMessage(), 422);
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception(
                $exception->getCode() === 422 ? $exception->getMessage() : QueryExceptionLibrary::message($exception),
                422
            );
        }
    }

    /**
     * @throws Exception
     */
    public function destroy(RestaurantDeliveryZone $zone): void
    {
        try {
            if ($this->hasActiveOrders($zone)) {
                throw new Exception(trans('all.message.zone_in_use_deactivate'), 422);
            }
            $zone->delete();
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception(
                $exception->getCode() === 422 ? $exception->getMessage() : QueryExceptionLibrary::message($exception),
                422
            );
        }
    }

    /**
     * Soft-deactivate instead of delete when zone is referenced.
     *
     * @throws Exception
     */
    public function deactivate(RestaurantDeliveryZone $zone): RestaurantDeliveryZone
    {
        try {
            $zone->update(['status' => Status::INACTIVE]);
            return $zone->fresh(['restaurant:id,name,latitude,longitude']);
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }

    public function hasActiveOrders(RestaurantDeliveryZone $zone): bool
    {
        if (!\Illuminate\Support\Facades\Schema::hasColumn('orders', 'delivery_zone_id')) {
            return false;
        }

        return Order::withoutGlobalScopes()
            ->where('delivery_zone_id', $zone->id)
            ->whereIn('status', [
                OrderStatus::PENDING,
                OrderStatus::ACCEPT,
                OrderStatus::PREPARING,
                OrderStatus::PREPARED,
                OrderStatus::OUT_FOR_DELIVERY,
            ])
            ->exists();
    }

    /**
     * Find first matching active zone for restaurant + coordinates.
     * Priority: lowest id among matches (documented overlap rule).
     */
    public function findMatchingZone(int $restaurantId, float $lat, float $lng): ?RestaurantDeliveryZone
    {
        $zones = RestaurantDeliveryZone::withoutGlobalScopes()
            ->where('restaurant_id', $restaurantId)
            ->where('status', Status::ACTIVE)
            ->orderBy('id')
            ->get();

        if ($zones->isEmpty()) {
            Log::info('No active delivery zones for restaurant', ['restaurant_id' => $restaurantId]);
            return null;
        }

        foreach ($zones as $zone) {
            $polygon = is_array($zone->polygon) ? $zone->polygon : [];
            if (GeoPolygon::contains($lat, $lng, $polygon)) {
                return $zone;
            }
        }

        Log::info('Customer outside all restaurant delivery zones', [
            'restaurant_id' => $restaurantId,
        ]);

        return null;
    }

    /**
     * Whether restaurant has at least one active delivery zone.
     */
    public function restaurantHasActiveZones(int $restaurantId): bool
    {
        return RestaurantDeliveryZone::withoutGlobalScopes()
            ->where('restaurant_id', $restaurantId)
            ->where('status', Status::ACTIVE)
            ->exists();
    }

    /**
     * Calculate delivery fee for a restaurant + customer location.
     *
     * @return array{fee: float, zone: ?RestaurantDeliveryZone, available: bool, reason: ?string}
     */
    public function resolveDelivery(int $restaurantId, float $lat, float $lng): array
    {
        $restaurant = Restaurant::query()->find($restaurantId);
        if (!$restaurant) {
            return ['fee' => 0.0, 'zone' => null, 'available' => false, 'reason' => 'restaurant_not_found'];
        }

        $hasZones = $this->restaurantHasActiveZones($restaurantId);

        if ($hasZones) {
            $zone = $this->findMatchingZone($restaurantId, $lat, $lng);
            if (!$zone) {
                return ['fee' => 0.0, 'zone' => null, 'available' => false, 'reason' => 'outside_zone'];
            }

            $distance = 0.0;
            if ($restaurant->latitude !== null && $restaurant->longitude !== null
                && $restaurant->latitude !== '' && $restaurant->longitude !== '') {
                $distance = GeoPolygon::distanceKm(
                    $lat,
                    $lng,
                    (float) $restaurant->latitude,
                    (float) $restaurant->longitude
                );
            }

            return [
                'fee'       => $this->calculateZoneFee($zone, $distance),
                'zone'      => $zone,
                'available' => true,
                'reason'    => null,
            ];
        }

        // Backward compatible: no zones configured → global delivery_setup + radius check left to caller.
        $fee = $this->calculateGlobalFee(
            $lat,
            $lng,
            (float) ($restaurant->latitude ?? 0),
            (float) ($restaurant->longitude ?? 0)
        );

        return ['fee' => $fee, 'zone' => null, 'available' => true, 'reason' => null];
    }

    public function calculateZoneFee(RestaurantDeliveryZone $zone, float $distanceKm): float
    {
        $additional = (float) ($zone->additional_delivery_charge ?? 0);
        $min        = $zone->min_delivery_charge !== null ? (float) $zone->min_delivery_charge : null;
        $max        = $zone->max_delivery_charge !== null ? (float) $zone->max_delivery_charge : null;
        $perKm      = $zone->charge_per_km !== null ? (float) $zone->charge_per_km : null;

        $globals = $this->globalDeliverySetup();

        if ($perKm === null) {
            $perKm = $globals['charge_per_kilo'];
        }
        if ($min === null) {
            $min = $globals['basic_fee'];
        }

        $chargeType = (int) $zone->charge_type;

        if ($chargeType === DeliveryChargeType::FIXED) {
            $fee = ($min ?? $globals['basic_fee']) + $additional;
        } else {
            // PER_KM and RANGE: distance-based, using zone per-km (fallback global).
            $freeKm = $globals['free_km'];
            if ($distanceKm > $freeKm) {
                $fee = (($distanceKm - $freeKm) * $perKm) + ($min ?? $globals['basic_fee']) + $additional;
            } else {
                $fee = ($min ?? $globals['basic_fee']) + $additional;
            }

            if ($chargeType === DeliveryChargeType::RANGE || $max !== null) {
                if ($min !== null) {
                    $fee = max($fee, $min + $additional);
                }
                if ($max !== null) {
                    $fee = min($fee, $max);
                }
            }
        }

        return round(max(0, $fee), 6);
    }

    public function calculateGlobalFee(float $custLat, float $custLng, float $restLat, float $restLng): float
    {
        $globals  = $this->globalDeliverySetup();
        $distance = GeoPolygon::distanceKm($custLat, $custLng, $restLat, $restLng);

        if ($distance > $globals['free_km']) {
            return round((($distance - $globals['free_km']) * $globals['charge_per_kilo']) + $globals['basic_fee'], 6);
        }

        return round($globals['basic_fee'], 6);
    }

    /**
     * @return array{free_km: float, basic_fee: float, charge_per_kilo: float}
     */
    public function globalDeliverySetup(): array
    {
        try {
            $group = Settings::group('delivery_setup');
            return [
                'free_km'         => (float) ($group->get('delivery_setup_free_delivery_kilometer') ?? 0),
                'basic_fee'       => (float) ($group->get('delivery_setup_basic_delivery_fee') ?? 0),
                'charge_per_kilo' => (float) ($group->get('delivery_setup_charge_per_kilo') ?? 0),
            ];
        } catch (\Throwable $e) {
            Log::warning('Failed loading delivery_setup settings', ['error' => $e->getMessage()]);
            return ['free_km' => 0.0, 'basic_fee' => 0.0, 'charge_per_kilo' => 0.0];
        }
    }

    /**
     * Customer point is deliverable for restaurant (zone or legacy).
     */
    public function isDeliverable(int $restaurantId, ?float $lat, ?float $lng, ?float $radiusKm = null): bool
    {
        if ($lat === null || $lng === null || !is_finite($lat) || !is_finite($lng)) {
            return true; // incomplete coords — preserve legacy lenient behavior
        }

        if ($this->restaurantHasActiveZones($restaurantId)) {
            return $this->findMatchingZone($restaurantId, $lat, $lng) !== null;
        }

        if ($radiusKm === null) {
            return true;
        }

        $restaurant = Restaurant::query()->find($restaurantId);
        if (!$restaurant || blank($restaurant->latitude) || blank($restaurant->longitude)) {
            return true;
        }

        $distance = GeoPolygon::distanceKm(
            $lat,
            $lng,
            (float) $restaurant->latitude,
            (float) $restaurant->longitude
        );

        return $distance <= $radiusKm;
    }
}
