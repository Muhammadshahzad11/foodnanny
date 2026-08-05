<?php

namespace Database\Seeders;


use Dipokhalder\EnvEditor\EnvEditor;
use Dipokhalder\Settings\Facades\Settings;
use Illuminate\Database\Seeder;


class TermsAndConditionsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $envService = new EnvEditor();
        Settings::group('terms_and_conditions')->set([
            'terms_and_conditions_customer_page_id'     => $envService->getValue('DEMO') ? 4 : null,
            'terms_and_conditions_restaurant_page_id'   => $envService->getValue('DEMO') ? 5 : null,
            'terms_and_conditions_delivery_boy_page_id' => $envService->getValue('DEMO') ? 6 : null,
        ]);
    }
}
