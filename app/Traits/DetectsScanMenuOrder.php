<?php

namespace App\Traits;

use App\Enums\OrderType;

trait DetectsScanMenuOrder
{
    /** Guest/customer can cancel QR/table orders within this window. */
    public const SCAN_MENU_CANCEL_MINUTES = 2;

    /**
     * Customer table-QR / scan-menu dine-in order.
     */
    public function isScanMenuOrder(): bool
    {
        return (int) $this->order_type === OrderType::DINING_TABLE && (int) $this->table_id > 0;
    }
}
