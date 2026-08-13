<?php

namespace App\Services;

use Exception;
use App\Enums\Ask;
use App\Enums\Status;
use App\Models\Offer;
use App\Enums\Activity;
use App\Enums\OfferType;
use App\Enums\OrderType;
use App\Enums\OfferStatus;
use App\Models\Restaurant;
use Illuminate\Support\Str;
use App\Libraries\AppLibrary;
use App\Models\OfferRestaurant;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\OfferRequest;
use App\Http\Requests\TranslationRequest;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\PaginateRequest;
use App\Libraries\QueryExceptionLibrary;
use App\Http\Requests\ChangeImageRequest;
use Dipokhalder\Settings\Facades\Settings;
use App\Http\Requests\OfferRestaurantByLatLongRadiusRequest;
use App\Http\Requests\OfferRestaurantByOnlyLatLongRadiusRequest;

class OfferService
{
    public object $offer;
    protected array $offerFilter = [
        'title',
        'amount',
        'status',
        'type'
    ];

    protected array $exceptFilter = [
        'excepts'
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
            $limit       = $request->get('limit') ?? '';

            return Offer::with('offerRestaurants')->where(function ($query) use ($requests) {
                if (isset($requests['start_date']) && isset($requests['end_date'])) {
                    $first_date = Date('Y-m-d', strtotime($requests['start_date']));
                    $last_date  = Date('Y-m-d', strtotime($requests['end_date']));
                    $query->whereDate('start_date', '>=', $first_date)->whereDate('end_date', '<=', $last_date);
                }

                if (isset($requests['start_time']) && isset($requests['end_time'])) {
                    $first_time = Date('H:i:s', strtotime($requests['start_time']));
                    $last_time  = Date('H:i:s', strtotime($requests['end_time']));
                    $query->whereTime('start_time', '>=', $first_time)->whereTime('end_time', '<=', $last_time);
                }
                foreach ($requests as $key => $request) {
                    if (in_array($key, $this->offerFilter)) {
                        if ($key === "status") {
                            $query->where($key, (int)$request);
                        } else {
                            $query->where($key, 'like', '%' . $request . '%');
                        }
                    }
                    if (in_array($key, $this->exceptFilter)) {
                        $explodes = explode('|', $request);
                        if (is_array($explodes)) {
                            foreach ($explodes as $explode) {
                                $query->where('id', '!=', $explode);
                            }
                        }
                    }
                }
            })->limit($limit)->orderBy($orderColumn, $orderType)->$method(
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
    public function store(OfferRequest $request): object
    {
        try {
            DB::transaction(function () use ($request) {
                $this->offer = Offer::create([
                    'title'       => $request->title,
                    'slug'        => Str::slug($request->title) . AppLibrary::timeWithRand(),
                    'description' => $request->description,
                    'location'    => $request->location,
                    'latitude'    => $request->latitude,
                    'longitude'   => $request->longitude,
                    'amount'      => $request->amount,
                    'status'      => $request->status,
                    'start_date'  => date('Y-m-d', strtotime($request->start_date)),
                    'end_date'    => date('Y-m-d', strtotime($request->end_date)),
                    'start_time'  => $request->start_time,
                    'end_time'    => $request->end_time,
                    'type'        => $request->type,
                    'is_single'   => $request->restaurant_id > 0 ? Ask::YES : Ask::NO
                ]);

                if ($request->restaurant_id > 0) {
                    OfferRestaurant::create([
                        'offer_id'      => $this->offer->id,
                        'restaurant_id' => $request->restaurant_id,
                        'apply'         => Ask::YES,
                        'status'        => OfferStatus::APPROVE
                    ]);
                }

                if ($request->thumbnail) {
                    $this->offer->addMedia($request->thumbnail)->toMediaCollection('offer-thumb');
                }

                if ($request->cover) {
                    $this->offer->addMedia($request->cover)->toMediaCollection('offer-cover');
                }
            });
            return $this->offer;
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            DB::rollBack();
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }

    /**
     * @throws Exception
     */
    public function update(OfferRequest $request, Offer $offer): Offer
    {
        try {
            DB::transaction(function () use ($request, $offer) {

                if (is_null($request->restaurant_id) && $offer->is_single == Ask::YES) {
                    $offer->offerRestaurants()?->delete();
                }

                $offer->title       = $request->title;
                $offer->slug        = Str::slug($request->title) . AppLibrary::timeWithRand();
                $offer->description = $request->description;
                $offer->location    = blank($request->latitude) && blank($request->longitude) ? null : $request->location;
                $offer->latitude    = $request->latitude;
                $offer->longitude   = $request->longitude;
                $offer->amount      = $request->amount;
                $offer->status      = $request->status;
                $offer->start_date  = date('Y-m-d', strtotime($request->start_date));
                $offer->end_date    = date('Y-m-d', strtotime($request->end_date));
                $offer->start_time  = $request->start_time;
                $offer->end_time    = $request->end_time;
                $offer->type        = $request->type;
                $offer->is_single   = $request->restaurant_id > 0 ? Ask::YES : Ask::NO;
                $offer->save();
            });

            if ($request->restaurant_id > 0) {
                $offer->offerRestaurants()->delete();
                OfferRestaurant::create([
                    'offer_id'      => $offer->id,
                    'restaurant_id' => $request->restaurant_id,
                    'apply'         => Ask::YES,
                    'status'        => OfferStatus::APPROVE
                ]);
            }

            if ($request->thumbnail) {
                $offer->clearMediaCollection('offer-thumb');
                $offer->addMedia($request->thumbnail)->toMediaCollection('offer-thumb');
            }

            if ($request->cover) {
                $offer->clearMediaCollection('offer-cover');
                $offer->addMedia($request->cover)->toMediaCollection('offer-cover');
            }
            return $offer;
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            DB::rollBack();
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }

    /**
     * @throws Exception
     */
    public function destroy(Offer $offer): void
    {
        try {
            $offer->clearMediaCollection('offer-thumb');
            $offer->clearMediaCollection('offer-cover');
            $offer->offerRestaurants()->delete();
            $offer->delete();
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }

    /**
     * @throws Exception
     */
    public function show(Offer $offer): Offer
    {
        try {
            return $offer->load('offerRestaurants', 'translations');
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }

    /**
     * @throws Exception
     */
    public function saveTranslations(TranslationRequest $request, Offer $offer): \Illuminate\Http\Response
    {
        try {
            foreach ($request->get('translations', []) as $locale => $keys) {
                foreach ($keys as $key => $value) {
                    $offer->translations()->updateOrCreate(
                        ['locale' => $locale, 'key' => $key],
                        ['value' => $value ?? '']
                    );
                }
            }
            return response(['message' => trans('all.message.translations_saved_successfully')], 200);
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }

    /**
     * @throws Exception
     */
    public function changeThumbnail(ChangeImageRequest $request, Offer $offer): Offer
    {
        try {
            if ($request->image) {
                $offer->clearMediaCollection('offer-thumb');
                $offer->addMedia($request->image)->toMediaCollection('offer-thumb');
            }
            return $offer;
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }

    /**
     * @throws Exception
     */
    public function changeCover(ChangeImageRequest $request, Offer $offer): Offer
    {
        try {
            if ($request->image) {
                $offer->clearMediaCollection('offer-cover');
                $offer->addMedia($request->image)->toMediaCollection('offer-cover');
            }
            return $offer;
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }

    public function activeMultiOffer(OfferRestaurantByLatLongRadiusRequest $request): \Illuminate\Database\Eloquent\Collection|array
    {
        return Offer::with(['media', 'translations', 'offerRestaurants' => fn($query) => $query->where(['status' => OfferStatus::APPROVE])->with(
            ['restaurant' => fn($query) => $query->where(['status' => Status::ACTIVE, 'current_status' => Status::ACTIVE])->withinDistanceOf($request->latitude, $request->longitude, Settings::group('site')->get('site_restaurant_search_radius'))
                ->tap(fn ($q) => app(ZoneService::class)->constrainRestaurants($q, (float) $request->latitude, (float) $request->longitude))
                ->whereHas('orderSetup', function ($query) use ($request) {
                    if (isset($request->delivery_order_type)) {
                        if ($request->delivery_order_type == OrderType::DELIVERY) {
                            $query->where(['delivery' => Activity::ENABLE]);
                        } elseif ($request->delivery_order_type == OrderType::TAKEAWAY) {
                            $query->where(['takeaway' => Activity::ENABLE]);
                        }
                    }
                })
            ])
        ])->where(function ($query) {
            $query->whereDate('start_date', '<=', Date('Y-m-d'));
            $query->whereDate('end_date', '>=', Date('Y-m-d'));
            $query->whereTime('start_time', '<=', Date('H:i:s'));
            $query->whereTime('end_time', '>=', Date('H:i:s'));
        })->where(function ($query) use ($request) {
            $query->withinDistanceOf($request->latitude, $request->longitude, Settings::group('site')->get('site_restaurant_search_radius'))->orWhere(['latitude' => null, 'longitude' => null]);
        })->where(['status' => Status::ACTIVE, 'type' => OfferType::REGULAR])->get();
    }

    public function activeSingleOffer(OfferRestaurantByLatLongRadiusRequest $request): Offer|\Illuminate\Database\Eloquent\Builder|null
    {
        return Offer::with(['translations', 'offerRestaurants' => fn($query) => $query->where(['status' => OfferStatus::APPROVE])->with(
            ['restaurant' => fn($query) => $query->where(['status' => Status::ACTIVE, 'current_status' => Status::ACTIVE])->with('orderSetup', 'timeSlots')
                ->withinDistanceOf($request->latitude, $request->longitude, Settings::group('site')->get('site_restaurant_search_radius'))
                ->tap(fn ($q) => app(ZoneService::class)->constrainRestaurants($q, (float) $request->latitude, (float) $request->longitude))
                ->withDistance($request)
                ->withReviewRating()
                ->with(['favorite' => fn($query) => $query->where('user_id', Auth::check() ? Auth::user()->id : 0)])
                ->whereHas('orderSetup', function ($query) use ($request) {
                    if (isset($request->delivery_order_type)) {
                        if ($request->delivery_order_type == OrderType::DELIVERY) {
                            $query->where(['delivery' => Activity::ENABLE]);
                        } elseif ($request->delivery_order_type == OrderType::TAKEAWAY) {
                            $query->where(['takeaway' => Activity::ENABLE]);
                        }
                    }
                })
            ])
        ])->where(function ($query) {
            $query->whereDate('start_date', '<=', Date('Y-m-d'));
            $query->whereDate('end_date', '>=', Date('Y-m-d'));
            $query->whereTime('start_time', '<=', Date('H:i:s'));
            $query->whereTime('end_time', '>=', Date('H:i:s'));
        })->where(function ($query) use ($request) {
            $query->withinDistanceOf($request->latitude, $request->longitude, Settings::group('site')->get('site_restaurant_search_radius'))->orWhere(['latitude' => null, 'longitude' => null]);
        })->where(['status' => Status::ACTIVE, 'type' => OfferType::PREMIER])->first();
    }

    public function activeShowOffer(OfferRestaurantByLatLongRadiusRequest $request, Offer $offer): Offer|\Illuminate\Database\Eloquent\Builder|null
    {
        return Offer::with(['translations', 'offerRestaurants' => fn($query) => $query->where(['status' => OfferStatus::APPROVE])->with(
            ['restaurant' => fn($query) => $query->where(['status' => Status::ACTIVE, 'current_status' => Status::ACTIVE])->with('orderSetup')
                ->withinDistanceOf($request->latitude, $request->longitude, Settings::group('site')->get('site_restaurant_search_radius'))
                ->tap(fn ($q) => app(ZoneService::class)->constrainRestaurants($q, (float) $request->latitude, (float) $request->longitude))
                ->withDistance($request)
                ->withReviewRating()
                ->whereHas('orderSetup', function ($query) use ($request) {
                    if (isset($request->delivery_order_type)) {
                        if ($request->delivery_order_type == OrderType::DELIVERY) {
                            $query->where(['delivery' => Activity::ENABLE]);
                        } elseif ($request->delivery_order_type == OrderType::TAKEAWAY) {
                            $query->where(['takeaway' => Activity::ENABLE]);
                        }
                    }
                })
            ])
        ])->where(function ($query) {
            $query->whereDate('start_date', '<=', Date('Y-m-d'));
            $query->whereDate('end_date', '>=', Date('Y-m-d'));
            $query->whereTime('start_time', '<=', Date('H:i:s'));
            $query->whereTime('end_time', '>=', Date('H:i:s'));
        })->where(function ($query) use ($request) {
            $query->withinDistanceOf($request->latitude, $request->longitude, Settings::group('site')->get('site_restaurant_search_radius'))->orWhere(['latitude' => null, 'longitude' => null]);
        })->where(['status' => Status::ACTIVE, 'id' => $offer->id])->first();
    }

    public function activeOfferFind(OfferRestaurantByOnlyLatLongRadiusRequest $request): \IlluminateAgnostic\Str\Support\Collection|\IlluminateAgnostic\Collection\Support\Collection|\Illuminate\Support\Collection|\IlluminateAgnostic\Arr\Support\Collection
    {
        $i           = 0;
        $restaurants = [];
        $offers      = Offer::with(['offerRestaurants' => fn($query) => $query->where(['status' => OfferStatus::APPROVE])->with(
            ['restaurant' => fn($query) => $query->where(['status' => Status::ACTIVE, 'current_status' => Status::ACTIVE])->with('orderSetup')
                ->withinDistanceOf($request->latitude, $request->longitude, Settings::group('site')->get('site_restaurant_search_radius'))
                ->tap(fn ($q) => app(ZoneService::class)->constrainRestaurants($q, (float) $request->latitude, (float) $request->longitude))
                ->withDistance($request)
                ->withReviewRating()
            ])
        ])->where(function ($query) {
            $query->whereDate('start_date', '<=', Date('Y-m-d'));
            $query->whereDate('end_date', '>=', Date('Y-m-d'));
            $query->whereTime('start_time', '<=', Date('H:i:s'));
            $query->whereTime('end_time', '>=', Date('H:i:s'));
        })->where(function ($query) use ($request) {
            $query->withinDistanceOf($request->latitude, $request->longitude, Settings::group('site')->get('site_restaurant_search_radius'))->orWhere(['latitude' => null, 'longitude' => null]);
        })->where(['status' => Status::ACTIVE])->orderBy('id', 'desc')->get();

        if (count($offers) > 0) {
            foreach ($offers as $offer) {
                if (count($offer->offerRestaurants) > 0) {
                    foreach ($offer->offerRestaurants as $restaurantRelation) {
                        if (isset($restaurantRelation->restaurant)) {
                            $restaurants[$i] = (object)[
                                'id'     => $restaurantRelation->restaurant->id,
                                'name'   => $restaurantRelation->restaurant->name,
                                'slug'   => $restaurantRelation->restaurant->slug,
                                'amount' => (float)$offer->amount
                            ];
                            $i++;
                        }
                    }
                }
            }
        }
        return collect($restaurants);
    }

    public function activeOfferCheck(OfferRestaurantByOnlyLatLongRadiusRequest $request, Restaurant $restaurant)
    {
        return Offer::with('translations')->whereHas('offerRestaurants', function ($query) use ($request, $restaurant) {
            $query->where(['status' => OfferStatus::APPROVE, 'restaurant_id' => $restaurant->id]);
        })->where(function ($query) {
            $query->whereDate('start_date', '<=', Date('Y-m-d'));
            $query->whereDate('end_date', '>=', Date('Y-m-d'));
            $query->whereTime('start_time', '<=', Date('H:i:s'));
            $query->whereTime('end_time', '>=', Date('H:i:s'));
        })->where(function ($query) use ($request) {
            $query->withinDistanceOf($request->latitude, $request->longitude, Settings::group('site')->get('site_restaurant_search_radius'))->orWhere(['latitude' => null, 'longitude' => null]);
        })->where(['status' => Status::ACTIVE])->first();
    }
}
