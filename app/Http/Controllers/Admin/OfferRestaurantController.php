<?php

namespace App\Http\Controllers\Admin;

use Exception;
use App\Models\Offer;
use App\Models\OfferRestaurant;
use App\Http\Requests\PaginateRequest;
 use App\Http\Requests\OfferRestaurantRequest;
use App\Services\OfferRestaurantService;
use App\Http\Resources\OfferRestaurantResource;
use App\Http\Requests\OfferRestaurantVerifyRequest;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Routing\Controllers\HasMiddleware;

class OfferRestaurantController extends AdminController implements HasMiddleware
{
    public OfferRestaurantService $offerRestaurantService;

    public function __construct(OfferRestaurantService $offerRestaurantService)
    {
        parent::__construct();
        $this->offerRestaurantService = $offerRestaurantService;
    }

    public static function middleware(): array
    {
        return [
            new Middleware('permission:offers_show', only: ['index']),
            new Middleware('permission:offers_create', only: ['store', 'verify']),
            new Middleware('permission:offers_delete', only: ['destroy']),
        ];
    }

    public function index(PaginateRequest $request, Offer $offer): \Illuminate\Http\Response | \Illuminate\Http\Resources\Json\AnonymousResourceCollection | \Illuminate\Contracts\Foundation\Application | \Illuminate\Contracts\Routing\ResponseFactory {
        try {
            return OfferRestaurantResource::collection($this->offerRestaurantService->list($request, $offer));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function store(OfferRestaurantRequest $request,  Offer $offer): \Illuminate\Http\Response | OfferRestaurantResource | \Illuminate\Contracts\Foundation\Application | \Illuminate\Contracts\Routing\ResponseFactory {
        try {
            return new OfferRestaurantResource($this->offerRestaurantService->store($request, $offer));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function destroy(Offer $offer, OfferRestaurant $offerRestaurant): \Illuminate\Http\Response | \Illuminate\Contracts\Foundation\Application | \Illuminate\Contracts\Routing\ResponseFactory {
        try {
            $this->offerRestaurantService->destroy($offer, $offerRestaurant);
            return response('', 202);
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function verify(OfferRestaurantVerifyRequest $request, Offer $offer, OfferRestaurant $offerRestaurant): \Illuminate\Foundation\Application|\Illuminate\Http\Response|OfferRestaurantResource|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return new OfferRestaurantResource($this->offerRestaurantService->verify($request, $offer, $offerRestaurant));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }
}
