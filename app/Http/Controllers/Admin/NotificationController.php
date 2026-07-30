<?php

namespace App\Http\Controllers\Admin;

use Exception;
use App\Services\NotificationService;
use App\Http\Requests\NotificationRequest;
use App\Http\Resources\NotificationResource;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Routing\Controllers\HasMiddleware;

class NotificationController extends AdminController implements HasMiddleware
{
    private NotificationService $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        parent::__construct();
        $this->notificationService = $notificationService;
    }

    public static function middleware(): array
    {
        return [
            new Middleware('permission:system_settings', only: ['index', 'update'])
        ];
    }

    public function index(): \Illuminate\Http\Response | NotificationResource | \Illuminate\Contracts\Foundation\Application | \Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return new NotificationResource($this->notificationService->list());
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function update(NotificationRequest $request): \Illuminate\Http\Response | NotificationResource | \Illuminate\Contracts\Foundation\Application | \Illuminate\Contracts\Routing\ResponseFactory {
        try {
            if(env('DEMO')) {
                return response(['status' => false, 'message' => trans('all.message.action_is_disabled_in_demo_mode')], 422);
            }else {
              return new NotificationResource($this->notificationService->update($request));
            }
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }
}
