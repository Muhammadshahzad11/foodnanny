<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('rider_tips', function (Blueprint $table) {
            $table->id();
            $table->string('label');
            $table->decimal('amount', 19, 6);
            $table->string('creator_type')->nullable();
            $table->unsignedBigInteger('creator_id')->nullable();
            $table->string('editor_type')->nullable();
            $table->unsignedBigInteger('editor_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rider_tips');
    }
};
