<?php

namespace App\Services;

use Exception;
use App\Enums\Role;
use App\Models\DeliveryLocation;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Libraries\QueryExceptionLibrary;
use App\Http\Requests\DeliveryLocationSetupRequest;

class DeliveryLocationSetupService
{
    /**
     * @throws Exception
     */
    public function list()
    {
        try {
            return DeliveryLocation::where('user_id', Auth::user()->id)->first();
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }

    /**
     * @throws Exception
     */
    public function store(DeliveryLocationSetupRequest $request)
    {
        try {
            if(Auth::user()->getRole?->id == Role::DELIVERY_BOY) {
                $deliveryBoyLocation = DeliveryLocation::where('user_id', Auth::user()->id)->first();
                if (!blank($deliveryBoyLocation)) {
                    $location = tap($deliveryBoyLocation)->update($request->validated());
                } else {
                    $location = DeliveryLocation::create($request->validated() + ['user_id' => Auth::user()->id]);
                }

                $user = Auth::user();
                $zoneId = app(ZoneService::class)->applyDetectedZoneId(
                    $location->latitude !== null ? (float) $location->latitude : null,
                    $location->longitude !== null ? (float) $location->longitude : null
                );
                if ((int) $user->zone_id !== (int) $zoneId) {
                    $user->zone_id = $zoneId;
                    $user->save();
                }

                return $location;
            } else {
                Log::info(trans('message.role_exist'));
                throw new Exception(trans('message.role_exist'), 422);
            }
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }
}
