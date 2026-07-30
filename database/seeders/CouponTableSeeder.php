<?php

namespace Database\Seeders;

use App\Enums\TaxType;
use App\Models\Coupon;
use App\Models\Restaurant;
use App\Models\User;
use Carbon\Carbon;
use Dipokhalder\EnvEditor\EnvEditor;
use Illuminate\Database\Seeder;

class CouponTableSeeder extends Seeder
{
    public function run(): void
    {
        $envService = new EnvEditor();
        if ($envService->getValue('DEMO')) {

            $coupons = [
                [
                    'name'             => 'Fairy',
                    'code'             => 'fairy',
                    'discount'         => '7.00',
                    'discount_type'    => TaxType::PERCENTAGE,
                    'start_date'       => now(),
                    'end_date'         => Carbon::now()->addDays(365),
                    'minimum_order'    => '19.00',
                    'maximum_discount' => '99.00',
                    'limit_per_user'   => '5',
                    'created_at'       => now(),
                    'updated_at'       => now()
                ],
                [
                    'name'             => 'Shake',
                    'code'             => 'shake',
                    'discount'         => '5.00',
                    'discount_type'    => TaxType::FIXED,
                    'start_date'       => now(),
                    'end_date'         => Carbon::now()->addDays(365),
                    'minimum_order'    => '19.00', 
                    'limit_per_user'   => '5',
                    'created_at'       => now(),
                    'updated_at'       => now()
                ],
                [
                    'name'             => 'TasteTrove',
                    'code'             => 'tastetrove',
                    'discount'         => '10.00',
                    'discount_type'    => TaxType::PERCENTAGE,
                    'start_date'       => now(),
                    'end_date'         => Carbon::now()->addDays(365),
                    'minimum_order'    => '25.00',
                    'maximum_discount' => '50.00',
                    'limit_per_user'   => '3',
                    'created_at'       => now(),
                    'updated_at'       => now()
                ],
                [
                    'name'             => 'EpicureanEscape',
                    'code'             => 'epicureanescape',
                    'discount'         => '15.00',
                    'discount_type'    => TaxType::PERCENTAGE,
                    'start_date'       => now(),
                    'end_date'         => Carbon::now()->addDays(365),
                    'minimum_order'    => '30.00',
                    'maximum_discount' => '75.00',
                    'limit_per_user'   => '2',
                    'created_at'       => now(),
                    'updated_at'       => now()
                ],
                [
                    'name'             => 'FlavorFest',
                    'code'             => 'flavorfest',
                    'discount'         => '20.00',
                    'discount_type'    => TaxType::PERCENTAGE,
                    'start_date'       => now(),
                    'end_date'         => Carbon::now()->addDays(365),
                    'minimum_order'    => '40.00',
                    'maximum_discount' => '100.00',
                    'limit_per_user'   => '1',
                    'created_at'       => now(),
                    'updated_at'       => now()
                ],
                [
                    'name'             => 'DelishDelight',
                    'code'             => 'delishdelight',
                    'discount'         => '5.00',
                    'discount_type'    => TaxType::FIXED,
                    'start_date'       => now(),
                    'end_date'         => Carbon::now()->addDays(365),
                    'minimum_order'    => '15.00', 
                    'limit_per_user'   => '5',
                    'created_at'       => now(),
                    'updated_at'       => now()
                ],
                [
                    'name'             => 'CuisineCove',
                    'code'             => 'cuisinecove',
                    'discount'         => '2.00',
                    'discount_type'    => TaxType::FIXED,
                    'start_date'       => now(),
                    'end_date'         => Carbon::now()->addDays(365),
                    'minimum_order'    => '10.00', 
                    'limit_per_user'   => '10',
                    'created_at'       => now(),
                    'updated_at'       => now()
                ],
                [
                    'name'             => 'SavorySavor',
                    'code'             => 'savorysavor',
                    'discount'         => '3.00',
                    'discount_type'    => TaxType::FIXED,
                    'start_date'       => now(),
                    'end_date'         => Carbon::now()->addDays(365),
                    'minimum_order'    => '12.00', 
                    'limit_per_user'   => '5',
                    'created_at'       => now(),
                    'updated_at'       => now()
                ],
                [
                    'name'             => 'GourmetGalore',
                    'code'             => 'gourmetgalore',
                    'discount'         => '12.00',
                    'discount_type'    => TaxType::PERCENTAGE,
                    'start_date'       => now(),
                    'end_date'         => Carbon::now()->addDays(365),
                    'minimum_order'    => '22.00',
                    'maximum_discount' => '40.00',
                    'limit_per_user'   => '3',
                    'created_at'       => now(),
                    'updated_at'       => now()
                ],
                [
                    'name'             => 'TasteTemple',
                    'code'             => 'tastetemple',
                    'discount'         => '8.00',
                    'discount_type'    => TaxType::FIXED,
                    'start_date'       => now(),
                    'end_date'         => Carbon::now()->addDays(365),
                    'minimum_order'    => '35.00', 
                    'limit_per_user'   => '2',
                    'created_at'       => now(),
                    'updated_at'       => now()
                ],
                [
                    'name'             => 'CulinaryCraze',
                    'code'             => 'culinarycraze',
                    'discount'         => '18.00',
                    'discount_type'    => TaxType::PERCENTAGE,
                    'start_date'       => now(),
                    'end_date'         => Carbon::now()->addDays(365),
                    'minimum_order'    => '45.00',
                    'maximum_discount' => '90.00',
                    'limit_per_user'   => '2',
                    'created_at'       => now(),
                    'updated_at'       => now()
                ],
                [
                    'name'             => 'PalatePleasure',
                    'code'             => 'palatepleasure',
                    'discount'         => '25.00',
                    'discount_type'    => TaxType::PERCENTAGE,
                    'start_date'       => now(),
                    'end_date'         => Carbon::now()->addDays(365),
                    'minimum_order'    => '50.00',
                    'maximum_discount' => '100.00',
                    'limit_per_user'   => '1',
                    'created_at'       => now(),
                    'updated_at'       => now()
                ]
            ];

            $restaurants = Restaurant::all();

            foreach ($restaurants as $restaurant) {
                foreach ($coupons as $coupon) {   
                    $data = [
                        'name'           => $coupon['name'],
                        'code'           => $coupon['code'],
                        'restaurant_id'  => $restaurant->id,
                        'start_date'     => $coupon['start_date'],
                        'end_date'       => $coupon['end_date'],
                        'discount'       => $coupon['discount'],
                        'discount_type'  => $coupon['discount_type'],
                        'minimum_order'  => $coupon['minimum_order'],
                        'limit_per_user' => $coupon['limit_per_user'],
                        'creator_type'   => User::class,
                        'creator_id'     => 1,
                        'editor_type'    => User::class,
                        'editor_id'      => 1,
                        'created_at'     => now(),
                        'updated_at'     => now()
                    ]; 
                    if ($coupon['discount_type'] === TaxType::PERCENTAGE && isset($coupon['maximum_discount'])) {
                        $data['maximum_discount'] = $coupon['maximum_discount'];
                    } 
                    Coupon::create($data);
                }
            } 
        }
    }
}
