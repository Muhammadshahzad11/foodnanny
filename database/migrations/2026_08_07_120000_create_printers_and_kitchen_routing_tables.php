<?php

use App\Enums\Ask;
use App\Enums\PrintFormat;
use App\Enums\PrinterType;
use App\Enums\PrintingChoice;
use App\Enums\Status;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('printers')) {
            Schema::create('printers', function (Blueprint $table) {
                $table->id();
                $table->foreignId('restaurant_id')->constrained('restaurants')->cascadeOnDelete();
                $table->string('name');
                $table->tinyInteger('printing_choice')->default(PrintingChoice::BROWSER_POPUP);
                $table->tinyInteger('print_format')->default(PrintFormat::KOT);
                $table->tinyInteger('printer_type')->default(PrinterType::NETWORK);
                $table->unsignedSmallInteger('characters_per_line')->default(42);
                $table->tinyInteger('open_cash_drawer')->default(Ask::NO);
                $table->tinyInteger('invoice_qr_status')->default(Ask::NO);
                $table->string('computer_ipv4', 45)->nullable();
                $table->string('printer_ip', 45)->nullable();
                $table->unsignedSmallInteger('printer_port')->nullable()->default(9100);
                $table->tinyInteger('status')->default(Status::ACTIVE);
                $table->string('creator_type')->nullable();
                $table->bigInteger('creator_id')->nullable();
                $table->string('editor_type')->nullable();
                $table->bigInteger('editor_id')->nullable();
                $table->timestamps();

                $table->index(['restaurant_id', 'status']);
                $table->index(['restaurant_id', 'print_format']);
                $table->index(['creator_id']);
                $table->index(['editor_id']);
            });
        } elseif (Schema::hasTable('printers')) {
            Schema::table('printers', function (Blueprint $table) {
                if (!Schema::hasColumn('printers', 'creator_type')) {
                    $table->string('creator_type')->nullable()->after('status');
                }
                if (!Schema::hasColumn('printers', 'creator_id')) {
                    $table->bigInteger('creator_id')->nullable()->after('creator_type');
                }
                if (!Schema::hasColumn('printers', 'editor_type')) {
                    $table->string('editor_type')->nullable()->after('creator_id');
                }
                if (!Schema::hasColumn('printers', 'editor_id')) {
                    $table->bigInteger('editor_id')->nullable()->after('editor_type');
                }
            });
        }

        if (Schema::hasTable('kitchen_stations') && !Schema::hasColumn('kitchen_stations', 'printer_id')) {
            Schema::table('kitchen_stations', function (Blueprint $table) {
                $table->foreignId('printer_id')
                    ->nullable()
                    ->after('status')
                    ->constrained('printers')
                    ->nullOnDelete();
            });
        }

        if (!Schema::hasTable('kitchen_station_categories')) {
            Schema::create('kitchen_station_categories', function (Blueprint $table) {
                $table->id();
                $table->foreignId('kitchen_station_id')->constrained('kitchen_stations')->cascadeOnDelete();
                $table->foreignId('item_category_id')->constrained('item_categories')->cascadeOnDelete();
                $table->timestamps();

                $table->unique(['kitchen_station_id', 'item_category_id'], 'kitchen_station_category_unique');
            });
        }

        if (Schema::hasTable('kitchen_tickets') && !Schema::hasColumn('kitchen_tickets', 'kitchen_station_id')) {
            Schema::table('kitchen_tickets', function (Blueprint $table) {
                $table->foreignId('kitchen_station_id')
                    ->nullable()
                    ->after('order_id')
                    ->constrained('kitchen_stations')
                    ->nullOnDelete();
                $table->foreignId('printer_id')
                    ->nullable()
                    ->after('kitchen_station_id')
                    ->constrained('printers')
                    ->nullOnDelete();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('kitchen_tickets')) {
            Schema::table('kitchen_tickets', function (Blueprint $table) {
                if (Schema::hasColumn('kitchen_tickets', 'printer_id')) {
                    $table->dropConstrainedForeignId('printer_id');
                }
                if (Schema::hasColumn('kitchen_tickets', 'kitchen_station_id')) {
                    $table->dropConstrainedForeignId('kitchen_station_id');
                }
            });
        }

        Schema::dropIfExists('kitchen_station_categories');

        if (Schema::hasTable('kitchen_stations') && Schema::hasColumn('kitchen_stations', 'printer_id')) {
            Schema::table('kitchen_stations', function (Blueprint $table) {
                $table->dropConstrainedForeignId('printer_id');
            });
        }

        Schema::dropIfExists('printers');
    }
};
