<?php

namespace Database\Seeders;

use App\Enums\SwitchBox;
use Illuminate\Database\Seeder;
use App\Models\NotificationAlert;

class NotificationAlertTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public array $notificationAlerts = [
        'name'    => [
            'Order Pending Message',
            'Order Confirmation Message',
            'Order Preparing Message',
            'Order Prepared Message',
            'Order Out For Delivery Message',
            'Order Delivered Message',
            'Order Canceled Message',
            'Order Rejected Message',
            'Order Returned Message',
            'Delivery Boy Order Accepted Message',
            'Delivery Boy Order Received Message',
            'Restaurant Owner New Order Message',
        ],
        'message' => [
            'Your order is successfully placed.',
            'Your order is Confirmed.',
            'Your order is being preparing.',
            'Your order is prepared.',
            'Your order is out for delivery.',
            'Your order is successfully delivered.',
            'Your order is canceled.',
            'Your order is rejected.',
            'Your order is returned.',
            'The delivery boy has accepted the order.',
            'The delivery boy has picked up the order.',
            'You have a new order.',
        ]
    ];

    public function run()
    {
        foreach ($this->notificationAlerts['name'] as $key => $notificationAlert) {
            NotificationAlert::create([
                'name'                      => $notificationAlert,
                'language'                  => str_replace(' ', '_', strtolower($notificationAlert)),
                'mail_message'              => $this->notificationAlerts['message'][$key],
                'sms_message'               => $this->notificationAlerts['message'][$key],
                'push_notification_message' => $this->notificationAlerts['message'][$key],
                'mail'                      => SwitchBox::OFF,
                'sms'                       => SwitchBox::OFF,
                'push_notification'         => SwitchBox::OFF,
            ]);
        }
    }
}
