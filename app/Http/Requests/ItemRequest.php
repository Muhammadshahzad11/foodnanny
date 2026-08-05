<?php

namespace App\Http\Requests;

use App\Rules\IniAmount;
use App\Enums\DiscountType;
use Illuminate\Validation\Rule;
use App\Traits\DefaultAccessModelTrait;
use Illuminate\Foundation\Http\FormRequest;

class ItemRequest extends FormRequest
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
            'name'                      => ['required', 'string', 'max:190', Rule::unique("items", "name")->whereNull('deleted_at')->ignore($this->route('item.id'))->where('restaurant_id', $this->restaurant())],
            'item_category_id'          => ['required', 'numeric', 'not_in:0'],
            'tax_id'                    => ['nullable', 'numeric', 'not_in:0'],
            'item_type'                 => ['nullable', 'numeric', 'not_in:0'],
            'price'                     => ['required', new IniAmount()],
            'description'               => ['nullable', 'string', 'max:5000'],
            'caution'                   => ['nullable', 'string', 'max:5000'],
            'status'                    => ['required', 'numeric', 'max:24'],
            'order'                     => ['required', 'numeric'],
            'is_halal'                  => ['required', 'numeric', 'max:24'],
            'available_time_start'      => ['nullable', 'string'],
            'available_time_end'        => ['nullable', 'string'],
            'discount_type'             => ['nullable', 'numeric', 'max:24'],
            'discount'                  => request('discount_type') > 0 ? ['required', 'numeric'] : ['nullable', 'numeric'],
            'maximum_purchase_quantity' => ['required', 'numeric', 'max:9999999999'],
            'image'                     => ['nullable', 'file', 'mimes:jpg,jpeg,png', 'max:2048']
        ];
    }

    public function attributes(): array
    {
        return [
            'item_category_id' => strtolower(trans('all.label.item_category_id')),
            'tax_id'           => strtolower(trans('all.label.tax_id'))
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {

            if (request('discount_type') == DiscountType::PERCENTAGE && request('discount') > 100) {
                $validator->errors()->add('discount', trans('all.message.percentage_amount_can_not_greater_than_100'));
            }

            if (request('discount_type') == DiscountType::FIXED && $this->isNotNull(request('discount')) && request('discount') > request('price')) {
                $validator->errors()->add('discount', trans('all.message.fixed_amount_can_not_greater_than_item_price'));
            }

            if ($this->isNotNull(request('available_time_start')) && strtotime(request('available_time_end')) < strtotime(request('available_time_start'))) {
                $validator->errors()->add('available_time_end', trans('all.message.available_time_end_can_not_older_than_available_time_start'));
            }
        });
    }

    private function isNotNull($value): bool
    {
        if ($value === 'null') {
            return false;
        }
        return true;
    }
}
