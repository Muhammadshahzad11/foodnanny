<?php

use App\Enums\OrderType;

return [
    OrderType::DELIVERY => 'Livraison',
    OrderType::TAKEAWAY => 'À emporter',
    OrderType::POS      => 'Point de vente (POS)'
];
