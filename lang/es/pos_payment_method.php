<?php


use App\Enums\PosPaymentMethod;

return [
    PosPaymentMethod::CASH           => 'Efectivo/Pago contra entrega',
    PosPaymentMethod::CREDIT         => 'Crédito',
    PosPaymentMethod::CARD           => 'Tarjeta',
    PosPaymentMethod::MOBILE_BANKING => 'Banca móvil',
    PosPaymentMethod::OTHER          => 'Otro'
];
