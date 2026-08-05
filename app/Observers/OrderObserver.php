<?php

namespace App\Observers;

use App\Models\Order;
use App\Traits\DefaultAccessModelTrait;

class OrderObserver
{
    use DefaultAccessModelTrait;

    public function creating(Order $order)
    {
        $order->restaurant_id = $this->setRestaurant($order->restaurant_id);
    }

    public function updating(Order $order)
    {
        $order->restaurant_id = $this->setRestaurant($order->restaurant_id);
    }
}
