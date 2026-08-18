<?php

namespace App\Services;

use Exception;
use App\Enums\Status;
use App\Enums\Activity;
use App\Enums\OrderType;
use App\Models\Campaign;
use App\Enums\CampaignType;
use Illuminate\Support\Str;
use App\Enums\CampaignStatus;
use App\Libraries\AppLibrary;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Requests\CampaignRequest;
use App\Http\Requests\PaginateRequest;
use App\Libraries\QueryExceptionLibrary;
use App\Http\Requests\ChangeImageRequest;
use App\Http\Requests\TranslationRequest;
use Dipokhalder\Settings\Facades\Settings;
use App\Http\Requests\CampaignRestaurantByLatLongRadiusRequest;

class CampaignService
{
    public object $campaign;
    protected array $campaignFilter = [
        'title',
        'amount',
        'type',
        'status'
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
            $limit       = $request->get('limit') ? $request->get('limit') : '';

            return Campaign::with('restaurants')->where(function ($query) use ($requests) {
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
                    if (in_array($key, $this->campaignFilter)) {
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
    public function store(CampaignRequest $request): object
    {
        try {
            DB::transaction(function () use ($request) {
                $this->campaign = Campaign::create([
                    'title'       => $request->title,
                    'tag'         => $request->input('tag'),
                    'slug'        => Str::slug($request->title) . AppLibrary::timeWithRand(),
                    'description' => $request->description,
                    'start_date'  => date('Y-m-d', strtotime($request->start_date)),
                    'end_date'    => date('Y-m-d', strtotime($request->end_date)),
                    'start_time'  => $request->start_time,
                    'end_time'    => $request->end_time,
                    'type'        => $request->type,
                    'amount'      => $request->type == CampaignType::FREE ? 0 : $request->amount,
                    'status'      => $request->status
                ]);

                if ($request->thumbnail) {
                    $this->campaign->addMedia($request->thumbnail)->toMediaCollection('campaign-thumb');
                }

                if ($request->cover) {
                    $this->campaign->addMedia($request->cover)->toMediaCollection('campaign-cover');
                }
            });
            return $this->campaign;
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            DB::rollBack();
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }

    /**
     * @throws Exception
     */
    public function update(CampaignRequest $request, Campaign $campaign): Campaign
    {
        try {
            DB::transaction(function () use ($request, $campaign) {
                $campaign->title       = $request->title;
                $campaign->tag         = $request->input('tag');
                $campaign->slug        = Str::slug($request->title) . AppLibrary::timeWithRand();
                $campaign->description = $request->description;
                $campaign->start_date  = date('Y-m-d', strtotime($request->start_date));
                $campaign->end_date    = date('Y-m-d', strtotime($request->end_date));
                $campaign->start_time  = $request->start_time;
                $campaign->end_time    = $request->end_time;
                $campaign->type        = $request->type;
                $campaign->amount      = $request->type == CampaignType::FREE ? 0 : $request->amount;
                $campaign->status      = $request->status;
                $campaign->save();
            });

            if ($request->thumbnail) {
                $campaign->clearMediaCollection('campaign-thumb');
                $campaign->addMedia($request->thumbnail)->toMediaCollection('campaign-thumb');
            }

            if ($request->cover) {
                $campaign->clearMediaCollection('campaign-cover');
                $campaign->addMedia($request->cover)->toMediaCollection('campaign-cover');
            }
            return $campaign;
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            DB::rollBack();
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }

    /**
     * @throws Exception
     */
    public function destroy(Campaign $campaign): void
    {
        try {
            $campaign->clearMediaCollection('campaign-thumb');
            $campaign->clearMediaCollection('campaign-cover');
            $campaign->campaignRestaurants()->delete();
            $campaign->delete();
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }

    /**
     * @throws Exception
     */
    public function show(Campaign $campaign): Campaign
    {
        try {
            return $campaign->load('translations');
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }

    /**
     * @throws Exception
     */
    public function saveTranslations(TranslationRequest $request, Campaign $campaign): \Illuminate\Http\Response
    {
        try {
            foreach ($request->get('translations', []) as $locale => $keys) {
                foreach ($keys as $key => $value) {
                    $campaign->translations()->updateOrCreate(
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
    public function changeThumbnail(ChangeImageRequest $request, Campaign $campaign): Campaign
    {
        try {
            if ($request->image) {
                $campaign->clearMediaCollection('campaign-thumb');
                $campaign->addMedia($request->image)->toMediaCollection('campaign-thumb');
            }
            return $campaign;
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }

    /**
     * @throws Exception
     */
    public function changeCover(ChangeImageRequest $request, Campaign $campaign): Campaign
    {
        try {
            if ($request->image) {
                $campaign->clearMediaCollection('campaign-cover');
                $campaign->addMedia($request->image)->toMediaCollection('campaign-cover');
            }
            return $campaign;
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }

    public function activeCampaign(CampaignRestaurantByLatLongRadiusRequest $request): \Illuminate\Database\Eloquent\Collection|array
    {
        return Campaign::with(['translations', 'media', 'campaignRestaurants' => fn($query) => $query->where(['status' => CampaignStatus::APPROVE])->with(
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
        })->where(['status' => Status::ACTIVE])->get();
    }

    public function activeShowCampaign(CampaignRestaurantByLatLongRadiusRequest $request, Campaign $campaign): Campaign|\Illuminate\Database\Eloquent\Builder|null
    {
        return Campaign::with(['translations', 'campaignRestaurants' => fn($query) => $query->where(['status' => CampaignStatus::APPROVE])->with(
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
        })->where(['status' => Status::ACTIVE, 'id' => $campaign->id])->first();
    }
}
