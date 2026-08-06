<?php

namespace App\Services;

use Exception;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class CacheService
{
    /**
     * Flush application caches safely (avoid optimize:clear).
     *
     * @throws Exception
     */
    public function flush(): array
    {
        $cleared = [];

        try {
            Cache::flush();
            $cleared[] = 'application';
        } catch (Exception $exception) {
            Log::warning('Cache flush failed: ' . $exception->getMessage());
        }

        $commands = [
            'cache:clear' => 'data',
            'view:clear' => 'views',
            'route:clear' => 'routes',
            'config:clear' => 'config',
            'event:clear' => 'events',
        ];

        foreach ($commands as $command => $label) {
            try {
                Artisan::call($command);
                $cleared[] = $label;
            } catch (Exception $exception) {
                Log::warning("Artisan {$command} failed: " . $exception->getMessage());
            }
        }

        if (empty($cleared)) {
            throw new Exception(trans('all.message.cache_clear_failed'));
        }

        return [
            'cleared' => array_values(array_unique($cleared)),
            'message' => trans('all.message.cache_cleared_successfully'),
        ];
    }
}
