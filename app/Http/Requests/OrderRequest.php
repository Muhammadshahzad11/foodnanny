<?php

namespace App\Http\Requests;

use App\Enums\Activity;
use App\Enums\OrderType;
use App\Models\Restaurant;
use App\Rules\ValidJsonOrder;
use App\Services\DineInOrderGuard;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class OrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $orderType = (int) $this->input('order_type');
        $isDelivery = $orderType === OrderType::DELIVERY;
        $isDining   = $orderType === OrderType::DINING_TABLE;

        return [
            'restaurant_id'      => ['required', 'numeric'],
            'subtotal'           => ['required', 'numeric'],
            'discount'           => ['nullable', 'numeric'],
            'delivery_fee'       => $isDelivery ? ['required', 'numeric'] : ['nullable'],
            'extra_delivery_fee' => ['nullable', 'numeric'],
            'total'              => ['required', 'numeric'],
            'tax'                => ['required', 'numeric'],
            'order_type'         => [
                'required',
                'numeric',
                Rule::in([OrderType::DELIVERY, OrderType::TAKEAWAY, OrderType::DINING_TABLE]),
            ],
            'is_advance_order'   => ['required', 'numeric'],
            'address_id'         => $isDelivery ? ['required', 'numeric'] : ['nullable'],
            'delivery_time'      => $isDelivery ? ['required', 'string'] : ['nullable'],
            'coupon_id'          => ['nullable', 'numeric'],
            'payment_method'     => ['required', 'numeric'],
            'source'             => ['required', 'numeric'],
            'cutlery'            => ['required', 'numeric'],
            'service_fee'        => ['required', 'numeric'],
            'rider_tip'          => ['required', 'numeric'],
            'table_id'           => $isDining ? ['required', 'integer', 'exists:restaurant_tables,id'] : ['nullable', 'integer'],
            'qr_token'           => $isDining ? ['required', 'string', 'size:64'] : ['nullable', 'string'],
            'items'              => ['required', 'json', new ValidJsonOrder],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            if ((int) $this->input('restaurant_id') <= 0) {
                $validator->errors()->add('restaurant_id', trans('all.message.restaurant_not_found'));

                return;
            }

            $restaurant = Restaurant::with('orderSetup')->where(['id' => $this->restaurant_id])->first();
            if (blank($restaurant)) {
                $validator->errors()->add('restaurant_id', trans('all.message.restaurant_not_found'));

                return;
            }

            $orderType = (int) $this->input('order_type');

            if ($orderType === OrderType::DELIVERY && $restaurant->orderSetup?->delivery == Activity::DISABLE) {
                $validator->errors()->add('order_type', trans('all.message.order_type_disabled_you_try_another_management'));
            } elseif ($orderType === OrderType::TAKEAWAY && $restaurant->orderSetup?->takeaway == Activity::DISABLE) {
                $validator->errors()->add('order_type', trans('all.message.order_type_disabled_you_try_another_management'));
            } elseif ($orderType === OrderType::DINING_TABLE) {
                try {
                    app(DineInOrderGuard::class)->assertOrderableTable(
                        (int) $this->input('restaurant_id'),
                        (int) $this->input('table_id'),
                        $this->input('qr_token')
                    );
                } catch (\Exception $exception) {
                    $validator->errors()->add('table_id', $exception->getMessage());
                }
            } elseif (blank($this->input('order_type'))) {
                $validator->errors()->add('order_type', trans('all.message.order_type_disabled_you_try_another_management'));
            }
        });
    }

    public function attributes(): array
    {
        return [
            'address_id' => strtolower(trans('all.label.address')),
            'table_id'   => strtolower(trans('all.label.table')),
        ];
    }
}
