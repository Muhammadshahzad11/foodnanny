<?php

namespace Database\Seeders;

use App\Enums\Role as EnumRole;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RoleTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        // Migrations may insert Waiter/Chef/etc. before seeding. Reset so role IDs
        // match App\Enums\Role (Admin=1 … Manager=9).
        $tableNames = config('permission.table_names');
        Schema::disableForeignKeyConstraints();
        DB::table($tableNames['role_has_permissions'])->delete();
        DB::table($tableNames['model_has_roles'])->delete();
        DB::table($tableNames['roles'])->delete();
        Schema::enableForeignKeyConstraints();
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $now = now();
        Role::insert([
            [
                'id'         => EnumRole::ADMIN,
                'name'       => 'Admin',
                'guard_name' => 'sanctum',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id'         => EnumRole::RESTAURANT_OWNER,
                'name'       => 'Restaurant Owner',
                'guard_name' => 'sanctum',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id'         => EnumRole::DELIVERY_BOY,
                'name'       => 'Delivery Boy',
                'guard_name' => 'sanctum',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id'         => EnumRole::CUSTOMER,
                'name'       => 'Customer',
                'guard_name' => 'sanctum',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id'         => EnumRole::STAFF,
                'name'       => 'Staff',
                'guard_name' => 'sanctum',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id'         => EnumRole::WAITER,
                'name'       => 'Waiter',
                'guard_name' => 'sanctum',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id'         => EnumRole::CHEF,
                'name'       => 'Chef',
                'guard_name' => 'sanctum',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id'         => EnumRole::CASHIER,
                'name'       => 'Cashier',
                'guard_name' => 'sanctum',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id'         => EnumRole::MANAGER,
                'name'       => 'Manager',
                'guard_name' => 'sanctum',
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);
    }
}
