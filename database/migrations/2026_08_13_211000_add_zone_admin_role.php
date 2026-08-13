<?php

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
        if (!DB::table('roles')->where('id', EnumRole::ZONE_ADMIN)->exists()) {
            DB::table('roles')->insert([
                'id'         => EnumRole::ZONE_ADMIN,
                'name'       => 'Zone Admin',
                'guard_name' => 'sanctum',
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        $zoneAdmin = Role::query()->find(EnumRole::ZONE_ADMIN);
        $permNames = [
            'dashboard',
            'restaurants',
            'restaurants_create',
            'restaurants_edit',
            'restaurants_show',
            'restaurant-owners',
            'delivery-zones',
            'online-orders',
            'pos',
            'pos-orders',
            'items',
            'items_create',
            'items_edit',
            'items_delete',
            'items_show',
            'customers',
            'customers_show',
            'delivery-boys',
            'delivery-boys_create',
            'delivery-boys_edit',
            'delivery-boys_show',
            'coupons',
            'coupons_create',
            'coupons_edit',
            'coupons_delete',
            'coupons_show',
            'campaigns-and-offers',
            'sales-report',
            'items-report',
            'payouts',
            'payouts_show',
            'employees',
            'employees_create',
            'employees_edit',
            'employees_show',
            'reviews',
            'reviews_show',
            'restaurant-settings',
        ];

        $permissions = Permission::query()->whereIn('name', $permNames)->get();
        if ($zoneAdmin && $permissions->isNotEmpty()) {
            $zoneAdmin->syncPermissions($permissions);
        }

        app()[PermissionRegistrar::class]->forgetCachedPermissions();
    }

    public function down(): void
    {
        $role = Role::query()->find(EnumRole::ZONE_ADMIN);
        if ($role && $role->name === 'Zone Admin') {
            $role->delete();
        }
        app()[PermissionRegistrar::class]->forgetCachedPermissions();
    }
};
