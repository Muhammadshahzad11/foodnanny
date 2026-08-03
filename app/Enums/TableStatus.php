<?php

namespace App\Enums;

interface TableStatus
{
    const AVAILABLE      = 5;
    const OCCUPIED       = 10;
    const RESERVED       = 15;
    const CLEANING       = 20;
    const OUT_OF_SERVICE = 25;
    const INACTIVE       = 30;
}
