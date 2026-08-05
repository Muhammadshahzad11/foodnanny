<?php

namespace App\Http\Requests;


use App\Enums\OrderStatus;
use Carbon\Carbon;
use Dipokhalder\Settings\Facades\Settings;
use Illuminate\Foundation\Http\FormRequest;

class DeliveryBoyRatingRequest extends FormRequest
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
            'star'   => ['required', 'numeric', 'max:5', 'not_in:0'],
            'review' => ['required', "max:5000"]
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            if ($this->order->delivery_boy_id > 0 && $this->order->status == OrderStatus::DELIVERED) {
                $hours       = Settings::group('site')->get('site_rating_time') * 60 * 60;
                $updatedTime = (int) Carbon::now()->diffInSeconds($this->order->updated_at,true);
                if ((int)$hours < $updatedTime) {
                    $validator->errors()->add('review', trans('all.message.review_time_expired'));
                }
            } else {
                $validator->errors()->add('review', trans('all.message.review_not_possible'));
            }
        });
    }
}
