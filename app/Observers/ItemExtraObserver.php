<?php

namespace App\Observers;

use App\Models\ItemExtra;
use App\Traits\DefaultAccessModelTrait;

class ItemExtraObserver
{
    use DefaultAccessModelTrait;

    public function creating(ItemExtra $itemExtra)
    {
        $itemExtra->restaurant_id = $this->setRestaurant($itemExtra->restaurant_id);
    }

    public function updating(ItemExtra $itemExtra)
    {
        $itemExtra->restaurant_id = $this->setRestaurant($itemExtra->restaurant_id);
    }
}