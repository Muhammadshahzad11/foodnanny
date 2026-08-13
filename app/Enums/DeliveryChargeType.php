<?php

namespace App\Enums;

interface DeliveryChargeType
{
    const FIXED  = 5;
    const PER_KM = 10;
    const RANGE  = 15;

    const LABELS = [
        self::FIXED  => 'fixed',
        self::PER_KM => 'per_km',
        self::RANGE  => 'range',
    ];
}
