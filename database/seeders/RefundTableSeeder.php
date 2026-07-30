<?php

namespace Database\Seeders;

use Carbon\Carbon;
use App\Models\Refund;
use App\Models\Restaurant;
use App\Models\User;
use Illuminate\Database\Seeder;
use Dipokhalder\EnvEditor\EnvEditor;

class RefundTableSeeder extends Seeder
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
            $refunds = [
                [
                    'order_serial_no'  => '0207257',
                    'refund_amount'    => 53.850000,
                    'deduction_amount' => 41.860000,
                    'info'             => '{"order_id":"0207257","order_date":"12:13 PM, 24-07-2025","restaurant_name":"Mandoro Kitchen","order_amount":"53.85","refund_date":"01:18 PM, 24-07-2025","refund_amount":"53.85","deducted_amount":"41.86","deducted_type":"Restaurant","deducted_name":"Mandoro Kitchen","deducted_phone":"+8801881895233","deducted_email":"restaurantowner25@example.com","refunded_to_name":"John Doe","refunded_to_phone":"+8801728660901","refunded_to_email":"admin@example.com"}',
                    'responsible_type' => Restaurant::class,
                    'responsible_id'   => 25,
                    'creator_type'     => User::class,
                    'creator_id'       => 1,
                    'editor_type'      => User::class,
                    'editor_id'        => 1,
                    'created_at'       => Carbon::now(),
                    'updated_at'       => Carbon::now(),
                ],
                [
                    'order_serial_no'  => '0207258',
                    'refund_amount'    => 53.850000,
                    'deduction_amount' => 6.950000,
                    'info'             => '{"order_id":"0207258","order_date":"02:45 PM, 24-07-2025","restaurant_name":"Grainova Express","order_amount":"53.85","refund_date":"03:16 PM, 24-07-2025","refund_amount":"53.85","deducted_amount":"6.95","deducted_type":"Delivery Boy","deducted_name":"Kawsar Uddin","deducted_phone":"+8801739558202","deducted_email":"deliveryboy@example.com","refunded_to_name":"David Kelly","refunded_to_phone":"+8801739558209","refunded_to_email":"customer5@example.com"}',
                    'responsible_type' => User::class,
                    'responsible_id'   => 8,
                    'creator_type'     => User::class,
                    'creator_id'       => 1,
                    'editor_type'      => User::class,
                    'editor_id'        => 1,
                    'created_at'       => Carbon::now(),
                    'updated_at'       => Carbon::now(),
                ], 
                [
                    'order_serial_no'  => '09122537',
                    'refund_amount'    => 27.930000,
                    'deduction_amount' => 23.740000,
                    'info'             => '{"order_id":"09122537","order_date":"12:37 PM, 09-12-2025","restaurant_name":"TacoFuse Mexican","order_amount":"27.93","refund_date":"12:42 PM, 09-12-2025","refund_amount":"27.93","deducted_amount":"23.74","deducted_type":"Restaurant","deducted_name":"TacoFuse Mexican","deducted_phone":"+8801881895212","deducted_email":"restaurantowner2@example.com","refunded_to_name":"Mahbubur Rahman","refunded_to_phone":"+8801739558206","refunded_to_email":"customer2@example.com"}',
                    'responsible_type' => Restaurant::class,
                    'responsible_id'   => 2,
                    'creator_type'     => User::class,
                    'creator_id'       => 1,
                    'editor_type'      => User::class,
                    'editor_id'        => 1,
                    'created_at'       => Carbon::now(),
                    'updated_at'       => Carbon::now(),
                ],
                [
                    'order_serial_no'  => '09122538',
                    'refund_amount'    => 25.710000,
                    'deduction_amount' => 22.710000,
                    'info'             => '{"order_id":"09122538","order_date":"01:16 PM, 09-12-2025","restaurant_name":"TacoFuse Mexican","order_amount":"25.71","refund_date":"01:19 PM, 09-12-2025","refund_amount":"25.71","deducted_amount":"22.71","deducted_type":"Delivery Boy","deducted_name":"Kawsar Uddin","deducted_phone":"+8801739558202","deducted_email":"deliveryboy@example.com","refunded_to_name":"Mahbubur Rahman","refunded_to_phone":"+8801739558206","refunded_to_email":"customer2@example.com"}',
                    'responsible_type' => User::class,
                    'responsible_id'   => 8,
                    'creator_type'     => User::class,
                    'creator_id'       => 1,
                    'editor_type'      => User::class,
                    'editor_id'        => 1,
                    'created_at'       => Carbon::now(),
                    'updated_at'       => Carbon::now(),
                ],
                [
                    'order_serial_no'  => '09122539',
                    'refund_amount'    => 27.730000,
                    'deduction_amount' => 22.480000,
                    'info'             => '{"order_id":"09122539","order_date":"01:50 PM, 09-12-2025","restaurant_name":"EmberStone BBQ","order_amount":"27.73","refund_date":"01:53 PM, 09-12-2025","refund_amount":"27.73","deducted_amount":"22.48","deducted_type":"Restaurant","deducted_name":"EmberStone BBQ","deducted_phone":"+8801881895211","deducted_email":"restaurantowner@example.com","refunded_to_name":"Mahbubur Rahman","refunded_to_phone":"+8801739558206","refunded_to_email":"customer2@example.com"}',
                    'responsible_type' => Restaurant::class,
                    'responsible_id'   => 1,
                    'creator_type'     => User::class,
                    'creator_id'       => 1,
                    'editor_type'      => User::class,
                    'editor_id'        => 1,
                    'created_at'       => Carbon::now(),
                    'updated_at'       => Carbon::now(),
                ], 
                [
                    'order_serial_no'  => '09122540',
                    'refund_amount'    => 19.430000,
                    'deduction_amount' => 19.430000,
                    'info'             => '{"order_id":"09122540","order_date":"02:48 PM, 09-12-2025","restaurant_name":"CharRoot Grillhouse","order_amount":"19.43","refund_date":"02:53 PM, 09-12-2025","refund_amount":"19.43","deducted_amount":"19.43","deducted_type":"Delivery Boy","deducted_name":"Kawsar Uddin","deducted_phone":"+8801739558202","deducted_email":"deliveryboy@example.com","refunded_to_name":"Mahbubur Rahman","refunded_to_phone":"+8801739558206","refunded_to_email":"customer2@example.com"}',
                    'responsible_type' => User::class,
                    'responsible_id'   => 8,
                    'creator_type'     => User::class,
                    'creator_id'       => 1,
                    'editor_type'      => User::class,
                    'editor_id'        => 1,
                    'created_at'       => Carbon::now(),
                    'updated_at'       => Carbon::now(),
                ], 
                [
                    'order_serial_no'  => '09122541',
                    'refund_amount'    => 34.980000,
                    'deduction_amount' => 34.980000,
                    'info'             => '{"order_id":"09122541","order_date":"03:23 PM, 09-12-2025","restaurant_name":"CheezoMania Burgers","order_amount":"34.98","refund_date":"03:28 PM, 09-12-2025","refund_amount":"34.98","deducted_amount":"34.98","deducted_type":"Delivery Boy","deducted_name":"Kawsar Uddin","deducted_phone":"+8801739558202","deducted_email":"deliveryboy@example.com","refunded_to_name":"Keith Morgan","refunded_to_phone":"+8801739558208","refunded_to_email":"customer4@example.com"}',
                    'responsible_type' => User::class,
                    'responsible_id'   => 8,
                    'creator_type'     => User::class,
                    'creator_id'       => 1,
                    'editor_type'      => User::class,
                    'editor_id'        => 1,
                    'created_at'       => Carbon::now(),
                    'updated_at'       => Carbon::now(),
                ],
                [
                    'order_serial_no'  => '09122542',
                    'refund_amount'    => 19.340000,
                    'deduction_amount' => 14.850000,
                    'info'             => '{"order_id":"09122542","order_date":"03:45 PM, 09-12-2025","restaurant_name":"Nepolizza Pizza","order_amount":"19.34","refund_date":"03:49 PM, 09-12-2025","refund_amount":"19.34","deducted_amount":"14.85","deducted_type":"Restaurant","deducted_name":"Nepolizza Pizza","deducted_phone":"+8801881895214","deducted_email":"restaurantowner4@example.com","refunded_to_name":"Mahbubur Rahman","refunded_to_phone":"+8801739558206","refunded_to_email":"customer2@example.com"}',
                    'responsible_type' => Restaurant::class,
                    'responsible_id'   => 4,
                    'creator_type'     => User::class,
                    'creator_id'       => 1,
                    'editor_type'      => User::class,
                    'editor_id'        => 1,
                    'created_at'       => Carbon::now(),
                    'updated_at'       => Carbon::now(),
                ],
                [
                    'order_serial_no'  => '09122543',
                    'refund_amount'    => 29.930000,
                    'deduction_amount' => 24.480000,
                    'info'             => '{"order_id":"09122543","order_date":"04:03 PM, 09-12-2025","restaurant_name":"Grainova Express","order_amount":"29.93","refund_date":"04:08 PM, 09-12-2025","refund_amount":"29.93","deducted_amount":"24.48","deducted_type":"Restaurant","deducted_name":"Grainova Express","deducted_phone":"+8801881895234","deducted_email":"restaurantowner24@example.com","refunded_to_name":"Mahbubur Rahman","refunded_to_phone":"+8801739558206","refunded_to_email":"customer2@example.com"}',
                    'responsible_type' => Restaurant::class,
                    'responsible_id'   => 24,
                    'creator_type'     => User::class,
                    'creator_id'       => 1,
                    'editor_type'      => User::class,
                    'editor_id'        => 1,
                    'created_at'       => Carbon::now(),
                    'updated_at'       => Carbon::now(),
                ], 
                [
                    'order_serial_no'  => '10122544',
                    'refund_amount'    => 39.630000,
                    'deduction_amount' => 33.300000,
                    'info'             => '{"order_id":"10122544","order_date":"10:42 AM, 10-12-2025","restaurant_name":"Nepolizza Pizza","order_amount":"39.63","refund_date":"10:52 AM, 10-12-2025","refund_amount":"39.63","deducted_amount":"33.30","deducted_type":"Restaurant","deducted_name":"Nepolizza Pizza","deducted_phone":"+8801881895214","deducted_email":"restaurantowner4@example.com","refunded_to_name":"Annelies Crius","refunded_to_phone":"+8801719558207","refunded_to_email":"customer7@example.com"}',
                    'responsible_type' => Restaurant::class,
                    'responsible_id'   => 4,
                    'creator_type'     => User::class,
                    'creator_id'       => 1,
                    'editor_type'      => User::class,
                    'editor_id'        => 1,
                    'created_at'       => Carbon::now(),
                    'updated_at'       => Carbon::now(),
                ],
                [
                    'order_serial_no'  => '10122545',
                    'refund_amount'    => 16.530000,
                    'deduction_amount' => 12.000000,
                    'info'             => '{"order_id":"10122545","order_date":"11:25 AM, 10-12-2025","restaurant_name":"Mandoro Kitchen","order_amount":"16.53","refund_date":"11:37 AM, 10-12-2025","refund_amount":"16.53","deducted_amount":"12.00","deducted_type":"Delivery Boy","deducted_name":"Kawsar Uddin","deducted_phone":"+8801739558202","deducted_email":"deliveryboy@example.com","refunded_to_name":"Will Smith","refunded_to_phone":"+8801739558205","refunded_to_email":"customer@example.com"}',
                    'responsible_type' => User::class,
                    'responsible_id'   => 8,
                    'creator_type'     => User::class,
                    'creator_id'       => 1,
                    'editor_type'      => User::class,
                    'editor_id'        => 1,
                    'created_at'       => Carbon::now(),
                    'updated_at'       => Carbon::now(),
                ],
                [
                    'order_serial_no'  => '10122546',
                    'refund_amount'    => 17.270000,
                    'deduction_amount' => 10.000000,
                    'info'             => '{"order_id":"10122546","order_date":"11:37 AM, 10-12-2025","restaurant_name":"Mandoro Kitchen","order_amount":"17.27","refund_date":"11:40 AM, 10-12-2025","refund_amount":"17.27","deducted_amount":"10.00","deducted_type":"Delivery Boy","deducted_name":"Kawsar Uddin","deducted_phone":"+8801739558202","deducted_email":"deliveryboy@example.com","refunded_to_name":"Will Smith","refunded_to_phone":"+8801739558205","refunded_to_email":"customer@example.com"}',
                    'responsible_type' => User::class,
                    'responsible_id'   => 8,
                    'creator_type'     => User::class,
                    'creator_id'       => 1,
                    'editor_type'      => User::class,
                    'editor_id'        => 1,
                    'created_at'       => Carbon::now(),
                    'updated_at'       => Carbon::now(),
                ]
            ];

            foreach ($refunds as $refund) {
                Refund::create([
                    'order_serial_no'  => $refund['order_serial_no'],
                    'refund_amount'    => $refund['refund_amount'],
                    'deduction_amount' => $refund['deduction_amount'],
                    'info'             => $refund['info'],
                    'responsible_type' => $refund['responsible_type'],
                    'responsible_id'   => $refund['responsible_id'],
                    'creator_type'     => $refund['creator_type'],
                    'creator_id'       => $refund['creator_id'],
                    'editor_type'      => $refund['editor_type'],
                    'editor_id'        => $refund['editor_id'],
                    'created_at'       => $refund['created_at'],
                    'updated_at'       => $refund['updated_at']
                ]);
            }
        }
    }
}
