<?php

namespace App\Http\Requests;

use App\Enums\Ask;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ChangePasswordRequest extends FormRequest
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
            'old_password'     => Auth::user()->is_guest == Ask::NO ? 'required|string|min:6' : 'nullable|string|min:6',
            'password'         => 'required|string|min:6|required_with:confirm_password',
            'confirm_password' => 'required|string|min:6|same:password'
        ];
    }

    public function attributes(): array
    {
        return [
            'password'         => strtolower(trans('all.label.new_password')),
            'confirm_password' => strtolower(trans('all.label.confirm_new_password')),
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            if (Auth::user()->is_guest == Ask::NO) {
                if (!$this->checkOldPassword()) {
                    $validator->errors()->add('old_password', trans('all.message.old_password_not_match'));
                }
            }
        });
    }

    private function checkOldPassword(): bool
    {
        $old_password = request('old_password');
        return Hash::check($old_password, auth()->user()->password);
    }
}
