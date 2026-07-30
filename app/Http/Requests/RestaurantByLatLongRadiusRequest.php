<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RestaurantByLatLongRadiusRequest extends FormRequest
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
            'per_page'            => ['numeric', 'min:1', 'max:1000'],
            'latitude'            => ['required', 'numeric'],
            'longitude'           => ['required', 'numeric'],
            'delivery_order_type' => ['required', 'numeric'],
            'cuisine_id'          => ['nullable', 'numeric'],
            'name'                => ['nullable', 'string']
        ];
    }
}
