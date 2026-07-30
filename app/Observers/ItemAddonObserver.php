<?php

namespace App\Observers;

use App\Models\ItemAddon;
use App\Traits\DefaultAccessModelTrait;

class ItemAddonObserver
{
    use DefaultAccessModelTrait;

    public function creating(ItemAddon $itemAddon)
    {
        $itemAddon->restaurant_id = $this->setRestaurant($itemAddon->restaurant_id);
    }

    public function updating(ItemAddon $itemAddon)
    {
        $itemAddon->restaurant_id = $this->setRestaurant($itemAddon->restaurant_id);
    }
}