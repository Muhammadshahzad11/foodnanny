<?php


use App\Enums\StatementType;

return [
    StatementType::SALE       => 'Sale',
    StatementType::DELIVERY   => 'Delivery',
    StatementType::PAYOUT     => 'Payout',
    StatementType::REVERSE    => 'Reverse',
    StatementType::COMMISSION => 'Commission'
];
