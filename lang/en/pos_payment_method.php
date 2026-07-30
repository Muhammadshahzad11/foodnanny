<?php


use App\Enums\PosPaymentMethod;

return [
    PosPaymentMethod::CASH           => 'Cash/Cash On Delivery',
    PosPaymentMethod::CREDIT         => 'Credit',
    PosPaymentMethod::CARD           => 'Card',
    PosPaymentMethod::MOBILE_BANKING => 'Mobile Banking',
    PosPaymentMethod::OTHER          => 'Other'
];
