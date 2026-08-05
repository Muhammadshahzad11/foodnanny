<?php

namespace App\Http\Controllers\Admin;

use Exception;
use App\Models\ItemCategory;
use Illuminate\Http\Request;
use App\Services\ItemCategoryService;
use App\Http\Requests\PaginateRequest;
use App\Http\Requests\ItemCategoryRequest;
use App\Http\Requests\TranslationRequest;
use App\Http\Resources\AdminItemCategoryResource;
use App\Http\Resources\AdminSimpleItemCategoryResource;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
class ItemCategoryController extends AdminController implements HasMiddleware
{
    private ItemCategoryService $itemCategoryService;

    public function __construct(ItemCategoryService $itemCategory)
    {
        parent::__construct();
        $this->itemCategoryService = $itemCategory;
    }

    public static function middleware(): array
    {
        return [
            new Middleware('permission:restaurant-settings|items|items-report', only: ['index']),
            new Middleware('permission:restaurant-settings', only: ['store', 'update', 'destroy', 'show', 'saveTranslations'])
        ];
    }

    public function index(PaginateRequest $request): \Illuminate\Http\Response | \Illuminate\Http\Resources\Json\AnonymousResourceCollection | \Illuminate\Contracts\Foundation\Application | \Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return AdminSimpleItemCategoryResource::collection($this->itemCategoryService->list($request));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function store(ItemCategoryRequest $request): \Illuminate\Http\Response | AdminItemCategoryResource | \Illuminate\Contracts\Foundation\Application | \Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return new AdminItemCategoryResource($this->itemCategoryService->store($request));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function show(ItemCategory $itemCategory): \Illuminate\Http\Response | AdminItemCategoryResource | \Illuminate\Contracts\Foundation\Application | \Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return new AdminItemCategoryResource($this->itemCategoryService->show($itemCategory));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function update(ItemCategoryRequest $request, ItemCategory $itemCategory): \Illuminate\Http\Response | AdminItemCategoryResource | \Illuminate\Contracts\Foundation\Application | \Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            if (env('DEMO')) {
                return response(['status' => false, 'message' => trans('all.message.action_is_disabled_in_demo_mode')], 422);
            } else {
                return new AdminItemCategoryResource($this->itemCategoryService->update($request, $itemCategory));
            }
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function saveTranslations(TranslationRequest $request, ItemCategory $itemCategory): \Illuminate\Http\Response
    {
        try {
            if (env('DEMO')) {
                return response(['status' => false, 'message' => trans('all.message.action_is_disabled_in_demo_mode')], 422);
            }
            return $this->itemCategoryService->saveTranslations($request, $itemCategory);
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function sort(Request $request): \Illuminate\Foundation\Application|\Illuminate\Http\Response|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            $this->itemCategoryService->sort($request);
            return response('', 202);
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function destroy(ItemCategory $itemCategory): \Illuminate\Http\Response | \Illuminate\Contracts\Foundation\Application | \Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            if(env('DEMO')) {
                return response(['status' => false, 'message' => trans('all.message.action_is_disabled_in_demo_mode')], 422);
            }else {
               $this->itemCategoryService->destroy($itemCategory);
               return response('', 202);
            }
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }
}
