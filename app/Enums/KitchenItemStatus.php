<?php

namespace App\Enums;

interface KitchenItemStatus
{
    const PENDING   = 1;
    const PREPARING = 5;
    const READY     = 10;
    const SERVED    = 15;
}
