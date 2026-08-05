<?php

use App\Enums\Ask;
use App\Enums\MenuType;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('menus', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('language')->nullable();
            $table->string('url');
            $table->string('icon');
            $table->unsignedTinyInteger('status');
            $table->unsignedInteger('parent')->default(0);
            $table->unsignedInteger('type')->default(MenuType::BACKEND);
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
        Schema::dropIfExists('menus');
    }
};
