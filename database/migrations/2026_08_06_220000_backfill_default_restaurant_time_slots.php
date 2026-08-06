<?php

use App\Models\Restaurant;
use App\Services\TimeSlotService;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

/**
 * Backfill default Mon–Sun 09:00–22:00 slots for restaurants that have none.
 * Does not overwrite existing schedules.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('time_slots') || !Schema::hasTable('restaurants')) {
            return;
        }

        $service = app(TimeSlotService::class);

        Restaurant::query()
            ->whereDoesntHave('timeSlots')
            ->pluck('id')
            ->each(function ($restaurantId) use ($service) {
                $service->ensureDefaults((int) $restaurantId);
            });
    }

    public function down(): void
    {
        // Non-destructive backfill — no automatic rollback of generated slots.
    }
};
