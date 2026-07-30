<?php


use App\Enums\StatementType;

return [
    StatementType::SALE       => 'Vente',
    StatementType::DELIVERY   => 'Livraison',
    StatementType::PAYOUT     => 'Paiement',
    StatementType::REVERSE    => 'Remboursement',
    StatementType::COMMISSION => 'Commission'
];
