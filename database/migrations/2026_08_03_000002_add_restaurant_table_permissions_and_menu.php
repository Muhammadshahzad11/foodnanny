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
                'title'      => 'Tables',
                'name'       => 'tables',
                'type'       => PermissionType::BOTH,
                'guard_name' => 'sanctum',
                'url'        => 'tables',
            ],
            [
                'title'      => 'Tables Create',
                'name'       => 'tables_create',
                'type'       => PermissionType::BOTH,
                'guard_name' => 'sanctum',
                'url'        => 'tables/create',
            ],
            [
                'title'      => 'Tables Edit',
                'name'       => 'tables_edit',
                'type'       => PermissionType::BOTH,
                'guard_name' => 'sanctum',
                'url'        => 'tables/edit',
            ],
            [
                'title'      => 'Tables Delete',
                'name'       => 'tables_delete',
                'type'       => PermissionType::BOTH,
                'guard_name' => 'sanctum',
                'url'        => 'tables/delete',
            ],
            [
                'title'      => 'Tables Show',
                'name'       => 'tables_show',
                'type'       => PermissionType::BOTH,
                'guard_name' => 'sanctum',
                'url'        => 'tables/show',
            ],
        ];

        $parentId = null;
        foreach ($permissions as $permission) {
            $exists = Permission::query()->where('name', $permission['name'])->where('guard_name', 'sanctum')->first();
            if ($exists) {
                if ($permission['name'] === 'tables') {
                    $parentId = $exists->id;
                }
                continue;
            }

            $row = $permission + [
                'parent'     => ($permission['name'] === 'tables') ? 0 : ($parentId ?: 0),
                'created_at' => $now,
                'updated_at' => $now,
            ];

            $created = Permission::query()->create($row);
            if ($permission['name'] === 'tables') {
                $parentId = $created->id;
            }
        }

        if ($parentId) {
            Permission::query()
                ->whereIn('name', ['tables_create', 'tables_edit', 'tables_delete', 'tables_show'])
                ->update(['parent' => $parentId]);
        }

        $tablePermissions = Permission::query()
            ->whereIn('name', ['tables', 'tables_create', 'tables_edit', 'tables_delete', 'tables_show'])
            ->get();

        $admin = Role::query()->find(EnumRole::ADMIN);
        if ($admin && $tablePermissions->isNotEmpty()) {
            $admin->givePermissionTo($tablePermissions);
        }

        $owner = Role::query()->find(EnumRole::RESTAURANT_OWNER);
        if ($owner && $tablePermissions->isNotEmpty()) {
            $owner->givePermissionTo($tablePermissions);
        }

        $managerPermissions = Permission::query()
            ->whereIn('name', ['tables', 'tables_show', 'tables_edit'])
            ->get();
        $manager = Role::query()->find(EnumRole::MANAGER);
        if ($manager && $managerPermissions->isNotEmpty()) {
            $manager->givePermissionTo($managerPermissions);
        }

        $menuParentId = DB::table('menus')->where('url', 'restaurant-settings')->value('parent');
        if (!$menuParentId) {
            $menuParentId = DB::table('menus')->where('language', 'setup')->where('parent', 0)->value('id');
        }

        $menuExists = DB::table('menus')->where('url', 'tables')->exists();
        if (!$menuExists) {
            DB::table('menus')->insert([
                'name'       => 'Tables',
                'language'   => 'tables',
                'url'        => 'tables',
                'icon'       => 'lab lab-line-restaurants',
                'priority'   => 95,
                'status'     => 1,
                'parent'     => $menuParentId ?: 0,
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
        DB::table('menus')->where('url', 'tables')->delete();

        $names = ['tables', 'tables_create', 'tables_edit', 'tables_delete', 'tables_show'];
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
