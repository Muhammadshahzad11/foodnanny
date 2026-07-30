<?php

namespace Database\Seeders;


use App\Models\CompanyBalance;
use Illuminate\Database\Seeder;

class  CompanyBalanceTableSeeder extends Seeder
{

    public function run(): void
    {
        CompanyBalance::create([
            'balance' => 0,
        ]);
    }
}
