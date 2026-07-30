<?php

namespace App\Http\Requests;

use App\Enums\Role as EnumRole;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class EmployeeRequest extends FormRequest
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
            'name'                  => ['required', 'string', 'max:190'],
            'email'                 => ['required', 'email', 'max:190', Rule::unique("users", "email")->ignore($this->route('employee.id'))],
            'password'              => [$this->route('employee.id') ? 'nullable' : 'required', 'string', 'min:6'],
            'password_confirmation' => [$this->route('employee.id') ? 'nullable' : 'required', 'string', 'min:6', 'same:password'],
            'username'              => ['nullable', 'max:190', Rule::unique("users", "username")->ignore($this->route('employee.id'))],
            'device_token'          => ['nullable', 'string'],
            'web_token'             => ['nullable', 'string'],
            'phone'                 => ['nullable', 'string', 'max:20', Rule::unique("users", "phone")->ignore($this->route('employee.id'))],
            'restaurant_id'         => ['nullable', 'numeric'],
            'status'                => ['required', 'numeric', 'max:24'],
            'role_id'               => $this->roleIdRules(),
            'country_code'          => ['required', 'string', 'max:20']
        ];
    }

    public function attributes(): array
    {
        return [
            'role_id' => strtolower(trans('all.label.role'))
        ];
    }

    protected function roleIdRules(): array
    {
        $actorRoleId = (int) optional(Auth::user()?->roles->first())->id;

        if ($actorRoleId === EnumRole::RESTAURANT_OWNER) {
            return [
                'required',
                'numeric',
                Rule::in([
                    EnumRole::WAITER,
                    EnumRole::CHEF,
                    EnumRole::CASHIER,
                    EnumRole::MANAGER,
                ]),
            ];
        }

        return [
            'required',
            'numeric',
            Rule::notIn([
                EnumRole::ADMIN,
                EnumRole::RESTAURANT_OWNER,
                EnumRole::DELIVERY_BOY,
                EnumRole::CUSTOMER,
            ]),
        ];
    }
}
