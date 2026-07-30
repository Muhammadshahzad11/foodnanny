<?php

namespace App\Http\Requests;


use App\Enums\Ask;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RestaurantOwnerSignupRequest extends FormRequest
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
            'name'             => ['required', 'string', 'max:255'],
            'email'            => ['required', 'string', 'email', 'max:255', Rule::unique("users", "email")->whereNull('deleted_at')->where('is_guest', Ask::NO)],
            'phone'            => ['required', 'string', Rule::unique("users", "phone")->whereNull('deleted_at')->where('country_code', request('country_code'))->where('phone', request('phone'))->where('is_guest', Ask::NO)],
            'country_code'     => ['required', 'string'],
            'password'         => ['required', 'string', 'min:6', 'required_with:confirm_password'],
            'confirm_password' => ['required', 'string', 'min:6', 'same:password'],
        ];
    }
}
