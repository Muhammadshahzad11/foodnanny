<?php

namespace App\Http\Controllers\Frontend;


use App\Http\Resources\CategoryWithItemResource;
use App\Http\Resources\ItemCategoryMenuResource;
use App\Models\ItemCategory;
use App\Models\Restaurant;
use Exception;
use App\Http\Controllers\Controller;
use App\Services\ItemCategoryService;
use App\Http\Requests\PaginateRequest;
use App\Http\Resources\ItemCategoryResource;


class ItemCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    private ItemCategoryService $itemCategoryService;

    public function __construct(ItemCategoryService $itemCategoryService)
    {
        $this->itemCategoryService = $itemCategoryService;
    }

    public function index(PaginateRequest $request) : \Illuminate\Http\Response | \Illuminate\Http\Resources\Json\AnonymousResourceCollection | \Illuminate\Contracts\Foundation\Application | \Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return ItemCategoryResource::collection($this->itemCategoryService->list($request));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function show(ItemCategory $itemCategory) : \Illuminate\Http\Response | ItemCategoryMenuResource | \Illuminate\Contracts\Foundation\Application | \Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return new ItemCategoryMenuResource($this->itemCategoryService->show($itemCategory));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function categoryWiseItems(Restaurant $restaurant, PaginateRequest $request): \Illuminate\Foundation\Application|\Illuminate\Http\Response|\Illuminate\Http\Resources\Json\AnonymousResourceCollection|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return CategoryWithItemResource::collection($this->itemCategoryService->categoryWiseItems($restaurant, $request));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }
}
