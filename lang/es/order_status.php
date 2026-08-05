<?php

use App\Enums\OrderStatus;

return [
    OrderStatus::PENDING          => 'Pendiente',
    OrderStatus::ACCEPT           => 'Aceptar',
    OrderStatus::PREPARING        => 'Preparando',
    OrderStatus::PREPARED         => 'Preparado',
    OrderStatus::OUT_FOR_DELIVERY => 'En camino',
    OrderStatus::DELIVERED        => 'Entregado',
    OrderStatus::CANCELED         => 'Cancelado',
    OrderStatus::REJECTED         => 'Rechazado',
    OrderStatus::RETURNED         => 'Devuelto',
];
