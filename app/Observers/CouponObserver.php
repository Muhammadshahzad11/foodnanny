<?php

namespace App\Observers;

use App\Models\Coupon;
use App\Traits\DefaultAccessModelTrait;

class CouponObserver
{
    use DefaultAccessModelTrait;

    public function creating(Coupon $coupon): void
    {
        $coupon->restaurant_id = $this->setRestaurant($coupon->restaurant_id);
    }

    public function updating(Coupon $coupon): void
    {
        $coupon->restaurant_id = $this->setRestaurant($coupon->restaurant_id);
    }
}
