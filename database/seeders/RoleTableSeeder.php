<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
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
        Role::insert([
            [
                'name'       => 'Admin',
                'guard_name' => 'sanctum',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name'       => 'Restaurant Owner',
                'guard_name' => 'sanctum',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name'       => 'Delivery Boy',
                'guard_name' => 'sanctum',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name'       => 'Customer',
                'guard_name' => 'sanctum',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name'       => 'Staff',
                'guard_name' => 'sanctum',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name'       => 'Waiter',
                'guard_name' => 'sanctum',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name'       => 'Chef',
                'guard_name' => 'sanctum',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name'       => 'Cashier',
                'guard_name' => 'sanctum',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name'       => 'Manager',
                'guard_name' => 'sanctum',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
