<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CashoutRequest extends FormRequest
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
            'user_id'        => ['required', 'numeric'],
            'amount'         => ['required', 'numeric', 'min:1'],
            'date'           => ['required', 'string'],
            'transaction_id' => ['nullable', 'string'],
            'remarks'        => ['nullable', 'string'],
            'file'           => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf,doc,docx,xls,xlsx,csv', 'max:2048']
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */

    public function attributes(): array
    {
        return [
            'user_id' =>  strtolower(trans('all.label.user'))
        ];
    }
}
