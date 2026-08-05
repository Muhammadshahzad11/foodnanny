<?php

use App\Enums\OrderStatus;

return [
    OrderStatus::PENDING          => 'قيد الانتظار',
    OrderStatus::ACCEPT           => 'مقبول',
    OrderStatus::PREPARING        => 'قيد التحضير',
    OrderStatus::PREPARED         => 'تم التحضير',
    OrderStatus::OUT_FOR_DELIVERY => 'قيد التوصيل',
    OrderStatus::DELIVERED        => 'تم التوصيل',
    OrderStatus::CANCELED         => 'ملغى',
    OrderStatus::REJECTED         => 'مرفوض',
    OrderStatus::RETURNED         => 'تم الإرجاع'
];
