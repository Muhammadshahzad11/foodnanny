<?php

namespace App\Services;

use Exception;
use App\Enums\Ask;
use App\Enums\Status;
use App\Models\Offer;
use App\Models\Campaign;
use App\Enums\OfferStatus;
use App\Models\Restaurant;
use App\Enums\CampaignStatus;
use App\Models\OfferRestaurant;
use App\Models\CampaignRestaurant;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Requests\PaginateRequest;
use App\Traits\DefaultAccessModelTrait;
use App\Libraries\QueryExceptionLibrary;
use Dipokhalder\Settings\Facades\Settings;

class CampaignAndOfferService
{
    use DefaultAccessModelTrait;

    protected array $searchFilter = [
        'title',
        'amount',
        'status',
        'type',
    ];

    protected array $exceptFilter = [
        'excepts'
    ];

    /**
     * @throws Exception
     */
    public function campaignList(PaginateRequest $request)
    {
        try {
            $requests    = $request->all();
            $method      = $request->get('paginate', 0) == 1 ? 'paginate' : 'get';
            $methodValue = $request->get('paginate', 0) == 1 ? $request->get('per_page', 10) : '*';
            $orderColumn = $request->get('order_column') ?? 'id';
            $orderType   = $request->get('order_type') ?? 'desc';
            $limit       = $request->get('limit') ?? '';

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
                    if (in_array($key, $this->searchFilter)) {
                            $query->where($key, 'like', '%' . $request . '%');
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
            })->where(['status' => Status::ACTIVE])->limit($limit)->orderBy($orderColumn, $orderType)->$method($methodValue);
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }

    /**
     * @throws Exception
     */
    public function offerList(PaginateRequest $request)
    {
        try {
            $requests    = $request->all();
            $method      = $request->get('paginate', 0) == 1 ? 'paginate' : 'get';
            $methodValue = $request->get('paginate', 0) == 1 ? $request->get('per_page', 10) : '*';
            $orderColumn = $request->get('order_column') ?? 'id';
            $orderType   = $request->get('order_type') ?? 'desc';
            $limit       = $request->get('limit') ? $request->get('limit') : '';

            $restaurant = Restaurant::findOrFail($this->restaurant());

            $offer =  Offer::with('offerRestaurants', 'restaurants')->where(function ($query) {
                $query->where('is_single', Ask::NO)->orWhere(function ($query) {
                    $query->where('is_single', Ask::YES)->whereHas('offerRestaurants', function ($q) {
                        $q->where('restaurant_id', $this->restaurant());
                    });
                });
            })->where(function ($query) use ($requests) {
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
                    if (in_array($key, $this->searchFilter)) {
                        $query->where($key, 'like', '%' . $request . '%');
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
            });

            if (!is_null($restaurant->latitude) && !is_null($restaurant->longitude)) {
                $offer->where(function ($query) use ($restaurant) {
                    $query->whereNull('latitude')->whereNull('longitude')->orWhere(function ($query) use ($restaurant) {
                        $query->whereNotNull('latitude')->whereNotNull('longitude')->withinDistanceOf($restaurant->latitude, $restaurant->longitude, Settings::group('site')->get('site_restaurant_search_radius'));
                    });
                });
            }
            return $offer->where(['status' => Status::ACTIVE])->limit($limit)->orderBy($orderColumn, $orderType)->$method($methodValue);
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }

    /**
     * @throws Exception
     */
    public function showCampaign(Campaign $campaign): Campaign
    {
        try {
            return $campaign;
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }

    /**
     * @throws Exception
     */
    public function showOffer(Offer $offer): Offer
    {
        try {
            return $offer;
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }

    /**
     * @throws Exception
     */
    public function applyCampaign(Campaign $campaign): Campaign
    {
        try {
            $today = strtotime(date('Y-m-d'));
            if (strtotime($campaign->end_date) < $today) {
                throw new Exception(trans('all.message.campaign_expire'), 422);
            } else {
                $checkRestaurant = CampaignRestaurant::where(['campaign_id' => $campaign->id, 'restaurant_id' => $this->restaurant()])->first();
                if ($checkRestaurant) {
                    throw new Exception(trans('all.message.restaurant_exist'), 422);
                } else {
                    DB::transaction(function () use ($campaign) {
                        CampaignRestaurant::create([
                            'campaign_id'   => $campaign->id,
                            'restaurant_id' => $this->restaurant(),
                            'apply'         => Ask::YES,
                            'status'        => CampaignStatus::PENDING
                        ]);
                    });
                    return $campaign;
                }
            }
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            DB::rollBack();
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }

    /**
     * @throws Exception
     */
    public function applyOffer(Offer $offer): Offer
    {
        try {
            $today = strtotime(date('Y-m-d'));
            if (strtotime($offer->end_date) < $today) {
                throw new Exception(trans('all.message.offer_expire'), 422);
            } else {
                $checkRestaurant = OfferRestaurant::where(['offer_id' => $offer->id, 'restaurant_id' => $this->restaurant()])->first();
                if ($checkRestaurant) {
                    throw new Exception(trans('all.message.restaurant_exist'), 422);
                } else {
                    if ($offer->is_single == Ask::NO) {
                        DB::transaction(function () use ($offer) {
                            OfferRestaurant::create([
                                'offer_id'      => $offer->id,
                                'restaurant_id' => $this->restaurant(),
                                'apply'         => Ask::YES,
                                'status'        => OfferStatus::PENDING
                            ]);
                        });
                        return $offer;
                    } else {
                        throw new Exception(trans('all.message.restaurant_match'), 422);
                    }
                }
            }
        } catch (Exception $exception) {
            DB::rollBack();
            Log::info($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }

    /**
     * @throws Exception
     */
    public function leaveCampaign(Campaign $campaign): void
    {
        try {
            $campaignRestaurant = CampaignRestaurant::where(['campaign_id' => $campaign->id, 'restaurant_id' => $this->restaurant()])->first();
            if ($campaignRestaurant) {
                $campaignRestaurant->delete();
            } else {
                throw new Exception(trans('all.message.restaurant_match'), 422);
            }
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }

    /**
     * @throws Exception
     */
    public function leaveOffer(Offer $offer): void
    {
        try {
            $offerRestaurant = OfferRestaurant::where(['offer_id' => $offer->id, 'restaurant_id' => $this->restaurant()])->first();
            if ($offerRestaurant) {
                $offerRestaurant->delete();
            } else {
                throw new Exception(trans('all.message.restaurant_match'), 422);
            }
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }
}
