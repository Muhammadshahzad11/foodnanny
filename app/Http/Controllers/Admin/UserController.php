<?php

namespace App\Http\Controllers\Admin;

use Exception;
use App\Services\UserService;
use App\Http\Requests\PaginateRequest;
use App\Http\Resources\SimpleUserResource;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Routing\Controllers\HasMiddleware;

class UserController extends AdminController implements HasMiddleware
{
    private UserService $userService;

    public function __construct(UserService $userService)
    {
        parent::__construct();
        $this->userService = $userService;
    }

    public static function middleware(): array
    {
        return [
            new Middleware('permission:push-notifications|push-notifications_create|push-notifications_show|pos', only: ['index']),
        ];
    }

    public function index(PaginateRequest $request): \Illuminate\Http\Response|\Illuminate\Http\Resources\Json\AnonymousResourceCollection|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return SimpleUserResource::collection($this->userService->list($request));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }
}
