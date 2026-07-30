<?php

namespace App\Http\Controllers\Admin;

use Exception;
use App\Services\PusherService;
use App\Http\Requests\PusherRequest;
use App\Http\Resources\PusherResource;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Routing\Controllers\HasMiddleware;

class PusherController extends AdminController implements HasMiddleware
{
    private PusherService $pusherService;

    public function __construct(PusherService $pusherService)
    {
        parent::__construct();
        $this->pusherService = $pusherService;
    }

    public static function middleware(): array
    {
        return [
            new Middleware('permission:system_settings', only: ['index', 'update'])
        ];
    }

    public function index(): \Illuminate\Http\Response | PusherResource | \Illuminate\Contracts\Foundation\Application | \Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return new PusherResource($this->pusherService->list());
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function update(PusherRequest $request): \Illuminate\Http\Response | PusherResource | \Illuminate\Contracts\Foundation\Application | \Illuminate\Contracts\Routing\ResponseFactory {
        try {
            return new PusherResource($this->pusherService->update($request));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }
}
