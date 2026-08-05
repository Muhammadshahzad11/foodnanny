<?php

namespace App\Enums;

interface KitchenPriority
{
    public const NORMAL = 0;
    public const HIGH   = 40;
    public const URGENT = 70;
    public const VIP    = 100;

    public const LABELS = [
        self::NORMAL => 'normal',
        self::HIGH   => 'high',
        self::URGENT => 'urgent',
        self::VIP    => 'vip',
    ];
}
