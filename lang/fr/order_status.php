<?php

use App\Enums\OrderStatus;

return [
    OrderStatus::PENDING          => 'En attente',
    OrderStatus::ACCEPT           => 'Accepté',
    OrderStatus::PREPARING        => 'En préparation',
    OrderStatus::PREPARED         => 'Préparé',
    OrderStatus::OUT_FOR_DELIVERY => 'En cours de livraison',
    OrderStatus::DELIVERED        => 'Livré',
    OrderStatus::CANCELED         => 'Annulé',
    OrderStatus::REJECTED         => 'Refusé',
    OrderStatus::RETURNED         => 'Retourné',
];
