<?php

namespace Database\Seeders;

use App\Enums\Ask;
use App\Models\Address;
use App\Enums\Role as EnumRole;
use Dipokhalder\EnvEditor\EnvEditor;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Enums\Status;

class DeliveryBoyTableSeeder2 extends Seeder
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
            $deliveryBoyOne = User::create([
                'name'                 => 'Brittany Kaiser',
                'email'                => 'deliveryboy7@example.com',
                'phone'                => '1719558202',
                'username'             => 'deliveryboy7',
                'email_verified_at'    => now(),
                'password'             => bcrypt('123456'),
                'restaurant_id'        => 0,
                'balance'              => 0,
                'collection'           => 210,
                'status'               => Status::ACTIVE,
                'country_code'         => '+880',
                'is_guest'             => Ask::NO,
                'terms_and_conditions' => Ask::YES,
                'creator_type'         => User::class,
                'creator_id'           => 1,
                'editor_type'          => User::class,
                'editor_id'            => 1
            ]);
            $deliveryBoyOne->assignRole(EnumRole::DELIVERY_BOY);
            Address::create([
                'label'        => 'Home',
                'address'      => 'Unnamed Road, Section 2, Mirpur, Dhaka, Dhaka Division, Bangladesh',
                'apartment'    => rand(0, 999),
                'latitude'     => '23.8062771',
                'longitude'    => '90.356362',
                'user_id'      => $deliveryBoyOne->id,
                'creator_type' => User::class,
                'creator_id'   => 1,
                'editor_type'  => User::class,
                'editor_id'    => 1
            ]);
            Address::create([
                'label'        => 'Work',
                'address'      => 'Road 23, Gulshan 1, Gulshan, Dhaka, 1212, Dhaka Division, Bangladesh',
                'apartment'    => rand(0, 999),
                'latitude'     => '23.7820624',
                'longitude'    => '90.4160527',
                'user_id'      => $deliveryBoyOne->id,
                'creator_type' => User::class,
                'creator_id'   => 1,
                'editor_type'  => User::class,
                'editor_id'    => 1
            ]);

            $deliveryBoyTwo = User::create([
                'name'                 => 'Nathan Pena',
                'email'                => 'deliveryboy8@example.com',
                'phone'                => '1729558203',
                'username'             => 'deliveryboy8',
                'email_verified_at'    => now(),
                'password'             => bcrypt('123456'),
                'restaurant_id'        => 0,
                'balance'              => 0,
                'collection'           => 200,
                'status'               => Status::ACTIVE,
                'country_code'         => '+880',
                'is_guest'             => Ask::NO,
                'terms_and_conditions' => Ask::YES,
                'creator_type'         => User::class,
                'creator_id'           => 1,
                'editor_type'          => User::class,
                'editor_id'            => 1
            ]);
            $deliveryBoyTwo->assignRole(EnumRole::DELIVERY_BOY);
            Address::create([
                'label'        => 'Home',
                'address'      => 'Darus Salam Road, Shah Ali Bag, Mirpur, Dhaka, 1216, Dhaka Division, Bangladesh',
                'apartment'    => rand(0, 999),
                'latitude'     => '23.7956037',
                'longitude'    => '90.3536548',
                'user_id'      => $deliveryBoyTwo->id,
                'creator_type' => User::class,
                'creator_id'   => 1,
                'editor_type'  => User::class,
                'editor_id'    => 1
            ]);
            Address::create([
                'label'        => 'Work',
                'address'      => 'Unnamed Road, Section 2, Mirpur, Dhaka, Dhaka Division, Bangladesh',
                'apartment'    => rand(0, 999),
                'latitude'     => '23.8062771',
                'longitude'    => '90.356362',
                'user_id'      => $deliveryBoyTwo->id,
                'creator_type' => User::class,
                'creator_id'   => 1,
                'editor_type'  => User::class,
                'editor_id'    => 1
            ]);

            $deliveryBoyThree = User::create([
                'name'                 => 'Arnold Torres',
                'email'                => 'deliveryboy9@example.com',
                'phone'                => '1739558205',
                'username'             => 'deliveryboy9',
                'email_verified_at'    => now(),
                'password'             => bcrypt('123456'),
                'restaurant_id'        => 0,
                'balance'              => 0,
                'collection'           => 190,
                'status'               => Status::ACTIVE,
                'country_code'         => '+880',
                'is_guest'             => Ask::NO,
                'terms_and_conditions' => Ask::YES,
                'creator_type'         => User::class,
                'creator_id'           => 1,
                'editor_type'          => User::class,
                'editor_id'            => 1
            ]);
            $deliveryBoyThree->assignRole(EnumRole::DELIVERY_BOY);
            Address::create([
                'label'        => 'Home',
                'address'      => 'Road 23, Gulshan 1, Gulshan, Dhaka, 1212, Dhaka Division, Bangladesh',
                'apartment'    => rand(0, 999),
                'latitude'     => '23.7820624',
                'longitude'    => '90.4160527',
                'user_id'      => $deliveryBoyThree->id,
                'creator_type' => User::class,
                'creator_id'   => 1,
                'editor_type'  => User::class,
                'editor_id'    => 1
            ]);
            Address::create([
                'label'        => 'Work',
                'address'      => 'Darus Salam Road, Shah Ali Bag, Mirpur, Dhaka, 1216, Dhaka Division, Bangladesh',
                'apartment'    => rand(0, 999),
                'latitude'     => '23.7956037',
                'longitude'    => '90.3536548',
                'user_id'      => $deliveryBoyThree->id,
                'creator_type' => User::class,
                'creator_id'   => 1,
                'editor_type'  => User::class,
                'editor_id'    => 1
            ]);

            $deliveryBoyFour = User::create([
                'name'                 => 'Delilah Mcdowell',
                'email'                => 'deliveryboy10@example.com',
                'phone'                => '1759558206',
                'username'             => 'deliveryboy10',
                'email_verified_at'    => now(),
                'password'             => bcrypt('123456'),
                'restaurant_id'        => 0,
                'balance'              => 0,
                'collection'           => 180,
                'status'               => Status::ACTIVE,
                'country_code'         => '+880',
                'is_guest'             => Ask::NO,
                'terms_and_conditions' => Ask::YES,
                'creator_type'         => User::class,
                'creator_id'           => 1,
                'editor_type'          => User::class,
                'editor_id'            => 1
            ]);
            $deliveryBoyFour->assignRole(EnumRole::DELIVERY_BOY);
            Address::create([
                'label'        => 'Home',
                'address'      => 'Unnamed Road, Section 2, Mirpur, Dhaka, Dhaka Division, Bangladesh',
                'apartment'    => rand(0, 999),
                'latitude'     => '23.8062771',
                'longitude'    => '90.356362',
                'user_id'      => $deliveryBoyFour->id,
                'creator_type' => User::class,
                'creator_id'   => 1,
                'editor_type'  => User::class,
                'editor_id'    => 1
            ]);
            Address::create([
                'label'        => 'Work',
                'address'      => 'Road 23, Gulshan 1, Gulshan, Dhaka, 1212, Dhaka Division, Bangladesh',
                'apartment'    => rand(0, 999),
                'latitude'     => '23.7820624',
                'longitude'    => '90.4160527',
                'user_id'      => $deliveryBoyFour->id,
                'creator_type' => User::class,
                'creator_id'   => 1,
                'editor_type'  => User::class,
                'editor_id'    => 1
            ]);

            $deliveryBoyFive = User::create([
                'name'                 => 'Elden Ray',
                'email'                => 'deliveryboy11@example.com',
                'phone'                => '1759558207',
                'username'             => 'deliveryboy11',
                'email_verified_at'    => now(),
                'password'             => bcrypt('123456'),
                'restaurant_id'        => 0,
                'balance'              => 0,
                'collection'           => 170,
                'status'               => Status::ACTIVE,
                'country_code'         => '+880',
                'is_guest'             => Ask::NO,
                'terms_and_conditions' => Ask::YES,
                'creator_type'         => User::class,
                'creator_id'           => 1,
                'editor_type'          => User::class,
                'editor_id'            => 1
            ]);
            $deliveryBoyFive->assignRole(EnumRole::DELIVERY_BOY);
            Address::create([
                'label'        => 'Home',
                'address'      => 'Road 23, Gulshan 1, Gulshan, Dhaka, 1212, Dhaka Division, Bangladesh',
                'apartment'    => rand(0, 999),
                'latitude'     => '23.7820624',
                'longitude'    => '90.4160527',
                'user_id'      => $deliveryBoyFive->id,
                'creator_type' => User::class,
                'creator_id'   => 1,
                'editor_type'  => User::class,
                'editor_id'    => 1
            ]);
            Address::create([
                'label'        => 'Work',
                'address'      => 'Dhanmondi Bridge, Dhanmondi, Dhaka, 1209, Dhaka Division, Bangladesh',
                'apartment'    => rand(0, 999) . ', Dhanmondi',
                'latitude'     => '23.7509735',
                'longitude'    => '90.373582',
                'user_id'      => $deliveryBoyFive->id,
                'creator_type' => User::class,
                'creator_id'   => 1,
                'editor_type'  => User::class,
                'editor_id'    => 1
            ]);

            $deliveryBoySix = User::create([
                'name'                 => 'Luke Arias',
                'email'                => 'deliveryboy12@example.com',
                'phone'                => '1759558210',
                'username'             => 'deliveryboy12',
                'email_verified_at'    => now(),
                'password'             => bcrypt('123456'),
                'restaurant_id'        => 0,
                'balance'              => 0,
                'collection'           => 160,
                'status'               => Status::ACTIVE,
                'country_code'         => '+880',
                'is_guest'             => Ask::NO,
                'terms_and_conditions' => Ask::YES,
                'creator_type'         => User::class,
                'creator_id'           => 1,
                'editor_type'          => User::class,
                'editor_id'            => 1
            ]);
            $deliveryBoySix->assignRole(EnumRole::DELIVERY_BOY);
            Address::create([
                'label'        => 'Home',
                'address'      => 'Unnamed Road, Section 2, Mirpur, Dhaka, Dhaka Division, Bangladesh',
                'apartment'    => rand(0, 999),
                'latitude'     => '23.8062771',
                'longitude'    => '90.356362',
                'user_id'      => $deliveryBoySix->id,
                'creator_type' => User::class,
                'creator_id'   => 1,
                'editor_type'  => User::class,
                'editor_id'    => 1
            ]);
            Address::create([
                'label'        => 'Work',
                'address'      => 'Dhanmondi Bridge, Dhanmondi, Dhaka, 1209, Dhaka Division, Bangladesh',
                'apartment'    => rand(0, 999) . ', Dhanmondi',
                'latitude'     => '23.7509735',
                'longitude'    => '90.373582',
                'user_id'      => $deliveryBoySix->id,
                'creator_type' => User::class,
                'creator_id'   => 1,
                'editor_type'  => User::class,
                'editor_id'    => 1
            ]);
        }
    }
}
