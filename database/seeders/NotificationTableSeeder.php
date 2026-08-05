<?php

namespace Database\Seeders;

use Dipokhalder\EnvEditor\EnvEditor;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;
use Dipokhalder\Settings\Facades\Settings;
use App\Models\NotificationSetting;

class NotificationTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        $envService = new EnvEditor();
        Settings::group('notification')->set([
            'notification_fcm_public_vapid_key'    => $envService->getValue('DEMO') ? 'BPLefgBrw-amD9LvAuwKgWpjrzIjyhC0XdXxIvruek0_r5cIyNbHTka77UNuJwsvA6DCiIeYl3OFY2bvtOYtr7M' : '',
            'notification_fcm_api_key'             => $envService->getValue('DEMO') ? 'AIzaSyAEotcEUmJoyZlBJzpsA1c3gZw2bsVoE08' : '',
            'notification_fcm_auth_domain'         => $envService->getValue('DEMO') ? 'foodnanny-27d2c.firebaseapp.com' : '',
            'notification_fcm_project_id'          => $envService->getValue('DEMO') ? 'foodnanny-27d2c' : '',
            'notification_fcm_storage_bucket'      => $envService->getValue('DEMO') ? 'foodnanny-27d2c.firebasestorage.app' : '',
            'notification_fcm_messaging_sender_id' => $envService->getValue('DEMO') ? '24639567330' : '',
            'notification_fcm_app_id'              => $envService->getValue('DEMO') ? '1:24639567330:web:d535c6ed8bf0dcf2d6b5bb' : '',
            'notification_fcm_measurement_id'      => $envService->getValue('DEMO') ? 'G-K95FZ49B6E' : '',
            'notification_fcm_json_file'           => ''
        ]);
        if ($envService->getValue('DEMO') && file_exists(public_path('/images/seeder/file/service-account-file.json'))) {
            $setting = NotificationSetting::where('key', 'notification_fcm_json_file')->first();
            $setting->addMedia(public_path('/images/seeder/file/service-account-file.json'))->preservingOriginal()->usingFileName('service-account-file.json')->toMediaCollection('notification-file');
        }
        Artisan::call('optimize:clear');
    }
}
