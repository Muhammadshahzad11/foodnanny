<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use App\Traits\DefaultAccessModelTrait;
use Illuminate\Foundation\Http\FormRequest;

class ItemAttributeRequest extends FormRequest
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
            'name'   => ['required', 'string', 'max:190', Rule::unique("item_attributes", "name")->ignore($this->route('itemAttribute.id'))->where('restaurant_id', $this->restaurant())],
            'status' => ['required', 'numeric', 'max:24']
        ];
    }
}
