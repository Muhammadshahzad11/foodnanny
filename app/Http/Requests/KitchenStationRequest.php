<?php

namespace App\Http\Requests;

use App\Enums\Status;
use App\Traits\DefaultAccessModelTrait;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class KitchenStationRequest extends FormRequest
{
    use DefaultAccessModelTrait;

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $stationId = $this->route('kitchenStation')?->id;

        return [
            'name'          => ['required', 'string', 'max:190'],
            'code'          => [
                'nullable',
                'string',
                'max:50',
                Rule::unique('kitchen_stations', 'code')
                    ->ignore($stationId)
                    ->where('restaurant_id', $this->restaurant()),
            ],
            'printer_id'    => [
                'required',
                'integer',
                Rule::exists('printers', 'id')->where('restaurant_id', $this->restaurant()),
            ],
            'category_ids'   => ['nullable', 'array'],
            'category_ids.*' => [
                'integer',
                Rule::exists('item_categories', 'id')->where('restaurant_id', $this->restaurant()),
            ],
            'sort_order'    => ['nullable', 'integer', 'min:0'],
            'status'        => ['required', 'numeric', Rule::in([Status::ACTIVE, Status::INACTIVE])],
        ];
    }

    public function attributes(): array
    {
        return [
            'printer_id'   => strtolower(trans('all.label.assigned_printer') ?: 'assigned printer'),
            'category_ids' => strtolower(trans('all.label.categories') ?: 'categories'),
        ];
    }
}
