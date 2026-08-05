<?php

namespace App\Services;

use App\Models\FrontendSetting;
use Dipokhalder\Settings\Facades\Settings;

class SettingService
{
    private array $frontendTextKeys = [
        'frontend_hero_section_title',
        'frontend_hero_section_sub_title',
        'frontend_app_section_title',
        'frontend_about_title',
        'frontend_benefit_title',
        'frontend_restaurant_section_title',
        'frontend_restaurant_section_sub_title',
        'frontend_delivery_section_title',
        'frontend_delivery_section_sub_title',
    ];

    public function list(): array
    {
        $array = [];
        $array = array_merge($array, Settings::group('company')->all());
        $array = array_merge($array, Settings::group('site')->all());
        $array = array_merge($array, Settings::group('theme')->all());
        $array = array_merge($array, Settings::group('otp')->all());
        $array = array_merge($array, Settings::group('social_media')->all());
        $array = array_merge($array, Settings::group('delivery_setup')->all());
        $array = array_merge($array, Settings::group('notification')->all());
        $array = array_merge($array, Settings::group('frontend')->all());
        $array = array_merge($array, Settings::group('terms_and_conditions')->all());
        $array = array_merge($array, Settings::group('cookies')->all());

        $locale = request()->header('x-localization');
        if ($locale && $locale !== 'en') {
            FrontendSetting::whereIn('key', $this->frontendTextKeys)
                ->with('translations')
                ->get()
                ->each(function ($setting) use (&$array, $locale) {
                    $translation = $setting->translations
                        ->where('locale', $locale)
                        ->where('key', 'value')
                        ->first();
                    if ($translation && !empty($translation->value)) {
                        $array[$setting->key] = $translation->value;
                    }
                });
        }

        return $array;
    }

}
