<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pwas', function (Blueprint $table) {
            $table->string('name')->nullable()->after('id');
            $table->string('short_name')->nullable()->after('name');
            $table->text('description')->nullable()->after('short_name');
            $table->string('theme_color', 32)->default('#148A3C')->after('description');
            $table->string('background_color', 32)->default('#FFFFFF')->after('theme_color');
            $table->string('orientation', 32)->default('any')->after('background_color');
            $table->string('display_mode', 32)->default('standalone')->after('orientation');
            $table->boolean('offline_mode')->default(true)->after('display_mode');
            $table->boolean('auto_update')->default(true)->after('offline_mode');
            $table->string('cache_strategy', 64)->default('balanced')->after('auto_update');
            $table->boolean('enable_install_popup')->default(true)->after('cache_strategy');
            $table->unsignedInteger('popup_delay_seconds')->default(3)->after('enable_install_popup');
            $table->unsignedInteger('popup_frequency_hours')->default(24)->after('popup_delay_seconds');
            $table->unsignedInteger('cache_version')->default(1)->after('popup_frequency_hours');
            $table->timestamp('force_updated_at')->nullable()->after('cache_version');
        });
    }

    public function down(): void
    {
        Schema::table('pwas', function (Blueprint $table) {
            $table->dropColumn([
                'name',
                'short_name',
                'description',
                'theme_color',
                'background_color',
                'orientation',
                'display_mode',
                'offline_mode',
                'auto_update',
                'cache_strategy',
                'enable_install_popup',
                'popup_delay_seconds',
                'popup_frequency_hours',
                'cache_version',
                'force_updated_at',
            ]);
        });
    }
};
