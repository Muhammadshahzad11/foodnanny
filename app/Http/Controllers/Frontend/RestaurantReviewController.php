<?php

namespace App\Http\Controllers\Frontend;


use App\Http\Controllers\Controller;
use App\Http\Requests\RestaurantRatingRequest;
use App\Http\Resources\SimpleReviewResource;
use App\Models\Order;
use App\Services\ReviewService;
use Exception;

class RestaurantReviewController extends Controller
{
    private ReviewService $reviewService;

    public function __construct(ReviewService $reviewService)
    {
        $this->reviewService = $reviewService;
    }

    public function store(Order $order, RestaurantRatingRequest $request): \Illuminate\Foundation\Application|\Illuminate\Http\Response|SimpleReviewResource|\Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return new SimpleReviewResource($this->reviewService->frontendRestaurantReviewStore($order, $request));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function show(Order $order): \Illuminate\Foundation\Application|\Illuminate\Http\Response|SimpleReviewResource|array|\Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            $review = $this->reviewService->frontendRestaurantReviewShow($order);
            if (!blank($review)) {
                return new SimpleReviewResource($review);
            }
            return [];
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }
}
