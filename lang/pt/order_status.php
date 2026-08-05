<?php

use App\Enums\OrderStatus;

return [
    OrderStatus::PENDING          => 'Pendente',
    OrderStatus::ACCEPT           => 'Aceito',
    OrderStatus::PREPARING        => 'Preparando',
    OrderStatus::PREPARED         => 'Pronto',
    OrderStatus::OUT_FOR_DELIVERY => 'Saiu para entrega',
    OrderStatus::DELIVERED        => 'Entregue',
    OrderStatus::CANCELED         => 'Cancelado',
    OrderStatus::REJECTED         => 'Rejeitado',
    OrderStatus::RETURNED         => 'Devolvido',
];
