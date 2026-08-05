<?php

namespace App\Observers;

use App\Models\OrderSetup;
use App\Traits\DefaultAccessModelTrait;

class OrderSetupObserver
{
    use DefaultAccessModelTrait;

    public function creating(OrderSetup $orderSetup)
    {
        $orderSetup->restaurant_id = $this->setRestaurant($orderSetup->restaurant_id);
    }

    public function updating(OrderSetup $orderSetup)
    {
        $orderSetup->restaurant_id = $this->setRestaurant($orderSetup->restaurant_id);
    }
}
