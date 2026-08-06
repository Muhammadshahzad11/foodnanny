<?php

namespace App\Services;

use Exception;
use App\Models\TimeSlot;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Http\Requests\PaginateRequest;
use App\Http\Requests\TimeSlotRequest;
use App\Libraries\QueryExceptionLibrary;
use App\Traits\DefaultAccessModelTrait;

class TimeSlotService
{
    use DefaultAccessModelTrait;

    public const DEFAULT_OPENING = '09:00';
    public const DEFAULT_CLOSING = '22:00';

    /**
     * @throws Exception
     */
    public array $timeSlotFilter = ['opening_time', 'closing_time', 'day'];

    /**
     * @throws Exception
     */
    public function list(PaginateRequest $request)
    {
        try {
            $requests = $request->all();
            $method = $request->get('paginate', 0) == 1 ? 'paginate' : 'get';
            $methodValue = $request->get('paginate', 0) == 1 ? $request->get('per_page', 10) : '*';
            $orderColumn = $request->get('order_column') ?? 'day';
            $orderType = $request->get('order_type') ?? 'asc';

            return TimeSlot::where(function ($query) use ($requests) {
                foreach ($requests as $key => $request) {
                    if (in_array($key, $this->timeSlotFilter)) {
                        $query->where($key, 'like', '%' . $request . '%');
                    }
                }
            })->orderBy($orderColumn, $orderType)->orderBy('opening_time')->$method(
                $methodValue
            );
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }

    /**
     * @throws Exception
     */
    public function store(TimeSlotRequest $request)
    {
        try {
            if ($this->hasOverlap((int) $request->day, $request->opening_time, $request->closing_time)) {
                throw new Exception(trans('all.message.time_slot_exist'), 422);
            }

            return TimeSlot::create($request->validated());
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }

    /**
     * Create Mon–Sun 09:00–22:00 defaults when the restaurant has zero slots.
     * Returns number of slots created (0 if restaurant already has slots).
     *
     * @throws Exception
     */
    public function ensureDefaults(?int $restaurantId = null): int
    {
        try {
            $restaurantId = (int) ($restaurantId ?: $this->restaurant());
            if ($restaurantId <= 0) {
                throw new Exception(trans('all.message.something_wrong'), 422);
            }

            $existing = TimeSlot::withoutGlobalScopes()
                ->where('restaurant_id', $restaurantId)
                ->exists();

            if ($existing) {
                return 0;
            }

            $now    = now();
            $userId = Auth::id() ?: 1;
            $rows   = [];

            for ($day = 0; $day <= 6; $day++) {
                $rows[] = [
                    'restaurant_id' => $restaurantId,
                    'opening_time'  => self::DEFAULT_OPENING,
                    'closing_time'  => self::DEFAULT_CLOSING,
                    'day'           => $day,
                    'creator_type'  => User::class,
                    'creator_id'    => $userId,
                    'editor_type'   => User::class,
                    'editor_id'     => $userId,
                    'created_at'    => $now,
                    'updated_at'    => $now,
                ];
            }

            // insert() bypasses observers/scopes so restaurant_id stays correct
            TimeSlot::withoutGlobalScopes()->insert($rows);

            return count($rows);
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }

    /**
     * @throws Exception
     */
    public function destroy(TimeSlot $timeSlot): void
    {
        try {
            $timeSlot->delete();
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }

    protected function hasOverlap(int $day, string $opening, string $closing, ?int $ignoreId = null): bool
    {
        $slots = TimeSlot::query()->where('day', $day)->get();

        foreach ($slots as $slot) {
            if ($ignoreId && (int) $slot->id === $ignoreId) {
                continue;
            }

            $existingOpen  = $slot->opening_time;
            $existingClose = $slot->closing_time;

            // Exact duplicate or any interval overlap (supports multiple non-overlapping shifts)
            if ($opening === $existingOpen && $closing === $existingClose) {
                return true;
            }

            if ($opening < $existingClose && $closing > $existingOpen) {
                return true;
            }
        }

        return false;
    }
}
