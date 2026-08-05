<?php


use App\Enums\StatementType;

return [
    StatementType::SALE       => 'Venda',
    StatementType::DELIVERY   => 'Entrega',
    StatementType::PAYOUT     => 'Pagamento / Saque',
    StatementType::REVERSE    => 'Estorno / Reversão',
    StatementType::COMMISSION => 'Comissão' 
];
