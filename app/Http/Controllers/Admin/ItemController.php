<?php

namespace App\Http\Controllers\Admin;

use App\Http\Resources\ItemDetailsResource;
use Exception;
use App\Models\Item;
use App\Exports\ItemExport;
use App\Services\ItemService;
use App\Http\Requests\ItemRequest;
use App\Http\Resources\ItemResource;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Requests\PaginateRequest;
use App\Http\Requests\ChangeImageRequest;
use App\Http\Requests\TranslationRequest;
use Illuminate\Routing\Controllers\Middleware;
use App\Http\Resources\SimpleItemAdminResource;
use Illuminate\Routing\Controllers\HasMiddleware;

class ItemController extends AdminController implements HasMiddleware
{
    public ItemService $itemService;

    public function __construct(ItemService $itemService)
    {
        parent::__construct();
        $this->itemService = $itemService;
    }

    public static function middleware(): array
    {
        return [
            new Middleware('permission:items|items-report|pos', only: ['index']),
            new Middleware('permission:items', only: ['export']),
            new Middleware('permission:items', only: ['changeImage']),
            new Middleware('permission:items_create', only: ['store']),
            new Middleware('permission:items_edit', only: ['update']),
            new Middleware('permission:items_delete', only: ['destroy']),
            new Middleware('permission:items_show|pos', only: ['show']),
            new Middleware('permission:items_edit', only: ['saveTranslations'])
        ];
    }

    public function index(PaginateRequest $request): \Illuminate\Http\Response|\Illuminate\Http\Resources\Json\AnonymousResourceCollection|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return SimpleItemAdminResource::collection($this->itemService->list($request));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function show(Item $item): \Illuminate\Foundation\Application|\Illuminate\Http\Response|\Illuminate\Contracts\Routing\ResponseFactory|ItemDetailsResource
    {
        try {
            return new ItemDetailsResource($this->itemService->show($item));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function saveTranslations(TranslationRequest $request, Item $item): \Illuminate\Http\Response|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            if (env('DEMO')) {
                return response(['status' => false, 'message' => trans('all.message.action_is_disabled_in_demo_mode')], 422);
            }
            return $this->itemService->saveTranslations($request, $item);
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function store(ItemRequest $request): \Illuminate\Http\Response|ItemResource|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return new ItemResource($this->itemService->store($request));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function update(ItemRequest $request, Item $item): \Illuminate\Http\Response|SimpleItemAdminResource|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            if (env('DEMO')) {
                return response(['status' => false, 'message' => trans('all.message.action_is_disabled_in_demo_mode')], 422);
            } else {
                return new SimpleItemAdminResource($this->itemService->update($request, $item));
            }
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function destroy(Item $item): \Illuminate\Http\Response|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            $this->itemService->destroy($item);
            return response('', 202);
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function changeImage(ChangeImageRequest $request, Item $item): \Illuminate\Http\Response|ItemResource|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            if (env('DEMO')) {
                return response(['status' => false, 'message' => trans('all.message.action_is_disabled_in_demo_mode')], 422);
            } else {
                return new ItemResource($this->itemService->changeImage($request, $item));
            }
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function export(PaginateRequest $request): \Illuminate\Http\Response|\Symfony\Component\HttpFoundation\BinaryFileResponse|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return Excel::download(new ItemExport($this->itemService, $request), 'Item.xlsx');
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }
}
