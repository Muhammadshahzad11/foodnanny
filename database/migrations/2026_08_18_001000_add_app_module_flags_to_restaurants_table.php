<?php

use App\Enums\Ask;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('restaurants', function (Blueprint $table) {
            $table->unsignedTinyInteger('enable_pos')->default(Ask::YES)->after('highlights');
            $table->unsignedTinyInteger('enable_kitchen')->default(Ask::YES)->after('enable_pos');
            $table->unsignedTinyInteger('enable_waiter')->default(Ask::YES)->after('enable_kitchen');
        });
    }

    public function down(): void
    {
        Schema::table('restaurants', function (Blueprint $table) {
            $table->dropColumn(['enable_pos', 'enable_kitchen', 'enable_waiter']);
        });
    }
};
