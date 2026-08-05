<?php

namespace Database\Seeders;

use App\Models\Restaurant;
use App\Models\User;
use Dipokhalder\EnvEditor\EnvEditor;
use Illuminate\Database\Seeder;
use App\Enums\Status;
use App\Models\ItemAttribute;

class ItemAttributeTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */

    public array $attributes = [
        'Size',
        'Quantity Choice',
        'Steak Size',
        'Steak Temperature',
        'Choose a filling',
        'Egg Variation'
    ];

    public function run(): void
    {
        $envService = new EnvEditor();
        if ($envService->getValue('DEMO')) {
            $restaurants = Restaurant::all();
            foreach ($restaurants as $restaurant) {
                foreach ($this->attributes as $attribute) {
                    ItemAttribute::create([
                        'restaurant_id' => $restaurant->id,
                        'name'          => $attribute,
                        'status'        => Status::ACTIVE,
                        'creator_type'  => User::class,
                        'creator_id'    => 1,
                        'editor_type'   => User::class,
                        'editor_id'     => 1
                    ]);
                }
            }
        }
    }
}
