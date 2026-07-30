<?php

namespace Database\Seeders;

use App\Enums\Ask;
use Carbon\Carbon;
use App\Models\Message;
use Illuminate\Database\Seeder;
use App\Enums\MessageChannelType;
use Dipokhalder\EnvEditor\EnvEditor;

class MessageTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */

    public function run(): void
    {
        $envService = new EnvEditor();
        if ($envService->getValue('DEMO')) {
            $messages = [
                [
                    'order_id'     => 10,
                    'user_id'      => 3,
                    'text'         => 'Hi',
                    'is_read'      => Ask::YES,
                    'channel_type' => MessageChannelType::DELIVERY,
                    'created_at'   => Carbon::now(),
                    'updated_at'   => Carbon::now()
                ],
                [
                    'order_id'     => 10,
                    'user_id'      => 8,
                    'text'         => 'Hi Sir.',
                    'is_read'      => Ask::YES,
                    'channel_type' => MessageChannelType::DELIVERY,
                    'created_at'   => Carbon::now(),
                    'updated_at'   => Carbon::now()
                ],
                [
                    'order_id'     => 10,
                    'user_id'      => 3,
                    'text'         => 'Where are you? How much more time will you take to deliver?',
                    'is_read'      => Ask::YES,
                    'channel_type' => MessageChannelType::DELIVERY,
                    'created_at'   => Carbon::now(),
                    'updated_at'   => Carbon::now()
                ],
                [
                    'order_id'     => 10,
                    'user_id'      => 8,
                    'text'         => 'I am in a traffic jam. I think I will reach your address in 10 minutes.',
                    'is_read'      => Ask::YES,
                    'channel_type' => MessageChannelType::DELIVERY,
                    'created_at'   => Carbon::now(),
                    'updated_at'   => Carbon::now()
                ],
                [
                    'order_id'     => 10,
                    'user_id'      => 3,
                    'text'         => 'Please come quickly. I am waiting for you.',
                    'is_read'      => Ask::YES,
                    'channel_type' => MessageChannelType::DELIVERY,
                    'created_at'   => Carbon::now(),
                    'updated_at'   => Carbon::now()
                ],
                [
                    'order_id'     => 10,
                    'user_id'      => 8,
                    'text'         => 'Ok sir.',
                    'is_read'      => Ask::NO,
                    'channel_type' => MessageChannelType::DELIVERY,
                    'created_at'   => Carbon::now(),
                    'updated_at'   => Carbon::now()
                ],
            ];

            foreach ($messages as $message) {
                Message::create([
                    'order_id'     => $message['order_id'],
                    'user_id'      => $message['user_id'],
                    'text'         => $message['text'],
                    'is_read'      => $message['is_read'],
                    'channel_type' => $message['channel_type'],
                    'created_at'   => $message['created_at']
                ]);
            }
        }
    }
}
