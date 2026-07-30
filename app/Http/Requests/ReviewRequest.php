<?php

namespace App\Http\Requests;

use App\Models\Order;
use App\Models\Restaurant;
use App\Models\Review;
use App\Enums\ModelType;
use App\Enums\OrderStatus;
use App\Models\User;
use Illuminate\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class ReviewRequest extends FormRequest
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
            'order_id' => $this->route('review.id') ? ['nullable', 'numeric'] : ['required', 'numeric', 'exists:orders,order_serial_no'],
            'type'     => $this->route('review.id') ? ['nullable', 'numeric'] : ['required', 'numeric', 'max:25'],
            'star'     => ['required', 'numeric', 'min:1', 'max:5'],
            'review'   => ['required', 'string', 'max:10000']
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function ($validator) {
            if ($this->route('review')) {
                return;
            }
            $order = Order::where('order_serial_no', $this->order_id)->first();
            if (!$order) {
                return;
            }
            if ((int)$order->status !== OrderStatus::DELIVERED) {
                $validator->errors()->add('order_id', trans('all.message.order_not_delivered_yet'));
                return;
            }

            $userId    = $order->user_id;
            $modelType = $this->type == ModelType::RESTAURANT ? Restaurant::class : User::class;
            $modelId   = $this->type == ModelType::RESTAURANT ? $order->restaurant_id : $order->delivery_boy_id;
            $exists    = Review::where([
                'user_id'    => $userId,
                'model_type' => $modelType,
                'model_id'   => $modelId,
            ])->exists();
            if ($exists) {
                $validator->errors()->add('order_id', trans('all.message.you_already_submitted_review'));
            }
        });
    }
}
