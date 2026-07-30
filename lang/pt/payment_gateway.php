<?php

use App\Enums\PaymentGateway;

return [
    PaymentGateway::CASH_ON_DELIVERY => 'Dinheiro na Entrega',
    PaymentGateway::CREDIT           => 'Crédito',
    PaymentGateway::PAYPAL           => 'PayPal'
];
