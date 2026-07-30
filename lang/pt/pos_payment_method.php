<?php


use App\Enums\PosPaymentMethod;

return [
    PosPaymentMethod::CASH           => 'Dinheiro/Dinheiro na Entrega',
    PosPaymentMethod::CREDIT         => 'Crédito',
    PosPaymentMethod::CARD           => 'Cartão',
    PosPaymentMethod::MOBILE_BANKING => 'Banco Móvel',
    PosPaymentMethod::OTHER          => 'Outro'
];
