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
                ['name' => 'table_qr_view'],
                ['name' => 'table_qr_generate'],
                ['name' => 'table_qr_download'],
                ['name' => 'table_qr_regenerate'],
                ['name' => 'waiter'],
                ['name' => 'waiter_dashboard'],
                ['name' => 'waiter_tables'],
                ['name' => 'waiter_orders'],
                ['name' => 'waiter_orders_create'],
                ['name' => 'waiter_orders_edit'],
                ['name' => 'waiter_orders_send'],
                ['name' => 'waiter_orders_cancel_draft'],
                ['name' => 'kitchen'],
                ['name' => 'kitchen_dashboard'],
                ['name' => 'kitchen_view'],
                ['name' => 'kitchen_accept'],
                ['name' => 'kitchen_prepare'],
                ['name' => 'kitchen_ready'],
                ['name' => 'kitchen_print'],
                ['name' => 'kitchen_reject'],
                ['name' => 'kitchen_cancel'],
            ];
            $restaurantOwnerPermissions = Permission::whereIn(
                'name',
                collect($restaurantOwnerPermissions)->pluck('name')
            )->get();
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
            $deliveryBoyPermissions = Permission::whereIn(
                'name',
                collect($deliveryBoyPermissions)->pluck('name')
            )->get();
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
            $staffPermissions = Permission::whereIn(
                'name',
                collect($staffPermissions)->pluck('name')
            )->get();
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
                ['name' => 'table_qr_view'],
                ['name' => 'table_qr_download'],
                ['name' => 'waiter'],
                ['name' => 'waiter_dashboard'],
                ['name' => 'waiter_tables'],
                ['name' => 'waiter_orders'],
                ['name' => 'waiter_orders_create'],
                ['name' => 'waiter_orders_edit'],
                ['name' => 'waiter_orders_send'],
                ['name' => 'waiter_orders_cancel_draft'],
                ['name' => 'kitchen'],
                ['name' => 'kitchen_dashboard'],
                ['name' => 'kitchen_view'],
                ['name' => 'kitchen_accept'],
                ['name' => 'kitchen_prepare'],
                ['name' => 'kitchen_ready'],
                ['name' => 'kitchen_print'],
                ['name' => 'kitchen_reject'],
                ['name' => 'kitchen_cancel'],
                ['name' => 'restaurant-settings'],
            ];
            $manager->givePermissionTo(
                Permission::whereIn('name', collect($managerPermissions)->pluck('name'))->get()
            );
        }

        $cashier = Role::find(EnumRole::CASHIER);
        if ($cashier) {
            $cashierPermissions = [
                'dashboard',
                'pos',
                'pos-orders',
                'pos-orders_show',
            ];
            $cashier->givePermissionTo(Permission::whereIn('name', $cashierPermissions)->get());
        }

        $waiter = Role::find(EnumRole::WAITER);
        if ($waiter) {
            $waiter->givePermissionTo(Permission::whereIn('name', [
                'dashboard',
                'waiter',
                'waiter_dashboard',
                'waiter_tables',
                'waiter_orders',
                'waiter_orders_create',
                'waiter_orders_edit',
                'waiter_orders_send',
                'waiter_orders_cancel_draft',
            ])->get());
        }

        $chef = Role::find(EnumRole::CHEF);
        if ($chef) {
            $chef->givePermissionTo(Permission::whereIn('name', [
                'dashboard',
                'kitchen',
                'kitchen_dashboard',
                'kitchen_view',
                'kitchen_accept',
                'kitchen_prepare',
                'kitchen_ready',
                'kitchen_print',
                'kitchen_reject',
                'kitchen_cancel',
            ])->get());
        }
    }
}
