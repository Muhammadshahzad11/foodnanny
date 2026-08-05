<?php

namespace App\Http\Controllers\Admin;

use Exception;
use App\Models\User;
use App\Services\OrderService;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Resources\OrderResource;
use App\Exports\RestaurantOwnerExport;
use App\Http\Requests\PaginateRequest;
use App\Services\RestaurantOwnerService;
use App\Http\Requests\ChangeImageRequest;
use App\Http\Requests\RestaurantOwnerRequest;
use Illuminate\Routing\Controllers\Middleware;
use App\Http\Resources\RestaurantOwnerResource;
use App\Http\Requests\UserChangePasswordRequest;
use Illuminate\Routing\Controllers\HasMiddleware;

class RestaurantOwnerController extends AdminController implements HasMiddleware
{
    private RestaurantOwnerService $restaurantOwnerService;
    private OrderService $orderService;

    public function __construct(RestaurantOwnerService $restaurantOwnerService, OrderService $orderService)
    {
        parent::__construct();
        $this->restaurantOwnerService = $restaurantOwnerService;
        $this->orderService           = $orderService;
    }

    public static function middleware(): array
    {
        return [
            new Middleware('permission:restaurant-owners', only: ['index', 'export', 'changePassword', 'changeImage', 'myOrder']),
            new Middleware('permission:restaurant-owners_create', only: ['store']),
            new Middleware('permission:restaurant-owners_edit', only: ['update']),
            new Middleware('permission:restaurant-owners_delete', only: ['destroy']),
            new Middleware('permission:restaurant-owners_show', only: ['show'])
        ];
    }

    public function index(PaginateRequest $request): \Illuminate\Http\Response | \Illuminate\Http\Resources\Json\AnonymousResourceCollection | \Illuminate\Contracts\Foundation\Application | \Illuminate\Contracts\Routing\ResponseFactory {
        try {
            return RestaurantOwnerResource::collection($this->restaurantOwnerService->list($request));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function store(RestaurantOwnerRequest $request): \Illuminate\Http\Response | RestaurantOwnerResource | \Illuminate\Contracts\Foundation\Application | \Illuminate\Contracts\Routing\ResponseFactory {
        try {
            return new RestaurantOwnerResource($this->restaurantOwnerService->store($request));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function update(RestaurantOwnerRequest $request, User $restaurantOwner): \Illuminate\Http\Response | RestaurantOwnerResource | \Illuminate\Contracts\Foundation\Application | \Illuminate\Contracts\Routing\ResponseFactory {
        try {
            return new RestaurantOwnerResource($this->restaurantOwnerService->update($request, $restaurantOwner));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function destroy(User $restaurantOwner): \Illuminate\Http\Response | \Illuminate\Contracts\Foundation\Application | \Illuminate\Contracts\Routing\ResponseFactory {
        try {
            $this->restaurantOwnerService->destroy($restaurantOwner);
            return response('', 202);
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function show(User $restaurantOwner): \Illuminate\Http\Response | RestaurantOwnerResource | \Illuminate\Contracts\Foundation\Application | \Illuminate\Contracts\Routing\ResponseFactory {
        try {
            return new RestaurantOwnerResource($this->restaurantOwnerService->show($restaurantOwner));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function export(PaginateRequest $request): \Illuminate\Http\Response | \Symfony\Component\HttpFoundation\BinaryFileResponse | \Illuminate\Contracts\Foundation\Application | \Illuminate\Contracts\Routing\ResponseFactory {
        try {
            return Excel::download(new RestaurantOwnerExport($this->restaurantOwnerService, $request), 'Customer.xlsx');
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function changePassword(UserChangePasswordRequest $request, User $restaurantOwner): \Illuminate\Http\Response | RestaurantOwnerResource | \Illuminate\Contracts\Foundation\Application | \Illuminate\Contracts\Routing\ResponseFactory {
        try {
            return new RestaurantOwnerResource($this->restaurantOwnerService->changePassword($request, $restaurantOwner));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function changeImage(ChangeImageRequest $request, User $restaurantOwner): \Illuminate\Http\Response | RestaurantOwnerResource | \Illuminate\Contracts\Foundation\Application | \Illuminate\Contracts\Routing\ResponseFactory {
        try {
            if(env('DEMO')) {
                return response(['status' => false, 'message' => trans('all.message.action_is_disabled_in_demo_mode')], 422);
            }else {
               return new RestaurantOwnerResource($this->restaurantOwnerService->changeImage($request, $restaurantOwner));
            }
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function myOrder(PaginateRequest $request, User $restaurantOwner) : \Illuminate\Http\Response | \Illuminate\Http\Resources\Json\AnonymousResourceCollection | \Illuminate\Contracts\Foundation\Application | \Illuminate\Contracts\Routing\ResponseFactory {
        try {
            return OrderResource::collection($this->orderService->userOrder($request, $restaurantOwner));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }
}
