<?php

namespace App\Http\Controllers\Admin;

use App\Http\Resources\DefaultRestaurantResource;
use Exception;
use App\Services\MyRestaurantService;
use App\Http\Requests\ChangeImageRequest;
use App\Http\Requests\MyRestaurantRequest;
use App\Http\Requests\CurrentStatusRequest;
use App\Http\Resources\MyRestaurantResource;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Routing\Controllers\HasMiddleware;

class MyRestaurantController extends AdminController implements HasMiddleware
{
    public MyRestaurantService $myRestaurantService;

    public function __construct(MyRestaurantService $myRestaurantService)
    {
        parent::__construct();
        $this->myRestaurantService = $myRestaurantService;
    }

    public static function middleware(): array
    {
        return [
            new Middleware('permission:restaurant-settings', only: ['index', 'update', 'changeImage', 'changeLogo', 'currentStatus'])
        ];
    }

    public function defaultRestaurant(): \Illuminate\Foundation\Application|\Illuminate\Http\Response|DefaultRestaurantResource|\Illuminate\Contracts\Routing\ResponseFactory|null
    {
        try {
            $response = $this->myRestaurantService->list();
            return $response ? new DefaultRestaurantResource($response) : null;
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function index(): \Illuminate\Http\Response|MyRestaurantResource|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return new MyRestaurantResource($this->myRestaurantService->list());
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function update(MyRestaurantRequest $request): \Illuminate\Http\Response|MyRestaurantResource|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return new MyRestaurantResource($this->myRestaurantService->update($request));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function changeImage(ChangeImageRequest $request): \Illuminate\Http\Response|MyRestaurantResource|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            if(env('DEMO')) {
                return response(['status' => false, 'message' => trans('all.message.action_is_disabled_in_demo_mode')], 422);
            }else {
                return new MyRestaurantResource($this->myRestaurantService->changeImage($request));
            }
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function changeLogo(ChangeImageRequest $request): \Illuminate\Http\Response|MyRestaurantResource|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            if(env('DEMO')) {
                return response(['status' => false, 'message' => trans('all.message.action_is_disabled_in_demo_mode')], 422);
            }else {
                return new MyRestaurantResource($this->myRestaurantService->changeLogo($request));
            }
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function currentStatus(CurrentStatusRequest $currentStatusRequest): \Illuminate\Foundation\Application|\Illuminate\Http\Response|MyRestaurantResource|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return new MyRestaurantResource($this->myRestaurantService->currentStatus($currentStatusRequest));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }
}
