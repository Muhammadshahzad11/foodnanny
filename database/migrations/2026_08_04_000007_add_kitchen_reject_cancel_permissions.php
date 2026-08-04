<?php

use App\Enums\PermissionType;
use App\Enums\Role as EnumRole;
use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

return new class extends Migration {
    public function up(): void
    {
        $now = now();
        $parent = Permission::query()->where('name', 'kitchen')->where('guard_name', 'sanctum')->first();

        $extra = [
            ['title' => 'Kitchen Reject', 'name' => 'kitchen_reject', 'url' => 'kitchen/reject'],
            ['title' => 'Kitchen Cancel', 'name' => 'kitchen_cancel', 'url' => 'kitchen/cancel'],
        ];

        $names = [];
        foreach ($extra as $permission) {
            $exists = Permission::query()
                ->where('name', $permission['name'])
                ->where('guard_name', 'sanctum')
                ->first();

            if ($exists) {
                $names[] = $exists->name;
                continue;
            }

            Permission::query()->create([
                'title'      => $permission['title'],
                'name'       => $permission['name'],
                'type'       => PermissionType::BOTH,
                'guard_name' => 'sanctum',
                'url'        => $permission['url'],
                'parent'     => $parent?->id ?? 0,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
            $names[] = $permission['name'];
        }

        $all = Permission::query()->whereIn('name', $names)->get();

        foreach ([EnumRole::ADMIN, EnumRole::RESTAURANT_OWNER, EnumRole::MANAGER, EnumRole::CHEF] as $roleId) {
            $role = Role::query()->find($roleId);
            if ($role && $all->isNotEmpty()) {
                $role->givePermissionTo($all);
            }
        }

        // Cancel also for managers/owners primarily; chefs get reject. Keep cancel on chef for kitchen ops.
        app()[PermissionRegistrar::class]->forgetCachedPermissions();
    }

    public function down(): void
    {
        $names = ['kitchen_reject', 'kitchen_cancel'];
        $permissionIds = Permission::query()->whereIn('name', $names)->pluck('id');

        if ($permissionIds->isNotEmpty()) {
            \Illuminate\Support\Facades\DB::table(config('permission.table_names.role_has_permissions'))
                ->whereIn('permission_id', $permissionIds)
                ->delete();
            Permission::query()->whereIn('id', $permissionIds)->delete();
        }

        app()[PermissionRegistrar::class]->forgetCachedPermissions();
    }
};
