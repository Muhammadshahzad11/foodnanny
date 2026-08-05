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
                'title'      => 'Kitchen',
                'name'       => 'kitchen',
                'type'       => PermissionType::BOTH,
                'guard_name' => 'sanctum',
                'url'        => 'kitchen',
            ],
            [
                'title'      => 'Kitchen Dashboard',
                'name'       => 'kitchen_dashboard',
                'type'       => PermissionType::BOTH,
                'guard_name' => 'sanctum',
                'url'        => 'kitchen',
            ],
            [
                'title'      => 'Kitchen View',
                'name'       => 'kitchen_view',
                'type'       => PermissionType::BOTH,
                'guard_name' => 'sanctum',
                'url'        => 'kitchen/queue',
            ],
            [
                'title'      => 'Kitchen Accept',
                'name'       => 'kitchen_accept',
                'type'       => PermissionType::BOTH,
                'guard_name' => 'sanctum',
                'url'        => 'kitchen/accept',
            ],
            [
                'title'      => 'Kitchen Prepare',
                'name'       => 'kitchen_prepare',
                'type'       => PermissionType::BOTH,
                'guard_name' => 'sanctum',
                'url'        => 'kitchen/prepare',
            ],
            [
                'title'      => 'Kitchen Ready',
                'name'       => 'kitchen_ready',
                'type'       => PermissionType::BOTH,
                'guard_name' => 'sanctum',
                'url'        => 'kitchen/ready',
            ],
            [
                'title'      => 'Kitchen Print',
                'name'       => 'kitchen_print',
                'type'       => PermissionType::BOTH,
                'guard_name' => 'sanctum',
                'url'        => 'kitchen/print',
            ],
        ];

        $parentId = null;
        foreach ($permissions as $permission) {
            $exists = Permission::query()->where('name', $permission['name'])->where('guard_name', 'sanctum')->first();
            if ($exists) {
                if ($permission['name'] === 'kitchen') {
                    $parentId = $exists->id;
                }
                continue;
            }

            $row = $permission + [
                'parent'     => ($permission['name'] === 'kitchen') ? 0 : ($parentId ?: 0),
                'created_at' => $now,
                'updated_at' => $now,
            ];

            $created = Permission::query()->create($row);
            if ($permission['name'] === 'kitchen') {
                $parentId = $created->id;
            }
        }

        if ($parentId) {
            Permission::query()
                ->whereIn('name', [
                    'kitchen_dashboard',
                    'kitchen_view',
                    'kitchen_accept',
                    'kitchen_prepare',
                    'kitchen_ready',
                    'kitchen_print',
                ])
                ->update(['parent' => $parentId]);
        }

        $all = Permission::query()
            ->whereIn('name', [
                'kitchen',
                'kitchen_dashboard',
                'kitchen_view',
                'kitchen_accept',
                'kitchen_prepare',
                'kitchen_ready',
                'kitchen_print',
            ])
            ->get();

        foreach ([EnumRole::ADMIN, EnumRole::RESTAURANT_OWNER, EnumRole::MANAGER, EnumRole::CHEF] as $roleId) {
            $role = Role::query()->find($roleId);
            if ($role && $all->isNotEmpty()) {
                $role->givePermissionTo($all);
            }
        }

        $menuExists = DB::table('menus')->where('url', 'kitchen')->exists();
        if (!$menuExists) {
            DB::table('menus')->insert([
                'name'       => 'Kitchen',
                'language'   => 'kitchen',
                'url'        => 'kitchen',
                'icon'       => 'lab lab-line-flame',
                'priority'   => 34,
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
        DB::table('menus')->where('url', 'kitchen')->delete();

        $names = [
            'kitchen',
            'kitchen_dashboard',
            'kitchen_view',
            'kitchen_accept',
            'kitchen_prepare',
            'kitchen_ready',
            'kitchen_print',
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
