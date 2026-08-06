<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Rename Operations → Restaurant Operations and order children:
 * Tables → Waiter → Kitchen.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('menus')) {
            return;
        }

        $now = now();

        $parentId = DB::table('menus')
            ->where('url', '#')
            ->where(function ($q) {
                $q->where('language', 'operations')
                    ->orWhere('language', 'restaurant_operations')
                    ->orWhere('name', 'Operations')
                    ->orWhere('name', 'Restaurant Operations');
            })
            ->value('id');

        if (!$parentId) {
            $parentId = DB::table('menus')->insertGetId([
                'name'       => 'Restaurant Operations',
                'language'   => 'restaurant_operations',
                'url'        => '#',
                'icon'       => 'lab lab-line-restaurants',
                'priority'   => 33,
                'status'     => 1,
                'parent'     => 0,
                'type'       => 1,
                'addon'      => 10,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        } else {
            DB::table('menus')->where('id', $parentId)->update([
                'name'       => 'Restaurant Operations',
                'language'   => 'restaurant_operations',
                'icon'       => 'lab lab-line-restaurants',
                'priority'   => 33,
                'parent'     => 0,
                'status'     => 1,
                'updated_at' => $now,
            ]);
        }

        // Nest under Restaurant Operations (and detach Tables from Setup if still there)
        $childPriorities = [
            'tables'  => 34,
            'waiter'  => 35,
            'kitchen' => 36,
        ];

        foreach ($childPriorities as $url => $priority) {
            DB::table('menus')
                ->where('url', $url)
                ->update([
                    'parent'     => $parentId,
                    'priority'   => $priority,
                    'updated_at' => $now,
                ]);
        }
    }

    public function down(): void
    {
        if (!Schema::hasTable('menus')) {
            return;
        }

        $parentId = DB::table('menus')
            ->where('language', 'restaurant_operations')
            ->where('url', '#')
            ->value('id');

        if (!$parentId) {
            return;
        }

        // Restore previous loose structure: tables under Setup if present, kitchen/waiter top-level
        $setupId = DB::table('menus')->where('language', 'setup')->where('url', '#')->value('id');

        DB::table('menus')->where('url', 'tables')->update([
            'parent'   => $setupId ?: 0,
            'priority' => 95,
        ]);
        DB::table('menus')->where('url', 'waiter')->update([
            'parent'   => 0,
            'priority' => 35,
        ]);
        DB::table('menus')->where('url', 'kitchen')->update([
            'parent'   => 0,
            'priority' => 34,
        ]);

        DB::table('menus')->where('id', $parentId)->delete();
    }
};
