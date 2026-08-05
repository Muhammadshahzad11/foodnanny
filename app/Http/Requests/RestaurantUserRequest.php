<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RestaurantUserRequest extends FormRequest
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
            'name'             => ['required', 'string', 'max:190'],
            'email'            => ['required', 'email', 'max:190', Rule::unique("users", "email")->ignore($this->route('restaurant.user_id'))],
            'phone'            => ['required', 'string', 'max:20', Rule::unique("users", "phone")->ignore($this->route('restaurant.user_id'))],
            'country_code'     => ['required', 'string', 'max:20'],
            'password'         => [$this->route('restaurant.user_id') ? 'nullable' : 'required', 'string', 'min:6'],
            'confirm_password' => [$this->route('restaurant.user_id') ? 'nullable' : 'required', 'string', 'min:6', 'same:password']
        ];
    }

    public function attributes():array
    {
        return [
            "password"         => strtolower(trans('all.label.new_password')),
            "confirm_password" => strtolower(trans('all.label.confirm_new_password'))
        ];
    }
}
