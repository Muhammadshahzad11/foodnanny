<?php

use App\Enums\DeliveryChargeType;
use App\Enums\Status;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('restaurant_delivery_zones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('restaurant_id')->constrained('restaurants')->cascadeOnDelete();
            $table->string('name', 190);
            $table->string('display_name', 190);
            $table->json('polygon');
            $table->tinyInteger('status')->default(Status::ACTIVE);
            $table->tinyInteger('charge_type')->default(DeliveryChargeType::PER_KM);
            $table->decimal('min_delivery_charge', 19, 6)->nullable();
            $table->decimal('max_delivery_charge', 19, 6)->nullable();
            $table->decimal('charge_per_km', 19, 6)->nullable();
            $table->decimal('max_cod_amount', 19, 6)->nullable();
            $table->decimal('additional_delivery_charge', 19, 6)->nullable()->default(0);
            $table->string('creator_type')->nullable();
            $table->unsignedBigInteger('creator_id')->nullable();
            $table->string('editor_type')->nullable();
            $table->unsignedBigInteger('editor_id')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['restaurant_id', 'status']);
            $table->index(['restaurant_id', 'name']);
        });

        if (Schema::hasTable('orders') && !Schema::hasColumn('orders', 'delivery_zone_id')) {
            Schema::table('orders', function (Blueprint $table) {
                $table->unsignedBigInteger('delivery_zone_id')->nullable()->after('delivery_fee');
                $table->index('delivery_zone_id');
            });
        }

        // Seed restaurant settings menu entry if missing (already-deployed DBs).
        if (Schema::hasTable('setting_menus')) {
            $exists = DB::table('setting_menus')
                ->where('url', 'delivery-zones')
                ->where('type', 2)
                ->exists();

            if (!$exists) {
                DB::table('setting_menus')->insert([
                    'name'       => 'Delivery Zones',
                    'language'   => 'delivery_zones',
                    'url'        => 'delivery-zones',
                    'icon'       => 'lab lab-line-delivery-setup',
                    'type'       => 2,
                    'priority'   => 858,
                    'status'     => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('orders') && Schema::hasColumn('orders', 'delivery_zone_id')) {
            Schema::table('orders', function (Blueprint $table) {
                $table->dropIndex(['delivery_zone_id']);
                $table->dropColumn('delivery_zone_id');
            });
        }

        Schema::dropIfExists('restaurant_delivery_zones');

        if (Schema::hasTable('setting_menus')) {
            DB::table('setting_menus')->where('url', 'delivery-zones')->where('type', 2)->delete();
        }
    }
};
