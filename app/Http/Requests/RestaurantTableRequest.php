<?php

namespace App\Http\Requests;

use App\Enums\Role as EnumRole;
use App\Enums\TableStatus;
use App\Traits\DefaultAccessModelTrait;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class RestaurantTableRequest extends FormRequest
{
    use DefaultAccessModelTrait;

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $restaurantId = $this->resolveRestaurantId();
        $tableId      = $this->route('restaurantTable')?->id ?? $this->route('table')?->id;

        return [
            'restaurant_id' => [
                Rule::requiredIf(fn () => $this->restaurant() == 0),
                'nullable',
                'integer',
                'exists:restaurants,id',
            ],
            'table_number'  => [
                'required',
                'string',
                'max:50',
                Rule::unique('restaurant_tables', 'table_number')
                    ->ignore($tableId)
                    ->where(fn ($query) => $query->where('restaurant_id', $restaurantId)->whereNull('deleted_at')),
            ],
            'name'          => ['required', 'string', 'max:190'],
            'capacity'      => ['required', 'integer', 'min:1', 'max:999'],
            'zone'          => ['nullable', 'string', 'max:190'],
            'status'        => [
                'required',
                'integer',
                Rule::in([
                    TableStatus::AVAILABLE,
                    TableStatus::OCCUPIED,
                    TableStatus::RESERVED,
                    TableStatus::CLEANING,
                    TableStatus::OUT_OF_SERVICE,
                    TableStatus::INACTIVE,
                ]),
            ],
            'notes'         => ['nullable', 'string', 'max:5000'],
        ];
    }

    public function messages(): array
    {
        return [
            'table_number.unique' => trans('all.message.table_number_exist'),
            'restaurant_id.required' => trans('all.message.restaurant_required_for_table'),
            'capacity.min' => trans('all.message.table_capacity_invalid'),
        ];
    }

    public function resolveRestaurantId(): int
    {
        $scoped = (int) $this->restaurant();
        if ($scoped > 0) {
            return $scoped;
        }

        return (int) ($this->input('restaurant_id') ?: 0);
    }

    public function isRestaurantOwner(): bool
    {
        $user = Auth::user();

        return $user && isset($user->roles[0]) && (int) $user->roles[0]->id === EnumRole::RESTAURANT_OWNER;
    }
}
