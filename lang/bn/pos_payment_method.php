<?php


use App\Enums\PosPaymentMethod;

return [
    PosPaymentMethod::CASH           => 'নগদ / ডেলিভারিতে নগদ',
    PosPaymentMethod::CREDIT         => 'ক্রেডিট',
    PosPaymentMethod::CARD           => 'কার্ড',
    PosPaymentMethod::MOBILE_BANKING => 'মোবাইল ব্যাংকিং',
    PosPaymentMethod::OTHER          => 'অন্যান্য'
];
