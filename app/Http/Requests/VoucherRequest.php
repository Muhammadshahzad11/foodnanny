<?php

namespace App\Http\Requests;

use App\Enums\Discount;
use App\Enums\DiscountType;
use Illuminate\Validation\Rule;
use App\Traits\DefaultAccessModelTrait;
use Illuminate\Foundation\Http\FormRequest;

class VoucherRequest extends FormRequest
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
            'name'             => ['required', 'string', 'max:190', Rule::unique("coupons", "name")->ignore($this->route('voucher.id'))],
            'description'      => ['nullable', 'string', 'max:900'],
            'code'             => ['required', 'string', 'max:24', Rule::unique("coupons", "code")->ignore($this->route('voucher.id'))],
            'discount'         => request('type') === Discount::DEFAULT ? ['required', 'numeric'] : ['nullable', 'numeric'],
            'discount_type'    => request('type') === Discount::DEFAULT ? ['required', 'numeric', 'max:24'] : ['nullable', 'numeric', 'max:24'],
            'start_date'       => ['required', 'string'],
            'end_date'         => ['required', 'string'],
            'minimum_order'    => ['required', 'numeric'],
            'maximum_discount' => request('type') === Discount::DEFAULT && request('discount_type') === DiscountType::PERCENTAGE ? ['required', 'numeric'] : ['nullable', 'numeric'],
            'limit_per_user'   => ['nullable', 'numeric'],
            'type'             => ['required', 'numeric']
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            if ($this->isPercentage() && request('discount') > 99) {
                $validator->errors()->add('discount', trans('all.message.percentage_amount'));
            }

            if (request('discount_type') == DiscountType::FIXED && request('discount') > request('minimum_order')) {
                $validator->errors()->add('discount', trans('all.message.fixed_amount'));
            }

            if ($this->isNotNull(request('start_date')) && strtotime(request('end_date')) < strtotime(request('start_date'))) {
                $validator->errors()->add('end_date', trans('all.message.start_date'));
            }

            if ($this->isNotNull(request('start_date')) && $this->checkToDate()) {
                $validator->errors()->add('end_date', trans('all.message.end_date'));
            }
        });
    }

    private function isPercentage(): bool
    {
        return request('discount_type') == DiscountType::PERCENTAGE;
    }

    public function checkToDate()
    {
        $today = strtotime(date('Y-m-d H:i:s'));
        if (strtotime(request('end_date')) < $today) {
            return true;
        }
    }

    private function isNotNull($value): bool
    {
        if ($value === 'null') {
            return false;
        }
        return true;
    }
}
