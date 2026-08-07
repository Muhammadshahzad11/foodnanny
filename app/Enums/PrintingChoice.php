<?php

namespace App\Enums;

interface PrintingChoice
{
    const BROWSER_POPUP = 5;
    const DIRECT_PRINT  = 10;

    public const LABELS = [
        self::BROWSER_POPUP => 'browser_popup',
        self::DIRECT_PRINT  => 'direct_print',
    ];
}
