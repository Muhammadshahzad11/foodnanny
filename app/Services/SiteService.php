<?php

namespace App\Services;

use App\Models\Storage;
use Exception;
use App\Enums\Activity;
use App\Models\Currency;
use App\Http\Requests\SiteRequest;
use Illuminate\Support\Facades\Log;
use Dipokhalder\EnvEditor\EnvEditor;
use Illuminate\Support\Facades\Artisan;
use App\Libraries\QueryExceptionLibrary;
use Dipokhalder\Settings\Facades\Settings;

class SiteService
{
    public EnvEditor $envService;

    public function __construct(EnvEditor $envEditor)
    {
        $this->envService = $envEditor;
    }

    /**
     * @throws Exception
     */
    public function list()
    {
        try {
            return Settings::group('site')->all();
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }

    /**
     * @throws Exception
     */
    public function update(SiteRequest $request)
    {
        try {
            $currency = Currency::find($request->site_default_currency);
            Settings::group('site')->set(array_merge($request->validated(), [
                'site_default_currency_symbol'                            => $currency->symbol,
                'site_digit_after_decimal_point'                          => (float)$request->site_digit_after_decimal_point,
                'site_default_order_commission'                           => (float)$request->site_default_order_commission,
                'site_default_delivery_commission'                        => (float)$request->site_default_delivery_commission,
                'site_default_pos_commission'                             => (float)$request->site_default_pos_commission,
                'site_service_fee'                                        => (float)$request->site_service_fee,
                'site_restaurant_search_radius'                           => (float)$request->site_restaurant_search_radius,
                'site_delivery_boy_order_radius'                          => (float)$request->site_delivery_boy_order_radius,
                'site_same_time_delivery_boy_maximum_orders_accept_limit' => (float)$request->site_same_time_delivery_boy_maximum_orders_accept_limit,
                'site_rating_time'                                        => (float)$request->site_rating_time,
                'site_return_order_time'                                  => (float)$request->site_return_order_time,
                'site_default_ai_agent'                                   => (int)$request->site_default_ai_agent,
                'site_default_ai_data_generation_limit'                   => (int)$request->site_default_ai_data_generation_limit
            ]));
            $this->envService->addData([
                'TIMEZONE'               => $request->site_default_timezone,
                'CURRENCY'               => $currency?->code,
                'CURRENCY_SYMBOL'        => $currency?->symbol,
                'CURRENCY_POSITION'      => $request->site_currency_position,
                'CURRENCY_DECIMAL_POINT' => $request->site_digit_after_decimal_point,
                'DATE_FORMAT'            => $request->site_date_format,
                'TIME_FORMAT'            => $request->site_time_format
            ]);

            if ($request->site_default_storage > 0) {
                $storage = Storage::find($request->site_default_storage);
                if ($storage) {
                    $this->envService->addData([
                        'FILESYSTEM_DISK' => strtolower($storage->slug),
                        'MEDIA_DISK'      => strtolower($storage->slug)
                    ]);
                    if ($storage->id == 1) {
                        $this->envService->addData([
                            'MEDIA_DISK' => 'public'
                        ]);
                    }
                } else {
                    $this->envService->addData([
                        'FILESYSTEM_DISK' => 'local',
                        'MEDIA_DISK'      => 'public'
                    ]);
                }
            }

            if (!$this->envService->getValue('DEMO')) {
                $this->envService->addData([
                    'APP_DEBUG'           => $request->site_app_debug == Activity::ENABLE ? 'true' : 'false',
                    'VITE_GOOGLE_MAP_KEY' => $request->site_google_map_key
                ]);
            }

            Artisan::call('optimize:clear');
            return $this->list();
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }
}
