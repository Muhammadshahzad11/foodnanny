<?php

namespace App\Http\Requests;

use App\Models\Order;
use App\Enums\OrderType;
use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Libraries\AppLibrary;
use Illuminate\Foundation\Http\FormRequest;

class RefundRequest extends FormRequest
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
            'order_serial_no'  => ['required', 'string', 'unique:refunds,order_serial_no'],
            'refund_amount'    => ['required', 'numeric'],
            'deduction_amount' => ['required', 'numeric'],
            'responsible'      => ['required', 'numeric']
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $orderSerialNo = $this->input('order_serial_no');
            if ($orderSerialNo) {
                $order = Order::where('order_serial_no', $orderSerialNo)
                    ->where(function ($query) {
                        $query->where('status', OrderStatus::DELIVERED)
                            ->where('payment_status', PaymentStatus::PAID)
                            ->orWhere(function ($q) {
                                $q->where('status', OrderStatus::RETURNED)
                                    ->where('payment_status', PaymentStatus::PAID);
                            });
                    })->where('order_type', OrderType::DELIVERY)->first();
                if (!$order) {
                    $validator->errors()->add('order_serial_no', trans('all.message.order_not_found_refund'));
                } elseif (!blank($order) && $this->input('refund_amount') > AppLibrary::convertAmountFormat($order->total)) {
                    $validator->errors()->add('refund_amount', trans('all.message.amount_not_greater_order_subtotal'));
                } elseif(!blank($order) && $this->input('deduction_amount') > AppLibrary::convertAmountFormat($order->total)) {
                    $validator->errors()->add('deduction_amount', trans('all.message.amount_not_greater_order_subtotal'));
                }
            }
        });
    }

    public function attributes(): array
    {
        return [
            'order_serial_no' => strtolower(trans('all.label.order_id'))
        ];
    } 
}
