<?php
use App\Enums\StatementType;

return [
    StatementType::SALE       => 'مبيعات',
    StatementType::DELIVERY   => 'توصيل',
    StatementType::PAYOUT     => 'دفع',
    StatementType::REVERSE    => 'عكسي',
    StatementType::COMMISSION => 'عمولة'
];
