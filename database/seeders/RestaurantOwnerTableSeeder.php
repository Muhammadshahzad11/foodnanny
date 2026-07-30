<?php

namespace Database\Seeders;

use App\Enums\Ask;
use App\Models\Address;
use App\Enums\Role as EnumRole;
use App\Models\Restaurant;
use Dipokhalder\EnvEditor\EnvEditor;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Enums\Status;


class RestaurantOwnerTableSeeder extends Seeder
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
            $restaurantOwnerOne = User::create([
                'name'                 => 'Kiron Khan',
                'email'                => 'restaurantowner@example.com',
                'phone'                => '1726449801',
                'username'             => 'restaurantowner1',
                'email_verified_at'    => now(),
                'password'             => bcrypt('123456'),
                'restaurant_id'        => 1,
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
            $restaurantOwnerOne->assignRole(EnumRole::RESTAURANT_OWNER);
            Address::create([
                'label'        => 'Home',
                'address'      => 'Senpara Parbata Lane, East Kazipara, Mirpur, Dhaka, Dhaka Division, Bangladesh',
                'apartment'    => rand(0, 999),
                'latitude'     => '23.8028556',
                'longitude'    => '90.3748344',
                'user_id'      => $restaurantOwnerOne->id,
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
                'user_id'      => $restaurantOwnerOne->id,
                'creator_type' => User::class,
                'creator_id'   => 1,
                'editor_type'  => User::class,
                'editor_id'    => 1
            ]);
            $restaurantOwnerTwo = User::create([
                'name'                 => 'Felix Hudson',
                'email'                => 'restaurantowner2@example.com',
                'phone'                => '1726449802',
                'username'             => 'restaurantowner2',
                'email_verified_at'    => now(),
                'password'             => bcrypt('123456'),
                'restaurant_id'        => 2,
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
            $restaurantOwnerTwo->assignRole(EnumRole::RESTAURANT_OWNER);
            Address::create([
                'label'        => 'Home',
                'address'      => 'Road 23, Gulshan 1, Gulshan, Dhaka, 1212, Dhaka Division, Bangladesh',
                'apartment'    => rand(0, 999),
                'latitude'     => '23.7820624',
                'longitude'    => '90.4160527',
                'user_id'      => $restaurantOwnerTwo->id,
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
                'user_id'      => $restaurantOwnerTwo->id,
                'creator_type' => User::class,
                'creator_id'   => 1,
                'editor_type'  => User::class,
                'editor_id'    => 1
            ]);
            $restaurantOwnerThree = User::create([
                'name'                 => 'Jonathon Peters',
                'email'                => 'restaurantowner3@example.com',
                'phone'                => '1726449803',
                'username'             => 'restaurantowner3',
                'email_verified_at'    => now(),
                'password'             => bcrypt('123456'),
                'restaurant_id'        => 3,
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
            $restaurantOwnerThree->assignRole(EnumRole::RESTAURANT_OWNER);
            Address::create([
                'label'        => 'Home',
                'address'      => 'Senpara Parbata Lane, East Kazipara, Mirpur, Dhaka, Dhaka Division, Bangladesh',
                'apartment'    => rand(0, 999),
                'latitude'     => '23.8028556',
                'longitude'    => '90.3748344',
                'user_id'      => $restaurantOwnerThree->id,
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
                'user_id'      => $restaurantOwnerThree->id,
                'creator_type' => User::class,
                'creator_id'   => 1,
                'editor_type'  => User::class,
                'editor_id'    => 1
            ]);
            $restaurantOwnerFour = User::create([
                'name'                 => 'Jean Arnold',
                'email'                => 'restaurantowner4@example.com',
                'phone'                => '1726449804',
                'username'             => 'restaurantowner4',
                'email_verified_at'    => now(),
                'password'             => bcrypt('123456'),
                'restaurant_id'        => 4,
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
            $restaurantOwnerFour->assignRole(EnumRole::RESTAURANT_OWNER);
            Address::create([
                'label'        => 'Home',
                'address'      => 'Darus Salam Road, Shah Ali Bag, Mirpur, Dhaka, 1216, Dhaka Division, Bangladesh',
                'apartment'    => rand(0, 999),
                'latitude'     => '23.7956037',
                'longitude'    => '90.3536548',
                'user_id'      => $restaurantOwnerFour->id,
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
                'user_id'      => $restaurantOwnerFour->id,
                'creator_type' => User::class,
                'creator_id'   => 1,
                'editor_type'  => User::class,
                'editor_id'    => 1
            ]);
            $restaurantOwnerFive = User::create([
                'name'                 => 'Joseph Tyler',
                'email'                => 'restaurantowner5@example.com',
                'phone'                => '1726449805',
                'username'             => 'restaurantowner5',
                'email_verified_at'    => now(),
                'password'             => bcrypt('123456'),
                'restaurant_id'        => 5,
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
            $restaurantOwnerFive->assignRole(EnumRole::RESTAURANT_OWNER);
            Address::create([
                'label'        => 'Home',
                'address'      => 'Unnamed Road, Section 2, Mirpur, Dhaka, Dhaka Division, Bangladesh',
                'apartment'    => rand(0, 999),
                'latitude'     => '23.8062771',
                'longitude'    => '90.356362',
                'user_id'      => $restaurantOwnerFive->id,
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
                'user_id'      => $restaurantOwnerFive->id,
                'creator_type' => User::class,
                'creator_id'   => 1,
                'editor_type'  => User::class,
                'editor_id'    => 1
            ]);
            $restaurantOwnerSix = User::create([
                'name'                 => 'Francis Lucas',
                'email'                => 'restaurantowner6@example.com',
                'phone'                => '1726449806',
                'username'             => 'restaurantowner6',
                'email_verified_at'    => now(),
                'password'             => bcrypt('123456'),
                'restaurant_id'        => 6,
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
            $restaurantOwnerSix->assignRole(EnumRole::RESTAURANT_OWNER);
            Address::create([
                'label'        => 'Home',
                'address'      => 'Road 23, Gulshan 1, Gulshan, Dhaka, 1212, Dhaka Division, Bangladesh',
                'apartment'    => rand(0, 999),
                'latitude'     => '23.7820624',
                'longitude'    => '90.4160527',
                'user_id'      => $restaurantOwnerSix->id,
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
                'user_id'      => $restaurantOwnerSix->id,
                'creator_type' => User::class,
                'creator_id'   => 1,
                'editor_type'  => User::class,
                'editor_id'    => 1
            ]);
            $restaurantOwnerSeven = User::create([
                'name'                 => 'Mack Phillips',
                'email'                => 'restaurantowner7@example.com',
                'phone'                => '1726449807',
                'username'             => 'restaurantowner7',
                'email_verified_at'    => now(),
                'password'             => bcrypt('123456'),
                'restaurant_id'        => 7,
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
            $restaurantOwnerSeven->assignRole(EnumRole::RESTAURANT_OWNER);
            Address::create([
                'label'        => 'Home',
                'address'      => 'Senpara Parbata Lane, East Kazipara, Mirpur, Dhaka, Dhaka Division, Bangladesh',
                'apartment'    => rand(0, 999),
                'latitude'     => '23.8028556',
                'longitude'    => '90.3748344',
                'user_id'      => $restaurantOwnerSeven->id,
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
                'user_id'      => $restaurantOwnerSeven->id,
                'creator_type' => User::class,
                'creator_id'   => 1,
                'editor_type'  => User::class,
                'editor_id'    => 1
            ]);
            $restaurantOwnerEight = User::create([
                'name'                 => 'Preston Paul',
                'email'                => 'restaurantowner8@example.com',
                'phone'                => '1726449808',
                'username'             => 'restaurantowner8',
                'email_verified_at'    => now(),
                'password'             => bcrypt('123456'),
                'restaurant_id'        => 8,
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
            $restaurantOwnerEight->assignRole(EnumRole::RESTAURANT_OWNER);
            Address::create([
                'label'        => 'Home',
                'address'      => 'Darus Salam Road, Shah Ali Bag, Mirpur, Dhaka, 1216, Dhaka Division, Bangladesh',
                'apartment'    => rand(0, 999),
                'latitude'     => '23.7956037',
                'longitude'    => '90.3536548',
                'user_id'      => $restaurantOwnerEight->id,
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
                'user_id'      => $restaurantOwnerEight->id,
                'creator_type' => User::class,
                'creator_id'   => 1,
                'editor_type'  => User::class,
                'editor_id'    => 1
            ]);
            $restaurantOwnerNine = User::create([
                'name'                 => 'Thomas Keller',
                'email'                => 'restaurantowner9@example.com',
                'phone'                => '1726449809',
                'username'             => 'restaurantowner9',
                'email_verified_at'    => now(),
                'password'             => bcrypt('123456'),
                'restaurant_id'        => 9,
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
            $restaurantOwnerNine->assignRole(EnumRole::RESTAURANT_OWNER);
            Address::create([
                'label'        => 'Home',
                'address'      => 'Unnamed Road, Section 2, Mirpur, Dhaka, Dhaka Division, Bangladesh',
                'apartment'    => rand(0, 999),
                'latitude'     => '23.8062771',
                'longitude'    => '90.356362',
                'user_id'      => $restaurantOwnerNine->id,
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
                'user_id'      => $restaurantOwnerNine->id,
                'creator_type' => User::class,
                'creator_id'   => 1,
                'editor_type'  => User::class,
                'editor_id'    => 1
            ]);
            $restaurantOwnerTen = User::create([
                'name'                 => 'Vera Richardson',
                'email'                => 'restaurantowner10@example.com',
                'phone'                => '1726449810',
                'username'             => 'restaurantowner10',
                'email_verified_at'    => now(),
                'password'             => bcrypt('123456'),
                'restaurant_id'        => 10,
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
            $restaurantOwnerTen->assignRole(EnumRole::RESTAURANT_OWNER);
            Address::create([
                'label'        => 'Home',
                'address'      => 'Road 23, Gulshan 1, Gulshan, Dhaka, 1212, Dhaka Division, Bangladesh',
                'apartment'    => rand(0, 999),
                'latitude'     => '23.7820624',
                'longitude'    => '90.4160527',
                'user_id'      => $restaurantOwnerTen->id,
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
                'user_id'      => $restaurantOwnerTen->id,
                'creator_type' => User::class,
                'creator_id'   => 1,
                'editor_type'  => User::class,
                'editor_id'    => 1
            ]);
            $restaurantOwnerEleven = User::create([
                'name'                 => 'Enrique Waters',
                'email'                => 'restaurantowner11@example.com',
                'phone'                => '1726449811',
                'username'             => 'restaurantowner11',
                'email_verified_at'    => now(),
                'password'             => bcrypt('123456'),
                'restaurant_id'        => 12,
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
            $restaurantOwnerEleven->assignRole(EnumRole::RESTAURANT_OWNER);
            Address::create([
                'label'        => 'Home',
                'address'      => 'Dhanmondi Bridge, Dhanmondi, Dhaka, 1209, Dhaka Division, Bangladesh',
                'apartment'    => rand(0, 999) . ', Dhanmondi',
                'latitude'     => '23.7509735',
                'longitude'    => '90.373582',
                'user_id'      => $restaurantOwnerEleven->id,
                'creator_type' => User::class,
                'creator_id'   => 1,
                'editor_type'  => User::class,
                'editor_id'    => 1
            ]);
            Address::create([
                'label'        => 'Work',
                'address'      => 'Senpara Parbata Lane, East Kazipara, Mirpur, Dhaka, Dhaka Division, Bangladesh',
                'apartment'    => rand(0, 999),
                'latitude'     => '23.8028556',
                'longitude'    => '90.3748344',
                'user_id'      => $restaurantOwnerEleven->id,
                'creator_type' => User::class,
                'creator_id'   => 1,
                'editor_type'  => User::class,
                'editor_id'    => 1
            ]);
            $restaurantOwnerTwelve = User::create([
                'name'                 => 'Harold Stewart',
                'email'                => 'restaurantowner12@example.com',
                'phone'                => '1726449812',
                'username'             => 'restaurantowner12',
                'email_verified_at'    => now(),
                'password'             => bcrypt('123456'),
                'restaurant_id'        => 13,
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
            $restaurantOwnerTwelve->assignRole(EnumRole::RESTAURANT_OWNER);
            Address::create([
                'label'        => 'Home',
                'address'      => 'Senpara Parbata Lane, East Kazipara, Mirpur, Dhaka, Dhaka Division, Bangladesh',
                'apartment'    => rand(0, 999),
                'latitude'     => '23.8028556',
                'longitude'    => '90.3748344',
                'user_id'      => $restaurantOwnerTwelve->id,
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
                'user_id'      => $restaurantOwnerTwelve->id,
                'creator_type' => User::class,
                'creator_id'   => 1,
                'editor_type'  => User::class,
                'editor_id'    => 1
            ]);
            $restaurantOwnerThirteen = User::create([
                'name'                 => 'Teresa Anderson',
                'email'                => 'restaurantowner13@example.com',
                'phone'                => '1726449813',
                'username'             => 'restaurantowner13',
                'email_verified_at'    => now(),
                'password'             => bcrypt('123456'),
                'restaurant_id'        => 14,
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
            $restaurantOwnerThirteen->assignRole(EnumRole::RESTAURANT_OWNER);
            Address::create([
                'label'        => 'Home',
                'address'      => 'Darus Salam Road, Shah Ali Bag, Mirpur, Dhaka, 1216, Dhaka Division, Bangladesh',
                'apartment'    => rand(0, 999),
                'latitude'     => '23.7956037',
                'longitude'    => '90.3536548',
                'user_id'      => $restaurantOwnerThirteen->id,
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
                'user_id'      => $restaurantOwnerThirteen->id,
                'creator_type' => User::class,
                'creator_id'   => 1,
                'editor_type'  => User::class,
                'editor_id'    => 1
            ]);
            $restaurantOwnerFourteen = User::create([
                'name'                 => 'Julia Young',
                'email'                => 'restaurantowner14@example.com',
                'phone'                => '1726449814',
                'username'             => 'restaurantowner14',
                'email_verified_at'    => now(),
                'password'             => bcrypt('123456'),
                'restaurant_id'        => 15,
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
            $restaurantOwnerFourteen->assignRole(EnumRole::RESTAURANT_OWNER);
            Address::create([
                'label'        => 'Home',
                'address'      => 'Unnamed Road, Section 2, Mirpur, Dhaka, Dhaka Division, Bangladesh',
                'apartment'    => rand(0, 999),
                'latitude'     => '23.8062771',
                'longitude'    => '90.356362',
                'user_id'      => $restaurantOwnerFourteen->id,
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
                'user_id'      => $restaurantOwnerFourteen->id,
                'creator_type' => User::class,
                'creator_id'   => 1,
                'editor_type'  => User::class,
                'editor_id'    => 1
            ]);
            $restaurantOwnerFifteen = User::create([
                'name'                 => 'Adam Ramirez',
                'email'                => 'restaurantowner15@example.com',
                'phone'                => '1726449815',
                'username'             => 'restaurantowner15',
                'email_verified_at'    => now(),
                'password'             => bcrypt('123456'),
                'restaurant_id'        => 16,
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
            $restaurantOwnerFifteen->assignRole(EnumRole::RESTAURANT_OWNER);
            Address::create([
                'label'        => 'Home',
                'address'      => 'Road 23, Gulshan 1, Gulshan, Dhaka, 1212, Dhaka Division, Bangladesh',
                'apartment'    => rand(0, 999),
                'latitude'     => '23.7820624',
                'longitude'    => '90.4160527',
                'user_id'      => $restaurantOwnerFifteen->id,
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
                'user_id'      => $restaurantOwnerFifteen->id,
                'creator_type' => User::class,
                'creator_id'   => 1,
                'editor_type'  => User::class,
                'editor_id'    => 1
            ]);
            $restaurantOwnerSixteen = User::create([
                'name'                 => 'Ruby Collins',
                'email'                => 'restaurantowner16@example.com',
                'phone'                => '1726449816',
                'username'             => 'restaurantowner16',
                'email_verified_at'    => now(),
                'password'             => bcrypt('123456'),
                'restaurant_id'        => 17,
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
            $restaurantOwnerSixteen->assignRole(EnumRole::RESTAURANT_OWNER);
            Address::create([
                'label'        => 'Home',
                'address'      => 'Unnamed Road, Section 2, Mirpur, Dhaka, Dhaka Division, Bangladesh',
                'apartment'    => rand(0, 999),
                'latitude'     => '23.8062771',
                'longitude'    => '90.356362',
                'user_id'      => $restaurantOwnerSixteen->id,
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
                'user_id'      => $restaurantOwnerSixteen->id,
                'creator_type' => User::class,
                'creator_id'   => 1,
                'editor_type'  => User::class,
                'editor_id'    => 1
            ]);
            $restaurantOwnerSeventeen = User::create([
                'name'                 => 'Margaret Bailey',
                'email'                => 'restaurantowner17@example.com',
                'phone'                => '1726449817',
                'username'             => 'restaurantowner17',
                'email_verified_at'    => now(),
                'password'             => bcrypt('123456'),
                'restaurant_id'        => 18,
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
            $restaurantOwnerSeventeen->assignRole(EnumRole::RESTAURANT_OWNER);
            Address::create([
                'label'        => 'Home',
                'address'      => 'Senpara Parbata Lane, East Kazipara, Mirpur, Dhaka, Dhaka Division, Bangladesh',
                'apartment'    => rand(0, 999),
                'latitude'     => '23.8028556',
                'longitude'    => '90.3748344',
                'user_id'      => $restaurantOwnerSeventeen->id,
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
                'user_id'      => $restaurantOwnerSeventeen->id,
                'creator_type' => User::class,
                'creator_id'   => 1,
                'editor_type'  => User::class,
                'editor_id'    => 1
            ]);
            $restaurantOwnerEighteen = User::create([
                'name'                 => 'Jessica Patterson',
                'email'                => 'restaurantowner18@example.com',
                'phone'                => '1726449818',
                'username'             => 'restaurantowner18',
                'email_verified_at'    => now(),
                'password'             => bcrypt('123456'),
                'restaurant_id'        => 19,
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
            $restaurantOwnerEighteen->assignRole(EnumRole::RESTAURANT_OWNER);
            Address::create([
                'label'        => 'Home',
                'address'      => 'Darus Salam Road, Shah Ali Bag, Mirpur, Dhaka, 1216, Dhaka Division, Bangladesh',
                'apartment'    => rand(0, 999),
                'latitude'     => '23.7956037',
                'longitude'    => '90.3536548',
                'user_id'      => $restaurantOwnerEighteen->id,
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
                'user_id'      => $restaurantOwnerEighteen->id,
                'creator_type' => User::class,
                'creator_id'   => 1,
                'editor_type'  => User::class,
                'editor_id'    => 1
            ]);
            $restaurantOwnerNineteen = User::create([
                'name'                 => 'Laura Evans',
                'email'                => 'restaurantowner19@example.com',
                'phone'                => '1726449819',
                'username'             => 'restaurantowner19',
                'email_verified_at'    => now(),
                'password'             => bcrypt('123456'),
                'restaurant_id'        => 20,
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
            $restaurantOwnerNineteen->assignRole(EnumRole::RESTAURANT_OWNER);
            Address::create([
                'label'        => 'Home',
                'address'      => 'Unnamed Road, Section 2, Mirpur, Dhaka, Dhaka Division, Bangladesh',
                'apartment'    => rand(0, 999),
                'latitude'     => '23.8062771',
                'longitude'    => '90.356362',
                'user_id'      => $restaurantOwnerNineteen->id,
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
                'user_id'      => $restaurantOwnerNineteen->id,
                'creator_type' => User::class,
                'creator_id'   => 1,
                'editor_type'  => User::class,
                'editor_id'    => 1
            ]);
            $restaurantOwnerTwenty = User::create([
                'name'                 => 'Susan Clark',
                'email'                => 'restaurantowner20@example.com',
                'phone'                => '1726449820',
                'username'             => 'restaurantowner20',
                'email_verified_at'    => now(),
                'password'             => bcrypt('123456'),
                'restaurant_id'        => 21,
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
            $restaurantOwnerTwenty->assignRole(EnumRole::RESTAURANT_OWNER);
            Address::create([
                'label'        => 'Home',
                'address'      => 'Road 23, Gulshan 1, Gulshan, Dhaka, 1212, Dhaka Division, Bangladesh',
                'apartment'    => rand(0, 999),
                'latitude'     => '23.7820624',
                'longitude'    => '90.4160527',
                'user_id'      => $restaurantOwnerTwenty->id,
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
                'user_id'      => $restaurantOwnerTwenty->id,
                'creator_type' => User::class,
                'creator_id'   => 1,
                'editor_type'  => User::class,
                'editor_id'    => 1
            ]);
            $restaurantOwnerTwentyOne = User::create([
                'name'                 => 'Dorothy Perry',
                'email'                => 'restaurantowner21@example.com',
                'phone'                => '1726449821',
                'username'             => 'restaurantowner21',
                'email_verified_at'    => now(),
                'password'             => bcrypt('123456'),
                'restaurant_id'        => 11,
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
            $restaurantOwnerTwentyOne->assignRole(EnumRole::RESTAURANT_OWNER);
            Address::create([
                'label'        => 'Home',
                'address'      => 'Dhanmondi Bridge, Dhanmondi, Dhaka, 1209, Dhaka Division, Bangladesh',
                'apartment'    => rand(0, 999) . ', Dhanmondi',
                'latitude'     => '23.7509735',
                'longitude'    => '90.373582',
                'user_id'      => $restaurantOwnerTwentyOne->id,
                'creator_type' => User::class,
                'creator_id'   => 1,
                'editor_type'  => User::class,
                'editor_id'    => 1
            ]);
            Address::create([
                'label'        => 'Work',
                'address'      => 'Senpara Parbata Lane, East Kazipara, Mirpur, Dhaka, Dhaka Division, Bangladesh',
                'apartment'    => rand(0, 999),
                'latitude'     => '23.8028556',
                'longitude'    => '90.3748344',
                'user_id'      => $restaurantOwnerTwentyOne->id,
                'creator_type' => User::class,
                'creator_id'   => 1,
                'editor_type'  => User::class,
                'editor_id'    => 1
            ]);
            $restaurantOwnerTwentyTwo = User::create([
                'name'                 => 'Fred Robinson',
                'email'                => 'restaurantowner22@example.com',
                'phone'                => '1726449822',
                'username'             => 'restaurantowner22',
                'email_verified_at'    => now(),
                'password'             => bcrypt('123456'),
                'restaurant_id'        => 22,
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
            $restaurantOwnerTwentyTwo->assignRole(EnumRole::RESTAURANT_OWNER);
            Address::create([
                'label'        => 'Home',
                'address'      => 'Senpara Parbata Lane, East Kazipara, Mirpur, Dhaka, Dhaka Division, Bangladesh',
                'apartment'    => rand(0, 999),
                'latitude'     => '23.8028556',
                'longitude'    => '90.3748344',
                'user_id'      => $restaurantOwnerTwentyTwo->id,
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
                'user_id'      => $restaurantOwnerTwentyTwo->id,
                'creator_type' => User::class,
                'creator_id'   => 1,
                'editor_type'  => User::class,
                'editor_id'    => 1
            ]);
            $restaurantOwnerTwentyThree = User::create([
                'name'                 => 'Doris Cook',
                'email'                => 'restaurantowner23@example.com',
                'phone'                => '1726449823',
                'username'             => 'restaurantowner23',
                'email_verified_at'    => now(),
                'password'             => bcrypt('123456'),
                'restaurant_id'        => 23,
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
            $restaurantOwnerTwentyThree->assignRole(EnumRole::RESTAURANT_OWNER);
            Address::create([
                'label'        => 'Home',
                'address'      => 'Darus Salam Road, Shah Ali Bag, Mirpur, Dhaka, 1216, Dhaka Division, Bangladesh',
                'apartment'    => rand(0, 999),
                'latitude'     => '23.7956037',
                'longitude'    => '90.3536548',
                'user_id'      => $restaurantOwnerTwentyThree->id,
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
                'user_id'      => $restaurantOwnerTwentyThree->id,
                'creator_type' => User::class,
                'creator_id'   => 1,
                'editor_type'  => User::class,
                'editor_id'    => 1
            ]);
            $restaurantOwnerTwentyFour = User::create([
                'name'                 => 'Maria Hill',
                'email'                => 'restaurantowner24@example.com',
                'phone'                => '1726449824',
                'username'             => 'restaurantowner24',
                'email_verified_at'    => now(),
                'password'             => bcrypt('123456'),
                'restaurant_id'        => 24,
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
            $restaurantOwnerTwentyFour->assignRole(EnumRole::RESTAURANT_OWNER);
            Address::create([
                'label'        => 'Home',
                'address'      => 'Unnamed Road, Section 2, Mirpur, Dhaka, Dhaka Division, Bangladesh',
                'apartment'    => rand(0, 999),
                'latitude'     => '23.8062771',
                'longitude'    => '90.356362',
                'user_id'      => $restaurantOwnerTwentyFour->id,
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
                'user_id'      => $restaurantOwnerTwentyFour->id,
                'creator_type' => User::class,
                'creator_id'   => 1,
                'editor_type'  => User::class,
                'editor_id'    => 1
            ]);
            $restaurantOwnerTwentyFive = User::create([
                'name'                 => 'Juan Perez',
                'email'                => 'restaurantowner25@example.com',
                'phone'                => '1726449825',
                'username'             => 'restaurantowner25',
                'email_verified_at'    => now(),
                'password'             => bcrypt('123456'),
                'restaurant_id'        => 25,
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
            $restaurantOwnerTwentyFive->assignRole(EnumRole::RESTAURANT_OWNER);
            Address::create([
                'label'        => 'Home',
                'address'      => 'Road 23, Gulshan 1, Gulshan, Dhaka, 1212, Dhaka Division, Bangladesh',
                'apartment'    => rand(0, 999),
                'latitude'     => '23.7820624',
                'longitude'    => '90.4160527',
                'user_id'      => $restaurantOwnerTwentyFive->id,
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
                'user_id'      => $restaurantOwnerTwentyFive->id,
                'creator_type' => User::class,
                'creator_id'   => 1,
                'editor_type'  => User::class,
                'editor_id'    => 1
            ]);

            $restaurantOne          = Restaurant::find(1);
            $restaurantOne->user_id = $restaurantOwnerOne->id;
            $restaurantOne->save();

            $restaurantTwo          = Restaurant::find(2);
            $restaurantTwo->user_id = $restaurantOwnerTwo->id;
            $restaurantTwo->save();

            $restaurantThree          = Restaurant::find(3);
            $restaurantThree->user_id = $restaurantOwnerThree->id;
            $restaurantThree->save();

            $restaurantFour          = Restaurant::find(4);
            $restaurantFour->user_id = $restaurantOwnerFour->id;
            $restaurantFour->save();

            $restaurantFive          = Restaurant::find(5);
            $restaurantFive->user_id = $restaurantOwnerFive->id;
            $restaurantFive->save();

            $restaurantSix          = Restaurant::find(6);
            $restaurantSix->user_id = $restaurantOwnerSix->id;
            $restaurantSix->save();

            $restaurantSeven          = Restaurant::find(7);
            $restaurantSeven->user_id = $restaurantOwnerSeven->id;
            $restaurantSeven->save();

            $restaurantEight          = Restaurant::find(8);
            $restaurantEight->user_id = $restaurantOwnerEight->id;
            $restaurantEight->save();

            $restaurantNine          = Restaurant::find(9);
            $restaurantNine->user_id = $restaurantOwnerNine->id;
            $restaurantNine->save();

            $restaurantTen          = Restaurant::find(10);
            $restaurantTen->user_id = $restaurantOwnerTen->id;
            $restaurantTen->save();

            $restaurantEleven          = Restaurant::find(11);
            $restaurantEleven->user_id = $restaurantOwnerEleven->id;
            $restaurantEleven->save();

            $restaurantTwelve          = Restaurant::find(12);
            $restaurantTwelve->user_id = $restaurantOwnerTwelve->id;
            $restaurantTwelve->save();

            $restaurantThirteen          = Restaurant::find(13);
            $restaurantThirteen->user_id = $restaurantOwnerThirteen->id;
            $restaurantThirteen->save();

            $restaurantFourteen          = Restaurant::find(14);
            $restaurantFourteen->user_id = $restaurantOwnerFourteen->id;
            $restaurantFourteen->save();

            $restaurantFifteen          = Restaurant::find(15);
            $restaurantFifteen->user_id = $restaurantOwnerFifteen->id;
            $restaurantFifteen->save();

            $restaurantSixteen          = Restaurant::find(16);
            $restaurantSixteen->user_id = $restaurantOwnerSixteen->id;
            $restaurantSixteen->save();

            $restaurantSeventeen          = Restaurant::find(17);
            $restaurantSeventeen->user_id = $restaurantOwnerSeventeen->id;
            $restaurantSeventeen->save();

            $restaurantEighteen          = Restaurant::find(18);
            $restaurantEighteen->user_id = $restaurantOwnerEighteen->id;
            $restaurantEighteen->save();

            $restaurantNineteen          = Restaurant::find(19);
            $restaurantNineteen->user_id = $restaurantOwnerNineteen->id;
            $restaurantNineteen->save();

            $restaurantTwenty          = Restaurant::find(20);
            $restaurantTwenty->user_id = $restaurantOwnerTwenty->id;
            $restaurantTwenty->save();

            $restaurantTwentyOne          = Restaurant::find(21);
            $restaurantTwentyOne->user_id = $restaurantOwnerTwentyOne->id;
            $restaurantTwentyOne->save();

            $restaurantTwentyTwo          = Restaurant::find(22);
            $restaurantTwentyTwo->user_id = $restaurantOwnerTwentyTwo->id;
            $restaurantTwentyTwo->save();

            $restaurantTwentyThree          = Restaurant::find(23);
            $restaurantTwentyThree->user_id = $restaurantOwnerTwentyThree->id;
            $restaurantTwentyThree->save();

            $restaurantTwentyFour          = Restaurant::find(24);
            $restaurantTwentyFour->user_id = $restaurantOwnerTwentyFour->id;
            $restaurantTwentyFour->save();

            $restaurantTwentyFive          = Restaurant::find(25);
            $restaurantTwentyFive->user_id = $restaurantOwnerTwentyFive->id;
            $restaurantTwentyFive->save();
        }
    }
}
