<?php

namespace App\Http\Controllers\Admin;

use Exception;
use App\Models\TimeSlot;
use App\Services\TimeSlotService;
use App\Http\Requests\PaginateRequest;
use App\Http\Requests\TimeSlotRequest;
use App\Http\Resources\TimeSlotResource;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Routing\Controllers\HasMiddleware;

class TimeSlotController extends AdminController implements HasMiddleware
{
    public TimeSlotService $timeSlotService;

    public function __construct(TimeSlotService $timeSlotService)
    {
        parent::__construct();
        $this->timeSlotService = $timeSlotService;
    }

    public static function middleware(): array
    {
        return [
            new Middleware('permission:restaurant-settings', only: ['index', 'store', 'destroy', 'generateDefaults'])
        ];
    }

    public function index(PaginateRequest $request): \Illuminate\Foundation\Application|\Illuminate\Http\Response|\Illuminate\Http\Resources\Json\AnonymousResourceCollection|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return TimeSlotResource::collection($this->timeSlotService->list($request));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function store(TimeSlotRequest $request): \Illuminate\Foundation\Application|\Illuminate\Http\Response|\Illuminate\Contracts\Foundation\Application|TimeSlotResource|\Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return new TimeSlotResource($this->timeSlotService->store($request));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function generateDefaults(): \Illuminate\Foundation\Application|\Illuminate\Http\Response|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            $created = $this->timeSlotService->ensureDefaults();

            return response([
                'status'  => true,
                'created' => $created,
                'message' => $created > 0
                    ? trans('all.message.default_time_slots_generated')
                    : trans('all.message.time_slots_already_exist'),
            ]);
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function destroy(TimeSlot $timeSlot): \Illuminate\Foundation\Application|\Illuminate\Http\Response|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            $this->timeSlotService->destroy($timeSlot);
            return response('', 202);
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }
}
