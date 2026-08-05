<?php

namespace App\Http\Controllers\Admin;

use Exception;
use App\Models\Campaign;
use App\Models\CampaignRestaurant;
use App\Http\Requests\PaginateRequest;
use App\Services\CampaignRestaurantService;
use App\Http\Requests\CampaignRestaurantRequest;
use App\Http\Resources\CampaignRestaurantResource;
use App\Http\Requests\CampaignRestaurantVerifyRequest;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class CampaignRestaurantController extends AdminController implements HasMiddleware
{
    public CampaignRestaurantService $campaignRestaurantService;

    public function __construct(CampaignRestaurantService $campaignRestaurantService)
    {
        parent::__construct();
        $this->campaignRestaurantService = $campaignRestaurantService;
    }

    public static function middleware(): array
    {
        return [
            new Middleware('permission:campaigns_show', only: ['index']),
            new Middleware('permission:campaigns_create', only: ['store', 'verify']),
            new Middleware('permission:campaigns_delete', only: ['destroy']),
        ];
    }

    public function index(PaginateRequest $request, Campaign $campaign): \Illuminate\Http\Response | \Illuminate\Http\Resources\Json\AnonymousResourceCollection | \Illuminate\Contracts\Foundation\Application | \Illuminate\Contracts\Routing\ResponseFactory {
        try {
            return CampaignRestaurantResource::collection($this->campaignRestaurantService->list($request, $campaign));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function store(CampaignRestaurantRequest $request, Campaign $campaign): \Illuminate\Http\Response | CampaignRestaurantResource | \Illuminate\Contracts\Foundation\Application | \Illuminate\Contracts\Routing\ResponseFactory {
        try {
            return new CampaignRestaurantResource($this->campaignRestaurantService->store($request, $campaign));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function destroy(Campaign $campaign, CampaignRestaurant $campaignRestaurant): \Illuminate\Http\Response | \Illuminate\Contracts\Foundation\Application | \Illuminate\Contracts\Routing\ResponseFactory {
        try {
            $this->campaignRestaurantService->destroy($campaign, $campaignRestaurant);
            return response('', 202);
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function verify(CampaignRestaurantVerifyRequest $request, Campaign $campaign, CampaignRestaurant $campaignRestaurant): \Illuminate\Foundation\Application|\Illuminate\Http\Response|CampaignRestaurantResource|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return new CampaignRestaurantResource($this->campaignRestaurantService->verify($request, $campaign, $campaignRestaurant));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }
}
