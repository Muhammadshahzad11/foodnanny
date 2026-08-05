<?php

namespace App\Observers;

use App\Models\TimeSlot;
use App\Traits\DefaultAccessModelTrait;

class TimeSlotObserver
{
    use DefaultAccessModelTrait;

    public function creating(TimeSlot $timeSlotSetup)
    {
        $timeSlotSetup->restaurant_id = $this->setRestaurant($timeSlotSetup->restaurant_id);
    }

    public function updating(TimeSlot $timeSlotSetup)
    {
        $timeSlotSetup->restaurant_id = $this->setRestaurant($timeSlotSetup->restaurant_id);
    }
}
