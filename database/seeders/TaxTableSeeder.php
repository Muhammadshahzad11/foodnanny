<?php

namespace Database\Seeders;

use App\Models\User;
use Dipokhalder\EnvEditor\EnvEditor;
use Illuminate\Database\Seeder;
use App\Models\Restaurant;
use App\Enums\TaxType;
use App\Enums\Status;
use App\Models\Tax;

class TaxTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        $envService = new EnvEditor();
        if ($envService->getValue('DEMO')) {
            $taxes       = [
                [
                    'name'     => 'No-VAT',
                    'code'     => 'VAT-0',
                    'tax_rate' => 0,
                    'type'     => TaxType::PERCENTAGE,
                    'status'   => Status::ACTIVE
                ],
                [
                    'name'     => 'VAT',
                    'code'     => 'VAT-5%',
                    'tax_rate' => 5,
                    'type'     => TaxType::PERCENTAGE,
                    'status'   => Status::ACTIVE
                ],
                [
                    'name'     => 'VAT',
                    'code'     => 'VAT-10%',
                    'tax_rate' => 10,
                    'type'     => TaxType::PERCENTAGE,
                    'status'   => Status::ACTIVE
                ],
                [
                    'name'     => 'GST',
                    'code'     => 'GST-5%',
                    'tax_rate' => 5,
                    'type'     => TaxType::PERCENTAGE,
                    'status'   => Status::ACTIVE
                ],
                [
                    'name'     => 'GST',
                    'code'     => 'GST-10%',
                    'tax_rate' => 10,
                    'type'     => TaxType::PERCENTAGE,
                    'status'   => Status::ACTIVE
                ]
            ];
            $restaurants = Restaurant::all();
            foreach ($restaurants as $restaurant) {
                foreach ($taxes as $tax) {
                    Tax::create([
                        'restaurant_id' => $restaurant->id,
                        'name'          => $tax['name'],
                        'code'          => $tax['code'],
                        'tax_rate'      => $tax['tax_rate'],
                        'type'          => $tax['type'],
                        'status'        => $tax['status'],
                        'creator_type'  => User::class,
                        'creator_id'    => 1,
                        'editor_type'   => User::class,
                        'editor_id'     => 1,
                        'created_at'    => now(),
                        'updated_at'    => now()
                    ]);
                }
            }
        }
    }
}
