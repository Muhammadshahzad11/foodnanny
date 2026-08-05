<?php


use App\Enums\StatementType;

return [
    StatementType::SALE       => 'Venta',
    StatementType::DELIVERY   => 'Entrega',
    StatementType::PAYOUT     => 'Pago/Retiro',
    StatementType::REVERSE    => 'Reversión',
    StatementType::COMMISSION => 'Comisión'
];
