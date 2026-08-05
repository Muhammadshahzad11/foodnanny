<?php

namespace Database\Seeders;

use App\Enums\Role as EnumRole;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;

class RoleTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        $roles = [
            EnumRole::ADMIN            => 'Admin',
            EnumRole::RESTAURANT_OWNER => 'Restaurant Owner',
            EnumRole::DELIVERY_BOY     => 'Delivery Boy',
            EnumRole::CUSTOMER         => 'Customer',
            EnumRole::STAFF            => 'Staff',
            EnumRole::WAITER           => 'Waiter',
            EnumRole::CHEF             => 'Chef',
            EnumRole::CASHIER          => 'Cashier',
            EnumRole::MANAGER          => 'Manager',
        ];

        $now = now();

        foreach ($roles as $id => $name) {
            $existing = Role::query()->where('name', $name)->where('guard_name', 'sanctum')->first();
            if ($existing) {
                continue;
            }

            if (Role::query()->find($id)) {
                continue;
            }

            DB::table(config('permission.table_names.roles'))->insert([
                'id'         => $id,
                'name'       => $name,
                'guard_name' => 'sanctum',
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }
}
