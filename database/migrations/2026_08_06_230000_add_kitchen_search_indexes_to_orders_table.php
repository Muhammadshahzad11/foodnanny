<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->index('order_serial_no', 'orders_order_serial_no_index');
            $table->index('token', 'orders_token_index');
            $table->index(['restaurant_id', 'order_datetime', 'status'], 'orders_kitchen_queue_index');
            $table->index(['restaurant_id', 'source', 'order_type'], 'orders_kitchen_source_index');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropIndex('orders_order_serial_no_index');
            $table->dropIndex('orders_token_index');
            $table->dropIndex('orders_kitchen_queue_index');
            $table->dropIndex('orders_kitchen_source_index');
        });
    }
};
