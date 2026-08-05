<?php

namespace Database\Seeders;

use App\Models\Collection;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Dipokhalder\EnvEditor\EnvEditor;

class CollectionTableSeeder extends Seeder
{
    public function run(): void
    {
        $envService = new EnvEditor();
        if ($envService->getValue('DEMO')) {
            $collections = [
                [
                    'source_user_id'      => 8,
                    'destination_user_id' => 1,
                    'amount'              => 10.000000,
                    'date'                => Carbon::now(),
                    'creator_type'        => User::class,
                    'creator_id'          => 1,
                    'editor_type'         => User::class,
                    'editor_id'           => 1,
                    'created_at'          => Carbon::now(),
                    'updated_at'          => Carbon::now(),
                ],
                [
                    'source_user_id'      => 9,
                    'destination_user_id' => 1,
                    'amount'              => 50.000000,
                    'date'                => Carbon::now(),
                    'creator_type'        => User::class,
                    'creator_id'          => 1,
                    'editor_type'         => User::class,
                    'editor_id'           => 1,
                    'created_at'          => Carbon::now(),
                    'updated_at'          => Carbon::now(),
                ],
                [
                    'source_user_id'      => 42,
                    'destination_user_id' => 1,
                    'amount'              => 500.000000,
                    'date'                => Carbon::now(),
                    'creator_type'        => User::class,
                    'creator_id'          => 1,
                    'editor_type'         => User::class,
                    'editor_id'           => 1,
                    'created_at'          => Carbon::now(),
                    'updated_at'          => Carbon::now(),
                ],
                [
                    'source_user_id'      => 44,
                    'destination_user_id' => 1,
                    'amount'              => 1000.000000,
                    'date'                => Carbon::now(),
                    'creator_type'        => User::class,
                    'creator_id'          => 1,
                    'editor_type'         => User::class,
                    'editor_id'           => 1,
                    'created_at'          => Carbon::now(),
                    'updated_at'          => Carbon::now(),
                ],
                [
                    'source_user_id'      => 43,
                    'destination_user_id' => 1,
                    'amount'              => 1000.000000,
                    'date'                => Carbon::now(),
                    'creator_type'        => User::class,
                    'creator_id'          => 1,
                    'editor_type'         => User::class,
                    'editor_id'           => 1,
                    'created_at'          => Carbon::now(),
                    'updated_at'          => Carbon::now(),
                ],
                [
                    'source_user_id'      => 13,
                    'destination_user_id' => 1,
                    'amount'              => 100.000000,
                    'date'                => Carbon::now(),
                    'creator_type'        => User::class,
                    'creator_id'          => 1,
                    'editor_type'         => User::class,
                    'editor_id'           => 1,
                    'created_at'          => Carbon::now(),
                    'updated_at'          => Carbon::now(),
                ],
                [
                    'source_user_id'      => 8,
                    'destination_user_id' => 1,
                    'amount'              => 200.000000,
                    'date'                => Carbon::now(),
                    'creator_type'        => User::class,
                    'creator_id'          => 1,
                    'editor_type'         => User::class,
                    'editor_id'           => 1,
                    'created_at'          => Carbon::now(),
                    'updated_at'          => Carbon::now(),
                ],
                [
                    'source_user_id'      => 13,
                    'destination_user_id' => 1, 
                    'amount'              => 100.000000,
                    'date'                => Carbon::now(),
                    'creator_type'        => User::class,
                    'creator_id'          => 1,
                    'editor_type'         => User::class,
                    'editor_id'           => 1,
                    'created_at'          => Carbon::now(),
                    'updated_at'          => Carbon::now(),
                ],
                [
                    'source_user_id'      => 8,
                    'destination_user_id' => 1,
                    'amount'              => 140.000000,
                    'date'                => Carbon::now(),
                    'creator_type'        => User::class,
                    'creator_id'          => 1,
                    'editor_type'         => User::class,
                    'editor_id'           => 1,
                    'created_at'          => Carbon::now(),
                    'updated_at'          => Carbon::now(),
                ],
                [
                    'source_user_id'      => 39,
                    'destination_user_id' => 1,
                    'amount'              => 120.000000,
                    'date'                => Carbon::now(),
                    'creator_type'        => User::class,
                    'creator_id'          => 1,
                    'editor_type'         => User::class,
                    'editor_id'           => 1,
                    'created_at'          => Carbon::now(),
                    'updated_at'          => Carbon::now(),
                ],
                [
                    'source_user_id'      => 40,
                    'destination_user_id' => 1,
                    'amount'              => 110.000000,
                    'date'                => Carbon::now(),
                    'creator_type'        => User::class,
                    'creator_id'          => 1,
                    'editor_type'         => User::class,
                    'editor_id'           => 1,
                    'created_at'          => Carbon::now(),
                    'updated_at'          => Carbon::now(),
                ],
                [
                    'source_user_id'      => 41,
                    'destination_user_id' => 1,
                    'amount'              => 80.000000,
                    'date'                => Carbon::now(),
                    'creator_type'        => User::class,
                    'creator_id'          => 1,
                    'editor_type'         => User::class,
                    'editor_id'           => 1,
                    'created_at'          => Carbon::now(),
                    'updated_at'          => Carbon::now(),
                ], 
                [
                    'source_user_id'      => 42,
                    'destination_user_id' => 1,
                    'amount'              => 90.000000,
                    'date'                => Carbon::now(),
                    'creator_type'        => User::class,
                    'creator_id'          => 1,
                    'editor_type'         => User::class,
                    'editor_id'           => 1,
                    'created_at'          => Carbon::now(),
                    'updated_at'          => Carbon::now(),
                ],
                [
                    'source_user_id'      => 43,
                    'destination_user_id' => 1,
                    'amount'              => 160.000000,
                    'date'                => Carbon::now(),
                    'creator_type'        => User::class,
                    'creator_id'          => 1,
                    'editor_type'         => User::class,
                    'editor_id'           => 1,
                    'created_at'          => Carbon::now(),
                    'updated_at'          => Carbon::now(),
                ],
                [
                    'source_user_id'      => 44,
                    'destination_user_id' => 1,
                    'amount'              => 190.000000,
                    'date'                => Carbon::now(),
                    'creator_type'        => User::class,
                    'creator_id'          => 1,
                    'editor_type'         => User::class,
                    'editor_id'           => 1,
                    'created_at'          => Carbon::now(),
                    'updated_at'          => Carbon::now(),
                ]
            ];

            foreach ($collections as $collection) {
                Collection::create($collection);
            }
        }
    }
}
