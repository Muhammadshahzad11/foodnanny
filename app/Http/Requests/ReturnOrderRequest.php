<?php

namespace App\Http\Requests;

use App\Enums\OrderStatus;
use App\Enums\OrderType;
use App\Models\Order;
use Carbon\Carbon;
use Dipokhalder\Settings\Facades\Settings;
use Illuminate\Foundation\Http\FormRequest;

class ReturnOrderRequest extends FormRequest
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
            'order_serial_no' => ['required', 'numeric'],
            'reason'          => ['required', 'string'],
            'images.*'        => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:2048']
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $orderSerialNo = $this->input('order_serial_no');
            if ($orderSerialNo) {
                $returnOrder = Order::where(['order_serial_no' => $orderSerialNo, 'order_type' => OrderType::DELIVERY, 'status' => OrderStatus::OUT_FOR_DELIVERY])->first();
                if(!blank($returnOrder)) {
                    $hours       = Settings::group('site')->get('site_return_order_time') * 60 * 60;
                    $updatedTime = (int) Carbon::now()->diffInSeconds($returnOrder->updated_at,true);
                    if ((int)$hours < $updatedTime) {
                        $validator->errors()->add('order_serial_no', trans('all.message.return_order_time_expired'));
                    }
                } else {
                    $validator->errors()->add('order_serial_no', trans('all.message.eligible_return_order'));
                }
            }
        });
    }
}
