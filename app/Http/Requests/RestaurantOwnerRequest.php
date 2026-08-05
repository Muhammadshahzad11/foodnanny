<?php

namespace App\Http\Requests;

use App\Models\Restaurant;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RestaurantOwnerRequest extends FormRequest
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
            'name'                  => ['required', 'string', 'max:190'],
            'email'                 => ['required', 'email', 'max:190', Rule::unique("users", "email")->ignore($this->route('restaurantOwner.id'))],
            'password'              => [$this->route('restaurantOwner.id') ? 'nullable' : 'required', 'string', 'min:6'],
            'username'              => ['nullable', 'max:190', Rule::unique("users", "username")->ignore($this->route('restaurantOwner.id'))],
            'device_token'          => ['nullable', 'string'],
            'web_token'             => ['nullable', 'string'],
            'password_confirmation' => [$this->route('restaurantOwner.id') ? 'nullable' : 'required', 'string', 'min:6', 'same:password'],
            'phone'                 => ['nullable', 'string', 'max:20', Rule::unique("users", "phone")->ignore($this->route('restaurantOwner.id'))],
            'restaurant_id'         => [$this->route('restaurantOwner.id') ? 'nullable' : 'required', 'numeric'],
            'status'                => ['required', 'numeric', 'max:24'],
            'country_code'          => ['required', 'string', 'max:20']
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            if (request('restaurant_id') !== null) {
                $checkUser = Restaurant::findOrfail(request('restaurant_id'));
                if ($checkUser->user_id && $this->route('restaurantOwner.id') !== $checkUser->user_id) {
                    $validator->errors()->add('restaurant_id', trans('all.message.restaurant_owner_already_exist'));
                }
            }
        });
    }

    public function attributes(): array
    {
        return [
            'restaurant_id' => strtolower(trans('all.label.restaurant')),
        ];
    }
}
