<?php

namespace App\Services;

use App\Enums\Status;
use App\Enums\TableStatus;
use App\Models\RestaurantTable;
use Exception;

class DineInOrderGuard
{
    /**
     * Validate a table is eligible for dine-in ordering.
     *
     * @throws Exception
     */
    public function assertOrderableTable(int $restaurantId, int $tableId, ?string $qrToken = null): RestaurantTable
    {
        $table = RestaurantTable::withoutGlobalScopes()
            ->withTrashed()
            ->with(['restaurant:id,name,slug,status,current_status'])
            ->where('id', $tableId)
            ->first();

        if (!$table || $table->trashed()) {
            throw new Exception(trans('all.message.table_qr_removed'), 422);
        }

        if ((int) $table->restaurant_id !== (int) $restaurantId) {
            throw new Exception(trans('all.message.table_restaurant_mismatch'), 422);
        }

        if (in_array((int) $table->status, [TableStatus::INACTIVE, TableStatus::OUT_OF_SERVICE], true)) {
            throw new Exception(trans('all.message.table_qr_inactive'), 422);
        }

        if (!$table->restaurant || (int) $table->restaurant->status === Status::INACTIVE) {
            throw new Exception(trans('all.message.restaurant_inactive_for_qr'), 422);
        }

        if ((int) $table->restaurant->current_status === Status::INACTIVE) {
            throw new Exception(trans('all.message.restaurant_closed_for_qr'), 422);
        }

        if ($qrToken !== null && $qrToken !== '') {
            if (!hash_equals((string) $table->qr_token, $qrToken)) {
                throw new Exception(trans('all.message.table_qr_invalid'), 422);
            }
        }

        if (!$table->hasQr()) {
            throw new Exception(trans('all.message.table_qr_invalid'), 422);
        }

        return $table;
    }

    /**
     * Validate a table is eligible for staff/waiter dine-in ordering (QR not required).
     *
     * @throws Exception
     */
    public function assertStaffOrderableTable(int $restaurantId, int $tableId): RestaurantTable
    {
        $table = RestaurantTable::withoutGlobalScopes()
            ->withTrashed()
            ->with(['restaurant:id,name,slug,status,current_status'])
            ->where('id', $tableId)
            ->first();

        if (!$table || $table->trashed()) {
            throw new Exception(trans('all.message.table_qr_removed'), 422);
        }

        if ((int) $table->restaurant_id !== (int) $restaurantId) {
            throw new Exception(trans('all.message.table_restaurant_mismatch'), 422);
        }

        if (in_array((int) $table->status, [TableStatus::INACTIVE, TableStatus::OUT_OF_SERVICE], true)) {
            throw new Exception(trans('all.message.table_qr_inactive'), 422);
        }

        if (!$table->restaurant || (int) $table->restaurant->status === Status::INACTIVE) {
            throw new Exception(trans('all.message.restaurant_inactive_for_qr'), 422);
        }

        if ((int) $table->restaurant->current_status === Status::INACTIVE) {
            throw new Exception(trans('all.message.restaurant_closed_for_qr'), 422);
        }

        return $table;
    }
}
