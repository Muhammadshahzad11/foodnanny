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
            'company_phone'        => '+919876543210',
            'company_website'      => 'https://demo.foodnanny.xyz',
            'company_city'         => 'Mumbai',
            'company_state'        => 'Maharashtra',
            'company_country_code' => 'IND',
            'company_zip_code'     => '400001',
            'company_address'      => 'Mumbai, Maharashtra, India'
        ]);

        $envService = new EnvEditor();
        $envService->addData([
            'APP_NAME' => "Cost to Cost Foods"
        ]);
        Artisan::call('optimize:clear');
    }
}
