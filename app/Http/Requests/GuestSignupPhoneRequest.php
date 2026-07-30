<?php

namespace App\Http\Requests;


use App\Enums\Ask;
use App\Rules\ValidPhone;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class GuestSignupPhoneRequest extends FormRequest
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
            'phone' => ['required', 'string', 'max:190'],
            'code'  => ['required', 'string'],
        ];
    }
}
