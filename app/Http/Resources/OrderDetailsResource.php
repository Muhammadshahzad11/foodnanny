<?php

namespace App\Http\Resources;

use App\Enums\Ask;
use App\Enums\OrderStatus;
use App\Enums\OrderType;
use App\Libraries\AppLibrary;
use Carbon\Carbon;
use Dipokhalder\Settings\Facades\Settings;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderDetailsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request): array
    {
        return [
            'id'                          => $this->id,
            'order_serial_no'             => $this->order_serial_no,
            'token'                       => $this->token,
            'discount'                    => AppLibrary::convertAmountFormat($this->discount),
            'service_fee'                 => AppLibrary::convertAmountFormat($this->service_fee),
            'rider_tip'                   => AppLibrary::convertAmountFormat($this->rider_tip),
            'delivery_fee'                => AppLibrary::convertAmountFormat($this->delivery_fee),
            'total_tax'                   => AppLibrary::convertAmountFormat($this->total_tax),
            'subtotal_currency_price'     => AppLibrary::currencyAmountFormat($this->subtotal),
            'discount_currency_price'     => AppLibrary::currencyAmountFormat($this->discount),
            'delivery_fee_currency_price' => AppLibrary::currencyAmountFormat($this->delivery_fee),
            'rider_tip_currency_price'    => AppLibrary::currencyAmountFormat($this->rider_tip),
            'service_fee_currency_price'  => AppLibrary::currencyAmountFormat($this->service_fee),
            'total_currency_price'        => AppLibrary::currencyAmountFormat($this->total),
            'total_tax_currency_price'    => AppLibrary::currencyAmountFormat($this->total_tax),
            'order_type'                  => $this->order_type,
            'table_id'                    => $this->table_id,
            'waiter_id'                   => $this->waiter_id,
            'order_note'                  => $this->order_note,
            'table'                       => $this->when((int) $this->table_id > 0, function () {
                return [
                    'id'           => $this->diningTable?->id,
                    'name'         => $this->diningTable?->name,
                    'table_number' => $this->diningTable?->table_number,
                    'zone'         => $this->diningTable?->zone,
                ];
            }),
            'waiter'                      => $this->when((int) $this->waiter_id > 0, function () {
                return [
                    'id'   => $this->waiter?->id,
                    'name' => $this->waiter?->name,
                ];
            }),
            'order_datetime'              => AppLibrary::datetime($this->order_datetime),
            'order_date'                  => AppLibrary::date($this->order_datetime),
            'order_time'                  => AppLibrary::time($this->order_datetime),
            'delivery_date'               => $this->is_advance_order == Ask::YES ? AppLibrary::increaseDate($this->order_datetime, 1) : AppLibrary::date($this->order_datetime),
            'delivery_time'               => AppLibrary::deliveryTime($this->delivery_time),
            'payment_method'              => $this->payment_method,
            'payment_status'              => $this->payment_status,
            'is_advance_order'            => $this->is_advance_order,
            'preparation_time'            => $this->preparation_time,
            'status'                      => $this->status,
            'cutlery'                     => $this->cutlery,
            'is_received'                 => $this->is_received,
            'status_name'                 => trans('order_status.' . $this->status),
            'reason'                      => $this->reason,
            'restaurant_review_status'    => $this->restaurantReviewStatus(),
            'delivery_boy_review_status'  => $this->deliveryBoyReviewStatus(),
            'cash_back_amount'            => $this->posDetail?->received_amount - $this->total,
            'cash_back_currency_amount'   => AppLibrary::currencyAmountFormat($this->posDetail?->received_amount - $this->total),
            'user'                        => new UserResource($this->user),
            'order_address'               => new AddressResource($this->address),
            'restaurant'                  => new RestaurantResource($this->restaurant),
            'delivery_boy'                => new UserResource($this->deliveryBoy),
            'coupon'                      => new SimpleCouponResource($this->coupon?->coupon),
            'transaction'                 => new TransactionResource($this->transaction),
            'order_items'                 => OrderItemResource::collection($this->orderItems),
            'pos_detail'                  => new PosDetailsResource($this->posDetail),
            'return_images'               => $this->returnImages
        ];
    }

    private function restaurantReviewStatus(): bool
    {
        if ($this->status == OrderStatus::DELIVERED) {
            $hours       = Settings::group('site')->get('site_rating_time') * 60 * 60;
            $updatedTime = (int) Carbon::now()->diffInSeconds($this->updated_at,true);
            if ((int)$hours >= $updatedTime) {
                return true;
            }
        }
        return false;
    }

    private function deliveryBoyReviewStatus(): bool
    {
        if ($this->delivery_boy_id > 0 && $this->status == OrderStatus::DELIVERED && $this->order_type == OrderType::DELIVERY) {
            $hours       = Settings::group('site')->get('site_rating_time') * 60 * 60;
            $updatedTime = (int) Carbon::now()->diffInSeconds($this->updated_at,true);
            if ((int)$hours >= $updatedTime) {
                return true;
            }
        }
        return false;
    }
}
