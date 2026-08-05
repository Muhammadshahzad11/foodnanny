<?php

use App\Enums\PermissionType;
use App\Enums\Role as EnumRole;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

return new class extends Migration {
    public function up(): void
    {
        $now = now();

        $roles = [
            EnumRole::WAITER  => 'Waiter',
            EnumRole::CHEF    => 'Chef',
            EnumRole::CASHIER => 'Cashier',
            EnumRole::MANAGER => 'Manager',
        ];

        foreach ($roles as $id => $name) {
            $existing = Role::query()->where('name', $name)->where('guard_name', 'sanctum')->first();
            if ($existing) {
                continue;
            }

            $byId = Role::query()->find($id);
            if ($byId) {
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

        Permission::query()
            ->whereIn('name', [
                'employees',
                'employees_create',
                'employees_edit',
                'employees_delete',
                'employees_show',
            ])
            ->update(['type' => PermissionType::BOTH]);

        $employeePermissions = Permission::query()
            ->whereIn('name', [
                'employees',
                'employees_create',
                'employees_edit',
                'employees_delete',
                'employees_show',
            ])
            ->get();

        $restaurantOwner = Role::query()->find(EnumRole::RESTAURANT_OWNER);
        if ($restaurantOwner && $employeePermissions->isNotEmpty()) {
            $restaurantOwner->givePermissionTo($employeePermissions);
        }

        $staffLikePermissions = Permission::query()
            ->whereIn('name', [
                'dashboard',
                'reviews',
                'reviews_show',
                'items',
                'items_show',
                'pos',
                'pos-orders',
                'pos-orders_show',
                'online-orders',
                'coupons',
                'coupons_show',
                'campaigns-and-offers',
                'statements',
                'payouts',
                'payouts_show',
                'sales-report',
                'items-report',
            ])
            ->get();

        $manager = Role::query()->find(EnumRole::MANAGER);
        if ($manager && $staffLikePermissions->isNotEmpty()) {
            $manager->givePermissionTo($staffLikePermissions);
        }

        $cashierPermissions = Permission::query()
            ->whereIn('name', [
                'dashboard',
                'pos',
                'pos-orders',
                'pos-orders_show',
            ])
            ->get();

        $cashier = Role::query()->find(EnumRole::CASHIER);
        if ($cashier && $cashierPermissions->isNotEmpty()) {
            $cashier->givePermissionTo($cashierPermissions);
        }

        $basicPermissions = Permission::query()->whereIn('name', ['dashboard'])->get();

        foreach ([EnumRole::WAITER, EnumRole::CHEF] as $roleId) {
            $role = Role::query()->find($roleId);
            if ($role && $basicPermissions->isNotEmpty()) {
                $role->givePermissionTo($basicPermissions);
            }
        }

        if (Schema::hasTable('users') && !$this->indexExists('users', 'users_restaurant_id_index')) {
            Schema::table('users', function (Blueprint $table) {
                $table->index('restaurant_id');
            });
        }

        app()[PermissionRegistrar::class]->forgetCachedPermissions();
    }

    public function down(): void
    {
        if (Schema::hasTable('users') && $this->indexExists('users', 'users_restaurant_id_index')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropIndex(['restaurant_id']);
            });
        }

        $roleNames = ['Waiter', 'Chef', 'Cashier', 'Manager'];
        Role::query()->whereIn('name', $roleNames)->where('guard_name', 'sanctum')->delete();

        Permission::query()
            ->whereIn('name', [
                'employees',
                'employees_create',
                'employees_edit',
                'employees_delete',
                'employees_show',
            ])
            ->update(['type' => PermissionType::ADMIN]);

        app()[PermissionRegistrar::class]->forgetCachedPermissions();
    }

    private function indexExists(string $table, string $index): bool
    {
        $connection = Schema::getConnection();
        $database   = $connection->getDatabaseName();

        if ($connection->getDriverName() === 'sqlite') {
            $indexes = $connection->select("PRAGMA index_list('{$table}')");
            foreach ($indexes as $row) {
                if (($row->name ?? '') === $index) {
                    return true;
                }
            }
            return false;
        }

        $result = $connection->select(
            'SELECT COUNT(*) AS aggregate FROM information_schema.statistics WHERE table_schema = ? AND table_name = ? AND index_name = ?',
            [$database, $table, $index]
        );

        return ((int) ($result[0]->aggregate ?? 0)) > 0;
    }
};
