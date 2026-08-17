<?php

namespace App\Services;

use Exception;
use App\Enums\Ask;
use App\Enums\Apply;
use App\Models\User;
use App\Enums\Status;
use App\Models\Payout;
use App\Enums\Activity;
use App\Enums\OrderType;
use App\Models\OrderSetup;
use App\Models\Restaurant;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Enums\Role as EnumRole;
use App\Models\RestaurantCuisine;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\PaginateRequest;
use App\Http\Requests\RestaurantRequest;
use App\Libraries\QueryExceptionLibrary;
use App\Http\Requests\ChangeImageRequest;
use Dipokhalder\Settings\Facades\Settings;
use App\Http\Requests\RestaurantUserRequest;
use App\Http\Requests\RestaurantByLatLongRadiusRequest;


class RestaurantService
{
    public object $restaurant;
    protected array $restaurantFilter = [
        'name',
        'email',
        'phone',
        'latitude',
        'longitude',
        'city',
        'state',
        'zip_code',
        'address',
        'status',
        'current_status',
        'apply',
        'cuisine_id'
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

            return Restaurant::with('user', 'cuisines', 'zone')->where(function ($query) use ($requests) {
                foreach ($requests as $key => $request) {
                    if (in_array($key, $this->restaurantFilter)) {
                        if ($key == "cuisine_id") {
                            $query->whereHas('cuisines', function ($q) use ($key, $request) {
                                $q->where($key, $request);
                            });
                        } else if ($key == 'phone') {
                            $query->whereRaw("CONCAT(country_code, phone) LIKE ?", ["%{$request}%"]);
                        } else {
                            $query->where($key, 'like', '%' . $request . '%');
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
    public function store(RestaurantRequest $request): object
    {
        try {
            DB::transaction(function () use ($request) {
                $payload = $request->validated() + ['slug' => Str::slug($request->name), 'apply' => Apply::ADMIN, 'terms_and_conditions' => Ask::YES];
                if ((int) (Auth::user()?->myrole ?? 0) === EnumRole::ZONE_ADMIN && (int) (Auth::user()?->zone_id ?? 0) > 0) {
                    $payload['zone_id'] = (int) Auth::user()->zone_id;
                } else {
                    $payload['zone_id'] = app(ZoneService::class)->applyDetectedZoneId(
                        isset($payload['latitude']) ? (float) $payload['latitude'] : null,
                        isset($payload['longitude']) ? (float) $payload['longitude'] : null
                    );
                }
                $this->restaurant = Restaurant::create($payload);
                OrderSetup::create([
                    'restaurant_id'                => $this->restaurant->id,
                    'food_preparation_time'        => 30,
                    'schedule_order_slot_duration' => 15,
                    'takeaway'                     => Activity::DISABLE,
                    'delivery'                     => Activity::DISABLE,
                    'minimum_order_limit'          => 1
                ]);

                app(TimeSlotService::class)->ensureDefaults($this->restaurant->id);

                if ($request->cuisine_id) {
                    foreach ($request->cuisine_id as $cuisine) {
                        RestaurantCuisine::create([
                            'restaurant_id' => $this->restaurant->id,
                            'cuisine_id'    => $cuisine
                        ]);
                    }
                }
            });
            return $this->restaurant;
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            DB::rollBack();
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }

    /**
     * @throws Exception
     */
    public function userStore(RestaurantUserRequest $request, Restaurant $restaurant): Restaurant
    {
        try {
            DB::transaction(function () use ($request, $restaurant) {
                if (!$restaurant->user_id) {
                    $user = User::create([
                        'name'                 => $request->name,
                        'email'                => $request->email,
                        'phone'                => $request->phone,
                        'username'             => $this->username($request->email),
                        'password'             => bcrypt($request->password),
                        'restaurant_id'        => $restaurant->id,
                        'zone_id'              => $restaurant->zone_id,
                        'email_verified_at'    => now(),
                        'status'               => Status::ACTIVE,
                        'country_code'         => $request->country_code,
                        'is_guest'             => Ask::NO,
                        'terms_and_conditions' => Ask::YES
                    ]);
                    $user->assignRole(EnumRole::RESTAURANT_OWNER);
                    $restaurant->user_id = $user->id;
                    $restaurant->save();
                } else {
                    $user               = User::findOrFail($restaurant->user_id);
                    $user->name         = $request->name;
                    $user->email        = $request->email;
                    $user->phone        = $request->phone;
                    $user->country_code = $request->country_code;
                    $user->zone_id      = $restaurant->zone_id;
                    if ($request->password) {
                        $user->password = bcrypt($request->password);
                    }
                    $user->save();
                }
            });
            return $restaurant;
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            DB::rollBack();
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }

    /**
     * @throws Exception
     */
    public function update(RestaurantRequest $request, Restaurant $restaurant): object
    {
        try {
            DB::transaction(function () use ($request, $restaurant) {
                $this->restaurant = tap($restaurant)->update($request->validated() + ['slug' => Str::slug($request->name)]);
                if ((int) (Auth::user()?->myrole ?? 0) !== EnumRole::ZONE_ADMIN) {
                    $zoneId = app(ZoneService::class)->applyDetectedZoneId(
                        $this->restaurant->latitude !== null ? (float) $this->restaurant->latitude : null,
                        $this->restaurant->longitude !== null ? (float) $this->restaurant->longitude : null
                    );
                    if ((int) $this->restaurant->zone_id !== (int) $zoneId) {
                        $this->restaurant->zone_id = $zoneId;
                        $this->restaurant->save();
                    }
                }
                if ($request->cuisine_id) {
                    $restaurant->cuisines()->delete();
                    foreach ($request->cuisine_id as $cuisine) {
                        RestaurantCuisine::create([
                            'restaurant_id' => $this->restaurant->id,
                            'cuisine_id'    => $cuisine
                        ]);
                    }
                }
                if (!$request->cuisine_id) {
                    $restaurant->cuisines()->delete();
                }

                // Keep owner login access in sync with restaurant platform status
                if ($this->restaurant->user_id) {
                    User::where('id', $this->restaurant->user_id)->update([
                        'zone_id' => $this->restaurant->zone_id,
                        'status'  => (int) $request->status === Status::ACTIVE ? Status::ACTIVE : Status::INACTIVE,
                    ]);
                }
            });
            return $this->restaurant;
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            DB::rollBack();
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }

    /**
     * Approve a self-registered restaurant and unlock the owner account for login.
     *
     * @throws Exception
     */
    public function approve(Restaurant $restaurant): Restaurant
    {
        try {
            if ((int)$restaurant->status === Status::ACTIVE) {
                throw new Exception(trans('all.message.restaurant_already_approved'), 422);
            }

            DB::transaction(function () use ($restaurant) {
                $restaurant->status         = Status::ACTIVE;
                $restaurant->current_status = Status::ACTIVE;
                $restaurant->save();

                if ($restaurant->user_id) {
                    User::where('id', $restaurant->user_id)->update([
                        'status' => Status::ACTIVE,
                    ]);
                }

                // Ensure the restaurant can appear in customer Delivery / Takeaway search.
                $setup = OrderSetup::query()->firstOrCreate(
                    ['restaurant_id' => $restaurant->id],
                    [
                        'food_preparation_time'        => 30,
                        'schedule_order_slot_duration' => 15,
                        'takeaway'                     => Activity::ENABLE,
                        'delivery'                     => Activity::ENABLE,
                        'minimum_order_limit'          => 1,
                    ]
                );
                if ($setup->wasRecentlyCreated === false) {
                    $setup->update([
                        'delivery' => Activity::ENABLE,
                        'takeaway' => Activity::ENABLE,
                    ]);
                }
            });

            return $restaurant->fresh()->load('user', 'cuisines');
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            DB::rollBack();
            throw new Exception($exception->getMessage(), 422);
        }
    }

    /**
     * @throws Exception
     */
    public function destroy(Restaurant $restaurant): void
    {
        try {
            $restaurant->orderSetup()->delete();
            $restaurant->cuisines()->delete();
            $restaurant->delete();
        } catch (Exception $exception) {
            Log::info(QueryExceptionLibrary::message($exception));
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }

    /**
     * @throws Exception
     */
    public function show(Restaurant $restaurant): Restaurant
    {
        try {
            return $restaurant->load('user', 'cuisines');
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }

    /**
     * @throws Exception
     */
    public function changeImage(ChangeImageRequest $request, Restaurant $restaurant): Restaurant
    {
        try {
            if ($request->image) {
                $restaurant->clearMediaCollection('restaurant');
                $restaurant->addMedia($request->image)->toMediaCollection('restaurant');
            }
            return $restaurant;
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }

    /**
     * @throws Exception
     */
    public function changeLogo(ChangeImageRequest $request, Restaurant $restaurant): Restaurant
    {
        try {
            if ($request->image) {
                $restaurant->clearMediaCollection('restaurant-logo');
                $restaurant->addMedia($request->image)->toMediaCollection('restaurant-logo');
            }
            return $restaurant;
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }

    private function username($email): string
    {
        $emails = explode('@', $email);
        return $emails[0] . mt_rand();
    }

    /**
     * @throws Exception
     */
    public function restaurantByLatLongRadius(RestaurantByLatLongRadiusRequest $request)
    {
        try {
            $requests    = $request->all();
            $method      = $request->get('paginate', 0) == 1 ? 'paginate' : 'get';
            $methodValue = $request->get('paginate', 0) == 1 ? $request->get('per_page', 10) : '*';
            $orderColumn = $request->get('order_column') ?? 'id';
            $orderType   = $request->get('order_type') ?? 'desc';

            $city     = trim((string) ($requests['city'] ?? ''));
            $district = trim((string) ($requests['district'] ?? ''));
            $state    = trim((string) ($requests['state'] ?? ''));
            $hasArea  = $city !== '' || $district !== '' || $state !== '';
            $lat      = (float) $request->latitude;
            $lng      = (float) $request->longitude;
            $radius   = (float) (Settings::group('site')->get('site_restaurant_search_radius') ?: 50);

            $query = Restaurant::query()
                ->withoutGlobalScope(\App\Models\Scopes\ZoneScope::class)
                ->where(['current_status' => Status::ACTIVE])
                ->with('orderSetup', 'timeSlots', 'activeDeliveryZones', 'zone')
                ->withReviewRating()
                ->with(['favorite' => fn($q) => $q->where('user_id', Auth::check() ? Auth::user()->id : 0)])
                ->whereHas('orderSetup', function ($query) use ($requests) {
                    if (isset($requests['delivery_order_type'])) {
                        if ((int) $requests['delivery_order_type'] === OrderType::DELIVERY) {
                            $query->where(['delivery' => Activity::ENABLE]);
                        } elseif ((int) $requests['delivery_order_type'] === OrderType::TAKEAWAY) {
                            $query->where(['takeaway' => Activity::ENABLE]);
                        }
                    }
                });

            $this->applyDistanceSelect($query, $lat, $lng);

            if ($hasArea) {
                $query->where(function ($area) use ($city, $district, $state, $lat, $lng, $radius) {
                    if ($city !== '') {
                        $area->orWhere('city', 'like', '%' . $city . '%')
                            ->orWhere('address', 'like', '%' . $city . '%');
                    }
                    if ($district !== '' && strcasecmp($district, $city) !== 0) {
                        $area->orWhere('city', 'like', '%' . $district . '%')
                            ->orWhere('state', 'like', '%' . $district . '%')
                            ->orWhere('address', 'like', '%' . $district . '%');
                    }
                    if ($state !== '') {
                        $area->orWhere('state', 'like', '%' . $state . '%')
                            ->orWhere('address', 'like', '%' . $state . '%');
                    }
                    // Also include restaurants near the pin (same metro), even if city text differs.
                    $this->applyNearbyConstraint($area, $lat, $lng, $radius, true);
                });
            } else {
                $this->applyNearbyConstraint($query, $lat, $lng, $radius, false);
            }

            if ($orderColumn === 'distance') {
                $query->orderByRaw('distance IS NULL ASC')->orderBy('distance', $orderType);
            } else {
                $query->orderBy($orderColumn, $orderType);
            }

            $isDelivery  = isset($requests['delivery_order_type']) && (int) $requests['delivery_order_type'] === OrderType::DELIVERY;
            $zoneService = app(ZoneService::class);
            $zoneService->constrainRestaurants($query, $lat, $lng, [
                'city'     => $city,
                'district' => $district,
                'state'    => $state,
            ]);

            return $query
                ->when(isset($requests['name']) && !blank($requests['name']), function ($q) use ($requests) {
                    $q->where(function ($inner) use ($requests) {
                        $inner->where('name', 'like', '%' . $requests['name'] . '%')
                            ->orWhereHas('items', function ($itemQuery) use ($requests) {
                                $itemQuery->where('name', 'like', '%' . $requests['name'] . '%');
                            });
                    });
                })
                ->when(isset($requests['cuisine_id']) && $requests['cuisine_id'] > 0, function ($q) use ($requests) {
                    $q->whereHas('cuisines', fn($sub) => $sub->where(['cuisine_id' => $requests['cuisine_id']]));
                })
                ->$method($methodValue)
                ->when(
                    $isDelivery && !$zoneService->hasActiveZones(),
                    function ($results) use ($lat, $lng) {
                        return $this->filterRestaurantsByPlatformZone($results, $lat, $lng);
                    }
                );
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }

    /**
     * Legacy per-restaurant polygons. Used only when no platform zones exist.
     */
    protected function filterRestaurantsByPlatformZone($results, float $lat, float $lng)
    {
        $filter = function ($restaurant) use ($lat, $lng) {
            $zones = $restaurant->activeDeliveryZones ?? collect();
            if ($zones->isEmpty()) {
                return true;
            }
            foreach ($zones as $zone) {
                $polygon = is_array($zone->polygon) ? $zone->polygon : [];
                if (\App\Libraries\GeoPolygon::contains($lat, $lng, $polygon)) {
                    return true;
                }
            }
            return false;
        };

        if ($results instanceof \Illuminate\Pagination\AbstractPaginator) {
            $results->setCollection($results->getCollection()->filter($filter)->values());
            return $results;
        }

        if ($results instanceof \Illuminate\Support\Collection) {
            return $results->filter($filter)->values();
        }

        return $results;
    }

    /**
     * True when the Netsells GeoScope package supports this DB driver.
     */
    protected function supportsGeoScope(): bool
    {
        return in_array(DB::connection()->getDriverName(), ['mysql', 'mariadb', 'pgsql'], true);
    }

    /**
     * Add a selectable distance column (kilometers) for sorting.
     */
    protected function applyDistanceSelect($query, float $lat, float $lng): void
    {
        if ($this->supportsGeoScope()) {
            $query->withDistance((object) ['latitude' => $lat, 'longitude' => $lng]);

            return;
        }

        // Haversine in kilometers — works on SQLite / drivers without GeoScope.
        $query->select('restaurants.*')
            ->selectRaw(
                '(6371 * acos(MIN(1, MAX(-1,
                    cos(radians(?)) * cos(radians(CAST(restaurants.latitude AS FLOAT)))
                    * cos(radians(CAST(restaurants.longitude AS FLOAT)) - radians(?))
                    + sin(radians(?)) * sin(radians(CAST(restaurants.latitude AS FLOAT)))
                )))) AS distance',
                [$lat, $lng, $lat]
            );
    }

    /**
     * Limit results to a radius around lat/lng (kilometers).
     */
    protected function applyNearbyConstraint($query, float $lat, float $lng, float $radius, bool $asOrWhere): void
    {
        $callback = function ($near) use ($lat, $lng, $radius) {
            $near->whereNotNull('latitude')
                ->whereNotNull('longitude')
                ->where('latitude', '!=', '')
                ->where('longitude', '!=', '');

            if ($this->supportsGeoScope()) {
                $near->withinDistanceOf($lat, $lng, $radius);
            } else {
                $near->whereRaw(
                    '(6371 * acos(MIN(1, MAX(-1,
                        cos(radians(?)) * cos(radians(CAST(restaurants.latitude AS FLOAT)))
                        * cos(radians(CAST(restaurants.longitude AS FLOAT)) - radians(?))
                        + sin(radians(?)) * sin(radians(CAST(restaurants.latitude AS FLOAT)))
                    )))) <= ?',
                    [$lat, $lng, $lat, $radius]
                );
            }
        };

        if ($asOrWhere) {
            $query->orWhere($callback);
        } else {
            $query->where($callback);
        }
    }

    /**
     * @throws Exception
     */
    public function favorite(PaginateRequest $request)
    {
        try {
            $method      = $request->get('paginate', 0) == 1 ? 'paginate' : 'get';
            $methodValue = $request->get('paginate', 0) == 1 ? $request->get('per_page', 10) : '*';
            $orderColumn = $request->get('order_column') ?? 'id';
            $orderType   = $request->get('order_type') ?? 'desc';

            return Restaurant::where(['current_status' => Status::ACTIVE])
                ->with('orderSetup', 'timeSlots')->withReviewRating()
                ->whereHas('favorite', function ($query) {
                    return $query->where('user_id', Auth::user()->id);
                })->orderBy($orderColumn, $orderType)->$method($methodValue);
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }

    /**
     * @throws Exception
     */
    public function showWithDetails(Restaurant $restaurant, Request $request)
    {
        try {
            return Restaurant::withoutGlobalScope(\App\Models\Scopes\ZoneScope::class)->with('media', 'cuisinesWithCuisineRelation', 'orderSetup', 'reviews', 'timeSlots', 'favorite', 'activeDeliveryZones', 'zone')->withDistance($request)->with(['reviews' => fn($query) => $query->with('user')])->withReviewRating()->with(['favorite' => fn($query) => $query->where('user_id', Auth::check() ? Auth::user()->id : 0)])->where(['id' => $restaurant->id, 'status' => Status::ACTIVE])->first();
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }


    /**
     * @throws Exception
     */
    public function onlyShowWithDetails(Restaurant $restaurant)
    {
        try {
            return Restaurant::with('media', 'cuisinesWithCuisineRelation')->where(['id' => $restaurant->id, 'status' => Status::ACTIVE])->first();
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    } 
}
