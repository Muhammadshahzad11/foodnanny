<?php

namespace App\Http\Requests;

use App\Enums\DeliveryChargeType;
use App\Enums\Status;
use App\Traits\DefaultAccessModelTrait;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RestaurantDeliveryZoneRequest extends FormRequest
{
    use DefaultAccessModelTrait;

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'restaurant_id'              => ['required', 'integer', 'exists:restaurants,id'],
            'name'                       => ['required', 'string', 'max:190'],
            'display_name'               => ['required', 'string', 'max:190'],
            'polygon'                    => ['required', 'array', 'min:3'],
            'polygon.*.lat'              => ['required_without:polygon.*.latitude', 'nullable', 'numeric', 'between:-90,90'],
            'polygon.*.lng'              => ['required_without:polygon.*.longitude', 'nullable', 'numeric', 'between:-180,180'],
            'polygon.*.latitude'         => ['nullable', 'numeric', 'between:-90,90'],
            'polygon.*.longitude'        => ['nullable', 'numeric', 'between:-180,180'],
            'status'                     => ['required', 'numeric', Rule::in([Status::ACTIVE, Status::INACTIVE])],
            'charge_type'                => ['required', 'numeric', Rule::in([
                DeliveryChargeType::FIXED,
                DeliveryChargeType::PER_KM,
                DeliveryChargeType::RANGE,
            ])],
            'min_delivery_charge'        => ['nullable', 'numeric', 'min:0'],
            'max_delivery_charge'        => ['nullable', 'numeric', 'min:0'],
            'charge_per_km'              => ['nullable', 'numeric', 'min:0'],
            'max_cod_amount'             => ['nullable', 'numeric', 'min:0'],
            'additional_delivery_charge' => ['nullable', 'numeric', 'min:0'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $min = $this->input('min_delivery_charge');
            $max = $this->input('max_delivery_charge');
            if ($min !== null && $max !== null && (float) $max < (float) $min) {
                $validator->errors()->add('max_delivery_charge', trans('all.message.max_must_be_gte_min'));
            }
        });
    }
}
