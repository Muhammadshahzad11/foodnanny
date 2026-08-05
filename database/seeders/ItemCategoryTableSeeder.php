<?php

namespace Database\Seeders;

use App\Libraries\AppLibrary;
use App\Models\User;
use Dipokhalder\EnvEditor\EnvEditor;
use Illuminate\Database\Seeder;
use App\Models\ItemCategory;
use Illuminate\Support\Str;
use App\Enums\Status;

class ItemCategoryTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public array $restaurantMenuCategories = [
        [
            'restaurant_id'  => 1,
            'menuCategories' => ['Appetizers', 'Flame Grill Burgers', 'Veggie & Plant Based Burgers', 'Sandwich from the Grill', 'Hot Chicken Entrees', 'Beef Entrees', 'Halal Food Platters', 'Cold Sandwiches', 'Side Orders', 'Beverages']
        ],
        [
            'restaurant_id'  => 2,
            'menuCategories' => ['Appetizers', 'Sandwich from the Grill', 'Gyros', 'Cold Sandwiches', 'Quesadillas', 'Sunshine Burritos', 'The Griddle', 'Beverages'],
        ],
        [
            'restaurant_id'  => 3,
            'menuCategories' => ['Flame Grill Burgers', 'Sandwich from the Grill', 'Hot Chicken Entrees', 'Beef Entrees', 'Gyros', 'Halal Food Platters', 'Sunshine Burritos', 'Zoop Soups', 'Shakes', 'Beverages'],
        ],
        [
            'restaurant_id'  => 4,
            'menuCategories' => ['Appetizers', 'Pizza', 'Quesadillas ', 'Sunshine Burritos', 'Side Orders', 'Shakes'],
        ],
        [
            'restaurant_id'  => 5,
            'menuCategories' => ['Appetizers', 'Flame Grill Burgers', 'Veggie & Plant Based Burgers', 'Sandwich from the Grill', 'Hot Chicken Entrees', 'Beef Entrees', 'Shakes', 'Vegan Desserts', 'The Griddle', 'Beverages'],
        ],
        [
            'restaurant_id'  => 6,
            'menuCategories' => ['Appetizers', 'Sandwich from the Grill', 'Gyros', 'Quesadillas', 'Sunshine Burritos', 'Beverages'],
        ],
        [
            'restaurant_id'  => 7,
            'menuCategories' => ['Appetizers', 'Hot Chicken Entrees', 'Beef Entrees', 'Seafood Entrees', 'Gyros', 'Pizza', 'Beverages'],
        ],
        [
            'restaurant_id'  => 8,
            'menuCategories' => ['Appetizers', 'Pizza', 'Quesadillas', 'Sunshine Burritos', 'Side Orders', 'Shakes'],
        ],
        [
            'restaurant_id'  => 9,
            'menuCategories' => ['Big Omelettes', 'Quesadillas', 'Sunshine Burritos', 'House Special Salads', 'Side Orders', 'Beverages'],
        ],
        [
            'restaurant_id'  => 10,
            'menuCategories' => ['Hot Chicken Entrees', 'Beef Entrees', 'Seafood Entrees', 'Halal Food Platters', 'Quesadillas', 'Sunshine Burritos', 'Pizza', 'Beverages'],
        ],
        [
            'restaurant_id'  => 11,
            'menuCategories' => ['Appetizers', 'Flame Grill Burgers', 'Veggie & Plant Based Burgers', 'Sandwich from the Grill', 'Gyros', 'Quesadillas', 'Beverages'],
        ],
        [
            'restaurant_id'  => 12,
            'menuCategories' => ['Beef Entrees', 'Seafood Entrees', 'Sunshine Burritos', 'Big Omelettes', 'Zoop Soups', 'Shakes'],
        ],
        [
            'restaurant_id'  => 13,
            'menuCategories' => ['Appetizers', 'Sandwich from the Grill', 'Gyros', 'Cold Sandwiches', 'Quesadillas', 'Sunshine Burritos', 'Beverages'],
        ],
        [
            'restaurant_id'  => 14,
            'menuCategories' => ['Sandwich from the Grill', 'Gyros', 'Cold Sandwiches', 'Quesadillas', 'House Special Salads', 'Side Orders', 'Beverages'],
        ],
        [
            'restaurant_id'  => 15,
            'menuCategories' => ['Appetizers', 'Hot Chicken Entrees', 'Beef Entrees', 'Sandwich from the Grill', 'Shakes', 'Zoop Soups'],
        ],
        [
            'restaurant_id'  => 16,
            'menuCategories' => ['Appetizers', 'Sunshine Burritos', 'Big Omelettes', 'Vegan Desserts', 'The Griddle', 'House Special Salads', 'Beverages'],
        ],
        [
            'restaurant_id'  => 17,
            'menuCategories' => ['Quesadillas', 'Sunshine Burritos', 'Big Omelettes', 'Zoop Soups', 'Shakes', 'House Special Salads', 'Side Orders'],
        ],
        [
            'restaurant_id'  => 18,
            'menuCategories' => ['Flame Grill Burgers', 'Veggie & Plant Based Burgers', 'Sandwich from the Grill', 'Hot Chicken Entrees', 'Gyros', 'Quesadillas', 'Side Orders'],
        ],
        [
            'restaurant_id'  => 19,
            'menuCategories' => ['Appetizers', 'Seafood Entrees', 'Gyros', 'Big Omelettes', 'House Special Salads', 'Side Orders'],
        ],
        [
            'restaurant_id'  => 20,
            'menuCategories' => ['Appetizers', 'Sunshine Burritos', 'Quesadillas', 'House Special Salads', 'Beverages'],
        ],
        [
            'restaurant_id'  => 21,
            'menuCategories' => ['Flame Grill Burgers', 'Sandwich from the Grill', 'Hot Chicken Entrees', 'Cold Sandwiches', 'Shakes'],
        ],
        [
            'restaurant_id'  => 22,
            'menuCategories' => ['Big Omelettes', 'The Griddle', 'Quesadillas', 'Sunshine Burritos', 'House Special Salads'],
        ],
        [
            'restaurant_id'  => 23,
            'menuCategories' => ['Appetizers', 'Sunshine Burritos', 'Pizza', 'Big Omelettes', 'Beverages'],
        ],
        [
            'restaurant_id'  => 24,
            'menuCategories' => ['Flame Grill Burgers', 'Sandwich from the Grill', 'Hot Chicken Entrees', 'Seafood Entrees', 'Cold Sandwiches', 'Shakes'],
        ],
        [
            'restaurant_id'  => 25,
            'menuCategories' => ['Cold Sandwiches', 'Pizza', 'Zoop Soups', 'Vegan Desserts', 'Side Orders'],
        ],
    ];

    public function run(): void
    {
        $envService = new EnvEditor();
        if ($envService->getValue('DEMO')) {
            foreach ($this->restaurantMenuCategories as $restaurantMenuCategory) {
                foreach ($restaurantMenuCategory['menuCategories'] as $menuCategory) {
                    $itemCategory = ItemCategory::create([
                        'name'          => $menuCategory,
                        'slug'          => Str::slug($menuCategory . AppLibrary::timeWithRand() . rand(1, 1000)),
                        'description'   => null,
                        'restaurant_id' => $restaurantMenuCategory['restaurant_id'],
                        'status'        => Status::ACTIVE,
                        'creator_type'  => User::class,
                        'creator_id'    => 1,
                        'editor_type'   => User::class,
                        'editor_id'     => 1
                    ]);

                    if (file_exists(public_path('/images/seeder/item-category/' . strtolower(str_replace(' ', '_', $menuCategory)) . '.png'))) {
                        $itemCategory->addMedia(public_path('/images/seeder/item-category/' . strtolower(str_replace(' ', '_', $menuCategory)) . '.png'))->preservingOriginal()->toMediaCollection('item-category');
                    }
                }
            }
        }
    }
}
