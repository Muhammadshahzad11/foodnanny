<?php

use App\Enums\Ask;
use App\Enums\Status;
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
        Schema::create('items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('restaurant_id')->constrained('restaurants');
            $table->foreignId('item_category_id')->constrained('item_categories');
            $table->foreignId('tax_id')->nullable()->constrained('taxes');
            $table->string('name');
            $table->string('slug')->unique();
            $table->longText('caution')->nullable();
            $table->longText('description')->nullable();
            $table->decimal('price', 19, 6)->default(0);
            $table->tinyInteger('status')->default(Status::ACTIVE);
            $table->tinyInteger('item_type')->nullable();
            $table->bigInteger('order')->default(1);
            $table->tinyInteger('is_halal')->default(Ask::NO); 
            $table->time('available_time_start')->nullable();
            $table->time('available_time_end')->nullable();
            $table->tinyInteger('discount_type')->nullable();
            $table->decimal('discount', 19, 6)->default(0);
            $table->integer('maximum_purchase_quantity')->nullable()->default(100);
            $table->string('creator_type',)->nullable();
            $table->bigInteger('creator_id',)->nullable();
            $table->string('editor_type',)->nullable();
            $table->bigInteger('editor_id',)->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('items');
    }
};
