<?php

namespace Database\Seeders;

use App\Enums\Ask;
use App\Enums\CampaignStatus;
use App\Models\Campaign;
use App\Models\Restaurant;
use App\Models\User;
use Illuminate\Database\Seeder;
use App\Models\CampaignRestaurant;
use Dipokhalder\EnvEditor\EnvEditor;

class CampaignRestaurantTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $envService = new EnvEditor();

        if ($envService->getValue('DEMO')) {
            $campaigns = Campaign::get();
            $restaurants = Restaurant::pluck('id')->toArray();   

            if ($campaigns->count() && count($restaurants)) {
                foreach ($campaigns as $campaign) { 
                    $assignedRestaurants = collect($restaurants)->shuffle()->take(rand(5, 7))->toArray();
                    foreach ($assignedRestaurants as $restaurantId) {
                        CampaignRestaurant::create([
                            'campaign_id'   => $campaign->id,
                            'restaurant_id' => $restaurantId,
                            'apply'         => Ask::YES,
                            'status'        => CampaignStatus::APPROVE,
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
}
