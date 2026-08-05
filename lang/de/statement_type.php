<?php


use App\Enums\StatementType;

return [
    StatementType::SALE       => 'Verkauf',
    StatementType::DELIVERY   => 'Lieferung',
    StatementType::PAYOUT     => 'Auszahlung',
    StatementType::REVERSE    => 'Stornierung/Rückbuchung',
    StatementType::COMMISSION => 'Provision'
];