<?php

namespace App\Services;

use Exception;
use Illuminate\Support\Facades\Log;
use Dipokhalder\Settings\Facades\Settings;
use Stevebauman\Location\Facades\Location;
use Illuminate\Support\Facades\Cache;
use App\Enums\Activity;
use App\Models\Language;
use Throwable;

class AutoLocalizationService
{
    /**
     * @throws Exception
     */
    public function lang(): Language
    {
        try {
            $default = Language::findOrFail(
                Settings::group('site')->get('site_default_language')
            );

            if (Settings::group('site')->get('site_auto_localization') != Activity::ENABLE) {
                return $default;
            }

            $ip       = request()->ip();
            $position = Cache::remember(
                "geoip_{$ip}",
                now()->addDay(),
                fn() => Location::get($ip)
            );

            $locale = $this->locale($position?->countryCode ?? 'US');
            return Language::where('code', $locale)->first() ?? $default;
        } catch (Throwable $e) {
            Log::error($e->getMessage());
            return Language::first();
        }
    }

    private function locale($code): string
    {
        static $country = null;
        if ($country === null) {
            $country = [
                'SA' => 'ar', 'AE' => 'ar', 'QA' => 'ar', 'KW' => 'ar', 'BH' => 'ar', 'OM' => 'ar',
                'YE' => 'ar', 'JO' => 'ar', 'EG' => 'ar', 'IQ' => 'ar', 'SY' => 'ar', 'PS' => 'ar',
                'LY' => 'ar', 'DZ' => 'ar', 'MA' => 'ar', 'TN' => 'ar', 'SD' => 'ar',

                'FR' => 'fr', 'BE' => 'fr', 'CH' => 'fr', 'CA' => 'fr', 'MC' => 'fr', 'SN' => 'fr',
                'CI' => 'fr', 'MG' => 'fr',

                'DE' => 'de', 'AT' => 'de', 'LU' => 'de', 'LI' => 'de',

                'ES' => 'es', 'MX' => 'es', 'AR' => 'es', 'CO' => 'es', 'CL' => 'es', 'PE' => 'es',
                'VE' => 'es', 'EC' => 'es', 'GT' => 'es', 'CU' => 'es', 'BO' => 'es', 'PR' => 'es',

                'PT' => 'pt', 'BR' => 'pt', 'AO' => 'pt', 'MZ' => 'pt', 'CV' => 'pt', 'GW' => 'pt',
            ];
        }
        return $country[strtoupper($code)] ?? 'en';
    }
}
