<?php

use App\Enums\PermissionType;
use App\Enums\Role as EnumRole;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

return new class extends Migration {
    public function up(): void
    {
        $now = now();

        $permissions = [
            [
                'title'      => 'Waiter',
                'name'       => 'waiter',
                'type'       => PermissionType::BOTH,
                'guard_name' => 'sanctum',
                'url'        => 'waiter',
            ],
            [
                'title'      => 'Waiter Dashboard',
                'name'       => 'waiter_dashboard',
                'type'       => PermissionType::BOTH,
                'guard_name' => 'sanctum',
                'url'        => 'waiter',
            ],
            [
                'title'      => 'Waiter Tables',
                'name'       => 'waiter_tables',
                'type'       => PermissionType::BOTH,
                'guard_name' => 'sanctum',
                'url'        => 'waiter/tables',
            ],
            [
                'title'      => 'Waiter Orders',
                'name'       => 'waiter_orders',
                'type'       => PermissionType::BOTH,
                'guard_name' => 'sanctum',
                'url'        => 'waiter/orders',
            ],
            [
                'title'      => 'Waiter Orders Create',
                'name'       => 'waiter_orders_create',
                'type'       => PermissionType::BOTH,
                'guard_name' => 'sanctum',
                'url'        => 'waiter/orders/create',
            ],
            [
                'title'      => 'Waiter Orders Edit',
                'name'       => 'waiter_orders_edit',
                'type'       => PermissionType::BOTH,
                'guard_name' => 'sanctum',
                'url'        => 'waiter/orders/edit',
            ],
            [
                'title'      => 'Waiter Orders Send',
                'name'       => 'waiter_orders_send',
                'type'       => PermissionType::BOTH,
                'guard_name' => 'sanctum',
                'url'        => 'waiter/orders/send',
            ],
            [
                'title'      => 'Waiter Orders Cancel Draft',
                'name'       => 'waiter_orders_cancel_draft',
                'type'       => PermissionType::BOTH,
                'guard_name' => 'sanctum',
                'url'        => 'waiter/orders/cancel-draft',
            ],
        ];

        $parentId = null;
        foreach ($permissions as $permission) {
            $exists = Permission::query()->where('name', $permission['name'])->where('guard_name', 'sanctum')->first();
            if ($exists) {
                if ($permission['name'] === 'waiter') {
                    $parentId = $exists->id;
                }
                continue;
            }

            $row = $permission + [
                'parent'     => ($permission['name'] === 'waiter') ? 0 : ($parentId ?: 0),
                'created_at' => $now,
                'updated_at' => $now,
            ];

            $created = Permission::query()->create($row);
            if ($permission['name'] === 'waiter') {
                $parentId = $created->id;
            }
        }

        if ($parentId) {
            Permission::query()
                ->whereIn('name', [
                    'waiter_dashboard',
                    'waiter_tables',
                    'waiter_orders',
                    'waiter_orders_create',
                    'waiter_orders_edit',
                    'waiter_orders_send',
                    'waiter_orders_cancel_draft',
                ])
                ->update(['parent' => $parentId]);
        }

        $allWaiterPermissions = Permission::query()
            ->whereIn('name', [
                'waiter',
                'waiter_dashboard',
                'waiter_tables',
                'waiter_orders',
                'waiter_orders_create',
                'waiter_orders_edit',
                'waiter_orders_send',
                'waiter_orders_cancel_draft',
            ])
            ->get();

        foreach ([EnumRole::ADMIN, EnumRole::RESTAURANT_OWNER, EnumRole::MANAGER, EnumRole::WAITER] as $roleId) {
            $role = Role::query()->find($roleId);
            if ($role && $allWaiterPermissions->isNotEmpty()) {
                $role->givePermissionTo($allWaiterPermissions);
            }
        }

        $menuExists = DB::table('menus')->where('url', 'waiter')->exists();
        if (!$menuExists) {
            DB::table('menus')->insert([
                'name'       => 'Waiter',
                'language'   => 'waiter',
                'url'        => 'waiter',
                'icon'       => 'lab lab-line-users',
                'priority'   => 35,
                'status'     => 1,
                'parent'     => 0,
                'type'       => 1,
                'addon'      => 10,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        app()[PermissionRegistrar::class]->forgetCachedPermissions();
    }

    public function down(): void
    {
        DB::table('menus')->where('url', 'waiter')->delete();

        $names = [
            'waiter',
            'waiter_dashboard',
            'waiter_tables',
            'waiter_orders',
            'waiter_orders_create',
            'waiter_orders_edit',
            'waiter_orders_send',
            'waiter_orders_cancel_draft',
        ];
        $permissionIds = Permission::query()->whereIn('name', $names)->pluck('id');

        if ($permissionIds->isNotEmpty()) {
            DB::table(config('permission.table_names.role_has_permissions'))
                ->whereIn('permission_id', $permissionIds)
                ->delete();
            Permission::query()->whereIn('id', $permissionIds)->delete();
        }

        app()[PermissionRegistrar::class]->forgetCachedPermissions();
    }
};
