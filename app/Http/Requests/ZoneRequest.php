<?php

namespace App\Http\Requests;

use App\Enums\Status;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ZoneRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $isCreate = !$this->route('zone');

        return [
            'name'                  => ['required', 'string', 'max:190'],
            'display_name'          => ['required', 'string', 'max:190'],
            'polygon'               => [$isCreate ? 'required' : 'nullable', 'array', 'min:3'],
            'polygon.*.lat'         => ['required_without:polygon.*.latitude', 'nullable', 'numeric', 'between:-90,90'],
            'polygon.*.lng'         => ['required_without:polygon.*.longitude', 'nullable', 'numeric', 'between:-180,180'],
            'polygon.*.latitude'    => ['nullable', 'numeric', 'between:-90,90'],
            'polygon.*.longitude'   => ['nullable', 'numeric', 'between:-180,180'],
            'status'                => ['required', 'numeric', Rule::in([Status::ACTIVE, Status::INACTIVE])],
            'base_delivery_fee'     => ['nullable', 'numeric', 'min:0'],
            'min_order_amount'      => ['nullable', 'numeric', 'min:0'],
            'free_delivery_above'   => ['nullable', 'numeric', 'min:0'],
            'free_delivery_km'      => ['nullable', 'numeric', 'min:0'],
            'extra_distance_charge' => ['nullable', 'numeric', 'min:0'],
            'peak_enabled'          => ['nullable', 'numeric', Rule::in([0, 1])],
            'peak_charge'           => ['nullable', 'numeric', 'min:0'],
        ];
    }
}
