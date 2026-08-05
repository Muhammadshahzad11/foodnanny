<?php

namespace App\Services;

use Exception;
use Illuminate\Support\Facades\Log;
use Dipokhalder\EnvEditor\EnvEditor;
use Dipokhalder\Settings\Facades\Settings;
use App\Http\Requests\DeliverySetupRequest;
use App\Libraries\QueryExceptionLibrary;

class DeliverySetupService
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
            return Settings::group('delivery_setup')->all();
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }

    /**
     * @throws Exception
     */
    public function update(DeliverySetupRequest $request)
    {
        try {
            Settings::group('delivery_setup')->set([
                'delivery_setup_free_delivery_kilometer' => (float) $request->delivery_setup_free_delivery_kilometer,
                'delivery_setup_basic_delivery_fee'      => (float) $request->delivery_setup_basic_delivery_fee,
                'delivery_setup_charge_per_kilo'         => (float) $request->delivery_setup_charge_per_kilo
            ]);
            return $this->list();
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }
}
