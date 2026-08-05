<?php

namespace App\Services;
 
use Exception;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Libraries\QueryExceptionLibrary;
use App\Http\Requests\FirebaseTokenRequest;


class FirebaseTokenService
{

    /**
     * @throws Exception
     */
    public function webToken(FirebaseTokenRequest $request): void
    {
        try {
            $user = User::find(auth()->user()->id);
            $user->web_token = $request->token;
            $user->save();
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }
    /**
     * @throws Exception
     */
    public function deviceToken(FirebaseTokenRequest $request): void
    {
        try {
            $user = User::find(auth()->user()->id);
            $user->device_token = $request->token;
            $user->save();
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }
 
}
