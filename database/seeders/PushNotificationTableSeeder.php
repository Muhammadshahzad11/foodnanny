<?php

namespace Database\Seeders;

use App\Models\PushNotification;
use App\Models\User;
use Illuminate\Database\Seeder;
use Dipokhalder\EnvEditor\EnvEditor;

class PushNotificationTableSeeder extends Seeder
{
    public function run(): void
    {
        $envService = new EnvEditor();
        if ($envService->getValue('DEMO')) {
            $pushNotifications = [
                [
                    'title'        => 'Any non-veg offer',
                    'description'  => 'Savory and satisfying offer. 5% off on any non-veg item.',
                    'role_id'      => 4,
                    'creator_type' => User::class,
                    'creator_id'   => 1,
                    'editor_type'  => User::class,
                    'editor_id'    => 1,
                    'created_at'   => now(),
                    'updated_at'   => now()
                ],
                [
                    'title'        => 'Rainy Day Incentive',
                    'description'  => 'Earn extra $2 per delivery during the rain.',
                    'role_id'      => 3,
                    'creator_type' => User::class,
                    'creator_id'   => 1,
                    'editor_type'  => User::class,
                    'editor_id'    => 1,
                    'created_at'   => now(),
                    'updated_at'   => now()
                ],
                [
                    'title'        => 'New beef offer',
                    'description'  => 'Uplifting anytime offer. 7% off on any beef item.',
                    'role_id'      => 4,
                    'creator_type' => User::class,
                    'creator_id'   => 1,
                    'editor_type'  => User::class,
                    'editor_id'    => 1,
                    'created_at'   => now(),
                    'updated_at'   => now()
                ],
                [
                    'title'        => 'Weekend Delivery Bonus',
                    'description'  => 'Complete 20 deliveries this weekend and get a $50 bonus!',
                    'role_id'      => 3,
                    'creator_type' => User::class,
                    'creator_id'   => 1,
                    'editor_type'  => User::class,
                    'editor_id'    => 1,
                    'created_at'   => now(),
                    'updated_at'   => now()
                ],
                [
                    'title'        => 'Commission Discount',
                    'description'  => 'Get 50% off on commission fees for the next 3 days.',
                    'role_id'      => 2,
                    'creator_type' => User::class,
                    'creator_id'   => 1,
                    'editor_type'  => User::class,
                    'editor_id'    => 1,
                    'created_at'   => now(),
                    'updated_at'   => now()
                ],
                [
                    'title'        => 'Boost Your Sales',
                    'description'  => 'Try our new "Featured Restaurant" plan to increase orders by 30%.',
                    'role_id'      => 2,
                    'creator_type' => User::class,
                    'creator_id'   => 1,
                    'editor_type'  => User::class,
                    'editor_id'    => 1,
                    'created_at'   => now(),
                    'updated_at'   => now()
                ], 
                [
                    'title'        => 'High Demand Area Alert',
                    'description'  => 'Head to Downtown for 2x orders!',
                    'role_id'      => 3,
                    'creator_type' => User::class,
                    'creator_id'   => 1,
                    'editor_type'  => User::class,
                    'editor_id'    => 1,
                    'created_at'   => now(),
                    'updated_at'   => now()
                ],
                [
                    'title'        => 'Menu Optimization',
                    'description'  => 'Add photos to your menu items to increase sales by 20%.',
                    'role_id'      => 2,
                    'creator_type' => User::class,
                    'creator_id'   => 1,
                    'editor_type'  => User::class,
                    'editor_id'    => 1,
                    'created_at'   => now(),
                    'updated_at'   => now()
                ],
                [
                    'title'        => 'Holiday Preparation',
                    'description'  => 'Get ready for the upcoming holiday rush! Stock up now.',
                    'role_id'      => 2,
                    'creator_type' => User::class,
                    'creator_id'   => 1,
                    'editor_type'  => User::class,
                    'editor_id'    => 1,
                    'created_at'   => now(),
                    'updated_at'   => now()
                ],
                [
                    'title'        => 'Weekly Performance',
                    'description'  => 'get 5% more commission for the next 3 days.',
                    'role_id'      => 3,
                    'creator_type' => User::class,
                    'creator_id'   => 1,
                    'editor_type'  => User::class,
                    'editor_id'    => 1,
                    'created_at'   => now(),
                    'updated_at'   => now()
                ], 
                [
                    'title'        => 'Holiday Bonus',
                    'description'  => 'Sell more than 100k and get 5% extra commission.',
                    'role_id'      => 2,
                    'creator_type' => User::class,
                    'creator_id'   => 1,
                    'editor_type'  => User::class,
                    'editor_id'    => 1,
                    'created_at'   => now(),
                    'updated_at'   => now()
                ]
            ];
            foreach ($pushNotifications as $pushNotification) {
                PushNotification::create($pushNotification);
            }
        }
    }
}
