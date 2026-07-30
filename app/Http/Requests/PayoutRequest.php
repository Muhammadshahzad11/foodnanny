<?php

namespace App\Http\Requests;

use App\Enums\ModelType;
use App\Libraries\AppLibrary;
use Illuminate\Foundation\Http\FormRequest;
use App\Models\User;
use App\Models\Restaurant;

class PayoutRequest extends FormRequest
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
            'type'     => ['required', 'numeric'],
            'model_id' => ['required', 'numeric'],
            'date'     => ['required', 'string'],
            'amount'   => ['required', 'numeric', 'not_in:0']
        ];
    }

    public function attributes(): array
    {
         if ($this->input('type') == ModelType::DELIVERY_BOY) {
            return [
                'model_id' => strtolower(trans('all.label.delivery_boy')),
            ];
        } else {
            return [
                'model_id' => strtolower(trans('all.label.restaurant')),
            ];
        }
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $amount = $this->input('amount');
            if($this->input('amount') <= 0) {
                $validator->errors()->add('amount', trans('all.message.negative_amount_cannot_acceptable'));
            }
            if ($this->input('type') == ModelType::RESTAURANT) {
                $balance = Restaurant::where('id', $this->input('model_id'))->value('balance');
                if ($amount > AppLibrary::convertAmountFormat($balance)) {
                    $validator->errors()->add('amount', trans('all.message.amount_cannot_greater_restaurant_balance'));
                }
            }
            if ($this->input('type') == ModelType::DELIVERY_BOY) {
                $balance = User::where('id', $this->input('model_id'))->value('balance');
                if ($amount > AppLibrary::convertAmountFormat($balance)) {
                    $validator->errors()->add('amount', trans('all.message.amount_cannot_greater_delivery_boy_balance'));
                }
            }
        });
    }
}
