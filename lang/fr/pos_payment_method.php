<?php


use App\Enums\PosPaymentMethod;

return [
    PosPaymentMethod::CASH           => 'Espèces/Paiement à la livraison',
    PosPaymentMethod::CREDIT         => 'Crédit',
    PosPaymentMethod::CARD           => 'Carte bancaire',
    PosPaymentMethod::MOBILE_BANKING => 'Paiement mobile',
    PosPaymentMethod::OTHER          => 'Autre'
];
