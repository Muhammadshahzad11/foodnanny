<?php

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
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders');
            $table->foreignId('restaurant_id')->constrained('restaurants');
            $table->foreignId('item_id')->constrained('items');
            $table->integer('quantity')->default(1);
            $table->decimal('discount', 19, 6);
            $table->string('tax_name')->nullable();
            $table->decimal('tax_rate', 19, 6)->nullable();
            $table->tinyInteger('tax_type')->nullable();
            $table->decimal('tax_amount', 19, 6)->nullable();
            $table->decimal('price', 19, 6);
            $table->longText('item_variations')->nullable();
            $table->longText('item_extras')->nullable();
            $table->decimal('item_variation_total', 19, 6)->nullable()->default(0);
            $table->decimal('item_extra_total', 19, 6)->nullable()->default(0);
            $table->decimal('total_price', 19, 6)->nullable()->default(0);
            $table->text('instruction')->nullable();
            $table->tinyInteger('status');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};
