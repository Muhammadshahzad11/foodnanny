<?php

use App\Enums\StatementType;

return [
    StatementType::SALE       => 'বিক্রয়',
    StatementType::DELIVERY   => 'ডেলিভারি',
    StatementType::PAYOUT     => 'পেআউট',
    StatementType::REVERSE    => 'রিভার্স',
    StatementType::COMMISSION => 'কমিশন'
];
