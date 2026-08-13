<?php

namespace Database\Seeders;


use App\Enums\Activity;
use App\Enums\CurrencyPosition;
use Dipokhalder\EnvEditor\EnvEditor;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;
use Dipokhalder\Settings\Facades\Settings;

class SiteTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        $envService = new EnvEditor();
        Settings::group('site')->set([
            'site_date_format'                                        => 'd-m-Y',
            'site_time_format'                                        => 'h:i A',
            'site_default_timezone'                                   => 'Asia/Dhaka',
            'site_default_currency'                                   => 1,
            'site_default_ai_agent'                                   => $envService->getValue('DEMO') ? 1 : 0,
            'site_default_ai_data_generation_limit'                   => 100,
            'site_default_currency_symbol'                            => '$',
            'site_currency_position'                                  => CurrencyPosition::LEFT,
            'site_digit_after_decimal_point'                          => 2,
            'site_email_verification'                                 => Activity::ENABLE,
            'site_phone_verification'                                 => Activity::ENABLE,
            'site_default_language'                                   => 1,
            'site_google_map_key'                                     => 'AIzaSyBvRR2Xoh_6-RY8-6WkU4JE9M9zg1LaL-I',
            'site_copyright'                                          => $envService->getValue('DEMO') ? '© Cost to Cost Foods 2026, All Rights Reserved.' : '',
            'site_language_switch'                                    => Activity::ENABLE,
            'site_app_debug'                                          => Activity::DISABLE,
            'site_auto_localization'                                  => Activity::ENABLE,
            'site_auto_update'                                        => Activity::DISABLE,
            'site_cash_on_delivery'                                   => Activity::ENABLE,
            'site_online_payment_gateway'                             => $envService->getValue('DEMO') ? Activity::ENABLE : Activity::DISABLE,
            'site_rider_tip'                                          => Activity::ENABLE,
            'site_cutlery'                                            => Activity::ENABLE,
            'site_default_sms_gateway'                                => 5,
            'site_default_storage'                                    => 1,
            'site_restaurant_search_radius'                           => 50000,
            'site_delivery_boy_order_radius'                          => 50000,
            'site_service_fee'                                        => 2,
            'site_default_order_commission'                           => 5,
            'site_default_delivery_commission'                        => 5,
            'site_default_pos_commission'                             => 5,
            'site_same_time_delivery_boy_maximum_orders_accept_limit' => 3,
            'site_rating_time'                                        => 3,
            'site_return_order_time'                                  => 3,
        ]);

        $envService->addData([
            'APP_DEBUG'              => 'false',
            'TIMEZONE'               => 'Asia/Dhaka',
            'CURRENCY'               => 'USD',
            'CURRENCY_SYMBOL'        => '$',
            'CURRENCY_POSITION'      => '5',
            'CURRENCY_DECIMAL_POINT' => '2',
            'DATE_FORMAT'            => 'd-m-Y',
            'TIME_FORMAT'            => 'h:i A'
        ]);
        Artisan::call('optimize:clear');
    }
}
