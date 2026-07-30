<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AboutStepsRequest extends FormRequest
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
            'title'       => ['required', 'string', 'max:190', Rule::unique("about_steps", "title")->ignore($this->route('aboutStep.id'))],
            'description' => ['required', 'string', 'max:900'],
            'status'      => ['required', 'numeric', 'max:24'],
            'image'       => $this->route('aboutStep.id') ? ['nullable', 'file', 'mimes:jpg,jpeg,png,webp', 'max:2048'] : ['required', 'file', 'mimes:jpg,jpeg,png,webp', 'max:2048']
        ];
    }
}
