<?php

namespace Database\Seeders;

use Dipokhalder\EnvEditor\EnvEditor;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;
use Dipokhalder\Settings\Facades\Settings;

class PusherTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        $envService = new EnvEditor();
        Settings::group('pusher')->set([
            'pusher_app_id'      => $envService->getValue('DEMO') ? '1999408' : '',
            'pusher_app_key'     => $envService->getValue('DEMO') ? '4a488c2f41a7d44b546d' : '',
            'pusher_app_secret'  => $envService->getValue('DEMO') ? '130f250f083706a1bf73' : '',
            'pusher_app_cluster' => $envService->getValue('DEMO') ? 'ap1' : ''
        ]);

        $envService->addData([
            'PUSHER_APP_ID'      => $envService->getValue('DEMO') ? '1999408' : '',
            'PUSHER_APP_KEY'     => $envService->getValue('DEMO') ? '4a488c2f41a7d44b546d' : '',
            'PUSHER_APP_SECRET'  => $envService->getValue('DEMO') ? '130f250f083706a1bf73' : '',
            'PUSHER_APP_CLUSTER' => $envService->getValue('DEMO') ? 'ap1' : ''
        ]);
        Artisan::call('optimize:clear');
    }
}
