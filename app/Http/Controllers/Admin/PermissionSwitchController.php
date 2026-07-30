<?php

namespace App\Http\Controllers\Admin;

use App\Enums\PermissionType;
use App\Http\Resources\PermissionResource;
use App\Services\DefaultAccessService;
use App\Services\PermissionService;
use Exception;
use Illuminate\Support\Facades\Auth;

class PermissionSwitchController extends AdminController
{
    public DefaultAccessService $defaultAccessService;
    public PermissionService $permissionService;

    public function __construct(DefaultAccessService $defaultAccessService, PermissionService $permissionService)
    {
        parent::__construct();
        $this->defaultAccessService = $defaultAccessService;
        $this->permissionService    = $permissionService;
    }

    public function index(): \Illuminate\Foundation\Application|\Illuminate\Http\Response|\Illuminate\Http\Resources\Json\AnonymousResourceCollection|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            $permission           = [];
            $defaultAccessService = $this->defaultAccessService->show();
            if (isset($defaultAccessService['restaurant_id'])) {
                $restaurantId = $defaultAccessService['restaurant_id'];
                $user         = Auth::user();
                $permission   = $this->permissionService->allPermission($user, $user->roles[0]);
                $permission   = $permission->filter(function ($p) use ($restaurantId) {
                    if ($restaurantId == 0 && ($p->type == PermissionType::BOTH || $p->type == PermissionType::ADMIN)) {
                        return $p;
                    } elseif ($restaurantId > 0 && ($p->type == PermissionType::BOTH || $p->type == PermissionType::RESTAURANT_OWNER)) {
                        return $p;
                    }
                });
            }
            return PermissionResource::collection($permission);
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }
}
