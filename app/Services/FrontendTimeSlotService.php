<?php

namespace App\Services;


use Exception;
use Carbon\Carbon;
use App\Models\Restaurant;
use App\Libraries\AppLibrary;
use App\Models\FrontendTimeSlot;
use App\Models\FrontendOrderSetup;
use Illuminate\Support\Facades\Log;
use App\Libraries\QueryExceptionLibrary;

class FrontendTimeSlotService
{

    /**
     * @throws Exception
     */
    public function todayTimeSlot(Restaurant $restaurant): \Vanilla\Support\Collection|\IlluminateAgnostic\Str\Support\Collection|\IlluminateAgnostic\Collection\Support\Collection|\IlluminateAgnostic\StrAgnostic\Str\Support\Collection|\IlluminateAgnostic\ArrAgnostic\Arr\Support\Collection|\Illuminate\Support\Collection|\IlluminateAgnostic\Arr\Support\Collection
    {
        try {
            $j                   = 0;
            $times               = [];
            $today               = Carbon::now()->dayOfWeek;
            $defaultScheduleTime = 30;
            $todayTimes          = FrontendTimeSlot::select('opening_time', 'closing_time', 'restaurant_id')->where(['restaurant_id' => $restaurant->id, 'day' => $today])->orderBy('opening_time', 'asc')->get()->toArray();
            $orderSetup          = FrontendOrderSetup::where(['restaurant_id' => $restaurant->id])->first();

            if (!blank($orderSetup)) {
                $defaultScheduleTime = (int)$orderSetup->schedule_order_slot_duration ?? 15;
            }

            foreach ($todayTimes as $time) {
                $nowArray = $this->nowTimeSlotCalculation($time, $defaultScheduleTime, $restaurant->id);
                if (count($nowArray) > 0) {
                    $times[$j] = (object)$nowArray;
                    $j++;
                }
            }

            foreach ($todayTimes as $time) {
                $arrays = $this->todayTimeSlotCalculation(
                    $defaultScheduleTime,
                    $time['opening_time'],
                    $time['closing_time'],
                    $time['restaurant_id']
                );
                if (count($arrays)) {
                    foreach ($arrays as $array) {
                        $times[$j] = (object)$array;
                        $j++;
                    }
                }
            }
            return collect($times);
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }

    /**
     * @throws Exception
     */
    public function tomorrowTimeSlot(Restaurant $restaurant): \Vanilla\Support\Collection|\IlluminateAgnostic\Str\Support\Collection|\IlluminateAgnostic\StrAgnostic\Str\Support\Collection|\IlluminateAgnostic\Collection\Support\Collection|\IlluminateAgnostic\ArrAgnostic\Arr\Support\Collection|\Illuminate\Support\Collection|\IlluminateAgnostic\Arr\Support\Collection
    {
        try {
            $tomorrow            = Carbon::tomorrow()->dayOfWeek;
            $defaultScheduleTime = 30;
            $tomorrowTimes       = FrontendTimeSlot::select('opening_time', 'closing_time', 'restaurant_id')->where(['restaurant_id' => $restaurant->id, 'day' => $tomorrow])->orderBy('id', 'asc')->get()->toArray();
            $orderSetup          = FrontendOrderSetup::where(['restaurant_id' => $restaurant->id])->first();

            if (!blank($orderSetup)) {
                $defaultScheduleTime = (int)$orderSetup->schedule_order_slot_duration;
            }

            $tomorrowSlots = [];
            foreach ($tomorrowTimes as $time) {
                $arrays = $this->tomorrowTimeSlotCalculation(
                    $defaultScheduleTime,
                    $time['opening_time'],
                    $time['closing_time'],
                    $time['restaurant_id']
                );

                if (count($arrays)) {
                    foreach ($arrays as $array) {
                        $tomorrowSlots[] = (object)$array;
                    }
                }
            }
            return collect($tomorrowSlots);
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }

    public function todayTimeSlotCalculation($interval, $startTime, $endTime, $restaurantId): array
    {
        $i              = 0;
        $time           = [];
        $strCurrentTime = strtotime(date('H:i'));
        $strStartTime   = strtotime($startTime);
        $strEndTime     = strtotime($endTime);

        while ($strStartTime < $strEndTime) {
            $convertStartTime = date('H:i', $strStartTime);
            $convertEndTime   = date('H:i', strtotime('+' . $interval . ' minutes', $strStartTime));

            if ($strStartTime >= $strCurrentTime) {
                $time[$i]['label']         = AppLibrary::deliveryTime($convertStartTime . ' - ' . $convertEndTime);
                $time[$i]['from_time']     = $convertStartTime;
                $time[$i]['to_time']       = $convertEndTime;
                $time[$i]['time']          = $convertStartTime . ' - ' . $convertEndTime;
                $time[$i]['restaurant_id'] = $restaurantId;
                $i++;
            }
            $strStartTime = strtotime('+' . $interval . ' minutes', $strStartTime);
        }
        return $time;
    }

    public function tomorrowTimeSlotCalculation($interval, $startTime, $endTime, $restaurantId): array
    {
        $i            = 0;
        $time         = [];
        $strStartTime = strtotime($startTime);
        $strEndTime   = strtotime($endTime);

        while ($strStartTime < $strEndTime) {
            $convertStartTime = date('H:i', $strStartTime);
            $convertEndTime   = date('H:i', strtotime('+' . $interval . ' minutes', $strStartTime));

            if ($strStartTime <= strtotime($endTime)) {
                $time[$i]['label']         = AppLibrary::deliveryTime($convertStartTime . ' - ' . $convertEndTime);
                $time[$i]['from_time']     = $convertStartTime;
                $time[$i]['to_time']       = $convertEndTime;
                $time[$i]['time']          = $convertStartTime . ' - ' . $convertEndTime;
                $time[$i]['restaurant_id'] = $restaurantId;
                $i++;
            }
            $strStartTime = strtotime('+' . $interval . ' minutes', $strStartTime);
        }
        return $time;
    }

    public function nowTimeSlotCalculation($timeSlot, $interval, $restaurantId): array
    {
        $time           = [];
        $strStartTime   = strtotime($timeSlot['opening_time']);
        $strEndTime     = strtotime($timeSlot['closing_time']);
        $strCurrentTime = strtotime(date('H:i'));

        if ($strCurrentTime >= $strStartTime && $strCurrentTime <= $strEndTime) {
            $convertStartTime = date('H:i');
            $convertEndTime   = date('H:i', strtotime('+' . $interval . ' minutes', strtotime($convertStartTime)));

            $time['label']         = 'now';
            $time['from_time']     = $convertStartTime;
            $time['to_time']       = $convertEndTime;
            $time['time']          = $convertStartTime . ' - ' . $convertEndTime;
            $time['restaurant_id'] = $restaurantId;
        }
        return $time;
    }
}
