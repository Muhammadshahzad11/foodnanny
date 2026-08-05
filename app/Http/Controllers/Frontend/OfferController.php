<?php

namespace App\Http\Controllers\Frontend;


use App\Http\Requests\OfferRestaurantByLatLongRadiusRequest;
use App\Http\Requests\OfferRestaurantByOnlyLatLongRadiusRequest;
use App\Http\Resources\CheckOfferResource;
use App\Http\Resources\OfferResource;
use App\Http\Resources\SimpleOfferResource;
use App\Http\Resources\SimpleOfferWithRestaurantResource;
use App\Models\Offer;
use App\Models\Restaurant;
use Exception;
use App\Services\OfferService;
use App\Http\Controllers\Controller;

class OfferController extends Controller
{

    private OfferService $offerService;

    public function __construct(OfferService $offerService)
    {
        $this->offerService = $offerService;
    }

    public function activeMultiOffer(OfferRestaurantByLatLongRadiusRequest $request): \Illuminate\Foundation\Application|\Illuminate\Http\Response|\Illuminate\Http\Resources\Json\AnonymousResourceCollection|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return SimpleOfferResource::collection($this->offerService->activeMultiOffer($request));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function activeSingleOffer(OfferRestaurantByLatLongRadiusRequest $request): \Illuminate\Foundation\Application|\Illuminate\Http\Response|OfferResource|\Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            $data = $this->offerService->activeSingleOffer($request);
            return $data == null ? response(['data' => (object) []]) : new OfferResource($data);
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function activeShowOffer(OfferRestaurantByLatLongRadiusRequest $request, Offer $offer): OfferResource|\Illuminate\Foundation\Application|\Illuminate\Http\Response|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            $data = $this->offerService->activeShowOffer($request, $offer);
            return $data == null ? response(['data' => (object) []]) : new OfferResource($data);
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function activeOfferFind(OfferRestaurantByOnlyLatLongRadiusRequest $request): \Illuminate\Foundation\Application|\Illuminate\Http\Response|\Illuminate\Http\Resources\Json\AnonymousResourceCollection|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return SimpleOfferWithRestaurantResource::collection($this->offerService->activeOfferFind($request));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function activeOfferCheck(OfferRestaurantByOnlyLatLongRadiusRequest $request, Restaurant $restaurant): \Illuminate\Foundation\Application|\Illuminate\Http\Response|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|CheckOfferResource
    {
        try {
            $data = $this->offerService->activeOfferCheck($request, $restaurant);
            return $data == null ? response(['data' => (object) []]) : new CheckOfferResource($data);
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }
}
