<?php

namespace App\Enums;

interface PrinterType
{
    const WINDOWS_SHARED = 5;
    const NETWORK        = 10;

    public const LABELS = [
        self::WINDOWS_SHARED => 'windows_shared',
        self::NETWORK        => 'network',
    ];
}
