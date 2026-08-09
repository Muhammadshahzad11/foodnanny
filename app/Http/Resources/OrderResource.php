<?php

namespace App\Http\Resources;

use App\Libraries\AppLibrary;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
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
            'user_id'                     => $this->user_id,
            'restaurant_id'               => $this->restaurant_id,
            'restaurant_name'             => optional($this->restaurant)->name,
            'order_items'                 => optional($this->orderItems)->count(),
            "total_currency_price"        => AppLibrary::currencyAmountFormat($this->total),
            "total_tax_currency_price"    => AppLibrary::currencyAmountFormat($this->total_tax),
            "total_amount_price"          => AppLibrary::flatAmountFormat($this->total),
            "discount_currency_price"     => AppLibrary::currencyAmountFormat($this->discount),
            "delivery_fee_currency_price" => AppLibrary::currencyAmountFormat($this->delivery_fee),
            'subtotal'                    => AppLibrary::convertAmountFormat($this->subtotal),
            'discount'                    => AppLibrary::convertAmountFormat($this->discount),
            'total'                       => AppLibrary::convertAmountFormat($this->total),
            'total_tax'                   => AppLibrary::convertAmountFormat($this->total_tax),
            'table_id'                    => $this->table_id,
            'payment_method'              => $this->payment_method,
            'payment_status'              => $this->payment_status,
            'preparation_time'            => $this->preparation_time,
            'order_type'                  => $this->order_type,
            'order_datetime'              => AppLibrary::datetime($this->order_datetime),
            'status'                      => $this->status,
            'is_advance_order'            => $this->is_advance_order,
            'status_name'                 => trans('order_status.' . $this->status),
            'customer'                    => new UserResource($this->user),
            'transaction'                 => new TransactionResource($this->transaction),
            'reason'                      => $this->reason,
        ];
    }
}
