<?php

namespace App\Observers;

use App\Models\ItemVariation;
use App\Traits\DefaultAccessModelTrait;

class ItemVariationObserver
{
    use DefaultAccessModelTrait;

    public function creating(ItemVariation $itemVariation)
    {
        $itemVariation->restaurant_id = $this->setRestaurant($itemVariation->restaurant_id);
    }

    public function updating(ItemVariation $itemVariation)
    {
        $itemVariation->restaurant_id = $this->setRestaurant($itemVariation->restaurant_id);
    }
}