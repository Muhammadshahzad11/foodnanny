<?php

namespace Database\Seeders;


use App\Enums\Role as EnumRole;
use App\Models\DeliveryLocation;
use App\Models\User;
use Dipokhalder\EnvEditor\EnvEditor;
use Illuminate\Database\Seeder;


class DeliveryLocationTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $envService = new EnvEditor();
        if ($envService->getValue('DEMO')) {
            $deliveryLocations = [
                [
                    'latitude'  => '23.8062771',
                    'longitude' => '90.356362',
                    'address'   => 'Unnamed Road, Section 2, Mirpur, Dhaka, Dhaka Division, Bangladesh'
                ],
                [
                    'latitude'  => '23.8062771',
                    'longitude' => '90.356362',
                    'address'   => 'Dhanmondi Bridge, Dhanmondi, Dhaka, 1209, Dhaka Division, Bangladesh'
                ],
                [
                    'latitude'  => '23.7820624',
                    'longitude' => '90.4160527',
                    'address'   => 'Road 23, Gulshan 1, Gulshan, Dhaka, 1212, Dhaka Division, Bangladesh'
                ],
                [
                    'latitude'  => '23.8062771',
                    'longitude' => '90.356362',
                    'address'   => 'Unnamed Road, Section 2, Mirpur, Dhaka, Dhaka Division, Bangladesh'
                ],
                [
                    'latitude'  => '23.8062771',
                    'longitude' => '90.356362',
                    'address'   => 'Dhanmondi Bridge, Dhanmondi, Dhaka, 1209, Dhaka Division, Bangladesh'
                ],
                [
                    'latitude'  => '23.7820624',
                    'longitude' => '90.4160527',
                    'address'   => 'Road 23, Gulshan 1, Gulshan, Dhaka, 1212, Dhaka Division, Bangladesh'
                ],
                [
                    'latitude'  => '23.8062771',
                    'longitude' => '90.356362',
                    'address'   => 'Unnamed Road, Section 2, Mirpur, Dhaka, Dhaka Division, Bangladesh'
                ],
                [
                    'latitude'  => '23.8062771',
                    'longitude' => '90.356362',
                    'address'   => 'Unnamed Road, Section 2, Mirpur, Dhaka, Dhaka Division, Bangladesh'
                ],
                [
                    'latitude'  => '23.8062771',
                    'longitude' => '90.356362',
                    'address'   => 'Unnamed Road, Section 2, Mirpur, Dhaka, Dhaka Division, Bangladesh'
                ],
                [
                    'latitude'  => '23.8062771',
                    'longitude' => '90.356362',
                    'address'   => 'Unnamed Road, Section 2, Mirpur, Dhaka, Dhaka Division, Bangladesh'
                ],
                [
                    'latitude'  => '23.8062771',
                    'longitude' => '90.356362',
                    'address'   => 'Unnamed Road, Section 2, Mirpur, Dhaka, Dhaka Division, Bangladesh'
                ],
                [
                    'latitude'  => '23.8062771',
                    'longitude' => '90.356362',
                    'address'   => 'Unnamed Road, Section 2, Mirpur, Dhaka, Dhaka Division, Bangladesh'
                ]
            ];

            $i            = 0;
            $deliveryBoys = User::role(EnumRole::DELIVERY_BOY)->get();
            foreach ($deliveryBoys as $deliveryBoy) {
                if (isset($deliveryLocations[$i])) {
                    $deliveryLocations[$i]['user_id']      = $deliveryBoy->id;
                    $deliveryLocations[$i]['creator_type'] = User::class;
                    $deliveryLocations[$i]['creator_id']   = 1;
                    $deliveryLocations[$i]['editor_type']  = User::class;
                    $deliveryLocations[$i]['editor_id']    = 1;
                    $deliveryLocations[$i]['created_at']   = now();
                    $deliveryLocations[$i]['updated_at']   = now();
                    $i++;
                }
            }
            DeliveryLocation::insert($deliveryLocations);
        }
    }
}
