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

        if (Schema::hasColumn('printers', 'creator_id') && !$this->indexExists('printers', 'printers_creator_id_index')) {
            Schema::table('printers', function (Blueprint $table) {
                $table->index(['creator_id']);
            });
        }
        if (Schema::hasColumn('printers', 'editor_id') && !$this->indexExists('printers', 'printers_editor_id_index')) {
            Schema::table('printers', function (Blueprint $table) {
                $table->index(['editor_id']);
            });
        }
    }

    public function down(): void
    {
        if (!Schema::hasTable('printers')) {
            return;
        }

        Schema::table('printers', function (Blueprint $table) {
            if (Schema::hasColumn('printers', 'editor_id')) {
                $table->dropColumn('editor_id');
            }
            if (Schema::hasColumn('printers', 'editor_type')) {
                $table->dropColumn('editor_type');
            }
            if (Schema::hasColumn('printers', 'creator_id')) {
                $table->dropColumn('creator_id');
            }
            if (Schema::hasColumn('printers', 'creator_type')) {
                $table->dropColumn('creator_type');
            }
        });
    }

    protected function indexExists(string $table, string $index): bool
    {
        $connection = Schema::getConnection();
        $dbName     = $connection->getDatabaseName();
        $result     = $connection->select(
            'SELECT 1 FROM information_schema.statistics WHERE table_schema = ? AND table_name = ? AND index_name = ? LIMIT 1',
            [$dbName, $table, $index]
        );

        return !empty($result);
    }
};
