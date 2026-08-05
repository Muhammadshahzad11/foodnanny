<?php

namespace App\Services;

use Exception;
use App\Models\Translation;
use App\Models\FrontendSetting;
use Illuminate\Support\Facades\Log;
use Dipokhalder\EnvEditor\EnvEditor;
use App\Http\Requests\FrontendRequest;
use App\Libraries\QueryExceptionLibrary;
use Dipokhalder\Settings\Facades\Settings;

class FrontendService
{
    public EnvEditor $envService;

    private array $translatableKeys = [
        'frontend_hero_section_title',
        'frontend_hero_section_sub_title',
        'frontend_app_section_title',
        'frontend_about_title',
        'frontend_benefit_title',
        'frontend_restaurant_section_title',
        'frontend_restaurant_section_sub_title',
        'frontend_delivery_section_title',
        'frontend_delivery_section_sub_title',
    ];

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
            return Settings::group('frontend')->all();
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }

    public function listTranslations(string $locale): array
    {
        $result = [];
        $settings = FrontendSetting::whereIn('key', $this->translatableKeys)
            ->with('translations')
            ->get()
            ->keyBy('key');

        foreach ($this->translatableKeys as $key) {
            $setting = $settings->get($key);
            if (!$setting) {
                $result[$key] = '';
                continue;
            }
            $translation = $setting->translations
                ->where('locale', $locale)
                ->where('key', 'value')
                ->first();
            $result[$key] = $translation ? $translation->value : '';
        }
        return $result;
    }

    public function saveTranslations(array $data, string $locale): void
    {
        $settings = FrontendSetting::whereIn('key', $this->translatableKeys)->get()->keyBy('key');

        foreach ($this->translatableKeys as $key) {
            $setting = $settings->get($key);
            if (!$setting) continue;

            Translation::updateOrCreate(
                [
                    'translatable_type' => FrontendSetting::class,
                    'translatable_id'   => $setting->id,
                    'locale'            => $locale,
                    'key'               => 'value',
                ],
                ['value' => $data[$key] ?? '']
            );
        }
    }

    /**
     * @throws Exception
     */
    public function update(FrontendRequest $request)
    {
        try {
            Settings::group('frontend')->set($request->validated());
            if ($request->frontend_hero_section_image) {
                $setting = FrontendSetting::where('key', 'frontend_hero_section_image')->first();
                $setting->clearMediaCollection('frontend-hero-section-image');
                $setting->addMediaFromRequest('frontend_hero_section_image')->toMediaCollection('frontend-hero-section-image');
            }
            if ($request->frontend_app_section_image) {
                $setting = FrontendSetting::where('key', 'frontend_app_section_image')->first();
                $setting->clearMediaCollection('frontend-app-section-image');
                $setting->addMediaFromRequest('frontend_app_section_image')->toMediaCollection('frontend-app-section-image');
            }
            if ($request->frontend_restaurant_section_image) {
                $setting = FrontendSetting::where('key', 'frontend_restaurant_section_image')->first();
                $setting->clearMediaCollection('frontend-restaurant-section-image');
                $setting->addMediaFromRequest('frontend_restaurant_section_image')->toMediaCollection('frontend-restaurant-section-image');
            }
            if ($request->frontend_delivery_section_image) {
                $setting = FrontendSetting::where('key', 'frontend_delivery_section_image')->first();
                $setting->clearMediaCollection('frontend-delivery-section-image');
                $setting->addMediaFromRequest('frontend_delivery_section_image')->toMediaCollection('frontend-delivery-section-image');
            }
            return $this->list();
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }
}
