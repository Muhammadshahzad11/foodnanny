<?php

namespace Database\Seeders;


use App\Enums\Ask;
use App\Models\Currency;
use App\Models\User;
use Dipokhalder\EnvEditor\EnvEditor;
use Illuminate\Database\Seeder;


class CurrencyTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        Currency::insert([
            [
                'name'              => 'Dollars',
                'symbol'            => '$',
                'code'              => 'USD',
                'is_cryptocurrency' => Ask::NO,
                'exchange_rate'     => 1,
                'creator_type'      => User::class,
                'creator_id'        => 1,
                'editor_type'       => User::class,
                'editor_id'         => 1,
                'created_at'        => now(),
                'updated_at'        => now()
            ],
        ]);

        $envService = new EnvEditor();
        if ($envService->getValue('DEMO')) {
            Currency::insert([
                [
                    'name'              => 'Rupee',
                    'symbol'            => '₹',
                    'code'              => 'INR',
                    'is_cryptocurrency' => Ask::NO,
                    'exchange_rate'     => 1,
                    'creator_type'      => User::class,
                    'creator_id'        => 1,
                    'editor_type'       => User::class,
                    'editor_id'         => 1,
                    'created_at'        => now(),
                    'updated_at'        => now()
                ],
                [
                    'name'              => 'Taka',
                    'symbol'            => '৳',
                    'code'              => 'BDT',
                    'is_cryptocurrency' => Ask::NO,
                    'exchange_rate'     => 1,
                    'creator_type'      => User::class,
                    'creator_id'        => 1,
                    'editor_type'       => User::class,
                    'editor_id'         => 1,
                    'created_at'        => now(),
                    'updated_at'        => now()
                ],
                [
                    'name'              => 'Naira',
                    'symbol'            => '₦',
                    'code'              => 'NGN',
                    'is_cryptocurrency' => Ask::NO,
                    'exchange_rate'     => 1,
                    'creator_type'      => User::class,
                    'creator_id'        => 1,
                    'editor_type'       => User::class,
                    'editor_id'         => 1,
                    'created_at'        => now(),
                    'updated_at'        => now()
                ],
                [
                    'name'              => 'Peso',
                    'symbol'            => '₱',
                    'code'              => 'ARS',
                    'is_cryptocurrency' => Ask::NO,
                    'exchange_rate'     => 1,
                    'creator_type'      => User::class,
                    'creator_id'        => 1,
                    'editor_type'       => User::class,
                    'editor_id'         => 1,
                    'created_at'        => now(),
                    'updated_at'        => now()
                ],
            ]);
        }
    }
}
