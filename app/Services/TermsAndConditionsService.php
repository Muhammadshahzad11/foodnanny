<?php

namespace App\Services;

use Exception;
use Illuminate\Support\Facades\Log;
use App\Libraries\QueryExceptionLibrary;
use Dipokhalder\Settings\Facades\Settings;
use App\Http\Requests\TermsAndConditionsRequest;

class TermsAndConditionsService
{
    /**
     * @throws Exception
     */
    public function list()
    {
        try {
            return Settings::group('terms_and_conditions')->all();
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }

    /**
     * @param TermsAndConditionsRequest $request
     * @return
     * @throws Exception
     */
    public function update(TermsAndConditionsRequest $request)
    {
        try {
            Settings::group('terms_and_conditions')->set($request->validated());
            return $this->list();
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }
}