<?php

namespace App\Enums;

interface PrintFormat
{
    const INVOICE     = 5;
    const KOT         = 10;
    const NO_AUTO_KOT = 15;

    public const LABELS = [
        self::INVOICE     => 'invoice',
        self::KOT         => 'kot',
        self::NO_AUTO_KOT => 'no_auto_kot',
    ];
}
