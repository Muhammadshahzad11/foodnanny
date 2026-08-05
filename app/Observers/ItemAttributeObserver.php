<?php

namespace App\Observers;

use App\Models\ItemAttribute;
use App\Traits\DefaultAccessModelTrait;

class ItemAttributeObserver
{
    use DefaultAccessModelTrait;

    public function creating(ItemAttribute $itemAttribute): void
    {
        $itemAttribute->restaurant_id = $this->setRestaurant($itemAttribute->restaurant_id);
    }

    public function updating(ItemAttribute $itemAttribute): void
    {
        $itemAttribute->restaurant_id = $this->setRestaurant($itemAttribute->restaurant_id);
    }
}
