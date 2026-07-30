<?php

namespace App\Services;

use Exception;
use Illuminate\Support\Facades\Log;
use App\Http\Requests\PusherRequest;
use Dipokhalder\EnvEditor\EnvEditor;
use Illuminate\Support\Facades\Artisan;
use App\Libraries\QueryExceptionLibrary;
use Dipokhalder\Settings\Facades\Settings;

class PusherService
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
            return Settings::group('pusher')->all();
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }

    /**
     * @param PusherRequest $request
     * @return
     * @throws Exception
     */
    public function update(PusherRequest $request)
    {
        try {
            if (!$this->envService->getValue('DEMO')) {
                Settings::group('pusher')->set($request->validated());
                $this->envService->addData(['PUSHER_APP_ID' => $request->pusher_app_id]);
                $this->envService->addData(['PUSHER_APP_KEY' => $request->pusher_app_key]);
                $this->envService->addData(['PUSHER_APP_SECRET' => $request->pusher_app_secret]);
                $this->envService->addData(['PUSHER_APP_CLUSTER' => $request->pusher_app_cluster]);
                Artisan::call('optimize:clear');
                return $this->list();
            } else {
                throw new Exception(trans('all.message.feature_disable'), 422);
            }
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }
}
