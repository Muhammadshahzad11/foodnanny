<?php

use App\Enums\PaymentGateway;

return [
    PaymentGateway::CASH_ON_DELIVERY => 'Paiement à la livraison',
    PaymentGateway::CREDIT           => 'Crédit',
    PaymentGateway::PAYPAL           => 'PayPal'
];
