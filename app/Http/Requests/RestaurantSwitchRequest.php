<?php

namespace App\Http\Requests;


use App\Traits\DefaultAccessModelTrait;
use Illuminate\Foundation\Http\FormRequest;

class RestaurantSwitchRequest extends FormRequest
{
    use DefaultAccessModelTrait;

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
            'restaurant_id' => ['required', 'numeric']
        ];
    }

    /**
     * Get the custom messages for the validation rules.
     *
     * @return array
     */

    public function attributes(): array
    {
        return[
            'restaurant_id' =>  strtolower(trans('all.label.restaurant'))
        ];
    }
}
