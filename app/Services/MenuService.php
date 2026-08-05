<?php

namespace App\Services;

use Exception;
use App\Models\Menu;
use App\Models\User;
use App\Enums\PermissionType;
use App\Libraries\AppLibrary;
use App\Enums\Role as RoleEnum;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Log;
use App\Libraries\QueryExceptionLibrary;
use Spatie\Permission\Models\Permission;
use Illuminate\Database\Eloquent\Collection;

class MenuService
{
    /**
     * @throws Exception
     */
    public function menu(User $user, Role $role): array
    {
        try {
            $adminMenus      = Menu::orderBy('priority')->orderBy('id')->get()->toArray();
            $restaurantMenus = Menu::orderBy('priority')->orderBy('id')->get()->toArray();

            $adminAllPermissions      = Permission::get();
            $restaurantAllPermissions = Permission::get();


            $adminUserModelPermissions = Permission::join("model_has_permissions", "model_has_permissions.permission_id", "=", "permissions.id")->where("model_has_permissions.model_id", $user->id)->where(function ($query) {
                $query->where('permissions.type', '=', PermissionType::BOTH)->orWhere('permissions.type', '=', PermissionType::ADMIN);
            })->get();

            $adminPermissions = Permission::join("role_has_permissions", "role_has_permissions.permission_id", "=", "permissions.id")
                ->where(["role_has_permissions.role_id" => $role->id])
                ->where(function ($query) use ($role) {
                    if ($role->id !== RoleEnum::DELIVERY_BOY) {
                        $query->whereNotIn('permissions.name', ['messages', 'available-orders', 'active-orders', 'delivery-boy-settings']);
                    }
                })->where(function ($query) {
                    $query->where('permissions.type', '=', PermissionType::BOTH)->orWhere('permissions.type', '=', PermissionType::ADMIN);
                })->get();

            $adminAllMergePermissions = new Collection;
            $adminAllMergePermissions = $adminAllMergePermissions->merge($adminPermissions);
            $adminAllMergePermissions = $adminAllMergePermissions->merge($adminUserModelPermissions);

            $adminAllMergePermissionsStatus = $adminAllMergePermissions->filter(function ($p) {
                if ($p->type == PermissionType::BOTH || $p->type == PermissionType::ADMIN) {
                    return $p;
                }
            });

            $adminAllMergePermissions = $adminAllMergePermissions->pluck('name', 'id');

            $restaurantUserModelPermissions = Permission::join("model_has_permissions", "model_has_permissions.permission_id", "=", "permissions.id")->where("model_has_permissions.model_id", $user->id)->where(function ($query) {
                $query->where('permissions.type', '=', PermissionType::BOTH)->orWhere('permissions.type', '=', PermissionType::RESTAURANT_OWNER);
            })->get();

            $restaurantPermissions = Permission::join("role_has_permissions", "role_has_permissions.permission_id", "=", "permissions.id"
            )->where(["role_has_permissions.role_id" => $role->id])->where(function ($query) {
                $query->where('permissions.type', '=', PermissionType::BOTH)->orWhere('permissions.type', '=', PermissionType::RESTAURANT_OWNER);
            })->get();

            $restaurantAllMergePermissions       = new Collection;
            $restaurantAllMergePermissions       = $restaurantAllMergePermissions->merge($restaurantPermissions);
            $restaurantAllMergePermissions       = $restaurantAllMergePermissions->merge($restaurantUserModelPermissions);
            $restaurantAllMergePermissionsStatus = $restaurantAllMergePermissions->filter(function ($p) {
                if ($p->type == PermissionType::BOTH || $p->type == PermissionType::RESTAURANT_OWNER) {
                    return $p;
                }
            });
            $restaurantAllMergePermissions       = $restaurantAllMergePermissions->pluck('name', 'id');

            $adminPermissions = AppLibrary::permissionWithAccess($adminAllPermissions, $adminAllMergePermissions);
            $adminPermissions = AppLibrary::pluck($adminPermissions, 'obj', 'url');

            $restaurantPermissions = AppLibrary::permissionWithAccess($restaurantAllPermissions, $restaurantAllMergePermissions);
            $restaurantPermissions = AppLibrary::pluck($restaurantPermissions, 'obj', 'url');
            return [
                'adminPermission'      => count($adminAllMergePermissionsStatus) > 0 ? AppLibrary::numericToAssociativeArrayBuilder(AppLibrary::menu($adminMenus, $adminPermissions)) : [],
                'restaurantPermission' => count($restaurantAllMergePermissionsStatus) > 0 ? AppLibrary::numericToAssociativeArrayBuilder(AppLibrary::menu($restaurantMenus, $restaurantPermissions)) : []
            ];
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }
}
