<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('printers')) {
            return;
        }

        Schema::table('printers', function (Blueprint $table) {
            if (!Schema::hasColumn('printers', 'windows_printer_name')) {
                $table->string('windows_printer_name', 190)->nullable()->after('printer_port');
            }
        });
    }

    public function down(): void
    {
        if (Schema::hasTable('printers') && Schema::hasColumn('printers', 'windows_printer_name')) {
            Schema::table('printers', function (Blueprint $table) {
                $table->dropColumn('windows_printer_name');
            });
        }
    }
};
