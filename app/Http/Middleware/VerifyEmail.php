<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\JsonResponse;

class VerifyEmail
{
    public function handle(Request $request, Closure $next): JsonResponse
    {
        if (Auth::user()->email_verified_at === null) {
            return new JsonResponse(
                [
                    'success' => false,
                    'message' => 'Please verify your email before you can continue'
                ],
                401
            );
        }
        return $next($request);
    }
}
