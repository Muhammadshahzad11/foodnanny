<?php

use App\Enums\Ask;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cuisines', function (Blueprint $table) {
            $table->unsignedTinyInteger('show_on_home')->default(Ask::YES)->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('cuisines', function (Blueprint $table) {
            $table->dropColumn('show_on_home');
        });
    }
};
