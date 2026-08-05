<?php

use App\Enums\PaymentGateway;

return [
    PaymentGateway::CASH_ON_DELIVERY => 'الدفع عند الاستلام',
    PaymentGateway::CREDIT           => 'بطاقة ائتمان',
    PaymentGateway::PAYPAL           => 'باي بال'
];
