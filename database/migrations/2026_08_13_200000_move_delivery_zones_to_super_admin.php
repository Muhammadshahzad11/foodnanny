<?php

use App\Enums\PermissionType;
use App\Enums\Role as EnumRole;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

return new class extends Migration {
    public function up(): void
    {
        $now = now();

        $permission = Permission::query()
            ->where('name', 'delivery-zones')
            ->where('guard_name', 'sanctum')
            ->first();

        if (!$permission) {
            $permission = Permission::query()->create([
                'title'      => 'Delivery Zones',
                'name'       => 'delivery-zones',
                'type'       => PermissionType::ADMIN,
                'guard_name' => 'sanctum',
                'url'        => 'delivery-zones',
                'parent'     => 0,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        $admin = Role::query()->find(EnumRole::ADMIN);
        if ($admin && $permission) {
            $admin->givePermissionTo($permission);
        }

        $menuExists = DB::table('menus')->where('url', 'delivery-zones')->exists();
        $coreMenus  = DB::table('menus')->where('url', 'dashboard')->exists();
        if (!$menuExists && $coreMenus) {
            DB::table('menus')->insert([
                'name'       => 'Delivery Zones',
                'language'   => 'delivery_zones',
                'url'        => 'delivery-zones',
                'icon'       => 'lab lab-line-delivery-setup',
                'priority'   => 31,
                'status'     => 1,
                'parent'     => 0,
                'type'       => 1,
                'addon'      => 10,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        if (Schema::hasTable('setting_menus')) {
            DB::table('setting_menus')
                ->where('url', 'delivery-zones')
                ->where('type', 2)
                ->delete();
        }

        app()[PermissionRegistrar::class]->forgetCachedPermissions();
    }

    public function down(): void
    {
        DB::table('menus')->where('url', 'delivery-zones')->delete();

        if (Schema::hasTable('setting_menus')) {
            $exists = DB::table('setting_menus')
                ->where('url', 'delivery-zones')
                ->where('type', 2)
                ->exists();

            if (!$exists) {
                DB::table('setting_menus')->insert([
                    'name'       => 'Delivery Zones',
                    'language'   => 'delivery_zones',
                    'url'        => 'delivery-zones',
                    'icon'       => 'lab lab-line-delivery-setup',
                    'type'       => 2,
                    'priority'   => 858,
                    'status'     => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        $permissionIds = Permission::query()->where('name', 'delivery-zones')->pluck('id');
        if ($permissionIds->isNotEmpty()) {
            DB::table(config('permission.table_names.role_has_permissions'))
                ->whereIn('permission_id', $permissionIds)
                ->delete();
            Permission::query()->whereIn('id', $permissionIds)->delete();
        }

        app()[PermissionRegistrar::class]->forgetCachedPermissions();
    }
};
