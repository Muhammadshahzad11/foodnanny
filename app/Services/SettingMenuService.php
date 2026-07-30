<?php

namespace App\Services;

use App\Enums\SettingMenuType;
use Exception;
use App\Models\SettingMenu;
use Illuminate\Support\Facades\Log;
use App\Libraries\QueryExceptionLibrary;

class SettingMenuService
{

    /**
     * @throws Exception
     */
    public function system()
    {
        try {
            return SettingMenu::where(['type' => SettingMenuType::SYSTEM])->orderBy('id', 'asc')->get();
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }

    /**
     * @throws Exception
     */
    public function restaurant()
    {
        try {
            return SettingMenu::where(['type' => SettingMenuType::RESTAURANT])->orderBy('id', 'asc')->get();
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }

    /**
     * @throws Exception
     */
    public function deliveryBoy()
    {
        try {
            return SettingMenu::where(['type' => SettingMenuType::DELIVERY_BOY])->orderBy('id', 'asc')->get();
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }
}
