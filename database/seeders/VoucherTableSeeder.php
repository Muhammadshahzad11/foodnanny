<?php

namespace Database\Seeders;

use App\Models\User;
use Carbon\Carbon;
use App\Enums\Owner;
use App\Enums\TaxType;
use App\Enums\Discount;
use App\Models\Voucher;
use Illuminate\Database\Seeder;
use Dipokhalder\EnvEditor\EnvEditor;

class VoucherTableSeeder extends Seeder
{
    public function run()
    {
        $envService = new EnvEditor();
        if ($envService->getValue('DEMO')) {
            $vouchers = [
                [
                    'restaurant_id'    => 0,
                    'name'             => 'Free Delivery',
                    'code'             => 'freedelivery',
                    'discount'         => 0,
                    'discount_type'    => TaxType::PERCENTAGE,
                    'start_date'       => now(),
                    'end_date'         => Carbon::now()->addDay(60),
                    'minimum_order'    => 50,
                    'maximum_discount' => 0,
                    'limit_per_user'   => 1,
                    'type'             => Discount::FREE_DELIVERY,
                    'owner'            => Owner::ADMIN,
                    'creator_type'     => User::class,
                    'creator_id'       => 1,
                    'editor_type'      => User::class,
                    'editor_id'        => 1,
                    'created_at'       => now(),
                    'updated_at'       => now()
                ],
                [
                    'restaurant_id'    => 0,
                    'name'             => 'Yummy',
                    'code'             => 'yummy',
                    'discount'         => '5.00',
                    'discount_type'    => TaxType::FIXED,
                    'start_date'       => now(),
                    'end_date'         => Carbon::now()->addDay(30),
                    'minimum_order'    => '19.00',
                    'maximum_discount' => '5.00',
                    'limit_per_user'   => 5,
                    'type'             => Discount::DEFAULT,
                    'owner'            => Owner::ADMIN,
                    'creator_type'     => User::class,
                    'creator_id'       => 1,
                    'editor_type'      => User::class,
                    'editor_id'        => 1,
                    'created_at'       => now(),
                    'updated_at'       => now()
                ],
                [
                    'restaurant_id'    => 0,
                    'name'             => 'Welcome Back',
                    'code'             => 'welcomeback',
                    'discount'         => 10,
                    'discount_type'    => TaxType::PERCENTAGE,
                    'start_date'       => now(),
                    'end_date'         => Carbon::now()->addDay(30),
                    'minimum_order'    => 20,
                    'maximum_discount' => 10,
                    'limit_per_user'   => 1,
                    'type'             => Discount::DEFAULT,
                    'owner'            => Owner::ADMIN,
                    'creator_type'     => User::class,
                    'creator_id'       => 1,
                    'editor_type'      => User::class,
                    'editor_id'        => 1,
                    'created_at'       => now(),
                    'updated_at'       => now()
                ],
                [
                    'restaurant_id'    => 0,
                    'name'             => 'Summer Sale',
                    'code'             => 'summer15',
                    'discount'         => 15,
                    'discount_type'    => TaxType::PERCENTAGE,
                    'start_date'       => now(),
                    'end_date'         => Carbon::now()->addDay(45),
                    'minimum_order'    => 30,
                    'maximum_discount' => 15,
                    'limit_per_user'   => 2,
                    'type'             => Discount::DEFAULT,
                    'owner'            => Owner::ADMIN,
                    'creator_type'     => User::class,
                    'creator_id'       => 1,
                    'editor_type'      => User::class,
                    'editor_id'        => 1,
                    'created_at'       => now(),
                    'updated_at'       => now()
                ],
                [
                    'restaurant_id'    => 0,
                    'name'             => 'Lunch Special',
                    'code'             => 'lunch3',
                    'discount'         => 3.00,
                    'discount_type'    => TaxType::FIXED,
                    'start_date'       => now(),
                    'end_date'         => Carbon::now()->addDay(30),
                    'minimum_order'    => 15,
                    'maximum_discount' => 3.00,
                    'limit_per_user'   => 10,
                    'type'             => Discount::DEFAULT,
                    'owner'            => Owner::ADMIN,
                    'creator_type'     => User::class,
                    'creator_id'       => 1,
                    'editor_type'      => User::class,
                    'editor_id'        => 1,
                    'created_at'       => now(),
                    'updated_at'       => now()
                ],
                [
                    'restaurant_id'    => 0,
                    'name'             => 'Dinner Delight',
                    'code'             => 'dinner5',
                    'discount'         => 5.00,
                    'discount_type'    => TaxType::FIXED,
                    'start_date'       => now(),
                    'end_date'         => Carbon::now()->addDay(30),
                    'minimum_order'    => 25,
                    'maximum_discount' => 5.00,
                    'limit_per_user'   => 5,
                    'type'             => Discount::DEFAULT,
                    'owner'            => Owner::ADMIN,
                    'creator_type'     => User::class,
                    'creator_id'       => 1,
                    'editor_type'      => User::class,
                    'editor_id'        => 1,
                    'created_at'       => now(),
                    'updated_at'       => now()
                ],
                [
                    'restaurant_id'    => 0,
                    'name'             => 'Weekend Blast',
                    'code'             => 'weekend20',
                    'discount'         => 20,
                    'discount_type'    => TaxType::PERCENTAGE,
                    'start_date'       => now(),
                    'end_date'         => Carbon::now()->addDay(14),
                    'minimum_order'    => 40,
                    'maximum_discount' => 20,
                    'limit_per_user'   => 1,
                    'type'             => Discount::DEFAULT,
                    'owner'            => Owner::ADMIN,
                    'creator_type'     => User::class,
                    'creator_id'       => 1,
                    'editor_type'      => User::class,
                    'editor_id'        => 1,
                    'created_at'       => now(),
                    'updated_at'       => now()
                ],
                [
                    'restaurant_id'    => 0,
                    'name'             => 'First Order',
                    'code'             => 'first25',
                    'discount'         => 25,
                    'discount_type'    => TaxType::PERCENTAGE,
                    'start_date'       => now(),
                    'end_date'         => Carbon::now()->addDay(90),
                    'minimum_order'    => 10,
                    'maximum_discount' => 10,
                    'limit_per_user'   => 1,
                    'type'             => Discount::DEFAULT,
                    'owner'            => Owner::ADMIN,
                    'creator_type'     => User::class,
                    'creator_id'       => 1,
                    'editor_type'      => User::class,
                    'editor_id'        => 1,
                    'created_at'       => now(),
                    'updated_at'       => now()
                ],
                [
                    'restaurant_id'    => 0,
                    'name'             => 'Party Pack',
                    'code'             => 'party10',
                    'discount'         => 10.00,
                    'discount_type'    => TaxType::FIXED,
                    'start_date'       => now(),
                    'end_date'         => Carbon::now()->addDay(60),
                    'minimum_order'    => 80,
                    'maximum_discount' => 10.00,
                    'limit_per_user'   => 2,
                    'type'             => Discount::DEFAULT,
                    'owner'            => Owner::ADMIN,
                    'creator_type'     => User::class,
                    'creator_id'       => 1,
                    'editor_type'      => User::class,
                    'editor_id'        => 1,
                    'created_at'       => now(),
                    'updated_at'       => now()
                ],
                [
                    'restaurant_id'    => 0,
                    'name'             => 'Midnight Snack',
                    'code'             => 'midnightfree',
                    'discount'         => 0,
                    'discount_type'    => TaxType::PERCENTAGE,
                    'start_date'       => now(),
                    'end_date'         => Carbon::now()->addDay(30),
                    'minimum_order'    => 15,
                    'maximum_discount' => 0,
                    'limit_per_user'   => 5,
                    'type'             => Discount::FREE_DELIVERY,
                    'owner'            => Owner::ADMIN,
                    'creator_type'     => User::class,
                    'creator_id'       => 1,
                    'editor_type'      => User::class,
                    'editor_id'        => 1,
                    'created_at'       => now(),
                    'updated_at'       => now()
                ],
                [
                    'restaurant_id'    => 0,
                    'name'             => 'Family Feast',
                    'code'             => 'family12',
                    'discount'         => 12,
                    'discount_type'    => TaxType::PERCENTAGE,
                    'start_date'       => now(),
                    'end_date'         => Carbon::now()->addDay(45),
                    'minimum_order'    => 60,
                    'maximum_discount' => 25,
                    'limit_per_user'   => 2,
                    'type'             => Discount::DEFAULT,
                    'owner'            => Owner::ADMIN,
                    'creator_type'     => User::class,
                    'creator_id'       => 1,
                    'editor_type'      => User::class,
                    'editor_id'        => 1,
                    'created_at'       => now(),
                    'updated_at'       => now()
                ], 
            ];
            foreach ($vouchers as $voucher) {
                Voucher::create($voucher);
            }
        }
    }
}
