<?php

namespace App\Enums;

interface OrderItemChangeAction
{
    const ADD              = 'ADD';
    const REMOVE           = 'REMOVE';
    const QUANTITY_CHANGE  = 'QUANTITY_CHANGE';
    const VOID             = 'VOID';
    const CANCEL           = 'CANCEL';
    const PRICE_CHANGE     = 'PRICE_CHANGE';
    const DISCOUNT_CHANGE  = 'DISCOUNT_CHANGE';
}
