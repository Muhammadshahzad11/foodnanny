<?php

namespace App\Http\Requests;

use App\Enums\PwaCacheStrategy;
use App\Enums\PwaDisplayMode;
use App\Enums\PwaOrientation;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PwaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $hasMediaUpload = $this->hasFile('pwa_icon') || $this->hasFile('pwa_splash');

        return [
            'name' => ['nullable', 'string', 'max:120'],
            'short_name' => ['nullable', 'string', 'max:32'],
            'description' => ['nullable', 'string', 'max:500'],
            'theme_color' => ['nullable', 'string', 'max:32'],
            'background_color' => ['nullable', 'string', 'max:32'],
            'orientation' => ['nullable', 'string', Rule::in([
                PwaOrientation::ANY,
                PwaOrientation::NATURAL,
                PwaOrientation::PORTRAIT,
                PwaOrientation::LANDSCAPE,
            ])],
            'display_mode' => ['nullable', 'string', Rule::in([
                PwaDisplayMode::STANDALONE,
                PwaDisplayMode::FULLSCREEN,
                PwaDisplayMode::MINIMAL_UI,
                PwaDisplayMode::BROWSER,
            ])],
            'offline_mode' => ['nullable', 'boolean'],
            'auto_update' => ['nullable', 'boolean'],
            'cache_strategy' => ['nullable', 'string', Rule::in([
                PwaCacheStrategy::AGGRESSIVE,
                PwaCacheStrategy::BALANCED,
                PwaCacheStrategy::NETWORK_FIRST,
            ])],
            'enable_install_popup' => ['nullable', 'boolean'],
            'popup_delay_seconds' => ['nullable', 'integer', 'min:0', 'max:120'],
            'popup_frequency_hours' => ['nullable', 'integer', 'min:1', 'max:720'],
            'pwa_splash' => [
                $hasMediaUpload && $this->hasFile('pwa_splash') ? 'required' : 'nullable',
                'image',
                'mimes:jpg,jpeg,png',
                'max:5048',
                'dimensions:min_width=2048,min_height=2732',
            ],
            'pwa_icon' => [
                $hasMediaUpload && $this->hasFile('pwa_icon') ? 'required' : 'nullable',
                'image',
                'mimes:jpg,jpeg,png',
                'max:4048',
                'dimensions:min_width=512,min_height=512',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'pwa_splash.dimensions' => trans('all.lebel.invalid_image_dimensions'),
            'pwa_icon.dimensions' => trans('all.lebel.invalid_image_dimensions'),
        ];
    }

    protected function prepareForValidation(): void
    {
        foreach (['offline_mode', 'auto_update', 'enable_install_popup'] as $boolField) {
            if ($this->has($boolField)) {
                $this->merge([
                    $boolField => filter_var($this->input($boolField), FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE),
                ]);
            }
        }
    }
}
