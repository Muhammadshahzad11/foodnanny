<?php

namespace App\Http\Controllers\Admin;

use Exception;
use App\Models\ItemAttribute;
use App\Http\Requests\PaginateRequest;
use App\Services\ItemAttributeService;
use App\Http\Requests\ItemAttributeRequest;
use App\Http\Resources\ItemAttributeResource;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Routing\Controllers\HasMiddleware;

class ItemAttributeController extends AdminController implements HasMiddleware
{
    public ItemAttributeService $itemAttributeService;

    public function __construct(ItemAttributeService $itemAttributeService)
    {
        parent::__construct();
        $this->itemAttributeService = $itemAttributeService;
    }

    public static function middleware(): array
    {
        return [
            new Middleware('permission:restaurant-settings|items', only: ['index']),
            new Middleware('permission:restaurant-settings', only: ['store', 'update', 'destroy'])
        ];
    }
    public function index(PaginateRequest $request): \Illuminate\Http\Response | \Illuminate\Http\Resources\Json\AnonymousResourceCollection | \Illuminate\Contracts\Foundation\Application | \Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return ItemAttributeResource::collection($this->itemAttributeService->list($request));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function store(ItemAttributeRequest $request): \Illuminate\Http\Response | ItemAttributeResource | \Illuminate\Contracts\Foundation\Application | \Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return new ItemAttributeResource($this->itemAttributeService->store($request));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function update(ItemAttributeRequest $request, ItemAttribute $itemAttribute): \Illuminate\Http\Response | ItemAttributeResource | \Illuminate\Contracts\Foundation\Application | \Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return new ItemAttributeResource($this->itemAttributeService->update($request, $itemAttribute));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function destroy(ItemAttribute $itemAttribute): \Illuminate\Http\Response | \Illuminate\Contracts\Foundation\Application | \Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            $this->itemAttributeService->destroy($itemAttribute);
            return response('', 202);
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }
}
