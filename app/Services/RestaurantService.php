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

            return Restaurant::with('user', 'cuisines')->where(function ($query) use ($requests) {
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
                $this->restaurant = Restaurant::create($request->validated() + ['slug' => Str::slug($request->name), 'apply' => Apply::ADMIN, 'terms_and_conditions' => Ask::YES]);
                OrderSetup::create([
                    'restaurant_id'                => $this->restaurant->id,
                    'food_preparation_time'        => 30,
                    'schedule_order_slot_duration' => 15,
                    'takeaway'                     => Activity::DISABLE,
                    'delivery'                     => Activity::DISABLE,
                    'minimum_order_limit'          => 1
                ]);

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
                        'status' => (int)$request->status === Status::ACTIVE ? Status::ACTIVE : Status::INACTIVE,
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
                $restaurant->status = Status::ACTIVE;
                $restaurant->save();

                if ($restaurant->user_id) {
                    User::where('id', $restaurant->user_id)->update([
                        'status' => Status::ACTIVE,
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

            return Restaurant::where(['current_status' => Status::ACTIVE])->with('orderSetup', 'timeSlots')->withinDistanceOf($request->latitude, $request->longitude, Settings::group('site')->get('site_restaurant_search_radius'))->withDistance($request)->withReviewRating()->with(['favorite' => fn($query) => $query->where('user_id', Auth::check() ? Auth::user()->id : 0)])->whereHas('orderSetup', function ($query) use ($requests) {
                if (isset($requests['delivery_order_type'])) {
                    if ($requests['delivery_order_type'] == OrderType::DELIVERY) {
                        $query->where(['delivery' => Activity::ENABLE]);
                    } elseif ($requests['delivery_order_type'] == OrderType::TAKEAWAY) {
                        $query->where(['takeaway' => Activity::ENABLE]);
                    }
                }
            })->whereHas('items', function ($q) use ($requests) {
                if (isset($requests['name']) && !blank($requests['name'])) {
                    $q->where('name', 'like', '%' . $requests['name'] . '%');
                }
            })->when(isset($requests['cuisine_id']) && $requests['cuisine_id'] > 0, function ($q) use ($requests) {
                $q->whereHas('cuisines', fn($sub) => $sub->where(['cuisine_id' => $requests['cuisine_id']]));
            })->orWhere(function ($query) use ($requests) {
                if (isset($requests['name']) && !blank($requests['name'])) {
                    $query->where('name', 'like', '%' . $requests['name'] . '%')->where(['current_status' => Status::ACTIVE])->withinDistanceOf($requests['latitude'], $requests['longitude'], Settings::group('site')->get('site_restaurant_search_radius'));
                }
            })->orderBy($orderColumn, $orderType)->$method($methodValue);
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
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
            return Restaurant::with('media', 'cuisinesWithCuisineRelation', 'orderSetup', 'reviews', 'timeSlots', 'favorite')->withDistance($request)->with(['reviews' => fn($query) => $query->with('user')])->withReviewRating()->with(['favorite' => fn($query) => $query->where('user_id', Auth::check() ? Auth::user()->id : 0)])->where(['id' => $restaurant->id, 'status' => Status::ACTIVE])->first();
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
