<?php

namespace App\Enums;

interface PwaCacheStrategy
{
    const AGGRESSIVE = 'aggressive';
    const BALANCED = 'balanced';
    const NETWORK_FIRST = 'network_first';
}
