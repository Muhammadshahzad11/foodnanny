<?php

namespace App\Http\Controllers\Frontend;


use App\Http\Resources\FrontendItemDetailsResource;
use App\Http\Resources\SimpleItemResource;
use App\Models\FrontendItem;
use App\Models\Restaurant;
use Exception;
use App\Http\Controllers\Controller;
use App\Http\Requests\PaginateRequest;
use App\Services\ItemService;

class ItemController extends Controller
{

    public ItemService $itemService;

    public function __construct(ItemService $itemService)
    {
        $this->itemService = $itemService;
    }

    public function index(PaginateRequest $request): \Illuminate\Http\Response | \Illuminate\Http\Resources\Json\AnonymousResourceCollection | \Illuminate\Contracts\Foundation\Application | \Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return SimpleItemResource::collection($this->itemService->list($request));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function searchItems(Restaurant $restaurant, PaginateRequest $request): \Illuminate\Foundation\Application|\Illuminate\Http\Response|\Illuminate\Http\Resources\Json\AnonymousResourceCollection|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return SimpleItemResource::collection($this->itemService->searchItems($restaurant, $request));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function show(FrontendItem $frontendItem): \Illuminate\Foundation\Application|\Illuminate\Http\Response|FrontendItemDetailsResource|\Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return new FrontendItemDetailsResource($this->itemService->showWithRelation($frontendItem));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }
}
