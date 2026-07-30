<?php

use App\Enums\OrderStatus;

return [
   OrderStatus::PENDING          => 'Ausstehend',
   OrderStatus::ACCEPT           => 'Angenommen',
   OrderStatus::PREPARING        => 'Wird zubereitet',
   OrderStatus::PREPARED         => 'Zubereitet',
   OrderStatus::OUT_FOR_DELIVERY => 'In der Zustellung',
   OrderStatus::DELIVERED        => 'Geliefert',
   OrderStatus::CANCELED         => 'Storniert',
   OrderStatus::REJECTED         => 'Abgelehnt',
   OrderStatus::RETURNED         => 'Rückgabe',
];
