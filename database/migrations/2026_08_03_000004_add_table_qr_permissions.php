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
        $parent = Permission::query()->where('name', 'tables')->where('guard_name', 'sanctum')->first();
        $parentId = $parent?->id ?? 0;

        $permissions = [
            [
                'title' => 'Table QR View',
                'name'  => 'table_qr_view',
                'url'   => 'tables/qr',
            ],
            [
                'title' => 'Table QR Generate',
                'name'  => 'table_qr_generate',
                'url'   => 'tables/qr/generate',
            ],
            [
                'title' => 'Table QR Download',
                'name'  => 'table_qr_download',
                'url'   => 'tables/qr/download',
            ],
            [
                'title' => 'Table QR Regenerate',
                'name'  => 'table_qr_regenerate',
                'url'   => 'tables/qr/regenerate',
            ],
        ];

        $created = collect();
        foreach ($permissions as $permission) {
            $model = Permission::query()->firstOrCreate(
                ['name' => $permission['name'], 'guard_name' => 'sanctum'],
                [
                    'title'      => $permission['title'],
                    'type'       => PermissionType::BOTH,
                    'url'        => $permission['url'],
                    'parent'     => $parentId,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]
            );
            $created->push($model);
        }

        $admin = Role::query()->find(EnumRole::ADMIN);
        $owner = Role::query()->find(EnumRole::RESTAURANT_OWNER);
        $manager = Role::query()->find(EnumRole::MANAGER);

        if ($admin) {
            $admin->givePermissionTo($created);
        }
        if ($owner) {
            $owner->givePermissionTo($created);
        }
        if ($manager) {
            $manager->givePermissionTo(
                $created->whereIn('name', ['table_qr_view', 'table_qr_download'])->values()
            );
        }

        app()[PermissionRegistrar::class]->forgetCachedPermissions();
    }

    public function down(): void
    {
        $names = ['table_qr_view', 'table_qr_generate', 'table_qr_download', 'table_qr_regenerate'];
        $ids = Permission::query()->whereIn('name', $names)->pluck('id');
        if ($ids->isNotEmpty()) {
            DB::table(config('permission.table_names.role_has_permissions'))
                ->whereIn('permission_id', $ids)
                ->delete();
            Permission::query()->whereIn('id', $ids)->delete();
        }
        app()[PermissionRegistrar::class]->forgetCachedPermissions();
    }
};
