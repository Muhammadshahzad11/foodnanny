<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Traits\DefaultAccessModelTrait;

class ItemCategoryRequest extends FormRequest
{
    use DefaultAccessModelTrait;
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
            'name'        => ['required', 'string', 'max:190', Rule::unique("item_categories", "name")->ignore($this->route('itemCategory.id'))->where('restaurant_id', $this->restaurant())],
            'description' => ['nullable', 'string', 'max:900'],
            'status'      => ['required', 'numeric', 'max:24'],
            'image'       => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:2048']
        ];
    }
}
