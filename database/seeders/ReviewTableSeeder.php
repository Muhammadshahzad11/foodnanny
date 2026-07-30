<?php

namespace Database\Seeders;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Review;
use App\Models\Restaurant;
use Illuminate\Database\Seeder;
use Dipokhalder\EnvEditor\EnvEditor;

class ReviewTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */

    public function run(): void
    {
        $reviews = [
            [
                'user_id'      => 1,
                'model_type'   => User::class,
                'model_id'     => 8,
                'star'         => 5,
                'review'       => 'Good boy with good manners',
                'creator_type' => User::class,
                'creator_id'   => 1,
                'editor_type'  => User::class,
                'editor_id'    => 1,
                'created_at'   => Carbon::now(),
                'updated_at'   => Carbon::now()
            ],
            [
                'user_id'      => 1,
                'model_type'   => Restaurant::class,
                'model_id'     => 25,
                'star'         => 5,
                'review'       => 'Good food and good packaging.',
                'creator_type' => User::class,
                'creator_id'   => 1,
                'editor_type'  => User::class,
                'editor_id'    => 1,
                'created_at'   => Carbon::now(),
                'updated_at'   => Carbon::now()
            ],
            [
                'user_id'      => 1,
                'model_type'   => User::class,
                'model_id'     => 13,
                'star'         => 5,
                'review'       => 'Fast delivery and good manners.',
                'creator_type' => User::class,
                'creator_id'   => 1,
                'editor_type'  => User::class,
                'editor_id'    => 1,
                'created_at'   => Carbon::now(),
                'updated_at'   => Carbon::now()
            ],
            [
                'user_id'      => 3,
                'model_type'   => Restaurant::class,
                'model_id'     => 1,
                'star'         => 5,
                'review'       => 'Delicious food, fast delivery, and excellent service with good manners!',
                'creator_type' => User::class,
                'creator_id'   => 3,
                'editor_type'  => User::class,
                'editor_id'    => 3,
                'created_at'   => Carbon::now(),
                'updated_at'   => Carbon::now()
            ],
            [
                'user_id'      => 3,
                'model_type'   => User::class,
                'model_id'     => 8,
                'star'         => 3,
                'review'       => 'The best restaurant experience in town!',
                'creator_type' => User::class,
                'creator_id'   => 3,
                'editor_type'  => User::class,
                'editor_id'    => 3,
                'created_at'   => Carbon::now(),
                'updated_at'   => Carbon::now()
            ],
            [
                'user_id'      => 3,
                'model_type'   => Restaurant::class,
                'model_id'     => 2,
                'star'         => 4,
                'review'       => 'Great place to dine with friends and family.',
                'creator_type' => User::class,
                'creator_id'   => 3,
                'editor_type'  => User::class,
                'editor_id'    => 3,
                'created_at'   => Carbon::now(),
                'updated_at'   => Carbon::now()
            ],

            [
                'user_id'      => 3,
                'model_type'   => Restaurant::class,
                'model_id'     => 3,
                'star'         => 5,
                'review'       => 'Quality food that feels like home-cooked.',
                'creator_type' => User::class,
                'creator_id'   => 3,
                'editor_type'  => User::class,
                'editor_id'    => 3,
                'created_at'   => Carbon::now(),
                'updated_at'   => Carbon::now()
            ],
            [
                'user_id'      => 3,
                'model_type'   => Restaurant::class,
                'model_id'     => 4,
                'star'         => 3,
                'review'       => 'Warm hospitality and perfectly cooked meals.',
                'creator_type' => User::class,
                'creator_id'   => 3,
                'editor_type'  => User::class,
                'editor_id'    => 3,
                'created_at'   => Carbon::now(),
                'updated_at'   => Carbon::now()
            ],
            [
                'user_id'      => 3,
                'model_type'   => Restaurant::class,
                'model_id'     => 5,
                'star'         => 4,
                'review'       => 'The flavors are rich, and the meals are hearty.',
                'creator_type' => User::class,
                'creator_id'   => 3,
                'editor_type'  => User::class,
                'editor_id'    => 3,
                'created_at'   => Carbon::now(),
                'updated_at'   => Carbon::now()
            ],
            [
                'user_id'      => 3,
                'model_type'   => Restaurant::class,
                'model_id'     => 6,
                'star'         => 4,
                'review'       => 'Truly a five-star experience!',
                'creator_type' => User::class,
                'creator_id'   => 3,
                'editor_type'  => User::class,
                'editor_id'    => 3,
                'created_at'   => Carbon::now(),
                'updated_at'   => Carbon::now()
            ],
            [
                'user_id'      => 3,
                'model_type'   => Restaurant::class,
                'model_id'     => 7,
                'star'         => 5,
                'review'       => 'Friendly service and consistently delicious food.',
                'creator_type' => User::class,
                'creator_id'   => 3,
                'editor_type'  => User::class,
                'editor_id'    => 3,
                'created_at'   => Carbon::now(),
                'updated_at'   => Carbon::now()
            ],
            [
                'user_id'      => 3,
                'model_type'   => Restaurant::class,
                'model_id'     => 8,
                'star'         => 4,
                'review'       => 'A must-visit spot for food lovers.',
                'creator_type' => User::class,
                'creator_id'   => 3,
                'editor_type'  => User::class,
                'editor_id'    => 3,
                'created_at'   => Carbon::now(),
                'updated_at'   => Carbon::now()
            ],

            [
                'user_id'      => 3,
                'model_type'   => Restaurant::class,
                'model_id'     => 9,
                'star'         => 3,
                'review'       => 'Perfect balance of quality, taste, and service.',
                'creator_type' => User::class,
                'creator_id'   => 3,
                'editor_type'  => User::class,
                'editor_id'    => 3,
                'created_at'   => Carbon::now(),
                'updated_at'   => Carbon::now()
            ],
            [
                'user_id'      => 3,
                'model_type'   => Restaurant::class,
                'model_id'     => 10,
                'star'         => 5,
                'review'       => 'Love the atmosphere and food presentation.',
                'creator_type' => User::class,
                'creator_id'   => 3,
                'editor_type'  => User::class,
                'editor_id'    => 3,
                'created_at'   => Carbon::now(),
                'updated_at'   => Carbon::now()
            ],
            [
                'user_id'      => 3,
                'model_type'   => Restaurant::class,
                'model_id'     => 11,
                'star'         => 2,
                'review'       => 'Service is fast, and food is always spot on.',
                'creator_type' => User::class,
                'creator_id'   => 3,
                'editor_type'  => User::class,
                'editor_id'    => 3,
                'created_at'   => Carbon::now(),
                'updated_at'   => Carbon::now()
            ],
            [
                'user_id'      => 3,
                'model_type'   => Restaurant::class,
                'model_id'     => 12,
                'star'         => 3,
                'review'       => 'Always my first choice for good food!',
                'creator_type' => User::class,
                'creator_id'   => 3,
                'editor_type'  => User::class,
                'editor_id'    => 3,
                'created_at'   => Carbon::now(),
                'updated_at'   => Carbon::now()
            ],
            [
                'user_id'      => 3,
                'model_type'   => Restaurant::class,
                'model_id'     => 13,
                'star'         => 4,
                'review'       => 'Excellent variety and generous portions.',
                'creator_type' => User::class,
                'creator_id'   => 3,
                'editor_type'  => User::class,
                'editor_id'    => 3,
                'created_at'   => Carbon::now(),
                'updated_at'   => Carbon::now()
            ],
            [
                'user_id'      => 3,
                'model_type'   => Restaurant::class,
                'model_id'     => 14,
                'star'         => 5,
                'review'       => 'A delightful mix of taste and hospitality.',
                'creator_type' => User::class,
                'creator_id'   => 3,
                'editor_type'  => User::class,
                'editor_id'    => 3,
                'created_at'   => Carbon::now(),
                'updated_at'   => Carbon::now()
            ],
            [
                'user_id'      => 3,
                'model_type'   => Restaurant::class,
                'model_id'     => 15,
                'star'         => 3,
                'review'       => 'Tasty food served hot and fresh.',
                'creator_type' => User::class,
                'creator_id'   => 3,
                'editor_type'  => User::class,
                'editor_id'    => 3,
                'created_at'   => Carbon::now(),
                'updated_at'   => Carbon::now()
            ],
            [
                'user_id'      => 3,
                'model_type'   => Restaurant::class,
                'model_id'     => 16,
                'star'         => 4,
                'review'       => 'Best place for authentic flavors and good vibes.',
                'creator_type' => User::class,
                'creator_id'   => 3,
                'editor_type'  => User::class,
                'editor_id'    => 3,
                'created_at'   => Carbon::now(),
                'updated_at'   => Carbon::now()
            ],
            [
                'user_id'      => 3,
                'model_type'   => Restaurant::class,
                'model_id'     => 17,
                'star'         => 5,
                'review'       => 'A hidden gem with mouth-watering dishes!',
                'creator_type' => User::class,
                'creator_id'   => 3,
                'editor_type'  => User::class,
                'editor_id'    => 3,
                'created_at'   => Carbon::now(),
                'updated_at'   => Carbon::now()
            ],
            [
                'user_id'      => 3,
                'model_type'   => Restaurant::class,
                'model_id'     => 18,
                'star'         => 5,
                'review'       => 'Affordable prices with restaurant-quality meals.',
                'creator_type' => User::class,
                'creator_id'   => 3,
                'editor_type'  => User::class,
                'editor_id'    => 3,
                'created_at'   => Carbon::now(),
                'updated_at'   => Carbon::now()
            ],
            [
                'user_id'      => 3,
                'model_type'   => Restaurant::class,
                'model_id'     => 19,
                'star'         => 5,
                'review'       => 'Friendly staff and consistently great food.',
                'creator_type' => User::class,
                'creator_id'   => 3,
                'editor_type'  => User::class,
                'editor_id'    => 3,
                'created_at'   => Carbon::now(),
                'updated_at'   => Carbon::now()
            ],
            [
                'user_id'      => 3,
                'model_type'   => Restaurant::class,
                'model_id'     => 20,
                'star'         => 4,
                'review'       => 'Perfect place for a quick and delicious bite.',
                'creator_type' => User::class,
                'creator_id'   => 3,
                'editor_type'  => User::class,
                'editor_id'    => 3,
                'created_at'   => Carbon::now(),
                'updated_at'   => Carbon::now()
            ],
            [
                'user_id'      => 3,
                'model_type'   => Restaurant::class,
                'model_id'     => 21,
                'star'         => 3,
                'review'       => 'Clean environment with tasty meals.',
                'creator_type' => User::class,
                'creator_id'   => 3,
                'editor_type'  => User::class,
                'editor_id'    => 3,
                'created_at'   => Carbon::now(),
                'updated_at'   => Carbon::now()
            ],
            [
                'user_id'      => 3,
                'model_type'   => Restaurant::class,
                'model_id'     => 22,
                'star'         => 5,
                'review'       => 'Always a satisfying and flavorful experience.',
                'creator_type' => User::class,
                'creator_id'   => 3,
                'editor_type'  => User::class,
                'editor_id'    => 3,
                'created_at'   => Carbon::now(),
                'updated_at'   => Carbon::now()
            ],
            [
                'user_id'      => 3,
                'model_type'   => Restaurant::class,
                'model_id'     => 23,
                'star'         => 4,
                'review'       => 'The food quality never disappoints!',
                'creator_type' => User::class,
                'creator_id'   => 3,
                'editor_type'  => User::class,
                'editor_id'    => 3,
                'created_at'   => Carbon::now(),
                'updated_at'   => Carbon::now()
            ],
            [
                'user_id'      => 3,
                'model_type'   => Restaurant::class,
                'model_id'     => 24,
                'star'         => 5,
                'review'       => 'Great taste, fresh ingredients, and cozy ambiance.',
                'creator_type' => User::class,
                'creator_id'   => 3,
                'editor_type'  => User::class,
                'editor_id'    => 3,
                'created_at'   => Carbon::now(),
                'updated_at'   => Carbon::now()
            ],
            [
                'user_id'      => 3,
                'model_type'   => Restaurant::class,
                'model_id'     => 25,
                'star'         => 4,
                'review'       => 'Delicious food and excellent service every time!',
                'creator_type' => User::class,
                'creator_id'   => 3,
                'editor_type'  => User::class,
                'editor_id'    => 3,
                'created_at'   => Carbon::now(),
                'updated_at'   => Carbon::now()
            ],

            // this is old
            [
                'user_id'      => 3,
                'model_type'   => Restaurant::class,
                'model_id'     => 1,
                'star'         => 4,
                'review'       => 'Absolutely loved the biryani! Portion size was generous and it was still warm when it arrived.',
                'creator_type' => User::class,
                'creator_id'   => 1,
                'editor_type'  => User::class,
                'editor_id'    => 1,
                'created_at'   => Carbon::now(),
                'updated_at'   => Carbon::now()
            ],
            [
                'user_id'      => 3,
                'model_type'   => User::class,
                'model_id'     => 8,
                'star'         => 5,
                'review'       => "Very polite delivery guy. He handled the food with care and arrived earlier than expected.",
                'creator_type' => User::class,
                'creator_id'   => 1,
                'editor_type'  => User::class,
                'editor_id'    => 1,
                'created_at'   => Carbon::now(),
                'updated_at'   => Carbon::now()
            ],
            [
                'user_id'      => 1,
                'model_type'   => User::class,
                'model_id'     => 13,
                'star'         => 4,
                'review'       => 'Delivery was on time and the rider followed all instructions. Good experience overall.',
                'creator_type' => User::class,
                'creator_id'   => 1,
                'editor_type'  => User::class,
                'editor_id'    => 1,
                'created_at'   => Carbon::now(),
                'updated_at'   => Carbon::now()
            ],
            [
                'user_id'      => 1,
                'model_type'   => Restaurant::class,
                'model_id'     => 25,
                'star'         => 5,
                'review'       => "Food was average. The taste was okay but the quantity don’t justify the price.",
                'creator_type' => User::class,
                'creator_id'   => 1,
                'editor_type'  => User::class,
                'editor_id'    => 1,
                'created_at'   => Carbon::now(),
                'updated_at'   => Carbon::now()
            ]
        ];

        $envService = new EnvEditor();
        if ($envService->getValue('DEMO')) {
            Review::insert($reviews);
        }
    }
}
