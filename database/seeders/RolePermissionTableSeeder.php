<?php

namespace Database\Seeders;

use App\Enums\Role as EnumRole;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $adminRole = Role::find(EnumRole::ADMIN);
        $adminRole?->givePermissionTo(Permission::all());

        $restaurantOwner = Role::find(EnumRole::RESTAURANT_OWNER);
        if ($restaurantOwner) {
            $restaurantOwnerPermissions = [
                ['name' => 'dashboard'],
                ['name' => 'reviews'],
                ['name' => 'reviews_show'],
                ['name' => 'items'],
                ['name' => 'items_create'],
                ['name' => 'items_edit'],
                ['name' => 'items_delete'],
                ['name' => 'items_show'],
                ['name' => 'pos'],
                ['name' => 'pos-orders'],
                ['name' => 'pos-orders_delete'],
                ['name' => 'pos-orders_show'],
                ['name' => 'online-orders'],
                ['name' => 'coupons'],
                ['name' => 'coupons_create'],
                ['name' => 'coupons_edit'],
                ['name' => 'coupons_delete'],
                ['name' => 'coupons_show'],
                ['name' => 'campaigns-and-offers'],
                ['name' => 'statements'],
                ['name' => 'payouts'],
                ['name' => 'payouts_show'],
                ['name' => 'sales-report'],
                ['name' => 'items-report'],
                ['name' => 'restaurant-settings'],
                ['name' => 'employees'],
                ['name' => 'employees_create'],
                ['name' => 'employees_edit'],
                ['name' => 'employees_delete'],
                ['name' => 'employees_show'],
                ['name' => 'tables'],
                ['name' => 'tables_create'],
                ['name' => 'tables_edit'],
                ['name' => 'tables_delete'],
                ['name' => 'tables_show'],
            ];
            $restaurantOwnerPermissions = Permission::whereIn('name', $restaurantOwnerPermissions)->get();
            $restaurantOwner->givePermissionTo($restaurantOwnerPermissions);
        }

        $deliveryBoy = Role::find(EnumRole::DELIVERY_BOY);
        if ($deliveryBoy) {
            $deliveryBoyPermissions = [
                ['name' => 'dashboard'],
                ['name' => 'reviews'],
                ['name' => 'reviews_show'],
                ['name' => 'available-orders'],
                ['name' => 'active-orders'],
                ['name' => 'messages'],
                ['name' => 'statements'],
                ['name' => 'payouts'],
                ['name' => 'payouts_show'],
                ['name' => 'collections'],
                ['name' => 'delivery-boy-settings']
            ];
            $deliveryBoyPermissions = Permission::whereIn('name', $deliveryBoyPermissions)->get();
            $deliveryBoy->givePermissionTo($deliveryBoyPermissions);
        }

        $staff = Role::find(EnumRole::STAFF);
        if ($staff) {
            $staffPermissions = [
                ['name' => 'dashboard'],
                ['name' => 'reviews'],
                ['name' => 'reviews_show'],
                ['name' => 'items'], 
                ['name' => 'items_show'],
                ['name' => 'pos'],
                ['name' => 'pos-orders'], 
                ['name' => 'pos-orders_show'],
                ['name' => 'online-orders'],
                ['name' => 'coupons'], 
                ['name' => 'coupons_show'],
                ['name' => 'campaigns-and-offers'],
                ['name' => 'statements'],
                ['name' => 'payouts'],
                ['name' => 'payouts_show'],
                ['name' => 'sales-report'],
                ['name' => 'items-report']
            ];
            $staffPermissions = Permission::whereIn('name', $staffPermissions)->get();
            $staff->givePermissionTo($staffPermissions);
        }

        $manager = Role::find(EnumRole::MANAGER);
        if ($manager) {
            $managerPermissions = [
                ['name' => 'dashboard'],
                ['name' => 'reviews'],
                ['name' => 'reviews_show'],
                ['name' => 'items'],
                ['name' => 'items_show'],
                ['name' => 'pos'],
                ['name' => 'pos-orders'],
                ['name' => 'pos-orders_show'],
                ['name' => 'online-orders'],
                ['name' => 'coupons'],
                ['name' => 'coupons_show'],
                ['name' => 'campaigns-and-offers'],
                ['name' => 'statements'],
                ['name' => 'payouts'],
                ['name' => 'payouts_show'],
                ['name' => 'sales-report'],
                ['name' => 'items-report'],
                ['name' => 'tables'],
                ['name' => 'tables_show'],
                ['name' => 'tables_edit'],
            ];
            $manager->givePermissionTo(Permission::whereIn('name', $managerPermissions)->get());
        }

        $cashier = Role::find(EnumRole::CASHIER);
        if ($cashier) {
            $cashierPermissions = [
                ['name' => 'dashboard'],
                ['name' => 'pos'],
                ['name' => 'pos-orders'],
                ['name' => 'pos-orders_show'],
            ];
            $cashier->givePermissionTo(Permission::whereIn('name', $cashierPermissions)->get());
        }

        $basicDashboard = Permission::where('name', 'dashboard')->get();
        foreach ([EnumRole::WAITER, EnumRole::CHEF] as $roleId) {
            $role = Role::find($roleId);
            if ($role && $basicDashboard->isNotEmpty()) {
                $role->givePermissionTo($basicDashboard);
            }
        }
    }
}
