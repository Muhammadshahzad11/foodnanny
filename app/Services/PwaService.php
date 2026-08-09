<?php

namespace App\Services;

use Exception;
use App\Models\Pwa;
use App\Enums\PwaCacheStrategy;
use App\Enums\PwaDisplayMode;
use App\Enums\PwaOrientation;
use App\Http\Requests\PwaRequest;
use Illuminate\Support\Facades\Log;
use Dipokhalder\EnvEditor\EnvEditor;
use App\Libraries\QueryExceptionLibrary;
use Dipokhalder\Settings\Facades\Settings;

class PwaService
{
    public EnvEditor $envService;

    public function __construct(EnvEditor $envEditor)
    {
        $this->envService = $envEditor;
    }

    /**
     * @throws Exception
     */
    public function list()
    {
        try {
            return $this->ensureRecord();
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }

    /**
     * @throws Exception
     */
    public function update(PwaRequest $request)
    {
        try {
            $pwa = $this->ensureRecord();

            $pwa->fill([
                'name' => $request->input('name', $pwa->name),
                'short_name' => $request->input('short_name', $pwa->short_name),
                'description' => $request->input('description', $pwa->description),
                'theme_color' => $request->input('theme_color', $pwa->theme_color),
                'background_color' => $request->input('background_color', $pwa->background_color),
                'orientation' => $request->input('orientation', $pwa->orientation),
                'display_mode' => $request->input('display_mode', $pwa->display_mode),
                'offline_mode' => $request->boolean('offline_mode', (bool) $pwa->offline_mode),
                'auto_update' => $request->boolean('auto_update', (bool) $pwa->auto_update),
                'cache_strategy' => $request->input('cache_strategy', $pwa->cache_strategy),
                'enable_install_popup' => $request->boolean('enable_install_popup', (bool) $pwa->enable_install_popup),
                'popup_delay_seconds' => (int) $request->input('popup_delay_seconds', $pwa->popup_delay_seconds),
                'popup_frequency_hours' => (int) $request->input('popup_frequency_hours', $pwa->popup_frequency_hours),
            ]);
            // Bump so browsers/manifest pick up changes without Force Update
            $pwa->cache_version = ((int) $pwa->cache_version) + 1;
            $pwa->save();

            if ($request->hasFile('pwa_splash')) {
                $pwa->clearMediaCollection('pwa_splash');
                $pwa->addMedia($request->file('pwa_splash'))->toMediaCollection('pwa_splash');
            }
            if ($request->hasFile('pwa_icon')) {
                $pwa->clearMediaCollection('pwa_icon');
                $pwa->addMedia($request->file('pwa_icon'))->toMediaCollection('pwa_icon');
            }

            // Do NOT rewrite APP_NAME here — EnvEditor can corrupt .env and blank the admin app.
            $this->syncEnvIcons($pwa);
            $this->syncEnvSplashes($pwa);

            return $pwa->fresh();
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }

    /**
     * @throws Exception
     */
    public function forceUpdate(): Pwa
    {
        try {
            $pwa = $this->ensureRecord();
            $pwa->cache_version = ((int) $pwa->cache_version) + 1;
            $pwa->force_updated_at = now();
            $pwa->save();

            return $pwa->fresh();
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }

    private function ensureRecord(): Pwa
    {
        $pwa = Pwa::query()->first();
        if ($pwa) {
            return $pwa;
        }

        $company = Settings::group('company')->get('company_name') ?: config('app.name', 'Cost to Cost Foods');

        return Pwa::query()->create([
            'id' => 1,
            'name' => $company,
            'short_name' => mb_substr($company, 0, 12),
            'description' => 'Order food, manage tables, kitchen, and POS from ' . $company,
            'theme_color' => '#148A3C',
            'background_color' => '#FFFFFF',
            'orientation' => PwaOrientation::ANY,
            'display_mode' => PwaDisplayMode::STANDALONE,
            'offline_mode' => true,
            'auto_update' => true,
            'cache_strategy' => PwaCacheStrategy::BALANCED,
            'enable_install_popup' => true,
            'popup_delay_seconds' => 3,
            'popup_frequency_hours' => 24,
            'cache_version' => 1,
        ]);
    }

    private function syncEnvIcons(Pwa $pwa): void
    {
        if (empty($pwa->getFirstMediaUrl('pwa_icon'))) {
            return;
        }

        try {
            $icons = $pwa->getMedia('pwa_icon')->first();
            if (!$icons) {
                return;
            }
            foreach (['72x72', '96x96', '128x128', '144x144', '152x152', '192x192', '384x384', '512x512'] as $size) {
                $key = 'D_' . $size;
                $url = $icons->getUrl($key);
                if ($url) {
                    // Prefer relative path to avoid host/port lock-in
                    $parts = parse_url($url);
                    $path = $parts['path'] ?? $url;
                    $this->envService->addData([$key => $path]);
                }
            }
        } catch (\Throwable $e) {
            Log::info('PWA syncEnvIcons: ' . $e->getMessage());
        }
    }

    private function syncEnvSplashes(Pwa $pwa): void
    {
        if (empty($pwa->getFirstMediaUrl('pwa_splash'))) {
            return;
        }

        try {
            $splash = $pwa->getMedia('pwa_splash')->first();
            if (!$splash) {
                return;
            }
            foreach ([
                '640x1136', '750x1334', '828x1792', '1125x2436', '1242x2208',
                '1242x2688', '1536x2048', '1668x2224', '1668x2388', '2048x2732',
            ] as $size) {
                $key = 'D_' . $size;
                $url = $splash->getUrl($key);
                if ($url) {
                    $parts = parse_url($url);
                    $path = $parts['path'] ?? $url;
                    $this->envService->addData([$key => $path]);
                }
            }
        } catch (\Throwable $e) {
            Log::info('PWA syncEnvSplashes: ' . $e->getMessage());
        }
    }
}
