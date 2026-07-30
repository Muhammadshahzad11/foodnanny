<?php

use App\Enums\Ask;
use App\Enums\SettingMenuType;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('setting_menus', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('language')->nullable();
            $table->string('url');
            $table->string('icon');
            $table->unsignedTinyInteger('status');
            $table->unsignedInteger('type')->default(SettingMenuType::SYSTEM);
            $table->unsignedInteger('priority')->default(100);
            $table->unsignedTinyInteger('addon')->default(Ask::NO);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('setting_menus');
    }
};
