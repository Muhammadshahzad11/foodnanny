<?php

namespace App\Services;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Libraries\QueryExceptionLibrary;
use PragmaRX\Countries\Package\Countries;

class CountryCodeService
{
    /**
     * @throws Exception
     */
    public function list(): array
    {
        try {
            $countryArray = [];
            $countries    = Countries::all();
            foreach ($countries as $key => $country) {
                if (isset($country['calling_codes'][0])) {
                    $countryArray[] = (object)[
                        'country_code'       => $key,
                        'country_name'       => $country['admin'] . ' (' . $key . ')',
                        'calling_code'       => $country['calling_codes'][0] == '+1201' ? '+1' : $country['calling_codes'][0],
                        'flag'               => $country?->extra['emoji'],
                        'sprite'             => $country->flag['sprite'],
                        'flag_icon'          => $country->flag['flag-icon'],
                        'flag_icon_squared'  => $country->flag['flag-icon-squared'],
                        'world_flags_sprite' => $country->flag['world-flags-sprite']
                    ];
                }
            }
            return ['data' => $countryArray];
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }

    /**
     * @throws Exception
     */
    public function show($country)
    {
        try {
            return Countries::where('cca3', $country)->first();
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }

    /**
     * @throws Exception
     */
    public function find(Request $request)
    {
        try {
            $countryCode = [];
            $countries   = Countries::all();
            foreach ($countries as $country) {
                if (isset($country['calling_codes'][0]) && $country['calling_codes'][0] == $request->country_code) {
                    $countryCode = $country;
                }
            }

            return $countryCode;
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }
}
