<?php

use App\Enums\Status;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('zones', function (Blueprint $table) {
            $table->id();
            $table->string('name', 190);
            $table->string('display_name', 190);
            $table->json('polygon');
            $table->tinyInteger('status')->default(Status::ACTIVE);
            $table->decimal('base_delivery_fee', 19, 6)->default(0);
            $table->decimal('min_order_amount', 19, 6)->nullable();
            $table->decimal('free_delivery_above', 19, 6)->nullable();
            $table->decimal('free_delivery_km', 19, 6)->nullable()->default(0);
            $table->decimal('extra_distance_charge', 19, 6)->nullable()->default(0);
            $table->tinyInteger('peak_enabled')->default(0);
            $table->decimal('peak_charge', 19, 6)->nullable()->default(0);
            $table->string('creator_type')->nullable();
            $table->unsignedBigInteger('creator_id')->nullable();
            $table->string('editor_type')->nullable();
            $table->unsignedBigInteger('editor_id')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        $this->addZoneId('restaurants');
        $this->addZoneId('users');
        $this->addZoneId('addresses');
        $this->addZoneId('orders');

    }

    protected function addZoneId(string $table): void
    {
        if (!Schema::hasTable($table) || Schema::hasColumn($table, 'zone_id')) {
            return;
        }

        Schema::table($table, function (Blueprint $blueprint) use ($table) {
            $blueprint->unsignedBigInteger('zone_id')->nullable()->after('id');
            $blueprint->index('zone_id');
        });
    }

    public function down(): void
    {
        foreach (['restaurants', 'users', 'addresses', 'orders'] as $table) {
            if (Schema::hasTable($table) && Schema::hasColumn($table, 'zone_id')) {
                Schema::table($table, function (Blueprint $blueprint) {
                    $blueprint->dropIndex(['zone_id']);
                    $blueprint->dropColumn('zone_id');
                });
            }
        }

        Schema::dropIfExists('zones');
    }
};
