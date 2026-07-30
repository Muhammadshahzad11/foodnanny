<?php

namespace Database\Seeders;

use App\Enums\Status;
use App\Models\Benefit;
use App\Models\User;
use Illuminate\Database\Seeder;
use Dipokhalder\EnvEditor\EnvEditor;


class BenefitTableSeeder extends Seeder
{
    public function run(): void
    {
        $envService = new EnvEditor();
        if ($envService->getValue('DEMO')) {
            $benefits = [
                [
                    'title'        => 'Easy to Order',
                    'description'  => 'Ordering is quick and simple with our user-friendly platform.',
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
                    'title'        => 'Fast Delivery',
                    'description'  => 'Our deliveries are fast and efficient, ensuring you receive your order promptly.',
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
                    'title'        => 'Secure Payment',
                    'description'  => 'Your payment is safe and secure with our trusted payment system.',
                    'status'       => Status::ACTIVE,
                    'sort'         => 3,
                    'creator_type' => User::class,
                    'creator_id'   => 1,
                    'editor_type'  => User::class,
                    'editor_id'    => 1,
                    'created_at'   => now(),
                    'updated_at'   => now()
                ],

            ];
            foreach ($benefits as $benefit) {
                $benefitObject = Benefit::create($benefit);
                if (file_exists(public_path('/images/seeder/benefit/' . strtolower(str_replace(' ', '_', $benefit['title'])) . '.png'))) {
                    $benefitObject->addMedia(public_path('/images/seeder/benefit/' . strtolower(str_replace(' ', '_', $benefit['title'])) . '.png'))->preservingOriginal()->toMediaCollection('benefit');
                }
            }
        }
    }
}
