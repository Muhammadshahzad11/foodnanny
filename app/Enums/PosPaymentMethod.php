<?php

namespace App\Enums;

interface PosPaymentMethod
{
    const CASH           = 1;
    const CREDIT         = 2;
    const CARD           = 4;
    const MOBILE_BANKING = 7;
    const OTHER          = 10;
}
