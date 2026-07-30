<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class OfferRestaurantRequest extends FormRequest
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
            'restaurant_id' => ['required', 'numeric', Rule::unique("offer_restaurants", "restaurant_id")->ignore($this->route('offerRestaurant.id'))->where('offer_id', $this->route('offer.id'))],
        ];
    }
    public function  attributes(): array
    {
        return [
            'restaurant_id' => strtolower(trans('all.label.restaurant'))
        ];
    }
}
