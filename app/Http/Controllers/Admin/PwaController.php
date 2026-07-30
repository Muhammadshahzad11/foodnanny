<?php

namespace App\Http\Controllers\Admin;

use Exception;
use App\Services\PwaService;
use App\Http\Requests\PwaRequest;
use App\Http\Resources\PwaResource;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Routing\Controllers\HasMiddleware;

class PwaController extends AdminController implements HasMiddleware
{
    private PwaService $pwaService;

    public function __construct(PwaService $pwaService)
    {
        parent::__construct();
        $this->pwaService = $pwaService;
    }

    public static function middleware(): array
    {
        return [
            new Middleware('permission:system_settings', only: ['index', 'update'])
        ];
    }

    public function index(): \Illuminate\Foundation\Application|\Illuminate\Http\Response|PwaResource|\Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return new PwaResource($this->pwaService->list());
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function update(PwaRequest $request): \Illuminate\Foundation\Application|\Illuminate\Http\Response|PwaResource|\Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            if(env('DEMO')) {
                return response(['status' => false, 'message' => trans('all.message.action_is_disabled_in_demo_mode')], 422);
            }else {
                return new PwaResource($this->pwaService->update($request));
            }
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }
}
