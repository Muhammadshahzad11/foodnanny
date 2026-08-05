<?php

use App\Enums\Apply;
use App\Enums\Ask;
use App\Enums\Status;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('restaurants', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('email')->nullable();
            $table->string('country_code')->nullable();
            $table->string('phone')->nullable();
            $table->string('latitude')->nullable();
            $table->string('longitude')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('zip_code')->nullable();
            $table->longText('address')->nullable();
            $table->unsignedTinyInteger('status')->default(Status::ACTIVE);
            $table->unsignedTinyInteger('current_status')->default(Status::INACTIVE);
            $table->unsignedTinyInteger('apply')->default(Apply::RESTAURANT_OWNER);
            $table->decimal('balance', 19, 6)->default(0);
            $table->decimal('online_commission', 19, 6)->nullable();
            $table->decimal('pos_commission', 19, 6)->nullable();
            $table->tinyInteger('terms_and_conditions')->default(Ask::NO);
            $table->string('creator_type',)->nullable();
            $table->bigInteger('creator_id',)->nullable();
            $table->string('editor_type',)->nullable();
            $table->bigInteger('editor_id',)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('restaurants');
    }
};
