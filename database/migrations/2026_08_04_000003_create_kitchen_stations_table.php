<?php

use App\Enums\Status;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('kitchen_stations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('restaurant_id')->constrained('restaurants')->cascadeOnDelete();
            $table->string('name');
            $table->string('code', 50)->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->tinyInteger('status')->default(Status::ACTIVE);
            $table->timestamps();

            $table->unique(['restaurant_id', 'code']);
            $table->index(['restaurant_id', 'status']);
        });

        // Seed a Default station per existing restaurant (future multi-kitchen ready).
        $now = now();
        $restaurantIds = DB::table('restaurants')->pluck('id');
        foreach ($restaurantIds as $restaurantId) {
            DB::table('kitchen_stations')->insert([
                'restaurant_id' => $restaurantId,
                'name'          => 'Default',
                'code'          => 'default',
                'sort_order'    => 0,
                'status'        => Status::ACTIVE,
                'created_at'    => $now,
                'updated_at'    => $now,
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('kitchen_stations');
    }
};
