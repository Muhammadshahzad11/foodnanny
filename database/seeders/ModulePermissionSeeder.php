<?php

namespace Database\Seeders;

use App\Enums\PermissionType;
use App\Enums\Role as EnumRole;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

/**
 * Idempotent: restores kitchen/waiter permissions + role grants.
 * Needed after permission tables are truncated/reseeded (migrations won't re-run).
 */
class ModulePermissionSeeder extends Seeder
{
    public function run(): void
    {
        $waiterParent = $this->ensurePermission([
            'title' => 'Waiter',
            'name'  => 'waiter',
            'url'   => 'waiter',
            'type'  => PermissionType::BOTH,
        ], 0);

        foreach ([
            ['title' => 'Waiter Dashboard', 'name' => 'waiter_dashboard', 'url' => 'waiter'],
            ['title' => 'Waiter Tables', 'name' => 'waiter_tables', 'url' => 'waiter/tables'],
            ['title' => 'Waiter Orders', 'name' => 'waiter_orders', 'url' => 'waiter/orders'],
            ['title' => 'Waiter Orders Create', 'name' => 'waiter_orders_create', 'url' => 'waiter/orders/create'],
            ['title' => 'Waiter Orders Edit', 'name' => 'waiter_orders_edit', 'url' => 'waiter/orders/edit'],
            ['title' => 'Waiter Orders Send', 'name' => 'waiter_orders_send', 'url' => 'waiter/orders/send'],
            ['title' => 'Waiter Orders Cancel Draft', 'name' => 'waiter_orders_cancel_draft', 'url' => 'waiter/orders/cancel-draft'],
        ] as $row) {
            $this->ensurePermission($row + ['type' => PermissionType::BOTH], $waiterParent->id);
        }

        $kitchenParent = $this->ensurePermission([
            'title' => 'Kitchen',
            'name'  => 'kitchen',
            'url'   => 'kitchen',
            'type'  => PermissionType::BOTH,
        ], 0);

        foreach ([
            ['title' => 'Kitchen Dashboard', 'name' => 'kitchen_dashboard', 'url' => 'kitchen'],
            ['title' => 'Kitchen View', 'name' => 'kitchen_view', 'url' => 'kitchen/queue'],
            ['title' => 'Kitchen Accept', 'name' => 'kitchen_accept', 'url' => 'kitchen/accept'],
            ['title' => 'Kitchen Prepare', 'name' => 'kitchen_prepare', 'url' => 'kitchen/prepare'],
            ['title' => 'Kitchen Ready', 'name' => 'kitchen_ready', 'url' => 'kitchen/ready'],
            ['title' => 'Kitchen Print', 'name' => 'kitchen_print', 'url' => 'kitchen/print'],
            ['title' => 'Kitchen Reject', 'name' => 'kitchen_reject', 'url' => 'kitchen/reject'],
            ['title' => 'Kitchen Cancel', 'name' => 'kitchen_cancel', 'url' => 'kitchen/cancel'],
        ] as $row) {
            $this->ensurePermission($row + ['type' => PermissionType::BOTH], $kitchenParent->id);
        }

        $waiterPerms = Permission::query()
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

        $kitchenPerms = Permission::query()
            ->whereIn('name', [
                'kitchen',
                'kitchen_dashboard',
                'kitchen_view',
                'kitchen_accept',
                'kitchen_prepare',
                'kitchen_ready',
                'kitchen_print',
                'kitchen_reject',
                'kitchen_cancel',
            ])
            ->get();

        foreach ([EnumRole::ADMIN, EnumRole::RESTAURANT_OWNER, EnumRole::MANAGER, EnumRole::WAITER] as $roleId) {
            $role = Role::query()->find($roleId);
            if ($role && $waiterPerms->isNotEmpty()) {
                $role->givePermissionTo($waiterPerms);
            }
        }

        foreach ([EnumRole::ADMIN, EnumRole::RESTAURANT_OWNER, EnumRole::MANAGER, EnumRole::CHEF] as $roleId) {
            $role = Role::query()->find($roleId);
            if ($role && $kitchenPerms->isNotEmpty()) {
                $role->givePermissionTo($kitchenPerms);
            }
        }

        $admin = Role::query()->find(EnumRole::ADMIN);
        $admin?->givePermissionTo(Permission::all());

        app()[PermissionRegistrar::class]->forgetCachedPermissions();
    }

    private function ensurePermission(array $data, int $parentId): Permission
    {
        $permission = Permission::query()
            ->where('name', $data['name'])
            ->where('guard_name', 'sanctum')
            ->first();

        if ($permission) {
            $permission->forceFill([
                'title' => $data['title'],
                'url'   => $data['url'],
                'type'  => $data['type'],
                'parent'=> $parentId,
            ])->save();

            return $permission;
        }

        return Permission::query()->create([
            'title'      => $data['title'],
            'name'       => $data['name'],
            'type'       => $data['type'],
            'guard_name' => 'sanctum',
            'url'        => $data['url'],
            'parent'     => $parentId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
