<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use App\Traits\DefaultAccessModelTrait;
use Illuminate\Foundation\Http\FormRequest;

class MyRestaurantRequest extends FormRequest
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
            'name'         => ['required', 'string', 'max:190', Rule::unique("restaurants", "name")->ignore($this->restaurant())],
            'email'        => ['nullable', 'email', 'max:190'],
            'phone'        => ['nullable', 'string', 'max:20'],
            'latitude'     => ['nullable', 'max:190'],
            'longitude'    => ['nullable', 'max:190'],
            'city'         => ['required', 'string', 'max:190'],
            'state'        => ['required', 'string', 'max:190'],
            'zip_code'     => ['required', 'numeric', 'digits_between:0,190'],
            'address'      => ['required', 'string', 'max:500'],
            'cuisine_id[]' => ['nullable', 'numeric', 'max_digits:10']
        ];
    }
}
