<?php

namespace Database\Seeders;

use App\Enums\Ask;
use App\Models\Address;
use App\Enums\Role as EnumRole;
use Dipokhalder\EnvEditor\EnvEditor;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Enums\Status;

class DeliveryBoyTableSeeder extends Seeder
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
                'name'                 => 'Kawsar Uddin',
                'email'                => 'deliveryboy@example.com',
                'phone'                => '1739558202',
                'username'             => 'kawsar-uddin131',
                'email_verified_at'    => now(),
                'password'             => bcrypt('123456'),
                'restaurant_id'        => 0,
                'balance'              => 0,
                'collection'           => 110,
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
                'name'                 => 'Heron Khan',
                'email'                => 'heron@example.com',
                'phone'                => '1739558203',
                'username'             => 'heron-khan131',
                'email_verified_at'    => now(),
                'password'             => bcrypt('123456'),
                'restaurant_id'        => 0,
                'balance'              => 0,
                'collection'           => 110,
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
                'name'                 => 'Nur Mahmud',
                'email'                => 'nurmahmud@example.com',
                'phone'                => '1739558204',
                'username'             => 'nur-mahmud123',
                'email_verified_at'    => now(),
                'password'             => bcrypt('123456'),
                'restaurant_id'        => 0,
                'balance'              => 0,
                'collection'           => 120,
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
                'name'                 => 'Chayan Roy',
                'email'                => 'chayan@example.com',
                'phone'                => '1745356240',
                'username'             => 'chayan-roy123',
                'email_verified_at'    => now(),
                'password'             => bcrypt('123456'),
                'restaurant_id'        => 0,
                'balance'              => 0,
                'collection'           => 130,
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
                'name'                 => 'Khaled Hasan',
                'email'                => 'khaled@example.com',
                'phone'                => '1745356633',
                'username'             => 'khaled-roy123',
                'email_verified_at'    => now(),
                'password'             => bcrypt('123456'),
                'restaurant_id'        => 0,
                'balance'              => 0,
                'collection'           => 140,
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
                'name'                 => 'Miron Mahmud',
                'email'                => 'miron@example.com',
                'phone'                => '1745776633',
                'username'             => 'miron-mahmud123',
                'email_verified_at'    => now(),
                'password'             => bcrypt('123456'),
                'restaurant_id'        => 0,
                'balance'              => 0,
                'collection'           => 150,
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
