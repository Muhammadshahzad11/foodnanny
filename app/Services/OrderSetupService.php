<?php

namespace App\Services;

use Exception;
use App\Models\OrderSetup;
use Illuminate\Support\Facades\Log;
use App\Http\Requests\OrderSetupRequest;
use App\Libraries\QueryExceptionLibrary;

class OrderSetupService
{
    /**
     * @throws Exception
     */
    public function list()
    {
        try {
            return OrderSetup::get();
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }

    /**
     * @throws Exception
     */
    public function store(OrderSetupRequest $request)
    {
        try {
            $checkOrderSetup = OrderSetup::first();
            if ($checkOrderSetup) {
                $checkOrderSetup->update($request->validated());
            } else {
                OrderSetup::create($request->validated());
            }
            return $this->list();
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }
}
