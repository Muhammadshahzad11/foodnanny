<?php

namespace App\Http\Requests;


use App\Enums\PosPaymentMethod;
use App\Rules\ValidJsonOrder;
use Illuminate\Foundation\Http\FormRequest;

class PosOrderRequest extends FormRequest
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
            'token'           => ['nullable', 'numeric', 'max:9999999999'],
            'subtotal'        => ['required', 'numeric'],
            'discount'        => ['nullable', 'numeric'],
            'total'           => ['required', 'numeric'],
            'tax'             => ['required', 'numeric'],
            'payment_method'  => ['required', 'numeric'],
            'items'           => ['required', 'json', new ValidJsonOrder],
            'payment_note'    => request('payment_method') === PosPaymentMethod::CARD || request('payment_method') === PosPaymentMethod::MOBILE_BANKING || request('payment_method') === PosPaymentMethod::OTHER ? (request('payment_method') === PosPaymentMethod::CARD ? ['required', 'numeric', 'min_digits:4', 'max_digits:4'] : ['required', 'string']) : ['nullable', 'string'],
            'received_amount' => request('payment_method') === PosPaymentMethod::CASH ? ['required', 'numeric'] : ['nullable', 'numeric'],

        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            if (request('payment_method') == PosPaymentMethod::CASH && ((float)request('total') > (float)request('received_amount'))) {
                $validator->errors()->add('received_amount', trans('all.message.received_amount_can_not_less'));
            }
        });
    }

    public function messages(): array
    {
        return [
            'payment_note.required'    => request('payment_method') == PosPaymentMethod::CARD ? trans('all.message.last_four_digits_required') : (request('payment_method') == PosPaymentMethod::MOBILE_BANKING ? trans('all.message.transaction_id_required') : trans('all.message.payment_note_required')),
            'payment_note.min_digits'  => trans('all.message.payment_note_min_digits'),
            'payment_note.max_digits'  => trans('all.message.payment_note_max_digits'),
            'received_amount.required' => trans('all.message.received_amount_required'),
        ];
    }
}
