<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('restaurant_tables', function (Blueprint $table) {
            $table->uuid('uuid')->nullable()->after('id');
            $table->string('qr_token', 64)->nullable()->after('notes');
            $table->unsignedInteger('qr_version')->default(1)->after('qr_token');
            $table->timestamp('qr_generated_at')->nullable()->after('qr_version');
            $table->string('qr_url', 500)->nullable()->after('qr_generated_at');
        });

        $tables = DB::table('restaurant_tables')->select('id')->get();
        foreach ($tables as $row) {
            DB::table('restaurant_tables')->where('id', $row->id)->update([
                'uuid' => (string) Str::uuid(),
            ]);
        }

        Schema::table('restaurant_tables', function (Blueprint $table) {
            $table->unique('uuid');
            $table->unique('qr_token');
            $table->index(['restaurant_id', 'qr_token']);
        });
    }

    public function down(): void
    {
        Schema::table('restaurant_tables', function (Blueprint $table) {
            $table->dropUnique(['uuid']);
            $table->dropUnique(['qr_token']);
            $table->dropIndex(['restaurant_id', 'qr_token']);
            $table->dropColumn(['uuid', 'qr_token', 'qr_version', 'qr_generated_at', 'qr_url']);
        });
    }
};
