<?php

namespace App\Observers;

use App\Models\RestaurantDeliveryZone;
use App\Traits\DefaultAccessModelTrait;

class RestaurantDeliveryZoneObserver
{
    use DefaultAccessModelTrait;

    public function creating(RestaurantDeliveryZone $zone): void
    {
        $zone->restaurant_id = $this->setRestaurant($zone->restaurant_id);
    }

    public function updating(RestaurantDeliveryZone $zone): void
    {
        // Restaurant assignment is Super Admin create-time only.
        if ($zone->isDirty('restaurant_id') && $zone->getOriginal('restaurant_id')) {
            $zone->restaurant_id = (int) $zone->getOriginal('restaurant_id');
        }
    }
}
