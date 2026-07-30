<?php
use App\Enums\PosPaymentMethod;

return [
    PosPaymentMethod::CASH           => 'الدفع عند الاستلام',
    PosPaymentMethod::CREDIT         => 'بطاقة ائتمان',
    PosPaymentMethod::CARD           => 'بطاقة',
    PosPaymentMethod::MOBILE_BANKING => 'المصرفية عبر الهاتف المحمول',
    PosPaymentMethod::OTHER          => 'أخرى'
];
