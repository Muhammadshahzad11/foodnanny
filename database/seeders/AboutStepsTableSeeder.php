<?php

namespace Database\Seeders;

use App\Enums\Status;
use App\Models\AboutStep;
use App\Models\User;
use Illuminate\Database\Seeder;
use Dipokhalder\EnvEditor\EnvEditor;


class  AboutStepsTableSeeder extends Seeder
{

    public function run(): void
    {
        $envService = new EnvEditor();
        if ($envService->getValue('DEMO')) {
            $aboutSteps = [
                [
                    'title'        => 'All Kinds of Foods',
                    'description'  => 'Discover a wide variety of foods from different cuisines and cultures.',
                    'status'       => Status::ACTIVE,
                    'sort'         => 1,
                    'creator_type' => User::class,
                    'creator_id'   => 1,
                    'editor_type'  => User::class,
                    'editor_id'    => 1,
                    'created_at'   => now(),
                    'updated_at'   => now()
                ],
                [
                    'title'        => 'Fresh Foods',
                    'description'  => 'Savor the goodness of fresh foods, straight from nature to you.',
                    'status'       => Status::ACTIVE,
                    'sort'         => 2,
                    'creator_type' => User::class,
                    'creator_id'   => 1,
                    'editor_type'  => User::class,
                    'editor_id'    => 1,
                    'created_at'   => now(),
                    'updated_at'   => now()
                ],
                [
                    'title'        => 'Best Taste',
                    'description'  => 'Experience the finest flavors with our dishes known for their exceptional taste.',
                    'status'       => Status::ACTIVE,
                    'sort'         => 3,
                    'creator_type' => User::class,
                    'creator_id'   => 1,
                    'editor_type'  => User::class,
                    'editor_id'    => 1,
                    'created_at'   => now(),
                    'updated_at'   => now()
                ],
                [
                    'title'        => 'On Time Delivery',
                    'description'  => 'Count on us for timely delivery of your orders and ensuring your satisfaction.',
                    'status'       => Status::ACTIVE,
                    'sort'         => 4,
                    'creator_type' => User::class,
                    'creator_id'   => 1,
                    'editor_type'  => User::class,
                    'editor_id'    => 1,
                    'created_at'   => now(),
                    'updated_at'   => now()
                ]
            ];
            foreach ($aboutSteps as $aboutStep) {
                $aboutStepsObject = AboutStep::create($aboutStep);
                if (file_exists(public_path('/images/seeder/about-steps/' . strtolower(str_replace(' ', '_', $aboutStep['title'])) . '.png'))) {
                    $aboutStepsObject->addMedia(public_path('/images/seeder/about-steps/' . strtolower(str_replace(' ', '_', $aboutStep['title'])) . '.png'))->preservingOriginal()->toMediaCollection('about-steps');
                }
            }
        }
    }
}
