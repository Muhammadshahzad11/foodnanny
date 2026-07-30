<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Dipokhalder\Settings\Facades\Settings;

class DeliverySetupTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Settings::group('delivery_setup')->set([
            'delivery_setup_free_delivery_kilometer' => "2",
            'delivery_setup_basic_delivery_fee'      => "1",
            'delivery_setup_charge_per_kilo'         => "1"
        ]);
    }
}
