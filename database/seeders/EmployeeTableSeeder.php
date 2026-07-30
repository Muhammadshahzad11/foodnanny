<?php

namespace Database\Seeders;

use App\Enums\Ask;
use App\Models\Address;
use App\Enums\Role as EnumRole;
use Dipokhalder\EnvEditor\EnvEditor;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Enums\Status;

class EmployeeTableSeeder extends Seeder
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
            $employeeOne = User::create([
                'name'                 => 'Kendrick Kerr',
                'email'                => 'employee1@example.com',
                'phone'                => '0123456789',
                'username'             => 'employee1',
                'email_verified_at'    => now(),
                'password'             => bcrypt('123456'),
                'restaurant_id'        => 0,
                'balance'              => 0,
                'collection'           => 0,
                'status'               => Status::ACTIVE,
                'country_code'         => '+880',
                'is_guest'             => Ask::NO,
                'terms_and_conditions' => Ask::YES,
                'creator_type'         => User::class,
                'creator_id'           => 1,
                'editor_type'          => User::class,
                'editor_id'            => 1
            ]);
            $employeeOne->assignRole(EnumRole::STAFF);
            Address::create([
                'label'        => 'Home',
                'address'      => 'Unnamed Road, Section 2, Mirpur, Dhaka, Dhaka Division, Bangladesh',
                'apartment'    => rand(0, 999),
                'latitude'     => '23.8062771',
                'longitude'    => '90.356362',
                'user_id'      => $employeeOne->id,
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
                'user_id'      => $employeeOne->id,
                'creator_type' => User::class,
                'creator_id'   => 1,
                'editor_type'  => User::class,
                'editor_id'    => 1
            ]);

            $employeeTwo = User::create([
                'name'                 => 'Caroline Molina',
                'email'                => 'employee2@example.com',
                'phone'                => '0123456788',
                'username'             => 'employee2',
                'email_verified_at'    => now(),
                'password'             => bcrypt('123456'),
                'restaurant_id'        => 0,
                'balance'              => 0,
                'collection'           => 0,
                'status'               => Status::ACTIVE,
                'country_code'         => '+880',
                'is_guest'             => Ask::NO,
                'terms_and_conditions' => Ask::YES,
                'creator_type'         => User::class,
                'creator_id'           => 1,
                'editor_type'          => User::class,
                'editor_id'            => 1
            ]);
            $employeeTwo->assignRole(EnumRole::STAFF);
            Address::create([
                'label'        => 'Home',
                'address'      => 'Darus Salam Road, Shah Ali Bag, Mirpur, Dhaka, 1216, Dhaka Division, Bangladesh',
                'apartment'    => rand(0, 999),
                'latitude'     => '23.7956037',
                'longitude'    => '90.3536548',
                'user_id'      => $employeeTwo->id,
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
                'user_id'      => $employeeTwo->id,
                'creator_type' => User::class,
                'creator_id'   => 1,
                'editor_type'  => User::class,
                'editor_id'    => 1
            ]);

            $employeeThree = User::create([
                'name'                 => 'Arnold Torres',
                'email'                => 'employee3@example.com',
                'phone'                => '0123456787',
                'username'             => 'employee3',
                'email_verified_at'    => now(),
                'password'             => bcrypt('123456'),
                'restaurant_id'        => 0,
                'balance'              => 0,
                'collection'           => 0,
                'status'               => Status::ACTIVE,
                'country_code'         => '+880',
                'is_guest'             => Ask::NO,
                'terms_and_conditions' => Ask::YES,
                'creator_type'         => User::class,
                'creator_id'           => 1,
                'editor_type'          => User::class,
                'editor_id'            => 1
            ]);
            $employeeThree->assignRole(EnumRole::STAFF);
            Address::create([
                'label'        => 'Home',
                'address'      => 'Road 23, Gulshan 1, Gulshan, Dhaka, 1212, Dhaka Division, Bangladesh',
                'apartment'    => rand(0, 999),
                'latitude'     => '23.7820624',
                'longitude'    => '90.4160527',
                'user_id'      => $employeeThree->id,
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
                'user_id'      => $employeeThree->id,
                'creator_type' => User::class,
                'creator_id'   => 1,
                'editor_type'  => User::class,
                'editor_id'    => 1
            ]);

            $employeeFour = User::create([
                'name'                 => 'Billy Bailey',
                'email'                => 'employee4@example.com',
                'phone'                => '0123456786',
                'username'             => 'employee4',
                'email_verified_at'    => now(),
                'password'             => bcrypt('123456'),
                'restaurant_id'        => 0,
                'balance'              => 0,
                'collection'           => 0,
                'status'               => Status::ACTIVE,
                'country_code'         => '+880',
                'is_guest'             => Ask::NO,
                'terms_and_conditions' => Ask::YES,
                'creator_type'         => User::class,
                'creator_id'           => 1,
                'editor_type'          => User::class,
                'editor_id'            => 1
            ]);
            $employeeFour->assignRole(EnumRole::STAFF);
            Address::create([
                'label'        => 'Home',
                'address'      => 'Unnamed Road, Section 2, Mirpur, Dhaka, Dhaka Division, Bangladesh',
                'apartment'    => rand(0, 999),
                'latitude'     => '23.8062771',
                'longitude'    => '90.356362',
                'user_id'      => $employeeFour->id,
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
                'user_id'      => $employeeFour->id,
                'creator_type' => User::class,
                'creator_id'   => 1,
                'editor_type'  => User::class,
                'editor_id'    => 1
            ]);

            $employeeFive = User::create([
                'name'                 => 'Abram Vargas',
                'email'                => 'employee5@example.com',
                'phone'                => '0123456785',
                'username'             => 'employee5',
                'email_verified_at'    => now(),
                'password'             => bcrypt('123456'),
                'restaurant_id'        => 0,
                'balance'              => 0,
                'collection'           => 0,
                'status'               => Status::ACTIVE,
                'country_code'         => '+880',
                'is_guest'             => Ask::NO,
                'terms_and_conditions' => Ask::YES,
                'creator_type'         => User::class,
                'creator_id'           => 1,
                'editor_type'          => User::class,
                'editor_id'            => 1
            ]);
            $employeeFive->assignRole(EnumRole::STAFF);
            Address::create([
                'label'        => 'Home',
                'address'      => 'Road 23, Gulshan 1, Gulshan, Dhaka, 1212, Dhaka Division, Bangladesh',
                'apartment'    => rand(0, 999),
                'latitude'     => '23.7820624',
                'longitude'    => '90.4160527',
                'user_id'      => $employeeFive->id,
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
                'user_id'      => $employeeFive->id,
                'creator_type' => User::class,
                'creator_id'   => 1,
                'editor_type'  => User::class,
                'editor_id'    => 1
            ]);

            $employeeSix = User::create([
                'name'                 => 'Rae Buchanan',
                'email'                => 'employee6@example.com',
                'phone'                => '0123456784',
                'username'             => 'employee6',
                'email_verified_at'    => now(),
                'password'             => bcrypt('123456'),
                'restaurant_id'        => 0,
                'balance'              => 0,
                'collection'           => 0,
                'status'               => Status::ACTIVE,
                'country_code'         => '+880',
                'is_guest'             => Ask::NO,
                'terms_and_conditions' => Ask::YES,
                'creator_type'         => User::class,
                'creator_id'           => 1,
                'editor_type'          => User::class,
                'editor_id'            => 1
            ]);
            $employeeSix->assignRole(EnumRole::STAFF);
            Address::create([
                'label'        => 'Home',
                'address'      => 'Unnamed Road, Section 2, Mirpur, Dhaka, Dhaka Division, Bangladesh',
                'apartment'    => rand(0, 999),
                'latitude'     => '23.8062771',
                'longitude'    => '90.356362',
                'user_id'      => $employeeSix->id,
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
                'user_id'      => $employeeSix->id,
                'creator_type' => User::class,
                'creator_id'   => 1,
                'editor_type'  => User::class,
                'editor_id'    => 1
            ]);
            $employeeSeven = User::create([
                'name'                 => 'Ann Miranda',
                'email'                => 'employee7@example.com',
                'phone'                => '0123456783',
                'username'             => 'employee7',
                'email_verified_at'    => now(),
                'password'             => bcrypt('123456'),
                'restaurant_id'        => 0,
                'balance'              => 0,
                'collection'           => 0,
                'status'               => Status::ACTIVE,
                'country_code'         => '+880',
                'is_guest'             => Ask::NO,
                'terms_and_conditions' => Ask::YES,
                'creator_type'         => User::class,
                'creator_id'           => 1,
                'editor_type'          => User::class,
                'editor_id'            => 1
            ]);
            $employeeSeven->assignRole(EnumRole::STAFF);
            Address::create([
                'label'        => 'Home',
                'address'      => 'Unnamed Road, Section 2, Mirpur, Dhaka, Dhaka Division, Bangladesh',
                'apartment'    => rand(0, 999),
                'latitude'     => '23.8062771',
                'longitude'    => '90.356362',
                'user_id'      => $employeeSeven->id,
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
                'user_id'      => $employeeSeven->id,
                'creator_type' => User::class,
                'creator_id'   => 1,
                'editor_type'  => User::class,
                'editor_id'    => 1
            ]);

            $employeeEight = User::create([
                'name'                 => 'Ingrid Moore',
                'email'                => 'employee8@example.com',
                'phone'                => '0123456782',
                'username'             => 'employee8',
                'email_verified_at'    => now(),
                'password'             => bcrypt('123456'),
                'restaurant_id'        => 0,
                'balance'              => 0,
                'collection'           => 0,
                'status'               => Status::ACTIVE,
                'country_code'         => '+880',
                'is_guest'             => Ask::NO,
                'terms_and_conditions' => Ask::YES,
                'creator_type'         => User::class,
                'creator_id'           => 1,
                'editor_type'          => User::class,
                'editor_id'            => 1
            ]);
            $employeeEight->assignRole(EnumRole::STAFF);
            Address::create([
                'label'        => 'Home',
                'address'      => 'Darus Salam Road, Shah Ali Bag, Mirpur, Dhaka, 1216, Dhaka Division, Bangladesh',
                'apartment'    => rand(0, 999),
                'latitude'     => '23.7956037',
                'longitude'    => '90.3536548',
                'user_id'      => $employeeEight->id,
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
                'user_id'      => $employeeEight->id,
                'creator_type' => User::class,
                'creator_id'   => 1,
                'editor_type'  => User::class,
                'editor_id'    => 1
            ]);

            $employeeNine = User::create([
                'name'                 => 'Al Torres',
                'email'                => 'employee9@example.com',
                'phone'                => '0123456781',
                'username'             => 'employee9',
                'email_verified_at'    => now(),
                'password'             => bcrypt('123456'),
                'restaurant_id'        => 0,
                'balance'              => 0,
                'collection'           => 0,
                'status'               => Status::ACTIVE,
                'country_code'         => '+880',
                'is_guest'             => Ask::NO,
                'terms_and_conditions' => Ask::YES,
                'creator_type'         => User::class,
                'creator_id'           => 1,
                'editor_type'          => User::class,
                'editor_id'            => 1
            ]);
            $employeeNine->assignRole(EnumRole::STAFF);
            Address::create([
                'label'        => 'Home',
                'address'      => 'Road 23, Gulshan 1, Gulshan, Dhaka, 1212, Dhaka Division, Bangladesh',
                'apartment'    => rand(0, 999),
                'latitude'     => '23.7820624',
                'longitude'    => '90.4160527',
                'user_id'      => $employeeNine->id,
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
                'user_id'      => $employeeNine->id,
                'creator_type' => User::class,
                'creator_id'   => 1,
                'editor_type'  => User::class,
                'editor_id'    => 1
            ]);

            $employeeTen = User::create([
                'name'                 => 'Theron Peck',
                'email'                => 'employee10@example.com',
                'phone'                => '0123456781',
                'username'             => 'employee10',
                'email_verified_at'    => now(),
                'password'             => bcrypt('123456'),
                'restaurant_id'        => 0,
                'balance'              => 0,
                'collection'           => 0,
                'status'               => Status::ACTIVE,
                'country_code'         => '+880',
                'is_guest'             => Ask::NO,
                'terms_and_conditions' => Ask::YES,
                'creator_type'         => User::class,
                'creator_id'           => 1,
                'editor_type'          => User::class,
                'editor_id'            => 1
            ]);
            $employeeTen->assignRole(EnumRole::STAFF);
            Address::create([
                'label'        => 'Home',
                'address'      => 'Unnamed Road, Section 2, Mirpur, Dhaka, Dhaka Division, Bangladesh',
                'apartment'    => rand(0, 999),
                'latitude'     => '23.8062771',
                'longitude'    => '90.356362',
                'user_id'      => $employeeTen->id,
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
                'user_id'      => $employeeTen->id,
                'creator_type' => User::class,
                'creator_id'   => 1,
                'editor_type'  => User::class,
                'editor_id'    => 1
            ]); 
            $employeeEleven = User::create([
                'name'                 => 'Jesus Mcdonald',
                'email'                => 'employee11@example.com',
                'phone'                => '0123456711',
                'username'             => 'employee11',
                'email_verified_at'    => now(),
                'password'             => bcrypt('123456'),
                'restaurant_id'        => 0,
                'balance'              => 0,
                'collection'           => 0,
                'status'               => Status::ACTIVE,
                'country_code'         => '+880',
                'is_guest'             => Ask::NO,
                'terms_and_conditions' => Ask::YES,
                'creator_type'         => User::class,
                'creator_id'           => 1,
                'editor_type'          => User::class,
                'editor_id'            => 1
            ]);
            $employeeEleven->assignRole(EnumRole::STAFF);
            Address::create([
                'label'        => 'Home',
                'address'      => 'Road 23, Gulshan 1, Gulshan, Dhaka, 1212, Dhaka Division, Bangladesh',
                'apartment'    => rand(0, 999),
                'latitude'     => '23.7820624',
                'longitude'    => '90.4160527',
                'user_id'      => $employeeEleven->id,
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
                'user_id'      => $employeeEleven->id,
                'creator_type' => User::class,
                'creator_id'   => 1,
                'editor_type'  => User::class,
                'editor_id'    => 1
            ]);

            $employeeTwelve = User::create([
                'name'                 => 'Chi Colon',
                'email'                => 'employee12@example.com',
                'phone'                => '0123456712',
                'username'             => 'employee12',
                'email_verified_at'    => now(),
                'password'             => bcrypt('123456'),
                'restaurant_id'        => 0,
                'balance'              => 0,
                'collection'           => 0,
                'status'               => Status::ACTIVE,
                'country_code'         => '+880',
                'is_guest'             => Ask::NO,
                'terms_and_conditions' => Ask::YES,
                'creator_type'         => User::class,
                'creator_id'           => 1,
                'editor_type'          => User::class,
                'editor_id'            => 1
            ]);
            $employeeTwelve->assignRole(EnumRole::STAFF);
            Address::create([
                'label'        => 'Home',
                'address'      => 'Unnamed Road, Section 2, Mirpur, Dhaka, Dhaka Division, Bangladesh',
                'apartment'    => rand(0, 999),
                'latitude'     => '23.8062771',
                'longitude'    => '90.356362',
                'user_id'      => $employeeTwelve->id,
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
                'user_id'      => $employeeTwelve->id,
                'creator_type' => User::class,
                'creator_id'   => 1,
                'editor_type'  => User::class,
                'editor_id'    => 1
            ]);
        }
    }
}
