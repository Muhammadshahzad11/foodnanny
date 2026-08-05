<?php

namespace Database\Seeders;

use Carbon\Carbon;
use App\Models\Favorite;
use Illuminate\Database\Seeder;
use Dipokhalder\EnvEditor\EnvEditor;

class FavoriteTableSeeder extends Seeder
{
    public function run(): void
    {
        $envService = new EnvEditor();
        if ($envService->getValue('DEMO')) {
            $favorites = [
                // admin
                [
                    'restaurant_id' => 25,
                    'user_id'       => 1,
                    'created_at'    => Carbon::now(),
                    'updated_at'    => Carbon::now(),
                ],
                [
                    'restaurant_id' => 24,
                    'user_id'       => 1,
                    'created_at'    => Carbon::now(),
                    'updated_at'    => Carbon::now(),
                ],
                [
                    'restaurant_id' => 23,
                    'user_id'       => 1,
                    'created_at'    => Carbon::now(),
                    'updated_at'    => Carbon::now(),
                ],
                                [
                    'restaurant_id' => 22,
                    'user_id'       => 1,
                    'created_at'    => Carbon::now(),
                    'updated_at'    => Carbon::now(),
                ],
                [
                    'restaurant_id' => 21,
                    'user_id'       => 1,
                    'created_at'    => Carbon::now(),
                    'updated_at'    => Carbon::now(),
                ],
                [
                    'restaurant_id' => 1,
                    'user_id'       => 1,
                    'created_at'    => Carbon::now(),
                    'updated_at'    => Carbon::now(),
                ],
                                [
                    'restaurant_id' => 2,
                    'user_id'       => 1,
                    'created_at'    => Carbon::now(),
                    'updated_at'    => Carbon::now(),
                ],
                [
                    'restaurant_id' => 3,
                    'user_id'       => 1,
                    'created_at'    => Carbon::now(),
                    'updated_at'    => Carbon::now(),
                ],
                [
                    'restaurant_id' => 4,
                    'user_id'       => 1,
                    'created_at'    => Carbon::now(),
                    'updated_at'    => Carbon::now(),
                ],
                // customer
                 [
                    'restaurant_id' => 25,
                    'user_id'       => 3,
                    'created_at'    => Carbon::now(),
                    'updated_at'    => Carbon::now(),
                ],
                [
                    'restaurant_id' => 24,
                    'user_id'       => 3,
                    'created_at'    => Carbon::now(),
                    'updated_at'    => Carbon::now(),
                ],
                [
                    'restaurant_id' => 23,
                    'user_id'       => 3,
                    'created_at'    => Carbon::now(),
                    'updated_at'    => Carbon::now(),
                ],
                [
                    'restaurant_id' => 22,
                    'user_id'       => 3,
                    'created_at'    => Carbon::now(),
                    'updated_at'    => Carbon::now(),
                ],
                [
                    'restaurant_id' => 21,
                    'user_id'       => 3,
                    'created_at'    => Carbon::now(),
                    'updated_at'    => Carbon::now(),
                ],
                [
                    'restaurant_id' => 1,
                    'user_id'       => 3,
                    'created_at'    => Carbon::now(),
                    'updated_at'    => Carbon::now(),
                ],
                [
                    'restaurant_id' => 2,
                    'user_id'       => 3,
                    'created_at'    => Carbon::now(),
                    'updated_at'    => Carbon::now(),
                ],
                [
                    'restaurant_id' => 3,
                    'user_id'       => 3,
                    'created_at'    => Carbon::now(),
                    'updated_at'    => Carbon::now(),
                ],
                [
                    'restaurant_id' => 4,
                    'user_id'       => 3,
                    'created_at'    => Carbon::now(),
                    'updated_at'    => Carbon::now(),
                ],
                // deliveryboy
                [
                    'restaurant_id' => 2,
                    'user_id'       => 8,
                    'created_at'    => Carbon::now(),
                    'updated_at'    => Carbon::now(),
                ],
                [
                    'restaurant_id' => 3,
                    'user_id'       => 8,
                    'created_at'    => Carbon::now(),
                    'updated_at'    => Carbon::now(),
                ],
                [
                    'restaurant_id' => 4,
                    'user_id'       => 8,
                    'created_at'    => Carbon::now(),
                    'updated_at'    => Carbon::now(),
                ], 
            ];

            foreach ($favorites as $favorite) {
                Favorite::create($favorite);
            }
        }
    }
}
