<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('revenues', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('order_id')->nullable();
            $table->tinyInteger('type')->nullable();
            $table->tinyInteger('detail')->nullable();
            $table->string('sign')->default('+');
            $table->decimal('order_amount', 19, 6)->default(0);
            $table->decimal('revenue_amount', 19, 6)->default(0);
            $table->dateTime('date');
            $table->text('info')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('revenues');
    }
};
