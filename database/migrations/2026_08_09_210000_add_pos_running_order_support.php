<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('order_item_changes')) {
            Schema::create('order_item_changes', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('restaurant_id')->index();
                $table->unsignedBigInteger('order_id')->index();
                $table->unsignedBigInteger('order_item_id')->nullable()->index();
                $table->unsignedBigInteger('item_id')->nullable()->index();
                $table->string('item_name', 190)->nullable();
                $table->string('action', 40); // ADD, REMOVE, QUANTITY_CHANGE, VOID, CANCEL
                $table->decimal('previous_quantity', 12, 3)->default(0);
                $table->decimal('new_quantity', 12, 3)->default(0);
                $table->decimal('difference', 12, 3)->default(0);
                $table->boolean('kot_printed')->default(false);
                $table->unsignedBigInteger('kitchen_ticket_id')->nullable();
                $table->unsignedBigInteger('user_id')->nullable()->index();
                $table->text('note')->nullable();
                $table->json('meta')->nullable();
                $table->timestamps();

                $table->foreign('order_id')->references('id')->on('orders')->cascadeOnDelete();
            });
        }

        Schema::table('orders', function (Blueprint $table) {
            if (!Schema::hasColumn('orders', 'customer_name')) {
                $table->string('customer_name', 120)->nullable()->after('order_note');
            }
            if (!Schema::hasColumn('orders', 'customer_phone')) {
                $table->string('customer_phone', 40)->nullable()->after('customer_name');
            }
            if (!Schema::hasColumn('orders', 'customer_address')) {
                $table->string('customer_address', 500)->nullable()->after('customer_phone');
            }
            if (!Schema::hasColumn('orders', 'delivery_note')) {
                $table->string('delivery_note', 500)->nullable()->after('customer_address');
            }
            if (!Schema::hasColumn('orders', 'billing_requested_at')) {
                $table->timestamp('billing_requested_at')->nullable()->after('delivery_note');
            }
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_item_changes');

        Schema::table('orders', function (Blueprint $table) {
            foreach (['customer_name', 'customer_phone', 'customer_address', 'delivery_note', 'billing_requested_at'] as $col) {
                if (Schema::hasColumn('orders', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
