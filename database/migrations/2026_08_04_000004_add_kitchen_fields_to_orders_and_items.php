<?php

use App\Enums\KitchenItemStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            if (!Schema::hasColumn('orders', 'kitchen_priority')) {
                $table->unsignedTinyInteger('kitchen_priority')->default(0)->after('order_note');
            }
            if (!Schema::hasColumn('orders', 'kitchen_station_id')) {
                $table->foreignId('kitchen_station_id')
                    ->nullable()
                    ->after('kitchen_priority')
                    ->constrained('kitchen_stations')
                    ->nullOnDelete();
            }
            if (!Schema::hasColumn('orders', 'kitchen_accepted_by')) {
                $table->foreignId('kitchen_accepted_by')
                    ->nullable()
                    ->after('kitchen_station_id')
                    ->constrained('users')
                    ->nullOnDelete();
            }
            if (!Schema::hasColumn('orders', 'kitchen_accepted_at')) {
                $table->timestamp('kitchen_accepted_at')->nullable()->after('kitchen_accepted_by');
            }
            if (!Schema::hasColumn('orders', 'kitchen_preparing_by')) {
                $table->foreignId('kitchen_preparing_by')
                    ->nullable()
                    ->after('kitchen_accepted_at')
                    ->constrained('users')
                    ->nullOnDelete();
            }
            if (!Schema::hasColumn('orders', 'kitchen_ready_by')) {
                $table->foreignId('kitchen_ready_by')
                    ->nullable()
                    ->after('kitchen_preparing_by')
                    ->constrained('users')
                    ->nullOnDelete();
            }
        });

        Schema::table('order_items', function (Blueprint $table) {
            if (!Schema::hasColumn('order_items', 'kitchen_status')) {
                $table->tinyInteger('kitchen_status')
                    ->nullable()
                    ->default(KitchenItemStatus::PENDING)
                    ->after('status');
            }
            if (!Schema::hasColumn('order_items', 'kitchen_station_id')) {
                $table->foreignId('kitchen_station_id')
                    ->nullable()
                    ->after('kitchen_status')
                    ->constrained('kitchen_stations')
                    ->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            if (Schema::hasColumn('order_items', 'kitchen_station_id')) {
                $table->dropConstrainedForeignId('kitchen_station_id');
            }
            if (Schema::hasColumn('order_items', 'kitchen_status')) {
                $table->dropColumn('kitchen_status');
            }
        });

        Schema::table('orders', function (Blueprint $table) {
            foreach (['kitchen_ready_by', 'kitchen_preparing_by', 'kitchen_accepted_by', 'kitchen_station_id'] as $column) {
                if (Schema::hasColumn('orders', $column)) {
                    $table->dropConstrainedForeignId($column);
                }
            }
            if (Schema::hasColumn('orders', 'kitchen_accepted_at')) {
                $table->dropColumn('kitchen_accepted_at');
            }
            if (Schema::hasColumn('orders', 'kitchen_priority')) {
                $table->dropColumn('kitchen_priority');
            }
        });
    }
};
