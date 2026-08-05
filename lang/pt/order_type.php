<?php

use App\Enums\OrderType;

return [
    OrderType::DELIVERY => 'Entrega',
    OrderType::TAKEAWAY => 'Para retirar',
    OrderType::POS      => 'Ponto de Venda (POS)'
];
