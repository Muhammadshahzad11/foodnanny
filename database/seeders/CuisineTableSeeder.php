<?php

namespace Database\Seeders;

use App\Enums\Status;
use App\Models\Cuisine;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use Dipokhalder\EnvEditor\EnvEditor;

class CuisineTableSeeder extends Seeder
{
    public array $cuisines = [
        [
            'name' => 'Sandwiches',
            'sort' => 9,
        ],
        [
            'name' => 'American',
            'sort' => 2,
        ],
        [
            'name' => 'Healthy',
            'sort' => 10,
        ],
        [
            'name' => 'Mexican',
            'sort' => 11,
        ],
        [
            'name' => 'Fast Food',
            'sort' => 12,
        ],
        [
            'name' => 'Tacos',
            'sort' => 13,
        ],
        [
            'name' => 'Halal',
            'sort' => 14,
        ],
        [
            'name' => 'Greek',
            'sort' => 27,
        ],
        [
            'name' => 'Italian',
            'sort' => 7,
        ],
        [
            'name' => 'Burgers',
            'sort' => 3,
        ],
        [
            'name' => 'Middle Eastern',
            'sort' => 16,
        ],
        [
            'name' => 'Korean',
            'sort' => 6,
        ],
        [
            'name' => 'Asian',
            'sort' => 1,
        ],
        [
            'name' => 'BBQ',
            'sort' => 17,
        ],
        [
            'name' => 'Sushi',
            'sort' => 18,
        ],
        [
            'name' => 'Japanese',
            'sort' => 5,
        ],
        [
            'name' => 'Seafood',
            'sort' => 19,
        ],
        [
            'name' => 'Fish and Chips',
            'sort' => 20,
        ],
        [
            'name' => 'Bowls',
            'sort' => 21,
        ],
        [
            'name' => 'European',
            'sort' => 22,
        ],
        [
            'name' => 'Gourmet',
            'sort' => 23,
        ],
        [
            'name' => 'Chicken',
            'sort' => 8,
        ],
        [
            'name' => 'Chinese',
            'sort' => 24,
        ],
        [
            'name' => 'Burritos',
            'sort' => 25,
        ],
        [
            'name' => 'Pizza',
            'sort' => 4,
        ],
        [
            'name' => 'Wings',
            'sort' => 26,
        ]
    ];

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        $envService = new EnvEditor();
        if ($envService->getValue('DEMO')) {
            foreach ($this->cuisines as $cuisine) {
                $cuisineModel = Cuisine::create([
                    'name'         => $cuisine['name'],
                    'slug'         => Str::slug($cuisine['name'] . '-' . rand(1, 1000)),
                    'description'  => null,
                    'status'       => Status::ACTIVE,
                    'sort'         => $cuisine['sort'],
                    'creator_type' => User::class,
                    'creator_id'   => 1,
                    'editor_type'  => User::class,
                    'editor_id'    => 1
                ]);

                if (file_exists(public_path('/images/seeder/cuisine/' . strtolower(str_replace(' ', '_', $cuisine['name'])) . '.png'))) {
                    $cuisineModel->addMedia(public_path('/images/seeder/cuisine/' . strtolower(str_replace(' ', '_', $cuisine['name'])) . '.png'))->preservingOriginal()->toMediaCollection('cuisine');
                }
            }
        }
    }
}
