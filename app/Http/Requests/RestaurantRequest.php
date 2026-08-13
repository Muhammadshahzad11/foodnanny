<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RestaurantRequest extends FormRequest
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
            'name'              => ['required', 'string', 'max:190', Rule::unique("restaurants", "name")->ignore($this->route('restaurant.id'))],
            'email'             => ['nullable', 'email', 'max:190'],
            'country_code'      => ['nullable', 'string', 'max:20'],
            'phone'             => ['nullable', 'string', 'max:20'],
            'latitude'          => ['nullable', 'max:190'],
            'longitude'         => ['nullable', 'max:190'],
            'city'              => ['required', 'string', 'max:190'],
            'state'             => ['required', 'string', 'max:190'],
            'zip_code'          => ['required', 'numeric', 'digits_between:0,190'],
            'address'           => ['required', 'string', 'max:500'],
            'status'            => ['required', 'numeric', 'max:24'],
            'cuisine_id[]'      => ['nullable', 'numeric', 'max_digits:10'],
            'online_commission' => ['nullable', 'numeric', 'max:100'],
            'pos_commission'    => ['nullable', 'numeric', 'max:100'],
            'zone_id'           => ['nullable', 'integer', 'exists:zones,id'],
        ];
    }
}
