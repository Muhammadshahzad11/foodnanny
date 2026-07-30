<?php

namespace App\Enums;

interface StatementDetail
{
    const ITEMS_SALE = 1;
    const DELIVERY_FEE_AND_TIP = 4;
    const RELEASE_PAYOUT = 7;
    const REVERSE_PAYOUT = 10;
    const SERVICE_FEE = 13;
    const REFUND = 16;
    const CASHBACK = 19;

}
