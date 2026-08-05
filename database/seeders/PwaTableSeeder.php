<?php

namespace Database\Seeders;

use App\Enums\PwaCacheStrategy;
use App\Enums\PwaDisplayMode;
use App\Enums\PwaOrientation;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Dipokhalder\Settings\Facades\Settings;

class PwaTableSeeder extends Seeder
{
    public function run(): void
    {
        $company = 'Cost to Cost Foods';
        try {
            $company = Settings::group('company')->get('company_name') ?: $company;
        } catch (\Throwable $e) {
        }

        $row = [
            'id' => 1,
            'name' => $company,
            'short_name' => 'CTC Foods',
            'description' => 'Order food, manage tables, kitchen, and POS from ' . $company,
            'theme_color' => '#148A3C',
            'background_color' => '#FFFFFF',
            'orientation' => PwaOrientation::ANY,
            'display_mode' => PwaDisplayMode::STANDALONE,
            'offline_mode' => 1,
            'auto_update' => 1,
            'cache_strategy' => PwaCacheStrategy::BALANCED,
            'enable_install_popup' => 1,
            'popup_delay_seconds' => 3,
            'popup_frequency_hours' => 24,
            'cache_version' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ];

        if (DB::table('pwas')->where('id', 1)->exists()) {
            unset($row['created_at']);
            DB::table('pwas')->where('id', 1)->update($row);
            return;
        }

        DB::table('pwas')->insert($row);
    }
}
