<?php

namespace Database\Seeders;

use App\Enums\Ask;
use App\Enums\Role as EnumRole;
use App\Enums\Status;
use App\Models\Address;
use App\Models\DeliveryLocation;
use App\Models\Restaurant;
use App\Models\User;
use App\Models\Zone;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class ZoneDemoDataSeeder extends Seeder
{
    public function run(): void
    {
        $madhapur = Zone::withoutGlobalScopes()->updateOrCreate(
            ['name' => 'Zone 1 – Madhapur'],
            [
                'display_name'          => 'Madhapur',
                'polygon'               => [
                    ['lat' => 17.430, 'lng' => 78.380],
                    ['lat' => 17.430, 'lng' => 78.410],
                    ['lat' => 17.460, 'lng' => 78.410],
                    ['lat' => 17.460, 'lng' => 78.380],
                    ['lat' => 17.430, 'lng' => 78.380],
                ],
                'status'                => Status::ACTIVE,
                'base_delivery_fee'     => 30,
                'min_order_amount'      => 0,
                'free_delivery_above'   => 299,
                'free_delivery_km'      => 2,
                'extra_distance_charge' => 10,
                'peak_enabled'          => 0,
                'peak_charge'           => 0,
            ]
        );

        $kondapur = Zone::withoutGlobalScopes()->updateOrCreate(
            ['name' => 'Zone 2 – Kondapur'],
            [
                'display_name'          => 'Kondapur',
                'polygon'               => [
                    ['lat' => 17.450, 'lng' => 78.340],
                    ['lat' => 17.450, 'lng' => 78.375],
                    ['lat' => 17.490, 'lng' => 78.375],
                    ['lat' => 17.490, 'lng' => 78.340],
                    ['lat' => 17.450, 'lng' => 78.340],
                ],
                'status'                => Status::ACTIVE,
                'base_delivery_fee'     => 25,
                'min_order_amount'      => 0,
                'free_delivery_above'   => 249,
                'free_delivery_km'      => 2,
                'extra_distance_charge' => 10,
                'peak_enabled'          => 0,
                'peak_charge'           => 0,
            ]
        );

        Restaurant::withoutGlobalScopes()->whereIn('id', [1, 2, 4, 5, 6])->update(['zone_id' => $madhapur->id]);
        Restaurant::withoutGlobalScopes()->whereIn('id', [3, 7, 8, 9, 10, 11])->update(['zone_id' => $kondapur->id]);

        $madhapurAdmin = User::query()->updateOrCreate(
            ['email' => 'madhapur.admin@example.com'],
            [
                'name'                 => 'Admin 1 – Madhapur',
                'phone'                => '9000000001',
                'username'             => 'madhapuradmin',
                'password'             => Hash::make('123456'),
                'status'               => Status::ACTIVE,
                'email_verified_at'    => now(),
                'restaurant_id'        => 0,
                'zone_id'              => $madhapur->id,
                'country_code'         => '+91',
                'is_guest'             => Ask::NO,
                'terms_and_conditions' => Ask::YES,
            ]
        );
        $madhapurAdmin->syncRoles([EnumRole::ZONE_ADMIN]);

        $kondapurAdmin = User::query()->updateOrCreate(
            ['email' => 'kondapur.admin@example.com'],
            [
                'name'                 => 'Admin 2 – Kondapur',
                'phone'                => '9000000002',
                'username'             => 'kondapuradmin',
                'password'             => Hash::make('123456'),
                'status'               => Status::ACTIVE,
                'email_verified_at'    => now(),
                'restaurant_id'        => 0,
                'zone_id'              => $kondapur->id,
                'country_code'         => '+91',
                'is_guest'             => Ask::NO,
                'terms_and_conditions' => Ask::YES,
            ]
        );
        $kondapurAdmin->syncRoles([EnumRole::ZONE_ADMIN]);

        $ravi = User::query()->updateOrCreate(
            ['email' => 'ravi.rider@example.com'],
            [
                'name'                 => 'Ravi',
                'phone'                => '9000000011',
                'username'             => 'ravi.rider',
                'password'             => Hash::make('123456'),
                'status'               => Status::ACTIVE,
                'email_verified_at'    => now(),
                'restaurant_id'        => 0,
                'zone_id'              => $madhapur->id,
                'country_code'         => '+91',
                'is_guest'             => Ask::NO,
                'terms_and_conditions' => Ask::YES,
            ]
        );
        $ravi->syncRoles([EnumRole::DELIVERY_BOY]);

        $ajay = User::query()->updateOrCreate(
            ['email' => 'ajay.rider@example.com'],
            [
                'name'                 => 'Ajay',
                'phone'                => '9000000012',
                'username'             => 'ajay.rider',
                'password'             => Hash::make('123456'),
                'status'               => Status::ACTIVE,
                'email_verified_at'    => now(),
                'restaurant_id'        => 0,
                'zone_id'              => $madhapur->id,
                'country_code'         => '+91',
                'is_guest'             => Ask::NO,
                'terms_and_conditions' => Ask::YES,
            ]
        );
        $ajay->syncRoles([EnumRole::DELIVERY_BOY]);

        $rahul = User::query()->updateOrCreate(
            ['email' => 'rahul.rider@example.com'],
            [
                'name'                 => 'Rahul',
                'phone'                => '9000000021',
                'username'             => 'rahul.rider',
                'password'             => Hash::make('123456'),
                'status'               => Status::ACTIVE,
                'email_verified_at'    => now(),
                'restaurant_id'        => 0,
                'zone_id'              => $kondapur->id,
                'country_code'         => '+91',
                'is_guest'             => Ask::NO,
                'terms_and_conditions' => Ask::YES,
            ]
        );
        $rahul->syncRoles([EnumRole::DELIVERY_BOY]);

        $suresh = User::query()->updateOrCreate(
            ['email' => 'suresh.rider@example.com'],
            [
                'name'                 => 'Suresh',
                'phone'                => '9000000022',
                'username'             => 'suresh.rider',
                'password'             => Hash::make('123456'),
                'status'               => Status::ACTIVE,
                'email_verified_at'    => now(),
                'restaurant_id'        => 0,
                'zone_id'              => $kondapur->id,
                'country_code'         => '+91',
                'is_guest'             => Ask::NO,
                'terms_and_conditions' => Ask::YES,
            ]
        );
        $suresh->syncRoles([EnumRole::DELIVERY_BOY]);

        User::query()->where('email', 'saireddy@gmail.com')->update(['zone_id' => $madhapur->id]);

        // Rider app never posts GPS; available-order requires a saved delivery_locations row.
        foreach ([
            [$ravi, '17.448300', '78.391500', 'Madhapur, Hyderabad – Zone 1 test pin'],
            [$ajay, '17.448300', '78.391500', 'Madhapur, Hyderabad – Zone 1 test pin'],
            [$rahul, '17.470000', '78.358000', 'Kondapur, Hyderabad – Zone 2 test pin'],
            [$suresh, '17.470000', '78.358000', 'Kondapur, Hyderabad – Zone 2 test pin'],
        ] as [$rider, $lat, $lng, $address]) {
            DeliveryLocation::query()->updateOrCreate(
                ['user_id' => $rider->id],
                [
                    'latitude'  => $lat,
                    'longitude' => $lng,
                    'address'   => $address,
                ]
            );
        }

        $customer = User::query()->updateOrCreate(
            ['email' => 'zone.customer@example.com'],
            [
                'name'                 => 'Zone Test Customer',
                'phone'                => '9000000099',
                'username'             => 'zonecustomer',
                'password'             => Hash::make('123456'),
                'status'               => Status::ACTIVE,
                'email_verified_at'    => now(),
                'restaurant_id'        => 0,
                'zone_id'              => $madhapur->id,
                'country_code'         => '+91',
                'is_guest'             => Ask::NO,
                'terms_and_conditions' => Ask::YES,
            ]
        );
        $customer->syncRoles([EnumRole::CUSTOMER]);

        Address::query()->updateOrCreate(
            ['user_id' => $customer->id, 'label' => 'Home'],
            [
                'address'   => 'Madhapur, Hyderabad – test pin inside Zone 1',
                'apartment' => 'A-101',
                'latitude'  => '17.448300',
                'longitude' => '78.391500',
                'zone_id'   => $madhapur->id,
            ]
        );

        Address::query()->updateOrCreate(
            ['user_id' => $customer->id, 'label' => 'Kondapur Home'],
            [
                'address'   => 'Kondapur, Hyderabad – test pin inside Zone 2',
                'apartment' => 'B-202',
                'latitude'  => '17.470000',
                'longitude' => '78.358000',
                'zone_id'   => $kondapur->id,
            ]
        );

        $this->command?->info('Zone demo data ready.');
        $this->command?->info('Madhapur admin: madhapur.admin@example.com / 123456');
        $this->command?->info('Kondapur admin: kondapur.admin@example.com / 123456');
        $this->command?->info('Customer: zone.customer@example.com / 123456');
    }
}
