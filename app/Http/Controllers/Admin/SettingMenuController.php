<?php

namespace App\Http\Controllers\Admin;


use App\Http\Resources\SettingMenuResource;
use Exception;
use App\Services\SettingMenuService;



class SettingMenuController extends AdminController
{
    private SettingMenuService $settingMenuService;

    public function __construct(SettingMenuService $settingMenuService)
    {
        parent::__construct();
        $this->settingMenuService = $settingMenuService;
    }

    public function systemMenu(): \Illuminate\Http\Response|\Illuminate\Http\Resources\Json\AnonymousResourceCollection|\Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return SettingMenuResource::collection($this->settingMenuService->system());
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function restaurantMenu(): \Illuminate\Http\Response|\Illuminate\Http\Resources\Json\AnonymousResourceCollection|\Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return SettingMenuResource::collection($this->settingMenuService->restaurant());
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function deliveryBoyMenu(): \Illuminate\Http\Response|\Illuminate\Http\Resources\Json\AnonymousResourceCollection|\Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return SettingMenuResource::collection($this->settingMenuService->deliveryBoy());
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }
}
