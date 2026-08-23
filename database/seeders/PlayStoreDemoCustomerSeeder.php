<?php

namespace Database\Seeders;

use App\Support\DemoCustomerLogin;
use Illuminate\Database\Seeder;

class PlayStoreDemoCustomerSeeder extends Seeder
{
    public function run(): void
    {
        $user = DemoCustomerLogin::ensureUser();
        $this->command?->info(
            'Play Store demo customer: '
            . DemoCustomerLogin::COUNTRY_CODE . ' ' . DemoCustomerLogin::phoneDigits()
            . ' (user #' . $user->id . '). OTP is skipped for this number only.'
        );
    }
}
