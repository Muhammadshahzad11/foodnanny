<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ThemeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'theme_logo'            => ['nullable', 'file', 'mimes:jpg,jpeg,png', 'max:2048'],
            'theme_favicon_logo'    => ['nullable', 'file', 'mimes:jpg,jpeg,png', 'max:2048'],
            'theme_footer_logo'     => ['nullable', 'file', 'mimes:jpg,jpeg,png', 'max:2048'],
            'theme_primary_color'   => ['nullable', 'string', 'regex:/^#([0-9a-fA-F]{6})$/'],
            'theme_secondary_color' => ['nullable', 'string', 'regex:/^#([0-9a-fA-F]{6})$/']
        ];
    }
}
