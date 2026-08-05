<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Services\RestaurantTableQrService;
use Exception;
use Illuminate\Http\JsonResponse;

class TableQrResolveController extends Controller
{
    public function __construct(
        protected RestaurantTableQrService $restaurantTableQrService
    ) {
    }

    public function resolve(string $token): JsonResponse
    {
        try {
            return response()->json([
                'data' => $this->restaurantTableQrService->resolveByToken($token),
            ]);
        } catch (Exception $exception) {
            $code = (int) $exception->getCode();
            if (!in_array($code, [404, 422], true)) {
                $code = 404;
            }

            return response()->json([
                'status'  => false,
                'message' => $exception->getMessage(),
            ], $code);
        }
    }
}
