<?php

namespace App\Services;

use Exception;
use App\Models\Restaurant;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Libraries\QueryExceptionLibrary;
use App\Http\Requests\RestaurantSwitchRequest;

class RestaurantSwitchService
{
    public DefaultAccessService $defaultAccessService;
    public MenuService $menuService;

    public function __construct(DefaultAccessService $defaultAccessService, MenuService $menuService)
    {
        $this->defaultAccessService = $defaultAccessService;
        $this->menuService          = $menuService;
    }

    /**
     * @throws Exception
     */
    public function index()
    {
        try {
            return Restaurant::get();
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }

    /**
     * @throws Exception
     */
    public function switch(RestaurantSwitchRequest $request): array
    {
        try {
            $user = Auth::user();
            $menu = $this->menuService->menu($user, $user->roles[0]);
            if (count($menu['restaurantPermission']) > 0 && $request->restaurant_id > 0) {
                return $this->defaultAccessService->storeOrUpdate(['restaurant_id' => $request->restaurant_id]);
            } elseif (count($menu['adminPermission']) > 0 && $request->restaurant_id == 0) {
                return $this->defaultAccessService->storeOrUpdate(['restaurant_id' => $request->restaurant_id]);
            } else {
                return $this->defaultAccessService->show();
            }
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            DB::rollBack();
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }
}
