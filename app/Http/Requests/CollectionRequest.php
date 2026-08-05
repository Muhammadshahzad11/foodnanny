<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class CollectionRequest extends FormRequest
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
            'source_user_id' => ['required', 'numeric'],
            'date'           => ['required', 'string'],
            'amount'         => ['required', 'numeric', 'not_in:0']
        ];
    }

    public function attributes(): array
    {
        return [
            'source_user_id' => strtolower(trans('all.label.user'))
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            if ($this->input('source_user_id') == Auth::user()->id) {
                $validator->errors()->add('source_user_id', trans('all.message.source_destination_user_same'));
            }
        });
    }
}
