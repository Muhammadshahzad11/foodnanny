<?php

use App\Enums\PaymentGateway;

return [
    PaymentGateway::CASH_ON_DELIVERY   => 'Pago contra entrega',
    PaymentGateway::CREDIT             => 'Crédito',
    PaymentGateway::PAYPAL             => 'PayPal'
];
