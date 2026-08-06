<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Admin sidebar: Dashboard → Restaurant Operations → everything else.
     */
    public function up(): void
    {
        DB::table('menus')
            ->where('url', '#')
            ->whereIn('language', ['restaurant_operations', 'operations'])
            ->update(['priority' => 21, 'updated_at' => now()]);

        DB::table('menus')->where('language', 'tables')->where('url', 'tables')
            ->update(['priority' => 22, 'updated_at' => now()]);
        DB::table('menus')->where('language', 'waiter')->where('url', 'waiter')
            ->update(['priority' => 23, 'updated_at' => now()]);
        DB::table('menus')->where('language', 'kitchen')->where('url', 'kitchen')
            ->update(['priority' => 24, 'updated_at' => now()]);
    }

    public function down(): void
    {
        DB::table('menus')
            ->where('url', '#')
            ->whereIn('language', ['restaurant_operations', 'operations'])
            ->update(['priority' => 33, 'updated_at' => now()]);

        DB::table('menus')->where('language', 'tables')->where('url', 'tables')
            ->update(['priority' => 34, 'updated_at' => now()]);
        DB::table('menus')->where('language', 'waiter')->where('url', 'waiter')
            ->update(['priority' => 35, 'updated_at' => now()]);
        DB::table('menus')->where('language', 'kitchen')->where('url', 'kitchen')
            ->update(['priority' => 36, 'updated_at' => now()]);
    }
};
