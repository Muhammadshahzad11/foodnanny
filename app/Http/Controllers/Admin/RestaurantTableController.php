<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\PaginateRequest;
use App\Http\Requests\RestaurantTableRequest;
use App\Http\Requests\RestaurantTableStatusRequest;
use App\Http\Resources\RestaurantTableResource;
use App\Models\RestaurantTable;
use App\Services\RestaurantTableService;
use Exception;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class RestaurantTableController extends AdminController implements HasMiddleware
{
    public RestaurantTableService $restaurantTableService;

    public function __construct(RestaurantTableService $restaurantTableService)
    {
        parent::__construct();
        $this->restaurantTableService = $restaurantTableService;
    }

    public static function middleware(): array
    {
        return [
            new Middleware('permission:tables', only: ['index']),
            new Middleware('permission:tables_create', only: ['store']),
            new Middleware('permission:tables_edit', only: ['update', 'changeStatus']),
            new Middleware('permission:tables_delete', only: ['destroy']),
            new Middleware('permission:tables_show', only: ['show']),
        ];
    }

    public function index(PaginateRequest $request): \Illuminate\Http\Response|\Illuminate\Http\Resources\Json\AnonymousResourceCollection|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return RestaurantTableResource::collection($this->restaurantTableService->list($request));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function store(RestaurantTableRequest $request): \Illuminate\Http\Response|RestaurantTableResource|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return new RestaurantTableResource($this->restaurantTableService->store($request));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function show(RestaurantTable $restaurantTable): \Illuminate\Http\Response|RestaurantTableResource|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return new RestaurantTableResource($this->restaurantTableService->show($restaurantTable));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function update(RestaurantTableRequest $request, RestaurantTable $restaurantTable): \Illuminate\Http\Response|RestaurantTableResource|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return new RestaurantTableResource($this->restaurantTableService->update($request, $restaurantTable));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function destroy(RestaurantTable $restaurantTable): \Illuminate\Http\Response|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            $this->restaurantTableService->destroy($restaurantTable);

            return response('', 202);
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function changeStatus(RestaurantTableStatusRequest $request, RestaurantTable $restaurantTable): \Illuminate\Http\Response|RestaurantTableResource|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return new RestaurantTableResource($this->restaurantTableService->changeStatus($request, $restaurantTable));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }
}
