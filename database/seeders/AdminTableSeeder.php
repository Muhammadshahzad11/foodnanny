<?php

namespace Database\Seeders;

use App\Enums\Ask;
use App\Models\Address;
use App\Enums\Role as EnumRole;
use Dipokhalder\EnvEditor\EnvEditor;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Enums\Status;


class AdminTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        $envService = new EnvEditor();
        $admin      = User::create([
            'name'                 => 'John Doe',
            'email'                => 'admin@example.com',
            'phone'                => '1728660901',
            'username'             => 'admin',
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
        $admin->assignRole(EnumRole::ADMIN);
        if ($envService->getValue('DEMO')) {
            Address::create([
                'label'        => 'Home',
                'address'      => 'Senpara Parbata Lane, East Kazipara, Mirpur, Dhaka, Dhaka Division, Bangladesh',
                'apartment'    => rand(0, 999),
                'latitude'     => '23.8028556',
                'longitude'    => '90.3748344',
                'user_id'      => $admin->id,
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
                'user_id'      => $admin->id,
                'creator_type' => User::class,
                'creator_id'   => 1,
                'editor_type'  => User::class,
                'editor_id'    => 1
            ]);
        }
    }
}
