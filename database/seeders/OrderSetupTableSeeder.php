<?php

namespace Database\Seeders;

use App\Enums\Activity;
use App\Models\User;
use Dipokhalder\EnvEditor\EnvEditor;
use App\Models\OrderSetup;
use Illuminate\Database\Seeder;

class OrderSetupTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */

    public array $orderSetups = [
        [
            'restaurant_id'                => 1,
            'food_preparation_time'        => 30,
            'schedule_order_slot_duration' => 15,
            'minimum_order_limit'          => 10,
            'takeaway'                     => Activity::ENABLE,
            'delivery'                     => Activity::ENABLE,
        ],
        [
            'restaurant_id'                => 2,
            'food_preparation_time'        => 20,
            'schedule_order_slot_duration' => 15,
            'minimum_order_limit'          => 20,
            'takeaway'                     => Activity::ENABLE,
            'delivery'                     => Activity::ENABLE,
        ],
        [
            'restaurant_id'                => 3,
            'food_preparation_time'        => 25,
            'schedule_order_slot_duration' => 15,
            'minimum_order_limit'          => 15,
            'takeaway'                     => Activity::ENABLE,
            'delivery'                     => Activity::ENABLE,
        ],
        [
            'restaurant_id'                => 4,
            'food_preparation_time'        => 30,
            'schedule_order_slot_duration' => 15,
            'minimum_order_limit'          => 10,
            'takeaway'                     => Activity::ENABLE,
            'delivery'                     => Activity::ENABLE,
        ],
        [
            'restaurant_id'                => 5,
            'food_preparation_time'        => 35,
            'schedule_order_slot_duration' => 15,
            'minimum_order_limit'          => 15,
            'takeaway'                     => Activity::DISABLE,
            'delivery'                     => Activity::ENABLE,
        ],
        [
            'restaurant_id'                => 6,
            'food_preparation_time'        => 20,
            'schedule_order_slot_duration' => 15,
            'minimum_order_limit'          => 20,
            'takeaway'                     => Activity::ENABLE,
            'delivery'                     => Activity::DISABLE,
        ],
        [
            'restaurant_id'                => 7,
            'food_preparation_time'        => 20,
            'schedule_order_slot_duration' => 15,
            'minimum_order_limit'          => 10,
            'takeaway'                     => Activity::ENABLE,
            'delivery'                     => Activity::ENABLE,
        ],
        [
            'restaurant_id'                => 8,
            'food_preparation_time'        => 25,
            'schedule_order_slot_duration' => 15,
            'minimum_order_limit'          => 15,
            'takeaway'                     => Activity::ENABLE,
            'delivery'                     => Activity::DISABLE,
        ],
        [
            'restaurant_id'                => 9,
            'food_preparation_time'        => 20,
            'schedule_order_slot_duration' => 15,
            'minimum_order_limit'          => 20,
            'takeaway'                     => Activity::DISABLE,
            'delivery'                     => Activity::ENABLE,
        ],
        [
            'restaurant_id'                => 10,
            'food_preparation_time'        => 20,
            'schedule_order_slot_duration' => 15,
            'minimum_order_limit'          => 10,
            'takeaway'                     => Activity::DISABLE,
            'delivery'                     => Activity::ENABLE,
        ],
        [
            'restaurant_id'                => 11,
            'food_preparation_time'        => 25,
            'schedule_order_slot_duration' => 15,
            'minimum_order_limit'          => 15,
            'takeaway'                     => Activity::ENABLE,
            'delivery'                     => Activity::DISABLE,
        ],
        [
            'restaurant_id'                => 12,
            'food_preparation_time'        => 30,
            'schedule_order_slot_duration' => 15,
            'minimum_order_limit'          => 20,
            'takeaway'                     => Activity::ENABLE,
            'delivery'                     => Activity::ENABLE,
        ],
        [
            'restaurant_id'                => 13,
            'food_preparation_time'        => 20,
            'schedule_order_slot_duration' => 15,
            'minimum_order_limit'          => 10,
            'takeaway'                     => Activity::ENABLE,
            'delivery'                     => Activity::ENABLE,
        ],
        [
            'restaurant_id'                => 14,
            'food_preparation_time'        => 25,
            'schedule_order_slot_duration' => 15,
            'minimum_order_limit'          => 15,
            'takeaway'                     => Activity::ENABLE,
            'delivery'                     => Activity::DISABLE,
        ],
        [
            'restaurant_id'                => 15,
            'food_preparation_time'        => 30,
            'schedule_order_slot_duration' => 15,
            'minimum_order_limit'          => 20,
            'takeaway'                     => Activity::ENABLE,
            'delivery'                     => Activity::ENABLE,
        ],
        [
            'restaurant_id'                => 16,
            'food_preparation_time'        => 35,
            'schedule_order_slot_duration' => 15,
            'minimum_order_limit'          => 10,
            'takeaway'                     => Activity::ENABLE,
            'delivery'                     => Activity::ENABLE,
        ],
        [
            'restaurant_id'                => 17,
            'food_preparation_time'        => 20,
            'schedule_order_slot_duration' => 15,
            'minimum_order_limit'          => 15,
            'takeaway'                     => Activity::ENABLE,
            'delivery'                     => Activity::ENABLE,
        ],
        [
            'restaurant_id'                => 18,
            'food_preparation_time'        => 25,
            'schedule_order_slot_duration' => 15,
            'minimum_order_limit'          => 20,
            'takeaway'                     => Activity::ENABLE,
            'delivery'                     => Activity::DISABLE,
        ],
        [
            'restaurant_id'                => 19,
            'food_preparation_time'        => 30,
            'schedule_order_slot_duration' => 15,
            'minimum_order_limit'          => 10,
            'takeaway'                     => Activity::ENABLE,
            'delivery'                     => Activity::ENABLE,
        ],
        [
            'restaurant_id'                => 20,
            'food_preparation_time'        => 35,
            'schedule_order_slot_duration' => 15,
            'minimum_order_limit'          => 15,
            'takeaway'                     => Activity::ENABLE,
            'delivery'                     => Activity::ENABLE,
        ],
        [
            'restaurant_id'                => 21,
            'food_preparation_time'        => 40,
            'schedule_order_slot_duration' => 15,
            'minimum_order_limit'          => 20,
            'takeaway'                     => Activity::DISABLE,
            'delivery'                     => Activity::ENABLE,
        ],
        [
            'restaurant_id'                => 22,
            'food_preparation_time'        => 20,
            'schedule_order_slot_duration' => 15,
            'minimum_order_limit'          => 10,
            'takeaway'                     => Activity::ENABLE,
            'delivery'                     => Activity::DISABLE,
        ],
        [
            'restaurant_id'                => 23,
            'food_preparation_time'        => 25,
            'schedule_order_slot_duration' => 15,
            'minimum_order_limit'          => 15,
            'takeaway'                     => Activity::ENABLE,
            'delivery'                     => Activity::ENABLE,
        ],
        [
            'restaurant_id'                => 24,
            'food_preparation_time'        => 30,
            'schedule_order_slot_duration' => 15,
            'minimum_order_limit'          => 20,
            'takeaway'                     => Activity::DISABLE,
            'delivery'                     => Activity::ENABLE,
        ],
        [
            'restaurant_id'                => 25,
            'food_preparation_time'        => 35,
            'schedule_order_slot_duration' => 15,
            'minimum_order_limit'          => 10,
            'takeaway'                     => Activity::ENABLE,
            'delivery'                     => Activity::ENABLE,
        ],
    ];

    public function run(): void
    {
        $envService = new EnvEditor();
        if ($envService->getValue('DEMO')) {
            foreach ($this->orderSetups as $orderSetup) {
                OrderSetup::create([
                    'restaurant_id'                => $orderSetup['restaurant_id'],
                    'food_preparation_time'        => $orderSetup['food_preparation_time'],
                    'schedule_order_slot_duration' => $orderSetup['schedule_order_slot_duration'],
                    'minimum_order_limit'          => $orderSetup['minimum_order_limit'],
                    'takeaway'                     => $orderSetup['takeaway'],
                    'delivery'                     => $orderSetup['delivery'],
                    'creator_type'                 => User::class,
                    'creator_id'                   => 1,
                    'editor_type'                  => User::class,
                    'editor_id'                    => 1
                ]);
            }
        }
    }
}
