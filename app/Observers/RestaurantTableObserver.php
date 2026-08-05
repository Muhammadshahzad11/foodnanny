<?php

namespace App\Observers;

use App\Models\RestaurantTable;
use App\Traits\DefaultAccessModelTrait;

class RestaurantTableObserver
{
    use DefaultAccessModelTrait;

    public function creating(RestaurantTable $restaurantTable): void
    {
        $restaurantTable->restaurant_id = $this->setRestaurant($restaurantTable->restaurant_id);
    }

    public function updating(RestaurantTable $restaurantTable): void
    {
        if ($this->restaurant() > 0) {
            $restaurantTable->restaurant_id = $this->restaurant();
        }
    }
}
