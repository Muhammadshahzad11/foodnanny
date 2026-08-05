<?php

namespace App\Observers;

use App\Models\OrderItem;
use App\Traits\DefaultAccessModelTrait;

class OrderItemObserver
{
    use DefaultAccessModelTrait;

    public function creating(OrderItem $orderItem)
    {
        $orderItem->restaurant_id = $this->setRestaurant($orderItem->restaurant_id);
    }

    public function updating(OrderItem $orderItem)
    {
        $orderItem->restaurant_id = $this->setRestaurant($orderItem->restaurant_id);
    }
}
