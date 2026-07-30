<?php

return [
    'rate_limits' => [
        'login'          => env('RATE_LIMIT_LOGIN', 5),
        'otp_send'       => env('RATE_LIMIT_OTP_SEND', 3),
        'otp_verify'     => env('RATE_LIMIT_OTP_VERIFY', 5),
        'request_verify' => env('RATE_LIMIT_REQUEST_VERIFY', 5),
        'payment'        => env('RATE_LIMIT_PAYMENT', 10),
        'webhook'        => env('RATE_LIMIT_WEBHOOK', 60)
    ],
];
