<?php

namespace App\Enums;

interface Role
{
    const ADMIN            = 1;
    const RESTAURANT_OWNER = 2;
    const DELIVERY_BOY     = 3;
    const CUSTOMER         = 4;
    const STAFF            = 5;
    const WAITER           = 6;
    const CHEF             = 7;
    const CASHIER          = 8;
    const MANAGER          = 9;
    const ZONE_ADMIN       = 11;
}
