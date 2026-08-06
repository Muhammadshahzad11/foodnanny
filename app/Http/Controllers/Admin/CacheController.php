<?php

namespace App\Http\Controllers\Admin;

use App\Services\CacheService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class CacheController extends AdminController implements HasMiddleware
{
    public function __construct(private readonly CacheService $cacheService)
    {
        parent::__construct();
    }

    public static function middleware(): array
    {
        return [
            new Middleware('permission:system_settings', only: ['flush']),
        ];
    }

    public function flush(Request $request): \Illuminate\Http\Response|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            $result = $this->cacheService->flush();

            return response([
                'status'  => true,
                'message' => $result['message'],
                'data'    => [
                    'cleared' => $result['cleared'],
                ],
            ]);
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }
}
