<?php

namespace Database\Seeders;

use Dipokhalder\EnvEditor\EnvEditor;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;
use Dipokhalder\Settings\Facades\Settings;

class CompanyTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        Settings::group('company')->set([
            'company_name'         => 'Cost to Cost Foods',
            'company_email'        => 'info@foodnanny.net',
            'company_phone'        => '+13333846282',
            'company_website'      => 'https://demo.foodnanny.xyz',
            'company_city'         => 'Juneau',
            'company_state'        => 'Alaska',
            'company_country_code' => 'USA',
            'company_zip_code'     => '99801',
            'company_address'      => '345 Egan Dr, Juneau, Alaska, United States'
        ]);

        $envService = new EnvEditor();
        $envService->addData([
            'APP_NAME' => "Cost to Cost Foods"
        ]);
        Artisan::call('optimize:clear');
    }
}
