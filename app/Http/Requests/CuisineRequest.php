<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CuisineRequest extends FormRequest
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
            'name'        => ['required', 'string', 'max:190', Rule::unique("cuisines", "name")->ignore($this->route('cuisine.id'))],
            'description' => ['nullable', 'string', 'max:5000'],
            'status'      => ['required', 'numeric', 'max:24'],
            'image'       => $this->route('cuisine.id') ? ['nullable', 'file', 'mimes:jpg,jpeg,png,webp', 'max:2048'] : ['required', 'file', 'mimes:jpg,jpeg,png,webp', 'max:2048']
        ];
    }
}
