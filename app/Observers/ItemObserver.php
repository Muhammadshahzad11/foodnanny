<?php

namespace App\Observers;

use App\Models\Item;
use App\Traits\DefaultAccessModelTrait;

class ItemObserver
{
    use DefaultAccessModelTrait;

    public function creating(Item $item)
    {
        $item->restaurant_id = $this->setRestaurant($item->restaurant_id);
    }

    public function updating(Item $item)
    {
        $item->restaurant_id = $this->setRestaurant($item->restaurant_id);
    }
}