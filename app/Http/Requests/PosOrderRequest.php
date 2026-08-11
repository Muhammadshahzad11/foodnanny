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

    protected function prepareForValidation(): void
    {
        if (!$this->filled('table_id')) {
            $this->merge(['table_id' => null]);
        }
        if (!$this->filled('order_note')) {
            $this->merge(['order_note' => null]);
        }

        // Axios may send place_only as boolean true; normalize for required_unless rules
        $placeOnly = $this->boolean('place_only')
            || ((int) $this->input('order_type') === \App\Enums\OrderType::DINING_TABLE
                && !$this->boolean('close_with_payment'));
        $this->merge([
            'place_only' => $placeOnly ? 1 : 0,
            'close_with_payment' => $this->boolean('close_with_payment') ? 1 : 0,
        ]);
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
            'payment_method'  => ['required_unless:place_only,1,true', 'nullable', 'numeric'],
            'items'           => ['required', 'json', new ValidJsonOrder],
            'payment_note'    => request('payment_method') === PosPaymentMethod::CARD || request('payment_method') === PosPaymentMethod::MOBILE_BANKING || request('payment_method') === PosPaymentMethod::OTHER ? (request('payment_method') === PosPaymentMethod::CARD ? ['required', 'numeric', 'min_digits:4', 'max_digits:4'] : ['required', 'string']) : ['nullable', 'string'],
            'received_amount' => (request('payment_method') === PosPaymentMethod::CASH && !request()->boolean('place_only')) ? ['required', 'numeric'] : ['nullable', 'numeric'],
            // POS service channel: takeaway / classic POS / dine-in / delivery
            'order_type'      => ['required', 'numeric', 'in:5,10,15,20'],
            'table_id'        => ['nullable', 'integer', 'exists:restaurant_tables,id'],
            'order_note'      => ['nullable', 'string', 'max:500'],
            'customer_name'   => ['nullable', 'string', 'max:120'],
            'customer_phone'  => ['nullable', 'string', 'max:40'],
            'customer_address'=> ['nullable', 'string', 'max:500'],
            'delivery_note'   => ['nullable', 'string', 'max:500'],
            'place_only'      => ['nullable', 'boolean'],
            'close_with_payment' => ['nullable', 'boolean'],
            'kot_only'        => ['nullable', 'boolean'],
            'skip_invoice'    => ['nullable', 'boolean'],
            // When paying an existing open order, cart items are already stored
            'editing_order_id' => ['nullable', 'integer'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $placeOnly = request()->boolean('place_only')
                || ((int) request('order_type') === \App\Enums\OrderType::DINING_TABLE && !request()->boolean('close_with_payment'));

            if (!$placeOnly && request('payment_method') == PosPaymentMethod::CASH) {
                // Compare in paise/cents to avoid float false negatives (e.g. 1967.86 == 1967.86)
                $totalCents    = (int) round(((float) request('total')) * 100);
                $receivedCents = (int) round(((float) request('received_amount')) * 100);
                if ($receivedCents < $totalCents) {
                    $validator->errors()->add('received_amount', trans('all.message.received_amount_can_not_less'));
                }
            }
            // Dine-in requires a table (skip when closing an existing open order that already has a table)
            $payingExisting = request()->boolean('close_with_payment') && (
                (int) request('editing_order_id') > 0
                || (function () {
                    $routeOrder = request()->route('order');
                    if (is_object($routeOrder) && isset($routeOrder->id)) {
                        return (int) $routeOrder->id > 0;
                    }
                    return (int) $routeOrder > 0;
                })()
            );
            if ((int) request('order_type') === \App\Enums\OrderType::DINING_TABLE
                && (int) request('table_id') <= 0
                && !$payingExisting
            ) {
                $validator->errors()->add('table_id', trans('all.message.table_required_for_dine_in') ?: 'Please select a table for dine-in orders.');
            }
            // Delivery: name + phone recommended
            if ((int) request('order_type') === \App\Enums\OrderType::DELIVERY) {
                if (!request()->filled('customer_name') && !request()->filled('customer_phone')) {
                    $validator->errors()->add('customer_name', 'Please enter customer name or phone for delivery.');
                }
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
