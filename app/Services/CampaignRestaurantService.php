<?php

namespace App\Services;

use Exception;
use App\Enums\Ask;
use App\Models\Campaign;
use App\Enums\CampaignStatus;
use App\Models\CampaignRestaurant;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Requests\PaginateRequest;
use App\Libraries\QueryExceptionLibrary;
use App\Http\Requests\CampaignRestaurantRequest;
use App\Http\Requests\CampaignRestaurantVerifyRequest;

class CampaignRestaurantService
{
    protected $campainRestaurantFilter = [
        'restaurant_id',
        'apply',
        'status'
    ];

    /**
     * @throws Exception
     */
    public function list(PaginateRequest $request, Campaign $campaign)
    {
        try {
            $requests    = $request->all();
            $method      = $request->get('paginate', 0) == 1 ? 'paginate' : 'get';
            $methodValue = $request->get('paginate', 0) == 1 ? $request->get('per_page', 10) : '*';
            $orderColumn = $request->get('order_column') ?? 'id';
            $orderType   = $request->get('order_type') ?? 'desc';

            return CampaignRestaurant::with('campaign', 'restaurant')->where(['campaign_id' => $campaign->id])->where(function ($query) use ($requests) {
                foreach ($requests as $key => $request) {
                    if (in_array($key, $this->campainRestaurantFilter)) {
                        $query->where($key, 'like', '%' . $request . '%');
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
    public function store(CampaignRestaurantRequest $request, Campaign $campaign)
    {
        try {
            return CampaignRestaurant::create($request->validated() + ['campaign_id' => $campaign->id, 'apply' => Ask::YES, 'status' => CampaignStatus::APPROVE]);
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }

    /**
     * @throws Exception
     */
    public function destroy(Campaign $campaign, CampaignRestaurant $campaignRestaurant)
    {
        try {
            if ($campaign->id == $campaignRestaurant->campaign_id) {
                $campaignRestaurant->delete();
            } else {
                throw new Exception(trans('all.restaurant_match'), 422);
            }
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }

    /**
     * @throws Exception
     */
    public function verify(CampaignRestaurantVerifyRequest $request, Campaign $campaign, CampaignRestaurant $campaignRestaurant): CampaignRestaurant
    {
        try {
            DB::transaction(function () use ($request, $campaign, $campaignRestaurant) {
                if ($campaign->id === $campaignRestaurant->campaign_id) {
                    $campaignRestaurant->status = $request->status;
                    $campaignRestaurant->save();
                } else {
                    throw new Exception(trans('all.restaurant_match'), 422);
                }
            });
            return $campaignRestaurant;
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            DB::rollBack();
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }
}
