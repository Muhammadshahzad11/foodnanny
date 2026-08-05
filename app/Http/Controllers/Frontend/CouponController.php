<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Resources\CouponDetailsResource;
use App\Http\Resources\SimpleCouponResource;
use App\Models\FrontendCoupon;
use App\Models\Restaurant;
use Exception;
use App\Services\CouponService;
use App\Http\Controllers\Controller;
use App\Http\Requests\CouponCheckRequest;

class CouponController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    private CouponService $couponService;

    public function __construct(CouponService $coupon)
    {
        $this->couponService = $coupon;
    }

    public function index(Restaurant $restaurant) : \Illuminate\Http\Response | \Illuminate\Http\Resources\Json\AnonymousResourceCollection | \Illuminate\Contracts\Foundation\Application | \Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return SimpleCouponResource::collection($this->couponService->couponDateWise($restaurant));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function show(FrontendCoupon $frontendCoupon): \Illuminate\Foundation\Application|\Illuminate\Http\Response|CouponDetailsResource|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return new CouponDetailsResource($this->couponService->frontendShow($frontendCoupon));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function couponChecking(Restaurant $restaurant, CouponCheckRequest $request): \Illuminate\Foundation\Application|\Illuminate\Http\Response|SimpleCouponResource|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return new SimpleCouponResource($this->couponService->couponChecking($restaurant, $request));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }
}
