<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('orders') || Schema::hasColumn('orders', 'delivery_otp')) {
            return;
        }

        Schema::table('orders', function (Blueprint $table) {
            $table->string('delivery_otp', 8)->nullable()->after('is_received');
            $table->timestamp('delivery_otp_verified_at')->nullable()->after('delivery_otp');
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('orders') || !Schema::hasColumn('orders', 'delivery_otp')) {
            return;
        }

        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['delivery_otp', 'delivery_otp_verified_at']);
        });
    }
};
