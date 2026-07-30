<?php

namespace Database\Seeders;

use App\Models\Subscriber;
use Illuminate\Database\Seeder;
use Dipokhalder\EnvEditor\EnvEditor;

class SubscriberTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */

    public array $subscribers = [
        [
            'email' => 'john.doe@example.com'
        ],
        [
            'email' => 'jim.rock@example.com'
        ],
        [
            'email' => 'michael.brown@example.com'
        ],
        [
            'email' => 'emily.jones@example.com'
        ],
        [
            'email' => 'david.wilson@example.com'
        ],
        [
            'email' => 'sarah.davis@example.com'
        ],
        [
            'email' => 'chris.martin@example.com'
        ],
        [
            'email' => 'lisa.white@example.com'
        ],
        [
            'email' => 'daniel.moore@example.com'
        ],
        [
            'email' => 'laura.taylor@example.com'
        ],
        [
            'email' => 'chayan.roy@example.com'
        ]
    ];

    public function run()
    {
        $envService = new EnvEditor();
        if ($envService->getValue('DEMO')) {
            foreach ($this->subscribers as $subscriber) {
                Subscriber::create([
                    'email' => $subscriber['email'],
                ]);
            }
        }
    }
}
