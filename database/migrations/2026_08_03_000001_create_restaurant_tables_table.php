<?php

use App\Enums\TableStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('restaurant_tables', function (Blueprint $table) {
            $table->id();
            $table->foreignId('restaurant_id')->constrained('restaurants')->cascadeOnDelete();
            $table->string('table_number', 50);
            $table->string('name', 190);
            $table->unsignedSmallInteger('capacity')->default(1);
            $table->string('zone', 190)->nullable();
            $table->unsignedTinyInteger('status')->default(TableStatus::AVAILABLE);
            $table->text('notes')->nullable();
            $table->string('creator_type')->nullable();
            $table->bigInteger('creator_id')->nullable();
            $table->string('editor_type')->nullable();
            $table->bigInteger('editor_id')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Uniqueness for active rows is enforced in FormRequest (whereNull deleted_at).
            $table->index(['restaurant_id', 'table_number']);
            $table->index(['restaurant_id', 'status']);
            $table->index(['restaurant_id', 'zone']);
            $table->index(['creator_id']);
            $table->index(['editor_id']);
        });

        Schema::table('orders', function (Blueprint $table) {
            if (!Schema::hasColumn('orders', 'table_id')) {
                $table->foreignId('table_id')
                    ->nullable()
                    ->after('restaurant_id')
                    ->constrained('restaurant_tables')
                    ->nullOnDelete();
                $table->index(['restaurant_id', 'table_id']);
            }
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            if (Schema::hasColumn('orders', 'table_id')) {
                $table->dropConstrainedForeignId('table_id');
            }
        });

        Schema::dropIfExists('restaurant_tables');
    }
};
