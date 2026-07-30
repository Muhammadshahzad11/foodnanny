<?php

namespace App\Http\Controllers\Admin;

use Exception;
use App\Models\Cuisine;
use Illuminate\Http\Request;
use App\Services\CuisineService;
use App\Http\Requests\CuisineRequest;
use App\Http\Requests\PaginateRequest;
use App\Http\Resources\CuisineResource;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Routing\Controllers\HasMiddleware;

class CuisineController extends AdminController implements HasMiddleware
{
    private CuisineService $cuisineService;

    public function __construct(CuisineService $cuisineService)
    {
        parent::__construct();
        $this->cuisineService = $cuisineService;
    }

    public static function middleware(): array
    {
        return [
            new Middleware('permission:system_settings', only: ['store', 'update', 'destroy']),
            new Middleware('permission:system_settings|restaurants', only: ['index']),
            new Middleware('permission:restaurant-settings', only: ['allCuisine']),
        ];
    }

    public function index(PaginateRequest $request): \Illuminate\Http\Response|\Illuminate\Http\Resources\Json\AnonymousResourceCollection|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return CuisineResource::collection($this->cuisineService->list($request));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function store(CuisineRequest $request): \Illuminate\Http\Response|CuisineResource|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return new CuisineResource($this->cuisineService->store($request));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function show(Cuisine $cuisine): \Illuminate\Foundation\Application|\Illuminate\Http\Response|CuisineResource|\Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return new CuisineResource($this->cuisineService->show($cuisine));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function update(CuisineRequest $request, Cuisine $cuisine): \Illuminate\Http\Response|CuisineResource|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            if(env('DEMO')) {
                return response(['status' => false, 'message' => trans('all.message.action_is_disabled_in_demo_mode')], 422);
            }else {
              return new CuisineResource($this->cuisineService->update($request, $cuisine));
            } 
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function destroy(Cuisine $cuisine): \Illuminate\Http\Response|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            $this->cuisineService->destroy($cuisine);
            return response('', 202);
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function sort(Request $request): \Illuminate\Foundation\Application|\Illuminate\Http\Response|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            $this->cuisineService->sort($request);
            return response('', 202);
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function allCuisine(PaginateRequest $request): \Illuminate\Http\Response|\Illuminate\Http\Resources\Json\AnonymousResourceCollection|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return CuisineResource::collection($this->cuisineService->list($request));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }
}
