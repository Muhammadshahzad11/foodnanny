<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserChangePasswordRequest extends FormRequest
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
    public function rules()
    {
        return [
            'password'              => 'required|string|min:6',
            'password_confirmation' => 'required|string|min:6|same:password'
        ];
    }

    public function attributes(){
        return [
            "password"         => strtolower(trans('all.label.new_password')),
            "confirm_password" => strtolower(trans('all.label.confirm_new_password'))
        ];
    }
}
