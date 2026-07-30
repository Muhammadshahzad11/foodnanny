<?php

namespace App\Observers;

use App\Models\Tax;
use App\Models\User;
use App\Traits\DefaultAccessModelTrait;
use Illuminate\Support\Facades\Auth;

class TaxObserver
{
    use DefaultAccessModelTrait;

    public function creating(Tax $tax)
    {
        $tax->restaurant_id = $this->setRestaurant($tax->restaurant_id);
    }

    public function updating(Tax $tax)
    {
        $tax->restaurant_id = $this->setRestaurant($tax->restaurant_id);
    }
}
