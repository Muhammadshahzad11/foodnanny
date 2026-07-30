<?php

namespace App\Http\Requests;

use App\Enums\Activity;
use App\Enums\OrderType;
use App\Models\Restaurant;
use App\Rules\ValidJsonOrder;
use Illuminate\Foundation\Http\FormRequest;

class OrderRequest extends FormRequest
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
            'restaurant_id'      => ['required', 'numeric'],
            'subtotal'           => ['required', 'numeric'],
            'discount'           => ['nullable', 'numeric'],
            'delivery_fee'       => (int)request('order_type') === OrderType::DELIVERY ? ['required', 'numeric'] : ['nullable'],
            'extra_delivery_fee' => ['nullable', 'numeric'],
            'total'              => ['required', 'numeric'],
            'tax'                => ['required', 'numeric'],
            'order_type'         => ['required', 'numeric'],
            'is_advance_order'   => ['required', 'numeric'],
            'address_id'         => (int)request('order_type') === OrderType::DELIVERY ? ['required', 'numeric'] : ['nullable'],
            'delivery_time'      => (int)request('order_type') === OrderType::DELIVERY ? ['required', 'string'] : ['nullable'],
            'coupon_id'          => ['nullable', 'numeric'],
            'payment_method'     => ['required', 'numeric'],
            'source'             => ['required', 'numeric'],
            'cutlery'            => ['required', 'numeric'],
            'service_fee'        => ['required', 'numeric'],
            'rider_tip'          => ['required', 'numeric'],
            'items'              => ['required', 'json', new ValidJsonOrder]
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            if (request('restaurant_id') > 0) {
                $restaurant = Restaurant::with('orderSetup')->where(['id' => $this->restaurant_id])->first();
                if (!blank($restaurant)) {
                    if (request('order_type') == OrderType::DELIVERY && $restaurant->orderSetup?->delivery == Activity::DISABLE) {
                        $validator->errors()->add('order_type', trans('all.message.order_type_disabled_you_try_another_management'));
                    } else if (request('order_type') == OrderType::TAKEAWAY && $restaurant->orderSetup?->takeaway == Activity::DISABLE) {
                        $validator->errors()->add('order_type', trans('all.message.order_type_disabled_you_try_another_management'));
                    } else if (blank(request('order_type'))) {
                        $validator->errors()->add('order_type', trans('all.message.order_type_disabled_you_try_another_management'));
                    }
                } else {
                    $validator->errors()->add('restaurant_id', trans('all.message.restaurant_not_found'));
                }
            } else {
                $validator->errors()->add('restaurant_id', trans('all.message.restaurant_not_found'));
            }
        });
    }


    public function attributes(): array
    {
        return [
            'address_id' => strtolower(trans('all.label.address')),
        ];
    }
}
