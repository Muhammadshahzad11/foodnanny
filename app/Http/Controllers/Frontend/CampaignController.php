<?php

namespace App\Http\Controllers\Frontend;


use App\Http\Requests\CampaignRestaurantByLatLongRadiusRequest;
use App\Http\Resources\SimpleCampaignResource;
use App\Models\Campaign;
use App\Services\CampaignService;
use Exception;
use App\Http\Controllers\Controller;

class CampaignController extends Controller
{

    private CampaignService $campaignService;

    public function __construct(CampaignService $campaignService)
    {
        $this->campaignService = $campaignService;
    }

    public function index(CampaignRestaurantByLatLongRadiusRequest $request): \Illuminate\Foundation\Application|\Illuminate\Http\Response|\Illuminate\Http\Resources\Json\AnonymousResourceCollection|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return SimpleCampaignResource::collection($this->campaignService->activeCampaign($request));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function show(CampaignRestaurantByLatLongRadiusRequest $request, Campaign $campaign): \Illuminate\Foundation\Application|\Illuminate\Http\Response|SimpleCampaignResource|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            $data = $this->campaignService->activeShowCampaign($request, $campaign);
            return $data == null ? response(['data' => (object) []]) : new SimpleCampaignResource($data);
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }
}
