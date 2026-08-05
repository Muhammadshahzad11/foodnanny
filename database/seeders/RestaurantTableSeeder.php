<?php

namespace Database\Seeders;

use App\Models\User;
use Dipokhalder\EnvEditor\EnvEditor;
use App\Enums\Status;
use Illuminate\Support\Str;
use App\Models\Restaurant;
use App\Models\RestaurantCuisine;
use Illuminate\Database\Seeder;

class RestaurantTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */

    public array $restaurants = [
        // Mirpur Restaurants
        [
            'cuisine_id' => [1, 2, 3, 24, 19, 25],
            'data'       => [
                'name'              => 'EmberStone BBQ',
                'email'             => 'restaurantowner@example.com',
                'country_code'      => '+880',
                'phone'             => '1881895211',
                'latitude'          => 23.80244274371095,
                'longitude'         => 90.35511592548137,
                'user_id'           => null,
                'city'              => 'Mirpur',
                'state'             => 'Dhaka',
                'zip_code'          => '1216',
                'address'           => 'House- 18, Avenue- 2, Block- G, Section- 2, Mirpur- 2, Dhaka 1216',
                'status'            => Status::ACTIVE,
                'balance'           => 0,
                'online_commission' => 2,
                'pos_commission'    => 3,
            ]
        ],
        [
            'cuisine_id' => [3, 4, 14, 5, 6, 23],
            'data'       => [
                'name'              => 'TacoFuse Mexican',
                'email'             => 'restaurantowner2@example.com',
                'country_code'      => '+880',
                'phone'             => '1881895212',
                'latitude'          => 23.80352891061119,
                'longitude'         => 90.35638093179973,
                'user_id'           => null,
                'city'              => 'Mirpur',
                'state'             => 'Dhaka',
                'zip_code'          => '1216',
                'address'           => 'Ave- 1, House- 14, 1st floor Dhaka, 15, no Rd No. 5, Dhaka 1216',
                'status'            => Status::ACTIVE,
                'balance'           => 0,
                'online_commission' => 2,
                'pos_commission'    => 3,
            ]
        ],
        [
            'cuisine_id' => [7, 8, 9, 22, 26],
            'data'       => [
                'name'              => 'GrillaFusion Grill & Bar',
                'email'             => 'restaurantowner3@example.com',
                'country_code'      => '+880',
                'phone'             => '1881895213',
                'latitude'          => 23.801637466595835,
                'longitude'         => 90.35429154827371,
                'user_id'           => null,
                'city'              => 'Mirpur',
                'state'             => 'Dhaka',
                'zip_code'          => '1216',
                'address'           => 'Tower, Level 8, Bangladesh Eye Hospital Building, 66, 2 66 Zoo Road, Dhaka',
                'status'            => Status::ACTIVE,
                'balance'           => 0,
                'online_commission' => 2,
                'pos_commission'    => 3,
            ]
        ],
        [
            'cuisine_id' => [9, 10, 11, 21, 22],
            'data'       => [
                'name'              => "Nepolizza Pizza",
                'email'             => 'restaurantowner4@example.com',
                'country_code'      => '+880',
                'phone'             => '1881895214',
                'latitude'          => 23.80243912260968,
                'longitude'         => 90.35344734669427,
                'user_id'           => null,
                'city'              => 'Mirpur',
                'state'             => 'Dhaka',
                'zip_code'          => '1216',
                'address'           => 'Zoo Road, Dhaka',
                'status'            => Status::ACTIVE,
                'balance'           => 0,
                'online_commission' => 2,
                'pos_commission'    => 3,
            ]
        ],
        [
            'cuisine_id' => [11, 12, 13, 20],
            'data'       => [
                'name'              => "CheezoMania Burgers",
                'email'             => 'restaurantowner5@example.com',
                'country_code'      => '+880',
                'phone'             => '1881895215',
                'latitude'          => 23.804416274051007,
                'longitude'         => 90.35872247202211,
                'user_id'           => null,
                'city'              => 'Mirpur',
                'state'             => 'Dhaka',
                'zip_code'          => '1216',
                'address'           => 'Shop - 1, National School Market, Mirpur- 2, Dhaka Dhaka, 1216',
                'status'            => Status::ACTIVE,
                'balance'           => 0,
                'online_commission' => 2,
                'pos_commission'    => 3,
            ]
        ],
        [
            'cuisine_id' => [13, 14, 15, 19],
            'data'       => [
                'name'              => "Wingora Chicken",
                'email'             => 'restaurantowner6@example.com',
                'country_code'      => '+880',
                'phone'             => '1881895216',
                'latitude'          => 23.80615906338512,
                'longitude'         => 90.36009928829421,
                'user_id'           => null,
                'city'              => 'Mirpur',
                'state'             => 'Dhaka',
                'zip_code'          => '1216',
                'address'           => 'Regent Heights, Road: 7, 6 Rd 3, Dhaka 1216',
                'status'            => Status::ACTIVE,
                'balance'           => 0,
                'online_commission' => 2,
                'pos_commission'    => 3,
            ]
        ],
        [
            'cuisine_id' => [15, 16, 17, 18],
            'data'       => [
                'name'              => "CharRoot Grillhouse",
                'email'             => 'restaurantowner7@example.com',
                'country_code'      => '+880',
                'phone'             => '1881895217',
                'latitude'          => 23.807465246857234,
                'longitude'         => 90.36448018130145,
                'user_id'           => null,
                'city'              => 'Mirpur',
                'state'             => 'Dhaka',
                'zip_code'          => '1216',
                'address'           => 'House - 26 Lane No. 6, Dhaka 1216',
                'status'            => Status::ACTIVE,
                'balance'           => 0,
                'online_commission' => 2,
                'pos_commission'    => 3,
            ]
        ],

        /* Dhanmondi Restaurants */
        [
            'cuisine_id' => [25, 9, 26],
            'data'       => [
                'name'              => "Pastario Express",
                'email'             => 'restaurantowner8@example.com',
                'country_code'      => '+880',
                'phone'             => '1881895218',
                'latitude'          => 23.745277110676763,
                'longitude'         => 90.37716451575736,
                'user_id'           => null,
                'city'              => 'Dhanmondi',
                'state'             => 'Dhaka',
                'zip_code'          => '1209',
                'address'           => 'Road-6, 4 Road No 6, Dhaka 1205',
                'status'            => Status::ACTIVE,
                'balance'           => 0,
                'online_commission' => 2,
                'pos_commission'    => 3,
            ]
        ],
        [
            'cuisine_id' => [4, 3],
            'data'       => [
                'name'              => "Greenlory Eatery",
                'email'             => 'restaurantowner9@example.com',
                'country_code'      => '+880',
                'phone'             => '1881895219',
                'latitude'          => 23.750437839254527,
                'longitude'         => 90.36837375246395,
                'user_id'           => null,
                'city'              => 'Dhanmondi',
                'state'             => 'Dhaka',
                'zip_code'          => '1209',
                'address'           => '49 Satmasjid Road, Dhaka 1209',
                'status'            => Status::ACTIVE,
                'balance'           => 0,
                'online_commission' => 2,
                'pos_commission'    => 3,
            ]
        ],
        [
            'cuisine_id' => [22, 7, 11],
            'data'       => [
                'name'              => "Grillivio Steakhouse",
                'email'             => 'restaurantowner10@example.com',
                'country_code'      => '+880',
                'phone'             => '1881895220',
                'latitude'          => 23.739798269718516,
                'longitude'         => 90.37449551829971,
                'user_id'           => null,
                'city'              => 'Dhanmondi',
                'state'             => 'Dhaka',
                'zip_code'          => '1209',
                'address'           => 'Level - 3, AMM Center, 56/A Rd 3A, Dhaka 1209',
                'status'            => Status::ACTIVE,
                'balance'           => 0,
                'online_commission' => 2,
                'pos_commission'    => 3,
            ]
        ],
        [
            'cuisine_id' => [10, 2, 1],
            'data'       => [
                'name'              => "Sushinza",
                'email'             => 'restaurantowner21@example.com',
                'country_code'      => '+880',
                'phone'             => '1881895221',
                'latitude'          => 23.746213450707533,
                'longitude'         => 90.37746362125387,
                'user_id'           => null,
                'city'              => 'Dhanmondi',
                'state'             => 'Dhaka',
                'zip_code'          => '1209',
                'address'           => '736, Level - 3 No, Rang,s KB Square, 9/A Satmasjid Road, Dhaka 1209',
                'status'            => Status::ACTIVE,
                'balance'           => 0,
                'online_commission' => 2,
                'pos_commission'    => 3,
            ]
        ],
        [
            'cuisine_id' => [12, 13, 14],
            'data'       => [
                'name'              => "KoraSuki Grill",
                'email'             => 'restaurantowner11@example.com',
                'country_code'      => '+880',
                'phone'             => '1881895222',
                'latitude'          => 23.75155009955497,
                'longitude'         => 90.36814133193609,
                'user_id'           => null,
                'city'              => 'Dhanmondi',
                'state'             => 'Dhaka',
                'zip_code'          => '1209',
                'address'           => 'new, 27, House No. 59 A, Road No. 16, Old Satmasjid Road, Dhaka 1209',
                'status'            => Status::ACTIVE,
                'balance'           => 0,
                'online_commission' => 2,
                'pos_commission'    => 3,
            ]
        ],
        [
            'cuisine_id' => [1, 10],
            'data'       => [
                'name'              => "BlueTide Seafood",
                'email'             => 'restaurantowner12@example.com',
                'country_code'      => '+880',
                'phone'             => '1881895223',
                'latitude'          => 23.74171357400443,
                'longitude'         => 90.382342279689,
                'user_id'           => null,
                'city'              => 'Dhanmondi',
                'state'             => 'Dhaka',
                'zip_code'          => '1205',
                'address'           => 'House#8/A, Road#4, Royal Palaza (5th Floor), Mirpur Rd, Dhaka 1205',
                'status'            => Status::ACTIVE,
                'balance'           => 0,
                'online_commission' => 2,
                'pos_commission'    => 3,
            ],
        ],
        [
            'cuisine_id' => [1, 26],
            'data'       => [
                'name'              => "FryStreets Gourmet",
                'email'             => 'restaurantowner13@example.com',
                'country_code'      => '+880',
                'phone'             => '1881895224',
                'latitude'          => 23.74617512113082,
                'longitude'         => 90.37413756813262,
                'user_id'           => null,
                'city'              => 'Dhanmondi',
                'state'             => 'Dhaka',
                'zip_code'          => '1209',
                'address'           => '7th floor, House: 54, 10/A Satmasjid Road, Dhaka 1209',
                'status'            => Status::ACTIVE,
                'balance'           => 0,
                'online_commission' => 2,
                'pos_commission'    => 3,
            ]
        ],
        [
            'cuisine_id' => [10, 2, 26],
            'data'       => [
                'name'              => "SoraZen Ramen",
                'email'             => 'restaurantowner14@example.com',
                'country_code'      => '+880',
                'phone'             => '1881895225',
                'latitude'          => 23.7456320814629,
                'longitude'         => 90.38398453897186,
                'user_id'           => null,
                'city'              => 'Dhanmondi',
                'state'             => 'Dhaka',
                'zip_code'          => '1209',
                'address'           => 'House no. 7, Road no. 8, Dhanmondi, Dhaka 1205',
                'status'            => Status::ACTIVE,
                'balance'           => 0,
                'online_commission' => 2,
                'pos_commission'    => 3,
            ]
        ],

        /* Gulshan 1 Restaurants */
        [
            'cuisine_id' => [1, 2, 3],
            'data'       => [
                'name'              => "Olivory Kitchen",
                'email'             => 'restaurantowner15@example.com',
                'country_code'      => '+880',
                'phone'             => '1881895226',
                'latitude'          => 23.78204902063523,
                'longitude'         => 90.41825457121905,
                'user_id'           => null,
                'city'              => 'Gulshan',
                'state'             => 'Dhaka',
                'zip_code'          => '1212',
                'address'           => 'Tower of Akash, 54 Gulshan Ave, Dhaka 1212',
                'status'            => Status::ACTIVE,
                'balance'           => 0,
                'online_commission' => 2,
                'pos_commission'    => 3,
            ]
        ],
        [
            'cuisine_id' => [1, 24],
            'data'       => [
                'name'              => "WrapNora",
                'email'             => 'restaurantowner16@example.com',
                'country_code'      => '+880',
                'phone'             => '1881895227',
                'latitude'          => 23.78193554301709,
                'longitude'         => 90.4158161966449,
                'user_id'           => null,
                'city'              => 'Gulshan',
                'state'             => 'Dhaka',
                'zip_code'          => '1212',
                'address'           => 'House - 1/A, Rd 23, Dhaka 1212',
                'status'            => Status::ACTIVE,
                'balance'           => 0,
                'online_commission' => 2,
                'pos_commission'    => 3,
            ]
        ],
        [
            'cuisine_id' => [2, 10, 1],
            'data'       => [
                'name'              => "Seoulixir BBQ",
                'email'             => 'restaurantowner17@example.com',
                'country_code'      => '+880',
                'phone'             => '1881895228',
                'latitude'          => 23.78132832920188,
                'longitude'         => 90.4154147877794,
                'user_id'           => null,
                'city'              => 'Gulshan',
                'state'             => 'Dhaka',
                'zip_code'          => '1212',
                'address'           => '60/D, Road:131,Gulshan-1,Dhaka 1212 dhaka, 1212',
                'status'            => Status::ACTIVE,
                'balance'           => 0,
                'online_commission' => 2,
                'pos_commission'    => 3,
            ]
        ],
        [
            'cuisine_id' => [17, 2, 18],
            'data'       => [
                'name'              => "ThaiSora Bowl",
                'email'             => 'restaurantowner18@example.com',
                'country_code'      => '+880',
                'phone'             => '1881895229',
                'latitude'          => 23.78020219662415,
                'longitude'         => 90.41728110269523,
                'user_id'           => null,
                'city'              => 'Gulshan',
                'state'             => 'Dhaka',
                'zip_code'          => '1212',
                'address'           => '10th floor, Jabbar Tower, 42 Gulshan Ave, Dhaka 1212',
                'status'            => Status::ACTIVE,
                'balance'           => 0,
                'online_commission' => 2,
                'pos_commission'    => 3,
            ]
        ],
        [
            'cuisine_id' => [4, 24, 19],
            'data'       => [
                'name'              => "FirePit Mexican Grill",
                'email'             => 'restaurantowner19@example.com',
                'country_code'      => '+880',
                'phone'             => '1881895230',
                'latitude'          => 23.78130576268452,
                'longitude'         => 90.41768580270792,
                'user_id'           => null,
                'city'              => 'Gulshan',
                'state'             => 'Dhaka',
                'zip_code'          => '1212',
                'address'           => 'House no 60/B, Road No 131, 2nd Floor 60/B, Rd 131, Dhaka 1212',
                'status'            => Status::ACTIVE,
                'balance'           => 0,
                'online_commission' => 2,
                'pos_commission'    => 3,
            ]
        ],
        [
            'cuisine_id' => [13, 23],
            'data'       => [
                'name'              => "PureNora Kitchen",
                'email'             => 'restaurantowner20@example.com',
                'country_code'      => '+880',
                'phone'             => '1881895231',
                'latitude'          => 23.777525997874736,
                'longitude'         => 90.41567171854695,
                'user_id'           => null,
                'city'              => 'Gulshan',
                'state'             => 'Dhaka',
                'zip_code'          => '1212',
                'address'           => 'House No. SWF 4/A, Road No 1, Gulshan - 1, Dhaka 1212',
                'status'            => Status::ACTIVE,
                'balance'           => 0,
                'online_commission' => 2,
                'pos_commission'    => 3,
            ]
        ],
        [
            'cuisine_id' => [20, 21],
            'data'       => [
                'name'              => "Grillova Burgers",
                'slug'              => "varicks-market-place",
                'email'             => 'restaurantowner22@example.com',
                'country_code'      => '+880',
                'phone'             => '1881895232',
                'latitude'          => 23.781438286926267,
                'longitude'         => 90.41785696798041,
                'user_id'           => null,
                'city'              => 'Gulshan',
                'state'             => 'Dhaka',
                'zip_code'          => '1212',
                'address'           => '37, South Gulshan, Circle-1, Dhaka 1212',
                'status'            => Status::ACTIVE,
                'balance'           => 0,
                'online_commission' => 2,
                'pos_commission'    => 3,
            ]
        ],
        [
            'cuisine_id' => [13, 12, 22],
            'data'       => [
                'name'              => "SizzleMex Cantina",
                'email'             => 'restaurantowner23@example.com',
                'country_code'      => '+880',
                'phone'             => '1881895233',
                'latitude'          => 23.77792376994761,
                'longitude'         => 90.41840885431678,
                'user_id'           => null,
                'city'              => 'Gulshan',
                'state'             => 'Dhaka',
                'zip_code'          => '1212',
                'address'           => 'House 10 Rd 138, Dhaka 1212',
                'status'            => Status::ACTIVE,
                'balance'           => 0,
                'online_commission' => 2,
                'pos_commission'    => 3,
            ]
        ],

        /*  Mirpur Restaurants */
        [
            'cuisine_id' => [23, 13, 17],
            'data'       => [
                'name'              => "Grainova Express",
                'email'             => 'restaurantowner24@example.com',
                'country_code'      => '+880',
                'phone'             => '1881895234',
                'latitude'          => 23.803728837177655,
                'longitude'         => 90.3548101809473,
                'user_id'           => null,
                'city'              => 'Mirpur',
                'state'             => 'Dhaka',
                'zip_code'          => '1216',
                'address'           => 'Avenue Road Section:2 , Block: A, Avenue:1 , House: 12/1, Dhaka 1216',
                'status'            => Status::ACTIVE,
                'balance'           => 0,
                'online_commission' => 2,
                'pos_commission'    => 3,
            ]
        ],
        [
            'cuisine_id' => [25, 2, 9],
            'data'       => [
                'name'              => "Mandoro Kitchen",
                'email'             => 'restaurantowner25@example.com',
                'country_code'      => '+880',
                'phone'             => '1881895233',
                'latitude'          => 23.801012271018745,
                'longitude'         => 90.35357134433137,
                'user_id'           => null,
                'city'              => 'Mirpur',
                'state'             => 'Dhaka',
                'zip_code'          => '1216',
                'address'           => 'Ist Floor Plot-1, Sony Cinema Hall, 1216, 2 Mirpur Rd, Dhaka 1216',
                'status'            => Status::ACTIVE,
                'balance'           => 0,
                'online_commission' => 2,
                'pos_commission'    => 3,
            ]
        ]
    ];


    public function run()
    {
        $envService = new EnvEditor();
        if ($envService->getValue('DEMO')) {
            foreach ($this->restaurants as $restaurant) {
                $restaurantObject = Restaurant::create([
                    'name'              => $restaurant['data']['name'],
                    'slug'              => Str::slug($restaurant['data']['name']),
                    'email'             => $restaurant['data']['email'],
                    'phone'             => $restaurant['data']['phone'],
                    'country_code'      => $restaurant['data']['country_code'],
                    'latitude'          => $restaurant['data']['latitude'],
                    'longitude'         => $restaurant['data']['longitude'],
                    'user_id'           => $restaurant['data']['user_id'],
                    'city'              => $restaurant['data']['city'],
                    'state'             => $restaurant['data']['state'],
                    'zip_code'          => $restaurant['data']['zip_code'],
                    'address'           => $restaurant['data']['address'],
                    'status'            => $restaurant['data']['status'],
                    'online_commission' => $restaurant['data']['online_commission'],
                    'pos_commission'    => $restaurant['data']['pos_commission'],
                    'current_status'    => Status::ACTIVE,
                    'balance'           => $restaurant['data']['balance'],
                    'creator_type'      => User::class,
                    'creator_id'        => 1,
                    'editor_type'       => User::class,
                    'editor_id'         => 1
                ]);

                if (file_exists(public_path('/images/seeder/restaurant/cover/' . strtolower(str_replace(' ', '_', $restaurant['data']['name'])) . '.png'))) {
                    $restaurantObject->addMedia(public_path('/images/seeder/restaurant/cover/' . strtolower(str_replace(' ', '_', $restaurant['data']['name'])) . '.png'))->preservingOriginal()->toMediaCollection('restaurant');
                }

                if (file_exists(public_path('/images/seeder/restaurant/logo/' . strtolower(str_replace(' ', '_', $restaurant['data']['name'])) . '.png'))) {
                    $restaurantObject->addMedia(public_path('/images/seeder/restaurant/logo/' . strtolower(str_replace(' ', '_', $restaurant['data']['name'])) . '.png'))->preservingOriginal()->toMediaCollection('restaurant-logo');
                }

                foreach ($restaurant['cuisine_id'] as $cuisineId) {
                    RestaurantCuisine::create([
                        'restaurant_id' => $restaurantObject->id,
                        'cuisine_id'    => $cuisineId,
                    ]);
                }
            }
        }
    }
}
