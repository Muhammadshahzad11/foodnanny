<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            if (!Schema::hasColumn('orders', 'waiter_id')) {
                $table->foreignId('waiter_id')
                    ->nullable()
                    ->after('table_id')
                    ->constrained('users')
                    ->nullOnDelete();
            }

            if (!Schema::hasColumn('orders', 'order_note')) {
                $table->text('order_note')->nullable()->after('reason');
            }
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            if (Schema::hasColumn('orders', 'waiter_id')) {
                $table->dropConstrainedForeignId('waiter_id');
            }
            if (Schema::hasColumn('orders', 'order_note')) {
                $table->dropColumn('order_note');
            }
        });
    }
};
