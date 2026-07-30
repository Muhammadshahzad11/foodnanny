<?php

use App\Enums\OrderStatus;

return [
    OrderStatus::PENDING          => 'প্রক্রিয়াধীন',
    OrderStatus::ACCEPT           => 'গ্রহণ করা হয়েছে',
    OrderStatus::PREPARING        => 'প্রস্তুত করা হচ্ছে',
    OrderStatus::PREPARED         => 'প্রস্তুত',
    OrderStatus::OUT_FOR_DELIVERY => 'ডেলিভারির জন্য বের হয়েছে',
    OrderStatus::DELIVERED        => 'ডেলিভারি সম্পন্ন',
    OrderStatus::CANCELED         => 'বাতিল',
    OrderStatus::REJECTED         => 'প্রত্যাখ্যান',
    OrderStatus::RETURNED         => 'ফিরতি হয়েছে'
];
