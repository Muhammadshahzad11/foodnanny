<?php

namespace Database\Seeders;

use App\Enums\Ask;
use App\Models\Address;
use App\Enums\Role as EnumRole;
use Dipokhalder\EnvEditor\EnvEditor;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Enums\Status;


class CustomerTableSeeder2 extends Seeder
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
            $customerSix = User::create([
                'name'                 => 'James A. Roe',
                'email'                => 'customer6@example.com',
                'phone'                => '1739558206',
                'username'             => 'customer6',
                'email_verified_at'    => now(),
                'password'             => bcrypt('123456'),
                'restaurant_id'        => 0,
                'status'               => Status::ACTIVE,
                'country_code'         => '+880',
                'is_guest'             => Ask::NO,
                'balance'              => 0,
                'terms_and_conditions' => Ask::YES,
                'creator_type'         => User::class,
                'creator_id'           => 1,
                'editor_type'          => User::class,
                'editor_id'            => 1
            ]);
            $customerSix->assignRole(EnumRole::CUSTOMER);
            Address::create([
                'label'        => 'Home',
                'address'      => 'Senpara Parbata Lane, East Kazipara, Mirpur, Dhaka, Dhaka Division, Bangladesh',
                'apartment'    => rand(0, 999),
                'latitude'     => '23.8028556',
                'longitude'    => '90.3748344',
                'user_id'      => $customerSix->id,
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
                'user_id'      => $customerSix->id,
                'creator_type' => User::class,
                'creator_id'   => 1,
                'editor_type'  => User::class,
                'editor_id'    => 1
            ]);

            $customerSeven = User::create([
                'name'                 => 'Annelies Crius',
                'email'                => 'customer7@example.com',
                'phone'                => '1719558207',
                'username'             => 'customer7',
                'email_verified_at'    => now(),
                'password'             => bcrypt('123456'),
                'restaurant_id'        => 0,
                'status'               => Status::ACTIVE,
                'country_code'         => '+880',
                'is_guest'             => Ask::NO,
                'balance'              => 0,
                'terms_and_conditions' => Ask::YES,
                'creator_type'         => User::class,
                'creator_id'           => 1,
                'editor_type'          => User::class,
                'editor_id'            => 1
            ]);
            $customerSeven->assignRole(EnumRole::CUSTOMER);
            Address::create([
                'label'        => 'Home',
                'address'      => 'Unnamed Road, Section 2, Mirpur, Dhaka, Dhaka Division, Bangladesh',
                'apartment'    => rand(0, 999),
                'latitude'     => '23.8062771',
                'longitude'    => '90.356362',
                'user_id'      => $customerSeven->id,
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
                'user_id'      => $customerSeven->id,
                'creator_type' => User::class,
                'creator_id'   => 1,
                'editor_type'  => User::class,
                'editor_id'    => 1
            ]);

            $customerEight = User::create([
                'name'                 => "Daa'uud Damien",
                'email'                => 'customer8@example.com',
                'phone'                => '1749558208',
                'username'             => 'customer8',
                'email_verified_at'    => now(),
                'password'             => bcrypt('123456'),
                'restaurant_id'        => 0,
                'status'               => Status::ACTIVE,
                'country_code'         => '+880',
                'is_guest'             => Ask::NO,
                'balance'              => 0,
                'terms_and_conditions' => Ask::YES,
                'creator_type'         => User::class,
                'creator_id'           => 1,
                'editor_type'          => User::class,
                'editor_id'            => 1
            ]);
            $customerEight->assignRole(EnumRole::CUSTOMER);
            Address::create([
                'label'        => 'Home',
                'address'      => 'Natore Bangladesh',
                'apartment'    => rand(0, 999) . ', Bongram',
                'latitude'     => '23.7948',
                'longitude'    => '90.4143',
                'user_id'      => $customerEight->id,
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
                'user_id'      => $customerEight->id,
                'creator_type' => User::class,
                'creator_id'   => 1,
                'editor_type'  => User::class,
                'editor_id'    => 1
            ]);

            $customerNine = User::create([
                'name'                 => 'Karrar Bernat',
                'email'                => 'customer9@example.com',
                'phone'                => '1769558209',
                'username'             => 'customer9',
                'email_verified_at'    => now(),
                'password'             => bcrypt('123456'),
                'restaurant_id'        => 0,
                'status'               => Status::ACTIVE,
                'country_code'         => '+880',
                'is_guest'             => Ask::NO,
                'balance'              => 0,
                'terms_and_conditions' => Ask::YES,
                'creator_type'         => User::class,
                'creator_id'           => 1,
                'editor_type'          => User::class,
                'editor_id'            => 1
            ]);
            $customerNine->assignRole(EnumRole::CUSTOMER);
            Address::create([
                'label'        => 'Home',
                'address'      => 'Rajshahi, Bangladesh',
                'apartment'    => rand(0, 999) . ', Vodra',
                'latitude'     => '23.7948',
                'longitude'    => '90.4143',
                'user_id'      => $customerNine->id,
                'creator_type' => User::class,
                'creator_id'   => 1,
                'editor_type'  => User::class,
                'editor_id'    => 1
            ]);
            Address::create([
                'label'        => 'Work',
                'address'      => 'Rajshahi, Bangladesh',
                'apartment'    => rand(0, 999) . ', Vodra',
                'latitude'     => '23.7873',
                'longitude'    => '90.3514',
                'user_id'      => $customerNine->id,
                'creator_type' => User::class,
                'creator_id'   => 1,
                'editor_type'  => User::class,
                'editor_id'    => 1
            ]);

            $customerTen = User::create([
                'name'                 => 'Dáire Malchiah',
                'email'                => 'customer10@example.com',
                'phone'                => '1769558210',
                'username'             => 'customer10',
                'email_verified_at'    => now(),
                'password'             => bcrypt('123456'),
                'restaurant_id'        => 0,
                'status'               => Status::ACTIVE,
                'country_code'         => '+880',
                'is_guest'             => Ask::NO,
                'balance'              => 0,
                'terms_and_conditions' => Ask::YES,
                'creator_type'         => User::class,
                'creator_id'           => 1,
                'editor_type'          => User::class,
                'editor_id'            => 1
            ]);
            $customerTen->assignRole(EnumRole::CUSTOMER);
            Address::create([
                'label'        => 'Home',
                'address'      => 'Darus Salam Road, Shah Ali Bag, Mirpur, Dhaka, 1216, Dhaka Division, Bangladesh',
                'apartment'    => rand(0, 999),
                'latitude'     => '23.7956037',
                'longitude'    => '90.3536548',
                'user_id'      => $customerTen->id,
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
                'user_id'      => $customerTen->id,
                'creator_type' => User::class,
                'creator_id'   => 1,
                'editor_type'  => User::class,
                'editor_id'    => 1
            ]);
        }
    }
}
