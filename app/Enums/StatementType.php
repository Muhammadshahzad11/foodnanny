<?php

namespace App\Enums;

interface StatementType
{
    const SALE = 1;
    const DELIVERY = 4;
    const PAYOUT = 7;
    const REVERSE = 10;
    const COMMISSION = 13;
    const REFUND = 16;
    const CASHBACK = 19;
}
