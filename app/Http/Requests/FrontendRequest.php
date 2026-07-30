<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FrontendRequest extends FormRequest
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
            'frontend_hero_section_title'           => ['required', 'string', 'max:2048'],
            'frontend_hero_section_sub_title'       => ['required', 'string', 'max:2048'],
            'frontend_app_section_title'            => ['required', 'string', 'max:2048'],
            'frontend_app_section_android_app_link' => ['required', 'url', 'max:2048'],
            'frontend_app_section_iso_app_link'     => ['required', 'url', 'max:2048'],
            'frontend_about_title'                  => ['required', 'string', 'max:2048'],
            'frontend_benefit_title'                => ['required', 'string', 'max:2048'],
            'frontend_restaurant_section_title'     => ['required', 'string', 'max:2048'],
            'frontend_restaurant_section_sub_title' => ['required', 'string', 'max:2048'],
            'frontend_delivery_section_title'       => ['required', 'string', 'max:2048'],
            'frontend_delivery_section_sub_title'   => ['required', 'string', 'max:2048'],
            'frontend_header_section_image'         => ['nullable', 'file', 'mimes:jpg,jpeg,png'],
            'frontend_app_section_image'            => ['nullable', 'file', 'mimes:jpg,jpeg,png'],
            'frontend_restaurant_section_image'     => ['nullable', 'file', 'mimes:jpg,jpeg,png'],
            'frontend_delivery_section_image'       => ['nullable', 'file', 'mimes:jpg,jpeg,png']
        ];
    }


    public function attributes(): array
    {
        return [
            'frontend_about_title'              => strtolower(trans('all.label.frontend_about_section_title')),
            'frontend_benefit_title'            => strtolower(trans('all.label.frontend_benefit_section_title')),
            'frontend_app_section_iso_app_link' => strtolower(trans('all.label.frontend_app_section_iso_app_link')),
        ];
    }
}
