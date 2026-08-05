<?php

use App\Enums\OrderType;

return [
    OrderType::DELIVERY => 'Entrega a domicilio',
    OrderType::TAKEAWAY => 'Para recoger',
    OrderType::POS      => 'Punto de venta (POS)'
];
