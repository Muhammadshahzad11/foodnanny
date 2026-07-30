<?php

namespace App\Observers;

use App\Models\ItemCategory;
use App\Traits\DefaultAccessModelTrait;

class ItemCategoryObserver
{
    use DefaultAccessModelTrait;

    public function creating(ItemCategory $itemCategory)
    {
        $itemCategory->restaurant_id = $this->setRestaurant($itemCategory->restaurant_id);
    }

    public function updating(ItemCategory $itemCategory)
    {
        $itemCategory->restaurant_id = $this->setRestaurant($itemCategory->restaurant_id);
    }
}
