<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class ItemAddonRequest extends FormRequest
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
            'addon_item_id'   => ['required', 'numeric', Rule::unique("item_addons", "addon_item_id")->whereNull('deleted_at')->ignore($this->route('itemAddon.id'))->where('item_id', $this->route('item.id'))],
            'addon_item_variation' => ['nullable', 'json']
        ];
    }

        /**
     * Get the validation messages that apply to the request.
     *
     * @return array
     */
    public function attributes(): array
    {
        return [
            'addon_item_id' => strtolower(trans('all.label.addon_item'))
        ];
    }
}