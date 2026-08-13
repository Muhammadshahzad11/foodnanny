<?php

namespace App\Services;

use App\Enums\Ask;
use App\Enums\Role as EnumRole;
use App\Enums\Status;
use App\Http\Requests\PaginateRequest;
use App\Http\Requests\ZoneRequest;
use App\Libraries\AppLibrary;
use App\Libraries\GeoPolygon;
use App\Libraries\QueryExceptionLibrary;
use App\Models\Restaurant;
use App\Models\User;
use App\Models\Zone;
use App\Traits\DefaultAccessModelTrait;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class ZoneService
{
    use DefaultAccessModelTrait;

    protected array $filter = [
        'name',
        'display_name',
        'status',
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

            return Zone::query()
                ->withCount('restaurants')
                ->with(['admins' => fn ($q) => $q->role(EnumRole::ZONE_ADMIN)])
                ->where(function ($query) use ($requests) {
                    foreach ($requests as $key => $value) {
                        if (!in_array($key, $this->filter, true) || $value === '' || $value === null) {
                            continue;
                        }
                        if ($key === 'status') {
                            $query->where($key, (int) $value);
                        } else {
                            $query->where($key, 'like', '%' . $value . '%');
                        }
                    }
                    if (!empty($requests['search'])) {
                        $search = $requests['search'];
                        $query->where(function ($q) use ($search) {
                            $q->where('name', 'like', '%' . $search . '%')
                                ->orWhere('display_name', 'like', '%' . $search . '%');
                        });
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
    public function store(ZoneRequest $request): Zone
    {
        $this->assertSuperAdmin();

        try {
            $data    = $request->validated();
            $polygon = GeoPolygon::normalize($data['polygon']);

            $zone = Zone::create([
                'name'                  => $data['name'],
                'display_name'          => $data['display_name'],
                'polygon'               => $polygon,
                'status'                => (int) ($data['status'] ?? Status::ACTIVE),
                'base_delivery_fee'     => $data['base_delivery_fee'] ?? 0,
                'min_order_amount'      => $data['min_order_amount'] ?? null,
                'free_delivery_above'   => $data['free_delivery_above'] ?? null,
                'free_delivery_km'      => $data['free_delivery_km'] ?? 0,
                'extra_distance_charge' => $data['extra_distance_charge'] ?? 0,
                'peak_enabled'          => (int) ($data['peak_enabled'] ?? 0),
                'peak_charge'           => $data['peak_charge'] ?? 0,
            ]);

            $this->syncMembersFromPolygon($zone);

            return $zone->fresh()->loadCount('restaurants')->load(['admins:id,name,email,phone,zone_id']);
        } catch (\InvalidArgumentException $exception) {
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
    public function update(ZoneRequest $request, Zone $zone): Zone
    {
        try {
            $data = $request->validated();
            $payload = [
                'name'                  => $data['name'],
                'display_name'          => $data['display_name'],
                'status'                => (int) ($data['status'] ?? $zone->status),
                'base_delivery_fee'     => $data['base_delivery_fee'] ?? $zone->base_delivery_fee,
                'min_order_amount'      => $data['min_order_amount'] ?? null,
                'free_delivery_above'   => $data['free_delivery_above'] ?? null,
                'free_delivery_km'      => $data['free_delivery_km'] ?? 0,
                'extra_distance_charge' => $data['extra_distance_charge'] ?? 0,
                'peak_enabled'          => (int) ($data['peak_enabled'] ?? 0),
                'peak_charge'           => $data['peak_charge'] ?? 0,
            ];

            if (!empty($data['polygon'])) {
                $payload['polygon'] = GeoPolygon::normalize($data['polygon']);
            }

            $zone->update($payload);
            $this->syncMembersFromPolygon($zone->fresh());

            return $zone->fresh()->loadCount('restaurants')->load(['admins:id,name,email,phone,zone_id']);
        } catch (\InvalidArgumentException $exception) {
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
    public function destroy(Zone $zone): void
    {
        $this->assertSuperAdmin();
        try {
            $deletedId = (int) $zone->id;
            $zone->delete();
            $this->syncAllActiveZones();
            Restaurant::withoutGlobalScopes()->where('zone_id', $deletedId)->update(['zone_id' => null]);
            User::query()->where('zone_id', $deletedId)->update(['zone_id' => null]);
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }

    /**
     * @throws Exception
     */
    public function deactivate(Zone $zone): Zone
    {
        $zone->update(['status' => Status::INACTIVE]);
        $this->syncMembersFromPolygon($zone->fresh());
        return $zone->fresh()->loadCount('restaurants')->load(['admins:id,name,email,phone,zone_id']);
    }

    /**
     * Assign restaurants and riders whose coordinates fall inside this zone polygon.
     * Overlaps use the first matching active zone (lowest id).
     */
    public function syncMembersFromPolygon(Zone $zone): void
    {
        $this->syncRestaurantsForZone($zone);
        $this->syncRidersForZone($zone);
    }

    public function syncAllActiveZones(): void
    {
        Zone::withoutGlobalScopes()
            ->where('status', Status::ACTIVE)
            ->orderBy('id')
            ->get()
            ->each(fn (Zone $zone) => $this->syncMembersFromPolygon($zone));
    }

    protected function syncRestaurantsForZone(Zone $zone): void
    {
        $restaurants = Restaurant::withoutGlobalScopes()
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->get(['id', 'latitude', 'longitude', 'zone_id', 'user_id']);

        foreach ($restaurants as $restaurant) {
            $lat = (float) $restaurant->latitude;
            $lng = (float) $restaurant->longitude;
            if (!is_finite($lat) || !is_finite($lng)) {
                continue;
            }

            $inside   = GeoPolygon::contains($lat, $lng, is_array($zone->polygon) ? $zone->polygon : []);
            $detected = $this->detect($lat, $lng);
            $newId    = $detected?->id;

            if ($inside && (int) $newId === (int) $zone->id) {
                $this->setRestaurantZone($restaurant, (int) $zone->id);
            } elseif ((int) $restaurant->zone_id === (int) $zone->id && (int) $newId !== (int) $zone->id) {
                $this->setRestaurantZone($restaurant, $newId);
            }
        }
    }

    protected function syncRidersForZone(Zone $zone): void
    {
        $riders = User::query()
            ->role(EnumRole::DELIVERY_BOY)
            ->with('deliveryLocation')
            ->get();

        foreach ($riders as $rider) {
            $loc = $rider->deliveryLocation;
            if (!$loc || blank($loc->latitude) || blank($loc->longitude)) {
                continue;
            }

            $lat = (float) $loc->latitude;
            $lng = (float) $loc->longitude;
            if (!is_finite($lat) || !is_finite($lng)) {
                continue;
            }

            $inside   = GeoPolygon::contains($lat, $lng, is_array($zone->polygon) ? $zone->polygon : []);
            $detected = $this->detect($lat, $lng);
            $newId    = $detected?->id;

            if ($inside && (int) $newId === (int) $zone->id) {
                if ((int) $rider->zone_id !== (int) $zone->id) {
                    $rider->zone_id = $zone->id;
                    $rider->save();
                }
            } elseif ((int) $rider->zone_id === (int) $zone->id && (int) $newId !== (int) $zone->id) {
                $rider->zone_id = $newId;
                $rider->save();
            }
        }
    }

    protected function setRestaurantZone(Restaurant $restaurant, ?int $zoneId): void
    {
        if ((int) $restaurant->zone_id === (int) $zoneId) {
            return;
        }

        Restaurant::withoutGlobalScopes()->where('id', $restaurant->id)->update(['zone_id' => $zoneId]);
        $restaurant->zone_id = $zoneId;

        if ($restaurant->user_id) {
            User::query()->where('id', $restaurant->user_id)->update(['zone_id' => $zoneId]);
        }
    }

    public function applyDetectedZoneId(?float $lat, ?float $lng): ?int
    {
        return $this->detect($lat, $lng)?->id;
    }

    /**
     * @throws Exception
     */
    public function assignRestaurants(Zone $zone, array $restaurantIds): Zone
    {
        $this->assertSuperAdmin();

        $ids = array_values(array_unique(array_map('intval', $restaurantIds)));

        Restaurant::withoutGlobalScopes()->where('zone_id', $zone->id)->update(['zone_id' => null]);

        if ($ids !== []) {
            Restaurant::withoutGlobalScopes()->whereIn('id', $ids)->update(['zone_id' => $zone->id]);
            User::query()
                ->whereIn('restaurant_id', $ids)
                ->update(['zone_id' => $zone->id]);
        }

        return $zone->fresh()->loadCount('restaurants')->load(['admins:id,name,email,phone,zone_id']);
    }

    /**
     * Create or attach a Zone Admin to this zone.
     *
     * @throws Exception
     */
    public function assignAdmin(Zone $zone, array $data): Zone
    {
        $this->assertSuperAdmin();

        try {
            DB::transaction(function () use ($zone, $data) {
                if (!empty($data['user_id'])) {
                    $user = User::query()->findOrFail((int) $data['user_id']);
                    $user->zone_id = $zone->id;
                    $user->restaurant_id = 0;
                    $user->save();
                    $user->syncRoles([EnumRole::ZONE_ADMIN]);
                    return;
                }

                $user = User::create([
                    'name'                 => $data['name'],
                    'email'                => $data['email'],
                    'phone'                => $data['phone'] ?? '',
                    'username'             => AppLibrary::username($data['name']),
                    'password'             => Hash::make($data['password']),
                    'status'               => Status::ACTIVE,
                    'email_verified_at'    => now(),
                    'restaurant_id'        => 0,
                    'zone_id'              => $zone->id,
                    'country_code'         => $data['country_code'] ?? '91',
                    'is_guest'             => Ask::NO,
                    'terms_and_conditions' => Ask::YES,
                ]);
                $user->assignRole(EnumRole::ZONE_ADMIN);
            });

            return $zone->fresh()->loadCount('restaurants')->load(['admins:id,name,email,phone,zone_id']);
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }

    /**
     * @throws Exception
     */
    public function assignDeliveryBoys(Zone $zone, array $userIds): Zone
    {
        $ids = array_values(array_unique(array_map('intval', $userIds)));

        User::role(EnumRole::DELIVERY_BOY)
            ->where('zone_id', $zone->id)
            ->update(['zone_id' => null]);

        if ($ids !== []) {
            User::role(EnumRole::DELIVERY_BOY)
                ->whereIn('id', $ids)
                ->update(['zone_id' => $zone->id]);
        }

        return $zone->fresh()->loadCount('restaurants')->load(['admins:id,name,email,phone,zone_id']);
    }

    public function detect(?float $lat, ?float $lng): ?Zone
    {
        if ($lat === null || $lng === null || !is_finite($lat) || !is_finite($lng)) {
            return null;
        }

        $zones = Zone::withoutGlobalScopes()
            ->where('status', Status::ACTIVE)
            ->orderBy('id')
            ->get();

        foreach ($zones as $zone) {
            $polygon = is_array($zone->polygon) ? $zone->polygon : [];
            if (GeoPolygon::contains($lat, $lng, $polygon)) {
                return $zone;
            }
        }

        return null;
    }

    public function hasActiveZones(): bool
    {
        return Zone::withoutGlobalScopes()->where('status', Status::ACTIVE)->exists();
    }

    /**
     * Restrict a restaurant query to the platform zone(s) for this pin / city.
     * Inside a polygon → that zone. City search (e.g. Hyderabad) → all matching zones.
     * Far away with no city match → empty.
     */
    public function constrainRestaurants($query, ?float $lat, ?float $lng, array $area = []): bool
    {
        if (!$this->hasActiveZones()) {
            return true;
        }

        $ids = $this->zoneIdsForPin($lat, $lng, $area);
        $table = method_exists($query, 'getModel') ? $query->getModel()->getTable() : 'restaurants';

        if (empty($ids)) {
            $query->whereRaw('1 = 0');
            return false;
        }

        $query->whereIn($table . '.zone_id', $ids);

        return true;
    }

    public function isServiceable(?float $lat, ?float $lng, array $area = []): bool
    {
        if (!$this->hasActiveZones()) {
            return true;
        }

        $ids = $this->zoneIdsForPin($lat, $lng, $area);
        if (empty($ids)) {
            return false;
        }

        return Restaurant::withoutGlobalScopes()
            ->whereIn('zone_id', $ids)
            ->where('current_status', Status::ACTIVE)
            ->exists();
    }

    /**
     * @return int[]
     */
    public function zoneIdsForPin(?float $lat, ?float $lng, array $area = []): array
    {
        if (!$this->hasActiveZones()) {
            return [];
        }

        $zone = $this->detect($lat, $lng);
        if ($zone) {
            return [(int) $zone->id];
        }

        $activeIds = Zone::withoutGlobalScopes()->where('status', Status::ACTIVE)->pluck('id');
        $city      = trim((string) ($area['city'] ?? ''));
        $district  = trim((string) ($area['district'] ?? ''));
        $terms     = array_values(array_unique(array_filter([$city, $district])));

        if ($terms) {
            $matched = Restaurant::withoutGlobalScopes()
                ->whereIn('zone_id', $activeIds)
                ->where(function ($q) use ($terms) {
                    foreach ($terms as $term) {
                        $q->orWhere('city', 'like', '%' . $term . '%')
                            ->orWhere('address', 'like', '%' . $term . '%');
                    }
                })
                ->pluck('zone_id')
                ->filter()
                ->map(fn ($id) => (int) $id)
                ->unique()
                ->values()
                ->all();

            if ($matched) {
                return $matched;
            }
        }

        $nearest = $this->nearestZone($lat, $lng, 25);
        return $nearest ? [(int) $nearest->id] : [];
    }

    protected function nearestZone(?float $lat, ?float $lng, float $maxKm): ?Zone
    {
        if ($lat === null || $lng === null || !is_finite($lat) || !is_finite($lng)) {
            return null;
        }

        $best   = null;
        $bestKm = $maxKm;

        foreach (Zone::withoutGlobalScopes()->where('status', Status::ACTIVE)->get() as $zone) {
            $polygon = is_array($zone->polygon) ? $zone->polygon : [];
            if (count($polygon) < 3) {
                continue;
            }

            $sumLat = 0.0;
            $sumLng = 0.0;
            $count  = 0;
            foreach ($polygon as $point) {
                if (!isset($point['lat'], $point['lng'])) {
                    continue;
                }
                $sumLat += (float) $point['lat'];
                $sumLng += (float) $point['lng'];
                $count++;
            }
            if ($count === 0) {
                continue;
            }

            $km = GeoPolygon::distanceKm($lat, $lng, $sumLat / $count, $sumLng / $count);
            if ($km <= $bestKm) {
                $bestKm = $km;
                $best   = $zone;
            }
        }

        return $best;
    }

    /**
     * @return array{available: bool, fee: float, zone: ?Zone, reason: ?string}
     */
    public function calculateFee(Zone $zone, float $distanceKm, float $subtotal): array
    {
        $minOrder = $zone->min_order_amount !== null ? (float) $zone->min_order_amount : 0;
        if ($minOrder > 0 && $subtotal < $minOrder) {
            return [
                'available' => false,
                'fee'       => 0.0,
                'zone'      => $zone,
                'reason'    => 'below_min_order',
            ];
        }

        $freeAbove = $zone->free_delivery_above !== null ? (float) $zone->free_delivery_above : 0;
        if ($freeAbove > 0 && $subtotal >= $freeAbove) {
            return [
                'available' => true,
                'fee'       => 0.0,
                'zone'      => $zone,
                'reason'    => null,
            ];
        }

        $fee    = (float) ($zone->base_delivery_fee ?? 0);
        $freeKm = (float) ($zone->free_delivery_km ?? 0);
        $perKm  = (float) ($zone->extra_distance_charge ?? 0);

        if ($distanceKm > $freeKm && $perKm > 0) {
            $fee += ($distanceKm - $freeKm) * $perKm;
        }

        if ((int) $zone->peak_enabled === 1) {
            $fee += (float) ($zone->peak_charge ?? 0);
        }

        return [
            'available' => true,
            'fee'       => round(max(0, $fee), 6),
            'zone'      => $zone,
            'reason'    => null,
        ];
    }

    /**
     * @throws Exception
     */
    protected function assertSuperAdmin(): void
    {
        if ($this->zone() > 0) {
            throw new Exception('Only Super Admin can create or delete zones.', 422);
        }
    }
}
