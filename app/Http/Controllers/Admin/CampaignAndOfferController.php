<?php

namespace App\Http\Controllers\Admin;

use Exception;
use App\Models\Offer;
use App\Models\Campaign;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\RestaurantOfferExport;
use App\Http\Requests\PaginateRequest;
use App\Exports\RestaurantCampaignExport;
use App\Services\CampaignAndOfferService;
use App\Http\Resources\RestaurantOfferResource;
use App\Http\Resources\RestaurantCampaignResource;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Routing\Controllers\HasMiddleware;

class CampaignAndOfferController extends AdminController implements HasMiddleware
{
    private CampaignAndOfferService $campaignAndOfferService;

    public function __construct(CampaignAndOfferService $campaignAndOfferService)
    {
        parent::__construct();
        $this->campaignAndOfferService = $campaignAndOfferService;
    }

    public static function middleware(): array
    {
        return [
            new Middleware('permission:campaigns-and-offers', only: ['campaignList', 'offerList', 'applyCampaign', 'leaveCampaign', 'applyOffer', 'leaveOffer', 'showCampaign', 'showOffer', 'exportCampaign', 'exportOffer']),
        ];
    }

    public function campaignList(PaginateRequest $request): \Illuminate\Http\Response | \Illuminate\Http\Resources\Json\AnonymousResourceCollection | \Illuminate\Contracts\Foundation\Application | \Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return RestaurantCampaignResource::collection($this->campaignAndOfferService->campaignList($request));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function offerList(PaginateRequest $request): \Illuminate\Http\Response | \Illuminate\Http\Resources\Json\AnonymousResourceCollection | \Illuminate\Contracts\Foundation\Application | \Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return RestaurantOfferResource::collection($this->campaignAndOfferService->offerList($request));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function applyCampaign(Campaign $campaign): \Illuminate\Foundation\Application|\Illuminate\Http\Response|\Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            $this->campaignAndOfferService->applyCampaign($campaign);
            return response('', 202);
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }
    public function leaveCampaign(Campaign $campaign): \Illuminate\Foundation\Application|\Illuminate\Http\Response|\Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            $this->campaignAndOfferService->leaveCampaign($campaign);
            return response('', 202);
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function applyOffer(Offer $offer): \Illuminate\Foundation\Application|\Illuminate\Http\Response|\Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            $this->campaignAndOfferService->applyOffer($offer);
            return response('', 202);
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function leaveOffer(Offer $offer): \Illuminate\Foundation\Application|\Illuminate\Http\Response|\Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            $this->campaignAndOfferService->leaveOffer($offer);
            return response('', 202);
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function showCampaign(Campaign $campaign): \Illuminate\Foundation\Application|\Illuminate\Http\Response|RestaurantCampaignResource|\Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return new RestaurantCampaignResource($this->campaignAndOfferService->showCampaign($campaign));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function showOffer(Offer $offer): \Illuminate\Foundation\Application|\Illuminate\Http\Response|RestaurantOfferResource|\Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return new RestaurantOfferResource($this->campaignAndOfferService->showOffer($offer));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function exportCampaign(PaginateRequest $request): \Illuminate\Http\Response | \Symfony\Component\HttpFoundation\BinaryFileResponse | \Illuminate\Contracts\Foundation\Application | \Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return Excel::download(new RestaurantCampaignExport($this->campaignAndOfferService, $request), 'Campaigns.xlsx');
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }
    public function exportOffer(PaginateRequest $request): \Illuminate\Http\Response | \Symfony\Component\HttpFoundation\BinaryFileResponse | \Illuminate\Contracts\Foundation\Application | \Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return Excel::download(new RestaurantOfferExport($this->campaignAndOfferService, $request), 'Offers.xlsx');
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }
}
