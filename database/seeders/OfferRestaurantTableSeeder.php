<?php

namespace Database\Seeders;

use App\Enums\Ask;
use App\Enums\OfferStatus;
use App\Models\Offer;
use App\Models\OfferRestaurant;
use App\Models\Restaurant;
use App\Models\User;
use Illuminate\Database\Seeder;
use Dipokhalder\EnvEditor\EnvEditor;

class OfferRestaurantTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $envService = new EnvEditor(); 
        if ($envService->getValue('DEMO')) {

            $offers = Offer::get();  
            $restaurants = Restaurant::pluck('id')->toArray(); 

            if ($offers->count() && count($restaurants)) {

                foreach ($offers as $offer) { 
                    $assignedRestaurants = collect($restaurants)->toArray(); 
                    foreach ($assignedRestaurants as $restaurantId) {
                        OfferRestaurant::create([
                            'offer_id'      => $offer->id,
                            'restaurant_id' => $restaurantId,
                            'apply'         => Ask::YES,
                            'status'        => OfferStatus::APPROVE,
                            'creator_type'  => User::class,
                            'creator_id'    => 1,
                            'editor_type'   => User::class,
                            'editor_id'     => 1,
                        ]);
                    }
                }
            }
        }
    }
}
