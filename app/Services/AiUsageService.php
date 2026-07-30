<?php

namespace App\Services;

use App\Libraries\QueryExceptionLibrary;
use App\Models\Restaurant;
use App\Traits\DefaultAccessModelTrait;
use Dipokhalder\Settings\Facades\Settings;
use App\Models\AiUsageLog;
use Exception;
use Illuminate\Support\Facades\Log;

class AiUsageService
{
    use DefaultAccessModelTrait;

    /**
     * @throws Exception
     */
    public function textUsage(Restaurant $restaurant): bool
    {
        try {
            $aiUsageLog = $this->colCheck($restaurant);
            if ($aiUsageLog->total_text_generated_count >= (int)Settings::group('site')->get('site_default_ai_data_generation_limit')) {
                return false;
            }
            $aiUsageLog->total_text_generated_count++;
            $aiUsageLog->save();
            return true;
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }

    /**
     * @throws Exception
     */
    public function checking(): bool
    {
        try {
            $restaurant = $this->restaurant();
            if($restaurant > 0) {
                if(Settings::group('site')->get('site_default_ai_agent') > 0) {
                    $restaurant = Restaurant::find($restaurant);
                    $aiUsageLog = $this->colCheck($restaurant);
                    if ($aiUsageLog->total_text_generated_count >= (int)Settings::group('site')->get('site_default_ai_data_generation_limit')) {
                        return false;
                    }
                    return true;
                }
                return false;
            }
            return false;
        } catch (Exception $exception) {
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }

    /**
     * @throws Exception
     */
    private function colCheck(Restaurant $restaurant)
    {
        try {
            $aiUsageLog = AiUsageLog::find($restaurant->id);
            if (!$aiUsageLog) {
                $aiUsageLog = AiUsageLog::create([
                    'restaurant_id'               => $restaurant->id,
                    'total_text_generated_count'  => 0,
                    'total_image_generated_count' => 0
                ]);
            }
            return $aiUsageLog;
        } catch (Exception $exception) {
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }
}
