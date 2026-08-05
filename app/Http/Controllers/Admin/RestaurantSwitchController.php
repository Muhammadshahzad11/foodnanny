<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\RestaurantSwitchRequest;
use App\Http\Resources\DefaultAccessResource;
use App\Http\Resources\OnlyRestaurantResource;
use App\Services\RestaurantSwitchService;
use Exception;

class RestaurantSwitchController extends AdminController
{
    public RestaurantSwitchService $restaurantSwitchService;

    public function __construct(RestaurantSwitchService $restaurantSwitchService)
    {
        parent::__construct();
        $this->restaurantSwitchService = $restaurantSwitchService;
    }

    public function index(): \Illuminate\Foundation\Application|\Illuminate\Http\Response|\Illuminate\Http\Resources\Json\AnonymousResourceCollection|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return OnlyRestaurantResource::collection($this->restaurantSwitchService->index());
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function switch(RestaurantSwitchRequest $request): \Illuminate\Foundation\Application|\Illuminate\Http\Response|\Illuminate\Contracts\Foundation\Application|DefaultAccessResource|\Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return new DefaultAccessResource($this->restaurantSwitchService->switch($request));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }
}
