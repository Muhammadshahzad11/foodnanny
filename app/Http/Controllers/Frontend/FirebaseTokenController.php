<?php

namespace App\Http\Controllers\Frontend;


use App\Http\Requests\FirebaseTokenRequest;
use Exception;
use App\Services\FirebaseTokenService;
use App\Http\Controllers\Controller;


class FirebaseTokenController extends Controller
{
    private FirebaseTokenService $firebaseTokenService;

    public function __construct(FirebaseTokenService $firebaseTokenService)
    {
        $this->firebaseTokenService = $firebaseTokenService;
    }

    public function webToken(FirebaseTokenRequest $request) : \Illuminate\Http\Response | \Illuminate\Contracts\Foundation\Application | \Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            $this->firebaseTokenService->webToken($request);
            return response(['status' => true, 'message' => trans("all.message.token_save")]);
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function deviceToken(FirebaseTokenRequest $request) : \Illuminate\Http\Response | \Illuminate\Contracts\Foundation\Application | \Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            $this->firebaseTokenService->deviceToken($request);
            return response(['status' => true, 'message' => trans("all.message.token_save")]);
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    } 
}
