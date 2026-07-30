<?php

use App\Enums\PaymentGateway;

return [
    PaymentGateway::CASH_ON_DELIVERY   => 'Barzahlung bei Lieferung',
    PaymentGateway::CREDIT             => 'Guthaben',
    PaymentGateway::PAYPAL             => 'PayPal'
];
