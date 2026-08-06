<?php

namespace Database\Seeders;

use App\Enums\Activity;
use App\Enums\Ask;
use App\Enums\CampaignStatus;
use App\Enums\CampaignType;
use App\Enums\Discount;
use App\Enums\DiscountType;
use App\Enums\ItemType;
use App\Enums\OfferStatus;
use App\Enums\OfferType;
use App\Enums\Owner;
use App\Enums\Role as EnumRole;
use App\Enums\Status;
use App\Enums\TaxType;
use App\Models\Campaign;
use App\Models\CampaignRestaurant;
use App\Models\Coupon;
use App\Models\Cuisine;
use App\Models\FrontendItem;
use App\Models\Item;
use App\Models\ItemAttribute;
use App\Models\ItemCategory;
use App\Models\ItemExtra;
use App\Models\ItemVariation;
use App\Models\Offer;
use App\Models\OfferRestaurant;
use App\Models\OrderSetup;
use App\Models\Restaurant;
use App\Models\RestaurantCuisine;
use App\Models\Subscriber;
use App\Models\Tax;
use App\Models\TimeSlot;
use App\Models\User;
use App\Models\Voucher;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

/**
 * Adds polished Hyderabad-area demo restaurants, menus, offers, coupons, etc.
 * Keeps existing Cost to Cost Foods (id 1). Safe to re-run (idempotent).
 */
class RichDemoDataSeeder extends Seeder
{
    public function run(): void
    {
        if (Restaurant::where('name', 'EmberStone BBQ')->exists()) {
            $this->command?->info('Rich demo data already present — skipping.');
            return;
        }

        $this->command?->info('Seeding rich demo data…');

        $this->polishCostToCost();
        $cuisineMap = $this->seedCuisines();
        $restaurants = $this->seedRestaurants($cuisineMap);
        $this->seedOffersAndCampaigns($restaurants);
        $this->seedPlatformVouchers();
        $this->seedSubscribers();

        $this->command?->info(sprintf(
            'Done: %d restaurants, %d items, %d offers, %d coupons.',
            Restaurant::count(),
            Item::count(),
            Offer::count(),
            Coupon::count()
        ));
    }

    protected function polishCostToCost(): void
    {
        $ctc = Restaurant::find(1);
        if (!$ctc) {
            return;
        }

        $ctc->update([
            'latitude'  => '17.510280',
            'longitude' => '79.123610',
            'city'      => 'Bhuvanagiri',
            'state'     => 'Telangana',
            'zip_code'  => '508116',
            'address'   => '220, Area Hospital Road, Pahadinagar, Bhuvanagiri, Telangana 508116',
            'country_code' => '+91',
            'current_status' => Status::ACTIVE,
            'status' => Status::ACTIVE,
        ]);

        $tax = Tax::firstOrCreate(
            ['restaurant_id' => 1, 'code' => 'GST-5%'],
            [
                'name' => 'GST',
                'tax_rate' => 5,
                'type' => TaxType::PERCENTAGE,
                'status' => Status::ACTIVE,
                'creator_type' => User::class,
                'creator_id' => 1,
                'editor_type' => User::class,
                'editor_id' => 1,
            ]
        );

        $category = ItemCategory::firstOrCreate(
            ['restaurant_id' => 1, 'name' => 'Main Menu'],
            [
                'slug' => Str::slug('Main Menu') . '-1',
                'description' => 'Customer favourites from Cost to Cost Foods',
                'status' => Status::ACTIVE,
                'sort' => 1,
                'creator_type' => User::class,
                'creator_id' => 1,
                'editor_type' => User::class,
                'editor_id' => 1,
            ]
        );

        $attr = ItemAttribute::firstOrCreate(
            ['restaurant_id' => 1, 'name' => 'Size'],
            [
                'status' => Status::ACTIVE,
                'creator_type' => User::class,
                'creator_id' => 1,
                'editor_type' => User::class,
                'editor_id' => 1,
            ]
        );

        $extraItems = [
            ['name' => 'Cheeseburger', 'price' => 149.00, 'type' => ItemType::NON_VEG, 'desc' => 'Juicy beef patty with melted cheese, pickles and house sauce.'],
            ['name' => 'Whopper', 'price' => 199.00, 'type' => ItemType::NON_VEG, 'desc' => 'Flame-grilled signature burger with fresh veggies.'],
            ['name' => 'Ultimate Pepperoni Pizza', 'price' => 349.00, 'type' => ItemType::NON_VEG, 'desc' => 'Loaded pepperoni pizza with extra cheese.'],
            ['name' => 'Garden Fresh Pizza', 'price' => 299.00, 'type' => ItemType::VEG, 'desc' => 'Crispy crust topped with garden vegetables and mozzarella.'],
            ['name' => 'French Fries', 'price' => 99.00, 'type' => ItemType::VEG, 'desc' => 'Golden crispy fries, lightly salted.'],
            ['name' => 'Onion Rings', 'price' => 119.00, 'type' => ItemType::VEG, 'desc' => 'Crunchy battered onion rings.'],
            ['name' => 'Chocolate Milkshake', 'price' => 149.00, 'type' => ItemType::VEG, 'desc' => 'Thick chocolate milkshake topped with cream.'],
            ['name' => 'Vanilla Milkshake', 'price' => 139.00, 'type' => ItemType::VEG, 'desc' => 'Classic vanilla shake made with real ice cream.'],
            ['name' => 'Homemade Lemonade', 'price' => 79.00, 'type' => ItemType::VEG, 'desc' => 'Freshly squeezed lemonade with a hint of mint.'],
            ['name' => 'Classic Ceasar Salad', 'price' => 179.00, 'type' => ItemType::VEG, 'desc' => 'Crisp romaine, parmesan and house Caesar dressing.'],
        ];

        foreach ($extraItems as $row) {
            if (Item::where('restaurant_id', 1)->where('name', $row['name'])->exists()) {
                continue;
            }
            $this->createItem([
                'restaurant_id' => 1,
                'category_id' => $category->id,
                'tax_id' => $tax->id,
                'attribute_id' => $attr->id,
                'name' => $row['name'],
                'price' => $row['price'],
                'item_type' => $row['type'],
                'description' => $row['desc'],
                'variations' => [
                    ['name' => 'Regular', 'price' => $row['price']],
                    ['name' => 'Large', 'price' => round($row['price'] * 1.25, 2)],
                ],
                'extras' => [
                    ['name' => 'Extra Cheese', 'price' => 30],
                    ['name' => 'Extra Sauce', 'price' => 20],
                ],
            ]);
        }

        // Ensure Cost to Cost is on open hours every day
        if (TimeSlot::where('restaurant_id', 1)->count() < 7) {
            $this->seedTimeSlots(1);
        }
    }

    protected function seedCuisines(): array
    {
        $cuisines = [
            ['name' => 'Indian', 'sort' => 1],
            ['name' => 'American', 'sort' => 2],
            ['name' => 'Burgers', 'sort' => 3],
            ['name' => 'Pizza', 'sort' => 4],
            ['name' => 'Italian', 'sort' => 5],
            ['name' => 'Chinese', 'sort' => 6],
            ['name' => 'Asian', 'sort' => 7],
            ['name' => 'Japanese', 'sort' => 8],
            ['name' => 'Sushi', 'sort' => 9],
            ['name' => 'Mexican', 'sort' => 10],
            ['name' => 'Tacos', 'sort' => 11],
            ['name' => 'BBQ', 'sort' => 12],
            ['name' => 'Chicken', 'sort' => 13],
            ['name' => 'Healthy', 'sort' => 14],
            ['name' => 'Halal', 'sort' => 15],
            ['name' => 'Seafood', 'sort' => 16],
            ['name' => 'Fast Food', 'sort' => 17],
            ['name' => 'Wings', 'sort' => 18],
            ['name' => 'Bowls', 'sort' => 19],
            ['name' => 'Sandwiches', 'sort' => 20],
        ];

        $map = [];
        foreach ($cuisines as $c) {
            $cuisine = Cuisine::firstOrCreate(
                ['name' => $c['name']],
                [
                    'slug' => Str::slug($c['name']) . '-' . Str::random(4),
                    'description' => $c['name'] . ' cuisine favourites delivered fresh.',
                    'status' => Status::ACTIVE,
                    'sort' => $c['sort'],
                    'creator_type' => User::class,
                    'creator_id' => 1,
                    'editor_type' => User::class,
                    'editor_id' => 1,
                ]
            );
            $image = public_path('/images/seeder/cuisine/' . strtolower(str_replace(' ', '_', $c['name'])) . '.png');
            if (file_exists($image) && !$cuisine->getFirstMedia('cuisine')) {
                $cuisine->addMedia($image)->preservingOriginal()->toMediaCollection('cuisine');
            }
            $map[$c['name']] = $cuisine->id;
        }

        return $map;
    }

    protected function seedRestaurants(array $cuisineMap): array
    {
        $defs = $this->restaurantDefinitions();
        $created = [];

        foreach ($defs as $index => $def) {
            $restaurant = Restaurant::create([
                'name' => $def['name'],
                'slug' => Str::slug($def['name']) . '-' . Str::random(5),
                'email' => $def['email'],
                'phone' => $def['phone'],
                'country_code' => '+91',
                'latitude' => $def['latitude'],
                'longitude' => $def['longitude'],
                'user_id' => null,
                'city' => $def['city'],
                'state' => 'Telangana',
                'zip_code' => $def['zip'],
                'address' => $def['address'],
                'status' => Status::ACTIVE,
                'current_status' => Status::ACTIVE,
                'balance' => 0,
                'online_commission' => 2,
                'pos_commission' => 3,
                'creator_type' => User::class,
                'creator_id' => 1,
                'editor_type' => User::class,
                'editor_id' => 1,
            ]);

            $this->attachRestaurantMedia($restaurant, $def['name']);
            $this->linkOwner($restaurant, $def, $index + 2);

            foreach ($def['cuisines'] as $cuisineName) {
                if (!isset($cuisineMap[$cuisineName])) {
                    continue;
                }
                RestaurantCuisine::firstOrCreate([
                    'restaurant_id' => $restaurant->id,
                    'cuisine_id' => $cuisineMap[$cuisineName],
                ]);
            }

            // Link Cost to Cost cuisines too once
            if ($index === 0) {
                foreach (['Indian', 'American', 'Burgers', 'Pizza', 'Fast Food'] as $cn) {
                    if (isset($cuisineMap[$cn])) {
                        RestaurantCuisine::firstOrCreate([
                            'restaurant_id' => 1,
                            'cuisine_id' => $cuisineMap[$cn],
                        ]);
                    }
                }
            }

            $taxIds = $this->seedTaxes($restaurant->id);
            $attrId = $this->seedAttribute($restaurant->id);
            $this->seedTimeSlots($restaurant->id);
            $this->seedOrderSetup($restaurant->id, $def['prep_time'] ?? 25);
            $this->seedCoupons($restaurant->id);
            $this->seedMenu($restaurant->id, $def['menu'], $taxIds[0], $attrId);

            $created[] = $restaurant;
        }

        // Coupons for Cost to Cost as well
        if (Coupon::where('restaurant_id', 1)->count() < 3) {
            $this->seedCoupons(1);
        }

        return $created;
    }

    protected function restaurantDefinitions(): array
    {
        // Clustered around Hyderabad / Bhuvanagiri so location search finds them together
        return [
            [
                'name' => 'EmberStone BBQ',
                'email' => 'emberstone@ctocfoods.in',
                'phone' => '9876501001',
                'owner' => 'Arjun Reddy',
                'latitude' => '17.441200',
                'longitude' => '78.391500',
                'city' => 'Hyderabad',
                'zip' => '500032',
                'address' => 'Plot 12, Gachibowli Main Road, Hyderabad, Telangana 500032',
                'cuisines' => ['BBQ', 'American', 'Halal'],
                'prep_time' => 35,
                'menu' => [
                    'Starters' => [
                        ['name' => 'Onion Rings', 'price' => 129, 'type' => ItemType::VEG],
                        ['name' => 'French Fries', 'price' => 99, 'type' => ItemType::VEG],
                        ['name' => 'Baked Potato', 'price' => 119, 'type' => ItemType::VEG],
                    ],
                    'Grill' => [
                        ['name' => 'BBQ Chicken', 'price' => 299, 'type' => ItemType::NON_VEG],
                        ['name' => 'BBQ Pulled Pork', 'price' => 349, 'type' => ItemType::NON_VEG],
                        ['name' => 'Plain Grilled Chicken', 'price' => 279, 'type' => ItemType::NON_VEG],
                        ['name' => 'American BBQ Single', 'price' => 259, 'type' => ItemType::NON_VEG],
                        ['name' => 'American BBQ Double', 'price' => 349, 'type' => ItemType::NON_VEG],
                    ],
                    'Sides & Drinks' => [
                        ['name' => 'Homemade Mashed Potato', 'price' => 129, 'type' => ItemType::VEG],
                        ['name' => 'Soda (Can)', 'price' => 49, 'type' => ItemType::VEG],
                        ['name' => 'Homemade Lemonade', 'price' => 79, 'type' => ItemType::VEG],
                    ],
                ],
            ],
            [
                'name' => 'TacoFuse Mexican',
                'email' => 'tacofuse@ctocfoods.in',
                'phone' => '9876501002',
                'owner' => 'Meera Sharma',
                'latitude' => '17.423900',
                'longitude' => '78.407500',
                'city' => 'Hyderabad',
                'zip' => '500034',
                'address' => 'Road No. 12, Banjara Hills, Hyderabad, Telangana 500034',
                'cuisines' => ['Mexican', 'Tacos', 'Fast Food'],
                'prep_time' => 20,
                'menu' => [
                    'Burritos' => [
                        ['name' => 'Loaded Veggie Burrito', 'price' => 199, 'type' => ItemType::VEG],
                        ['name' => 'Meat Eaters Burrito', 'price' => 249, 'type' => ItemType::NON_VEG],
                        ['name' => 'Bacon, Egg & Cheddar Burrito', 'price' => 229, 'type' => ItemType::NON_VEG],
                    ],
                    'Quesadillas' => [
                        ['name' => 'Cheese-Quesadilla', 'price' => 179, 'type' => ItemType::VEG],
                        ['name' => 'Beef Quesadilla', 'price' => 229, 'type' => ItemType::NON_VEG],
                        ['name' => 'Italian Chicken Quesadilla', 'price' => 239, 'type' => ItemType::NON_VEG],
                        ['name' => 'Olive and Spinach Quesadilla', 'price' => 199, 'type' => ItemType::VEG],
                    ],
                    'Drinks' => [
                        ['name' => 'Mojito', 'price' => 129, 'type' => ItemType::VEG],
                        ['name' => 'Iced Coffee', 'price' => 99, 'type' => ItemType::VEG],
                    ],
                ],
            ],
            [
                'name' => 'GrillaFusion Grill & Bar',
                'email' => 'grillafusion@ctocfoods.in',
                'phone' => '9876501003',
                'owner' => 'Rahul Verma',
                'latitude' => '17.448500',
                'longitude' => '78.390800',
                'city' => 'Hyderabad',
                'zip' => '500081',
                'address' => 'Cyber Towers Lane, Hitech City, Hyderabad, Telangana 500081',
                'cuisines' => ['BBQ', 'American', 'Burgers'],
                'prep_time' => 30,
                'menu' => [
                    'Burgers' => [
                        ['name' => 'Cheeseburger', 'price' => 179, 'type' => ItemType::NON_VEG],
                        ['name' => 'Bacon Double Cheeseburger', 'price' => 249, 'type' => ItemType::NON_VEG],
                        ['name' => 'Whopper', 'price' => 229, 'type' => ItemType::NON_VEG],
                        ['name' => 'Plant Based Whopper', 'price' => 219, 'type' => ItemType::VEG],
                    ],
                    'Grill Plates' => [
                        ['name' => 'Steak Sandwich', 'price' => 299, 'type' => ItemType::NON_VEG],
                        ['name' => 'Pepper Steak with Onions', 'price' => 349, 'type' => ItemType::NON_VEG],
                        ['name' => 'Chicken Mushroom', 'price' => 279, 'type' => ItemType::NON_VEG],
                    ],
                    'Sides' => [
                        ['name' => 'French Fries', 'price' => 99, 'type' => ItemType::VEG],
                        ['name' => 'Onion Rings', 'price' => 119, 'type' => ItemType::VEG],
                    ],
                ],
            ],
            [
                'name' => 'Cheezomania Burgers',
                'email' => 'cheezomania@ctocfoods.in',
                'phone' => '9876501004',
                'owner' => 'Sneha Patel',
                'latitude' => '17.432800',
                'longitude' => '78.407200',
                'city' => 'Hyderabad',
                'zip' => '500033',
                'address' => 'Jubilee Hills Check Post, Hyderabad, Telangana 500033',
                'cuisines' => ['Burgers', 'American', 'Fast Food'],
                'prep_time' => 18,
                'menu' => [
                    'Signature Burgers' => [
                        ['name' => 'Cheeseburger', 'price' => 169, 'type' => ItemType::NON_VEG],
                        ['name' => 'Bacon Double Cheeseburger', 'price' => 259, 'type' => ItemType::NON_VEG],
                        ['name' => 'Vegan Hum-Burger with Cheese', 'price' => 199, 'type' => ItemType::VEG],
                        ['name' => 'Vegan Royale', 'price' => 209, 'type' => ItemType::VEG],
                        ['name' => 'Plant Based Bakon', 'price' => 189, 'type' => ItemType::VEG],
                    ],
                    'Shakes' => [
                        ['name' => 'Chocolate Milkshake', 'price' => 149, 'type' => ItemType::VEG],
                        ['name' => 'Strawberry Milkshake', 'price' => 149, 'type' => ItemType::VEG],
                        ['name' => 'Banana Milkshake', 'price' => 139, 'type' => ItemType::VEG],
                        ['name' => 'Blueberry Milkshake', 'price' => 159, 'type' => ItemType::VEG],
                    ],
                ],
            ],
            [
                'name' => 'Nepolizza Pizza',
                'email' => 'nepolizza@ctocfoods.in',
                'phone' => '9876501005',
                'owner' => 'Vikram Rao',
                'latitude' => '17.439900',
                'longitude' => '78.395600',
                'city' => 'Hyderabad',
                'zip' => '500081',
                'address' => 'Madhapur Main Road, Hyderabad, Telangana 500081',
                'cuisines' => ['Pizza', 'Italian'],
                'prep_time' => 28,
                'menu' => [
                    'Pizzas' => [
                        ['name' => 'Garden Fresh Pizza', 'price' => 299, 'type' => ItemType::VEG],
                        ['name' => 'Ultimate Pepperoni Pizza', 'price' => 349, 'type' => ItemType::NON_VEG],
                        ['name' => 'BBQ Chicken Bacon Pizza', 'price' => 379, 'type' => ItemType::NON_VEG],
                        ['name' => 'Meatball Pepperoni Pizza', 'price' => 369, 'type' => ItemType::NON_VEG],
                        ['name' => 'Extra Cheesy Alfredo Pizza', 'price' => 339, 'type' => ItemType::VEG],
                        ['name' => 'Super Hawaiian Pizza', 'price' => 329, 'type' => ItemType::NON_VEG],
                        ['name' => 'The Works Pizza', 'price' => 399, 'type' => ItemType::NON_VEG],
                        ['name' => 'Philly Cheesesteak Pizza', 'price' => 389, 'type' => ItemType::NON_VEG],
                    ],
                    'Sides' => [
                        ['name' => 'Garlic Bread', 'price' => 99, 'type' => ItemType::VEG, 'image' => 'french_fries.png'],
                        ['name' => 'Soda (Bottle)', 'price' => 59, 'type' => ItemType::VEG],
                    ],
                ],
            ],
            [
                'name' => 'Sushinza',
                'email' => 'sushinza@ctocfoods.in',
                'phone' => '9876501006',
                'owner' => 'Ananya Iyer',
                'latitude' => '17.415600',
                'longitude' => '78.448900',
                'city' => 'Hyderabad',
                'zip' => '500082',
                'address' => 'Film Nagar Road, Jubilee Hills, Hyderabad, Telangana 500082',
                'cuisines' => ['Japanese', 'Sushi', 'Asian'],
                'prep_time' => 32,
                'menu' => [
                    'Soups & Starters' => [
                        ['name' => 'Wonton Soup', 'price' => 149, 'type' => ItemType::NON_VEG],
                        ['name' => 'Egg Drop Soup', 'price' => 129, 'type' => ItemType::VEG],
                        ['name' => 'Hot & Sour Soup', 'price' => 139, 'type' => ItemType::VEG],
                        ['name' => 'Vegetable Dumplings', 'price' => 179, 'type' => ItemType::VEG],
                        ['name' => 'Chicken Dumplings', 'price' => 199, 'type' => ItemType::NON_VEG],
                    ],
                    'Mains' => [
                        ['name' => 'Kung Pao Chicken', 'price' => 279, 'type' => ItemType::NON_VEG],
                        ['name' => 'Sesame Chicken', 'price' => 269, 'type' => ItemType::NON_VEG],
                        ['name' => 'Sweet & Sour Chicken', 'price' => 259, 'type' => ItemType::NON_VEG],
                        ['name' => 'Szechuan Beef', 'price' => 299, 'type' => ItemType::NON_VEG],
                        ['name' => 'Szechuan Shrimp', 'price' => 329, 'type' => ItemType::NON_VEG],
                    ],
                ],
            ],
            [
                'name' => 'Thaisora Bowl',
                'email' => 'thaisora@ctocfoods.in',
                'phone' => '9876501007',
                'owner' => 'Priya Nair',
                'latitude' => '17.494500',
                'longitude' => '78.399200',
                'city' => 'Hyderabad',
                'zip' => '500090',
                'address' => 'Kukatpally Housing Board, Hyderabad, Telangana 500090',
                'cuisines' => ['Asian', 'Bowls', 'Healthy'],
                'prep_time' => 22,
                'menu' => [
                    'Rice Bowls' => [
                        ['name' => 'Chicken Over Rice', 'price' => 229, 'type' => ItemType::NON_VEG],
                        ['name' => 'Lamb Over Rice', 'price' => 259, 'type' => ItemType::NON_VEG],
                        ['name' => 'Chicken & Lamb Over Rice', 'price' => 279, 'type' => ItemType::NON_VEG],
                        ['name' => 'Spicy Chicken & Lamb Over Rice', 'price' => 289, 'type' => ItemType::NON_VEG],
                        ['name' => 'Grilled Salmon Over Rice', 'price' => 349, 'type' => ItemType::NON_VEG],
                    ],
                    'Salads' => [
                        ['name' => 'Mix Vegetables Salad', 'price' => 159, 'type' => ItemType::VEG],
                        ['name' => 'Fresh Tuna Salad', 'price' => 249, 'type' => ItemType::NON_VEG],
                        ['name' => 'Roasted Salmon Salad', 'price' => 299, 'type' => ItemType::NON_VEG],
                        ['name' => 'Poached Pear Salad', 'price' => 179, 'type' => ItemType::VEG],
                    ],
                ],
            ],
            [
                'name' => 'Wingora Chicken',
                'email' => 'wingora@ctocfoods.in',
                'phone' => '9876501008',
                'owner' => 'Karan Singh',
                'latitude' => '17.361600',
                'longitude' => '78.474700',
                'city' => 'Hyderabad',
                'zip' => '500001',
                'address' => 'Abids Circle, Hyderabad, Telangana 500001',
                'cuisines' => ['Chicken', 'Wings', 'Fast Food'],
                'prep_time' => 20,
                'menu' => [
                    'Chicken Specials' => [
                        ['name' => 'BBQ Chicken', 'price' => 249, 'type' => ItemType::NON_VEG],
                        ['name' => 'Hentai Chicken', 'price' => 269, 'type' => ItemType::NON_VEG],
                        ['name' => 'Yemete Kudasai Khicken', 'price' => 279, 'type' => ItemType::NON_VEG],
                        ['name' => 'Plain Grilled Chicken', 'price' => 229, 'type' => ItemType::NON_VEG],
                        ['name' => 'Sesame Chicken', 'price' => 259, 'type' => ItemType::NON_VEG],
                    ],
                    'Sides' => [
                        ['name' => 'French Fries', 'price' => 99, 'type' => ItemType::VEG],
                        ['name' => 'Onion Rings', 'price' => 119, 'type' => ItemType::VEG],
                        ['name' => 'Soda (Can)', 'price' => 49, 'type' => ItemType::VEG],
                    ],
                ],
            ],
            [
                'name' => 'Pastario Express',
                'email' => 'pastario@ctocfoods.in',
                'phone' => '9876501009',
                'owner' => 'Deepak Kumar',
                'latitude' => '17.385000',
                'longitude' => '78.486700',
                'city' => 'Hyderabad',
                'zip' => '500095',
                'address' => 'Nampally Station Road, Hyderabad, Telangana 500095',
                'cuisines' => ['Italian', 'Sandwiches', 'Fast Food'],
                'prep_time' => 24,
                'menu' => [
                    'Sandwiches' => [
                        ['name' => 'Italian Sandwich', 'price' => 199, 'type' => ItemType::NON_VEG],
                        ['name' => 'American Sandwich', 'price' => 189, 'type' => ItemType::NON_VEG],
                        ['name' => 'Steak Sandwich', 'price' => 259, 'type' => ItemType::NON_VEG],
                        ['name' => 'Chopheads Sandwich', 'price' => 219, 'type' => ItemType::NON_VEG],
                        ['name' => 'Sweet Stuff Sandwich', 'price' => 179, 'type' => ItemType::VEG],
                    ],
                    'Breakfast' => [
                        ['name' => 'Classic Pancake Stack', 'price' => 149, 'type' => ItemType::VEG],
                        ['name' => 'Banana Pancakes', 'price' => 159, 'type' => ItemType::VEG],
                        ['name' => 'Classic French Toast', 'price' => 139, 'type' => ItemType::VEG],
                        ['name' => 'Cappuccino', 'price' => 99, 'type' => ItemType::VEG],
                        ['name' => 'Espresso', 'price' => 79, 'type' => ItemType::VEG],
                    ],
                ],
            ],
            [
                'name' => 'Greenlory Eatery',
                'email' => 'greenlory@ctocfoods.in',
                'phone' => '9876501010',
                'owner' => 'Nisha Gupta',
                'latitude' => '17.506500',
                'longitude' => '79.118900',
                'city' => 'Bhuvanagiri',
                'zip' => '508116',
                'address' => 'Near Bus Stand, Bhuvanagiri, Telangana 508116',
                'cuisines' => ['Healthy', 'Bowls', 'Indian'],
                'prep_time' => 20,
                'menu' => [
                    'Healthy Bowls' => [
                        ['name' => 'Mix Vegetables Salad', 'price' => 149, 'type' => ItemType::VEG],
                        ['name' => 'Classic Ceasar Salad', 'price' => 179, 'type' => ItemType::VEG],
                        ['name' => 'Fresh Tuna Salad', 'price' => 249, 'type' => ItemType::NON_VEG],
                        ['name' => 'Beef with Mix Vegetables', 'price' => 279, 'type' => ItemType::NON_VEG],
                        ['name' => 'Salmon with Mix Vegetables', 'price' => 329, 'type' => ItemType::NON_VEG],
                        ['name' => 'Beef with Brocolli', 'price' => 269, 'type' => ItemType::NON_VEG],
                    ],
                    'Light Bites' => [
                        ['name' => 'Vegetable Roll', 'price' => 99, 'type' => ItemType::VEG],
                        ['name' => 'Egg Roll', 'price' => 119, 'type' => ItemType::NON_VEG],
                        ['name' => 'Chai Latte', 'price' => 89, 'type' => ItemType::VEG],
                        ['name' => 'Carrot Walnut Muffin', 'price' => 79, 'type' => ItemType::VEG],
                    ],
                ],
            ],
        ];
    }

    protected function attachRestaurantMedia(Restaurant $restaurant, string $name): void
    {
        $file = strtolower(str_replace(' ', '_', $name)) . '.png';
        $cover = public_path('/images/seeder/restaurant/cover/' . $file);
        $logo = public_path('/images/seeder/restaurant/logo/' . $file);
        if (file_exists($cover)) {
            $restaurant->addMedia($cover)->preservingOriginal()->toMediaCollection('restaurant');
        }
        if (file_exists($logo)) {
            $restaurant->addMedia($logo)->preservingOriginal()->toMediaCollection('restaurant-logo');
        }
    }

    protected function linkOwner(Restaurant $restaurant, array $def, int $seq): void
    {
        $email = 'owner' . $seq . '@ctocfoods.in';
        if (User::where('email', $email)->exists()) {
            $owner = User::where('email', $email)->first();
        } else {
            $owner = User::create([
                'name' => $def['owner'],
                'email' => $email,
                'phone' => $def['phone'],
                'username' => 'owner' . $seq,
                'email_verified_at' => now(),
                'password' => bcrypt('123456'),
                'restaurant_id' => $restaurant->id,
                'status' => Status::ACTIVE,
                'country_code' => '+91',
                'is_guest' => Ask::NO,
                'balance' => 0,
                'terms_and_conditions' => Ask::YES,
                'creator_type' => User::class,
                'creator_id' => 1,
                'editor_type' => User::class,
                'editor_id' => 1,
            ]);
            $owner->assignRole(EnumRole::RESTAURANT_OWNER);
        }

        $restaurant->update(['user_id' => $owner->id]);
    }

    protected function seedTaxes(int $restaurantId): array
    {
        $defs = [
            ['name' => 'No GST', 'code' => 'GST-0', 'rate' => 0],
            ['name' => 'GST', 'code' => 'GST-5%', 'rate' => 5],
            ['name' => 'GST', 'code' => 'GST-12%', 'rate' => 12],
        ];
        $ids = [];
        foreach ($defs as $d) {
            $tax = Tax::create([
                'restaurant_id' => $restaurantId,
                'name' => $d['name'],
                'code' => $d['code'],
                'tax_rate' => $d['rate'],
                'type' => TaxType::PERCENTAGE,
                'status' => Status::ACTIVE,
                'creator_type' => User::class,
                'creator_id' => 1,
                'editor_type' => User::class,
                'editor_id' => 1,
            ]);
            $ids[] = $tax->id;
        }
        return $ids;
    }

    protected function seedAttribute(int $restaurantId): int
    {
        return ItemAttribute::create([
            'restaurant_id' => $restaurantId,
            'name' => 'Size',
            'status' => Status::ACTIVE,
            'creator_type' => User::class,
            'creator_id' => 1,
            'editor_type' => User::class,
            'editor_id' => 1,
        ])->id;
    }

    protected function seedTimeSlots(int $restaurantId): void
    {
        TimeSlot::where('restaurant_id', $restaurantId)->delete();
        for ($day = 0; $day <= 6; $day++) {
            TimeSlot::create([
                'restaurant_id' => $restaurantId,
                'opening_time' => '08:00',
                'closing_time' => '23:55',
                'day' => $day,
                'creator_type' => User::class,
                'creator_id' => 1,
                'editor_type' => User::class,
                'editor_id' => 1,
            ]);
        }
    }

    protected function seedOrderSetup(int $restaurantId, int $prepTime): void
    {
        OrderSetup::updateOrCreate(
            ['restaurant_id' => $restaurantId],
            [
                'food_preparation_time' => $prepTime,
                'schedule_order_slot_duration' => 15,
                'minimum_order_limit' => 99,
                'takeaway' => Activity::ENABLE,
                'delivery' => Activity::ENABLE,
                'creator_type' => User::class,
                'creator_id' => 1,
                'editor_type' => User::class,
                'editor_id' => 1,
            ]
        );
    }

    protected function seedCoupons(int $restaurantId): void
    {
        $coupons = [
            ['name' => 'Welcome 10', 'code' => 'WELCOME10', 'discount' => 10, 'type' => TaxType::PERCENTAGE, 'min' => 199, 'max' => 100],
            ['name' => 'Save 50', 'code' => 'SAVE50', 'discount' => 50, 'type' => TaxType::FIXED, 'min' => 299, 'max' => null],
            ['name' => 'Lunch Special', 'code' => 'LUNCH15', 'discount' => 15, 'type' => TaxType::PERCENTAGE, 'min' => 249, 'max' => 150],
            ['name' => 'Festive Flat', 'code' => 'FESTIVE100', 'discount' => 100, 'type' => TaxType::FIXED, 'min' => 499, 'max' => null],
        ];

        foreach ($coupons as $c) {
            $code = strtoupper($c['code'] . $restaurantId);
            if (Coupon::where('code', $code)->exists()) {
                continue;
            }
            $data = [
                'name' => $c['name'],
                'code' => $code,
                'restaurant_id' => $restaurantId,
                'start_date' => now(),
                'end_date' => Carbon::now()->addDays(365),
                'discount' => $c['discount'],
                'discount_type' => $c['type'],
                'minimum_order' => $c['min'],
                'limit_per_user' => 5,
                'type' => Discount::DEFAULT,
                'owner' => Owner::RESTAURANT_OWNER,
                'creator_type' => User::class,
                'creator_id' => 1,
                'editor_type' => User::class,
                'editor_id' => 1,
            ];
            if ($c['max'] !== null) {
                $data['maximum_discount'] = $c['max'];
            }
            Coupon::create($data);
        }
    }

    protected function seedMenu(int $restaurantId, array $menu, int $taxId, int $attributeId): void
    {
        $sort = 1;
        foreach ($menu as $categoryName => $items) {
            $category = ItemCategory::create([
                'restaurant_id' => $restaurantId,
                'name' => $categoryName,
                'slug' => Str::slug($categoryName) . '-' . $restaurantId . '-' . Str::random(4),
                'description' => $categoryName . ' from our kitchen',
                'status' => Status::ACTIVE,
                'sort' => $sort++,
                'creator_type' => User::class,
                'creator_id' => 1,
                'editor_type' => User::class,
                'editor_id' => 1,
            ]);

            $catImage = public_path('/images/seeder/item-category/' . strtolower(str_replace([' ', '&'], ['_', '&'], $categoryName)) . '.png');
            // try common category images
            foreach (['pizza.png', 'flame_grill_burgers.png', 'beverages.png', 'side_orders.png', 'house_special_salads.png', 'appetizers.png'] as $fallback) {
                $path = public_path('/images/seeder/item-category/' . $fallback);
                if (file_exists($path) && !$category->getFirstMedia('item-category')) {
                    $category->addMedia($path)->preservingOriginal()->toMediaCollection('item-category');
                    break;
                }
            }
            unset($catImage);

            foreach ($items as $item) {
                $this->createItem([
                    'restaurant_id' => $restaurantId,
                    'category_id' => $category->id,
                    'tax_id' => $taxId,
                    'attribute_id' => $attributeId,
                    'name' => $item['name'],
                    'price' => $item['price'],
                    'item_type' => $item['type'],
                    'description' => $item['desc'] ?? ('Freshly prepared ' . $item['name'] . ' made with quality ingredients.'),
                    'image_override' => $item['image'] ?? null,
                    'variations' => [
                        ['name' => 'Regular', 'price' => $item['price']],
                        ['name' => 'Large', 'price' => round($item['price'] * 1.3, 2)],
                    ],
                    'extras' => [
                        ['name' => 'Extra Cheese', 'price' => 30],
                        ['name' => 'Add Sauce', 'price' => 20],
                    ],
                ]);
            }
        }
    }

    protected function createItem(array $data): Item
    {
        $item = Item::create([
            'restaurant_id' => $data['restaurant_id'],
            'name' => $data['name'],
            'slug' => Str::slug($data['name']) . '-' . uniqid(),
            'item_category_id' => $data['category_id'],
            'price' => $data['price'],
            'is_halal' => Ask::YES,
            'available_time_start' => null,
            'available_time_end' => null,
            'discount_type' => DiscountType::PERCENTAGE,
            'discount' => rand(0, 1) ? rand(5, 15) : 0,
            'maximum_purchase_quantity' => 20,
            'status' => Status::ACTIVE,
            'tax_id' => $data['tax_id'],
            'item_type' => $data['item_type'],
            'order' => 1,
            'caution' => null,
            'description' => $data['description'],
            'creator_type' => User::class,
            'creator_id' => 1,
            'editor_type' => User::class,
            'editor_id' => 1,
        ]);

        $fileName = $data['image_override']
            ?? (strtolower(str_replace(' ', '_', $data['name'])) . '.png');
        $path = public_path('/images/seeder/item/' . $fileName);
        if (file_exists($path)) {
            $item->addMedia($path)->preservingOriginal()->toMediaCollection('item');
            $frontendItem = FrontendItem::find($item->id);
            if ($frontendItem) {
                $frontendItem->addMedia($path)->preservingOriginal()->toMediaCollection('frontend-item');
            }
        }

        foreach ($data['variations'] ?? [] as $variation) {
            ItemVariation::create([
                'restaurant_id' => $item->restaurant_id,
                'item_id' => $item->id,
                'item_attribute_id' => $data['attribute_id'],
                'name' => $variation['name'],
                'price' => $variation['price'],
                'status' => Status::ACTIVE,
                'creator_type' => User::class,
                'creator_id' => 1,
                'editor_type' => User::class,
                'editor_id' => 1,
            ]);
        }

        foreach ($data['extras'] ?? [] as $extra) {
            ItemExtra::create([
                'restaurant_id' => $item->restaurant_id,
                'item_id' => $item->id,
                'name' => $extra['name'],
                'price' => $extra['price'],
                'status' => Status::ACTIVE,
                'creator_type' => User::class,
                'creator_id' => 1,
                'editor_type' => User::class,
                'editor_id' => 1,
            ]);
        }

        return $item;
    }

    protected function seedOffersAndCampaigns(array $restaurants): void
    {
        $allRestaurantIds = Restaurant::pluck('id')->all();

        $offers = [
            [
                'title' => 'Festival Food Fiesta',
                'amount' => 25,
                'type' => OfferType::REGULAR,
                'thumb' => 'festival_food_fiesta_thumb.png',
                'banner' => 'festival_food_fiesta_banner.png',
            ],
            [
                'title' => 'Happy Hour Deals',
                'amount' => 20,
                'type' => OfferType::PREMIER,
                'thumb' => 'happy_hour_deals_thumb.png',
                'banner' => 'happy_hour_deals_banner.png',
            ],
            [
                'title' => 'Weekend Grill Boost',
                'amount' => 30,
                'type' => OfferType::PREMIER,
                'thumb' => 'weekend_grill_boost_thumb.png',
                'banner' => 'weekend_grill_boost_banner.png',
            ],
            [
                'title' => 'Spicy Fiesta Campaign',
                'amount' => 15,
                'type' => OfferType::REGULAR,
                'thumb' => 'spicy_fiesta_campaign_thumb.png',
                'banner' => 'spicy_fiesta_campaign_banner.png',
            ],
            [
                'title' => 'New Restaurant Launch Boost',
                'amount' => 10,
                'type' => OfferType::REGULAR,
                'thumb' => 'new_restaurant_launch_boost_thumb.png',
                'banner' => 'new_restaurant_launch_boost_banner.png',
            ],
        ];

        foreach ($offers as $o) {
            if (Offer::where('title', $o['title'])->exists()) {
                continue;
            }
            $offer = Offer::create([
                'title' => $o['title'],
                'slug' => Str::slug($o['title']) . '-' . Str::random(4),
                'description' => '<p><strong>Terms &amp; conditions:</strong></p><p>1. Valid on selected restaurants.</p><p>2. Cost to Cost Foods may update offer terms without prior notice.</p>',
                'start_date' => now(),
                'end_date' => Carbon::now()->addDays(365),
                'start_time' => '00:00:00',
                'end_time' => '23:59:00',
                'type' => $o['type'],
                'amount' => $o['amount'],
                'location' => null,
                'latitude' => null,
                'longitude' => null,
                'is_single' => Ask::NO,
                'status' => Status::ACTIVE,
                'creator_type' => User::class,
                'creator_id' => 1,
                'editor_type' => User::class,
                'editor_id' => 1,
            ]);

            $thumb = public_path('/images/seeder/offer/' . $o['thumb']);
            $banner = public_path('/images/seeder/offer/' . $o['banner']);
            // offer images live under both offer/ and campaign/ folders in this project
            if (!file_exists($thumb)) {
                $thumb = public_path('/images/seeder/campaign/' . $o['thumb']);
            }
            if (!file_exists($banner)) {
                $banner = public_path('/images/seeder/campaign/' . $o['banner']);
            }
            if (file_exists($thumb)) {
                $offer->addMedia($thumb)->preservingOriginal()->toMediaCollection('offer-thumb');
            }
            if (file_exists($banner)) {
                $offer->addMedia($banner)->preservingOriginal()->toMediaCollection('offer-cover');
            }

            foreach ($allRestaurantIds as $rid) {
                OfferRestaurant::firstOrCreate(
                    ['offer_id' => $offer->id, 'restaurant_id' => $rid],
                    [
                        'apply' => Ask::YES,
                        'status' => OfferStatus::APPROVE,
                        'creator_type' => User::class,
                        'creator_id' => 1,
                        'editor_type' => User::class,
                        'editor_id' => 1,
                    ]
                );
            }
        }

        $campaigns = [
            [
                'title' => 'Flat 20 percent Off on First Order',
                'amount' => 200,
                'thumb' => 'flat_20_persent_off_on_first_order_thumb.png',
                'banner' => 'flat_20_persent_off_on_first_order_banner.png',
            ],
            [
                'title' => 'Seasonal Combo Meal',
                'amount' => 500,
                'thumb' => 'seasonal_combo_meal_thumb.png',
                'banner' => 'seasonal_combo_meal_banner.png',
            ],
            [
                'title' => '10 percent Off on All Asian Cuisine',
                'amount' => 300,
                'thumb' => '10_persent_off_on_all_asian_cuisine_thumb.png',
                'banner' => '10_persent_off_on_all_asian_cuisine_banner.png',
            ],
            [
                'title' => 'Cheezy Blast Offer',
                'amount' => 250,
                'thumb' => 'cheezy_blast_offer_thumb.png',
                'banner' => 'cheezy_blast_offer_banner.png',
            ],
            [
                'title' => 'Big Salad Combo',
                'amount' => 180,
                'thumb' => 'big_salad_combo_thumb.png',
                'banner' => 'big_salad_combo_banner.png',
            ],
        ];

        foreach ($campaigns as $c) {
            if (Campaign::where('title', $c['title'])->exists()) {
                continue;
            }
            $campaign = Campaign::create([
                'title' => $c['title'],
                'slug' => Str::slug($c['title']) . '-' . Str::random(4),
                'description' => '<p><strong>Terms &amp; conditions:</strong></p><p>1. Valid on selected restaurants &amp; items.</p><p>2. Offer limited while stocks last.</p>',
                'start_date' => now(),
                'end_date' => Carbon::now()->addDays(365),
                'start_time' => '00:00:00',
                'end_time' => '23:59:00',
                'type' => CampaignType::PAID,
                'amount' => $c['amount'],
                'status' => Status::ACTIVE,
                'creator_type' => User::class,
                'creator_id' => 1,
                'editor_type' => User::class,
                'editor_id' => 1,
            ]);

            $thumb = public_path('/images/seeder/campaign/' . $c['thumb']);
            $banner = public_path('/images/seeder/campaign/' . $c['banner']);
            if (file_exists($thumb)) {
                $campaign->addMedia($thumb)->preservingOriginal()->toMediaCollection('campaign-thumb');
            }
            if (file_exists($banner)) {
                $campaign->addMedia($banner)->preservingOriginal()->toMediaCollection('campaign-cover');
            }

            foreach (collect($allRestaurantIds)->shuffle()->take(7) as $rid) {
                CampaignRestaurant::firstOrCreate(
                    ['campaign_id' => $campaign->id, 'restaurant_id' => $rid],
                    [
                        'apply' => Ask::YES,
                        'status' => CampaignStatus::APPROVE,
                        'creator_type' => User::class,
                        'creator_id' => 1,
                        'editor_type' => User::class,
                        'editor_id' => 1,
                    ]
                );
            }
        }
    }

    protected function seedPlatformVouchers(): void
    {
        $vouchers = [
            [
                'name' => 'Free Delivery',
                'code' => 'FREEDEL',
                'discount' => 0,
                'discount_type' => TaxType::PERCENTAGE,
                'minimum_order' => 299,
                'maximum_discount' => 0,
                'type' => Discount::FREE_DELIVERY,
            ],
            [
                'name' => 'Welcome Back',
                'code' => 'WELCOMEBACK',
                'discount' => 10,
                'discount_type' => TaxType::PERCENTAGE,
                'minimum_order' => 199,
                'maximum_discount' => 100,
                'type' => Discount::DEFAULT,
            ],
            [
                'name' => 'Yummy Treat',
                'code' => 'YUMMY50',
                'discount' => 50,
                'discount_type' => TaxType::FIXED,
                'minimum_order' => 249,
                'maximum_discount' => 50,
                'type' => Discount::DEFAULT,
            ],
            [
                'name' => 'Weekend Saver',
                'code' => 'WEEKEND20',
                'discount' => 20,
                'discount_type' => TaxType::PERCENTAGE,
                'minimum_order' => 399,
                'maximum_discount' => 200,
                'type' => Discount::DEFAULT,
            ],
        ];

        foreach ($vouchers as $v) {
            if (Voucher::where('code', $v['code'])->exists()) {
                continue;
            }
            Voucher::create([
                'restaurant_id' => 0,
                'name' => $v['name'],
                'code' => $v['code'],
                'discount' => $v['discount'],
                'discount_type' => $v['discount_type'],
                'start_date' => now(),
                'end_date' => Carbon::now()->addDays(90),
                'minimum_order' => $v['minimum_order'],
                'maximum_discount' => $v['maximum_discount'],
                'limit_per_user' => 3,
                'type' => $v['type'],
                'owner' => Owner::ADMIN,
                'creator_type' => User::class,
                'creator_id' => 1,
                'editor_type' => User::class,
                'editor_id' => 1,
            ]);
        }
    }

    protected function seedSubscribers(): void
    {
        $emails = [
            'priya.sharma@example.com',
            'rahul.verma@example.com',
            'ananya.iyer@example.com',
            'karan.singh@example.com',
            'sneha.patel@example.com',
            'vikram.rao@example.com',
            'meera.nair@example.com',
            'arjun.reddy@example.com',
        ];
        foreach ($emails as $email) {
            Subscriber::firstOrCreate(['email' => $email]);
        }
    }
}
