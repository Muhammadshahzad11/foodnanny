<?php


use App\Enums\PosPaymentMethod;

return [
    PosPaymentMethod::CASH           => 'Barzahlung/Nachnahme',
    PosPaymentMethod::CREDIT         => 'Guthaben',
    PosPaymentMethod::CARD           => 'Kartenzahlung',
    PosPaymentMethod::MOBILE_BANKING => 'Mobiles Banking',
    PosPaymentMethod::OTHER          => 'Sonstige'
];
