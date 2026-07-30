<?php

namespace App\Http\Controllers\Admin;


use Exception;
use App\Enums\Status;
use App\Models\Restaurant;
use App\Exports\RestaurantExport;
use App\Services\RestaurantService;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Requests\PaginateRequest;
use App\Http\Requests\RestaurantRequest;
use App\Http\Requests\ChangeImageRequest;
use App\Http\Resources\RestaurantResource;
use App\Http\Requests\RestaurantUserRequest;
use Illuminate\Routing\Controllers\Middleware;
use App\Http\Resources\RestaurantDetailsResource;
use Illuminate\Routing\Controllers\HasMiddleware;
use App\Http\Resources\SimpleAdminRestaurantResource;

class RestaurantController extends AdminController implements HasMiddleware
{
    public RestaurantService $restaurantService;

    public function __construct(RestaurantService $restaurantService)
    {
        parent::__construct();
        $this->restaurantService = $restaurantService;
    }

    public static function middleware(): array
    {
        return [
            new Middleware('permission:restaurants|offers|campaigns', only: ['index']),
            new Middleware('permission:restaurants', only: ['export']),
            new Middleware('permission:restaurants_create', only: ['store']),
            new Middleware('permission:restaurants_edit', only: ['update']),
            new Middleware('permission:restaurants_edit', only: ['userStore']),
            new Middleware('permission:restaurants_edit', only: ['verify']),
            new Middleware('permission:restaurants_delete', only: ['destroy']),
            new Middleware('permission:restaurants_show', only: ['changeImage']),
            new Middleware('permission:restaurants_show', only: ['changeLogo']),
            new Middleware('permission:payouts|payouts_create|restaurant-owners', only: ['allRestaurant']),
            new Middleware('permission:restaurants_show', only: ['show'])
        ];
    }

    public function index(PaginateRequest $request): \Illuminate\Http\Response|\Illuminate\Http\Resources\Json\AnonymousResourceCollection|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return RestaurantResource::collection($this->restaurantService->list($request));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function show(Restaurant $restaurant): RestaurantDetailsResource|\Illuminate\Http\Response|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return new RestaurantDetailsResource($this->restaurantService->show($restaurant));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function store(RestaurantRequest $request): RestaurantDetailsResource|\Illuminate\Http\Response|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return new RestaurantDetailsResource($this->restaurantService->store($request));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function update(RestaurantRequest $request, Restaurant $restaurant): RestaurantDetailsResource|\Illuminate\Http\Response|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return new RestaurantDetailsResource($this->restaurantService->update($request, $restaurant));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function destroy(Restaurant $restaurant): \Illuminate\Http\Response|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            $this->restaurantService->destroy($restaurant);
            return response('', 202);
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function export(PaginateRequest $request): \Illuminate\Http\Response|\Symfony\Component\HttpFoundation\BinaryFileResponse|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return Excel::download(new RestaurantExport($this->restaurantService, $request), 'Restaurants.xlsx');
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function changeImage(ChangeImageRequest $request, Restaurant $restaurant): \Illuminate\Http\Response|RestaurantDetailsResource|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            if(env('DEMO')) {
                return response(['status' => false, 'message' => trans('all.message.action_is_disabled_in_demo_mode')], 422);
            }else {
              return new RestaurantDetailsResource($this->restaurantService->changeImage($request, $restaurant));
            }
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function changeLogo(ChangeImageRequest $request, Restaurant $restaurant): \Illuminate\Http\Response|RestaurantDetailsResource|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            if(env('DEMO')) {
                return response(['status' => false, 'message' => trans('all.message.action_is_disabled_in_demo_mode')], 422);
            }else {
                return new RestaurantDetailsResource($this->restaurantService->changeLogo($request, $restaurant));
            }
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function userStore(RestaurantUserRequest $request, Restaurant $restaurant): RestaurantDetailsResource|\Illuminate\Http\Response|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return new RestaurantDetailsResource($this->restaurantService->userStore($request, $restaurant));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function allRestaurant(): \Illuminate\Foundation\Application|\Illuminate\Http\Response|\Illuminate\Http\Resources\Json\AnonymousResourceCollection|\Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return SimpleAdminRestaurantResource::collection(Restaurant::where(['status' => Status::ACTIVE])->orderBy('id', 'asc')->get());
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }
}
