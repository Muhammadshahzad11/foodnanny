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
            $table->unsignedTinyInteger('show_important_notice')->default(Ask::YES)->after('pos_commission');
            $table->text('important_notice')->nullable()->after('show_important_notice');
            $table->text('important_notice_emphasis')->nullable()->after('important_notice');
            $table->unsignedTinyInteger('show_highlights')->default(Ask::YES)->after('important_notice_emphasis');
            $table->json('highlights')->nullable()->after('show_highlights');
        });
    }

    public function down(): void
    {
        Schema::table('restaurants', function (Blueprint $table) {
            $table->dropColumn([
                'show_important_notice',
                'important_notice',
                'important_notice_emphasis',
                'show_highlights',
                'highlights',
            ]);
        });
    }
};
