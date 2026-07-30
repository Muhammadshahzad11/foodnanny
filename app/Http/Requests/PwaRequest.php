<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PwaRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize() : bool
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
            'pwa_splash' => ['required', 'image', 'mimes:jpg,jpeg,png', 'max:5048', 'dimensions:min_width=2048,min_height=2732'],
            'pwa_icon'   => ['required', 'image', 'mimes:jpg,jpeg,png', 'max:4048', 'dimensions:min_width=512,min_height=512']
        ];
    }
    public function messages()
    {
        return [
            'pwa_splash.dimensions' => trans('all.lebel.invalid_image_dimensions'),
            'pwa_icon.dimensions'   => trans('all.lebel.invalid_image_dimensions'),
        ];
    }
}
