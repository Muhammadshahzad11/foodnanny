<?php

namespace App\Traits;

use App\Enums\Activity;
use App\Enums\OrderStatus;
use App\Enums\OrderType;
use Carbon\Carbon;
use Dipokhalder\Settings\Facades\Settings;

trait DetectsScanMenuOrder
{
    /** Guest/customer can cancel after placing, while this window is open and the order is still pending. */
    public const SCAN_MENU_CANCEL_MINUTES = 2;

    /**
     * Customer table-QR / scan-menu dine-in order.
     */
    public function isScanMenuOrder(): bool
    {
        return (int) $this->order_type === OrderType::DINING_TABLE && (int) $this->table_id > 0;
    }

    public function customerCancelSettingEnabled(): bool
    {
        try {
            return (int) Settings::group('site')->get('site_order_cancel') === Activity::ENABLE;
        } catch (\Throwable $e) {
            return false;
        }
    }

    public function cancelExpiresAt(): ?Carbon
    {
        if (blank($this->order_datetime)) {
            return null;
        }

        return Carbon::parse($this->order_datetime)->addMinutes(self::SCAN_MENU_CANCEL_MINUTES);
    }

    public function cancelWindowSeconds(): int
    {
        $expires = $this->cancelExpiresAt();
        if (!$expires) {
            return 0;
        }

        return max(0, now()->diffInSeconds($expires, false));
    }

    public function customerCanCancel(): bool
    {
        if (!$this->customerCancelSettingEnabled()) {
            return false;
        }

        if ((int) $this->status !== OrderStatus::PENDING) {
            return false;
        }

        $expires = $this->cancelExpiresAt();

        return $expires !== null && now()->lt($expires);
    }
}
