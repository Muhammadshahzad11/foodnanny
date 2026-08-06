<?php

namespace App\Http\Resources;

use App\Enums\Ask;
use App\Enums\OrderStatus;
use App\Libraries\AppLibrary;
use Illuminate\Http\Resources\Json\JsonResource;

class WaiterOrderResource extends JsonResource
{
    public function toArray($request): array
    {
        $isDraft = (int) $this->status === OrderStatus::PENDING && (int) $this->active === Ask::NO;

        return [
            'id'                        => $this->id,
            'order_serial_no'           => $this->order_serial_no,
            'token'                     => $this->token,
            'table_id'                  => $this->table_id,
            'waiter_id'                 => $this->waiter_id,
            'restaurant_id'             => $this->restaurant_id,
            'subtotal'                  => AppLibrary::convertAmountFormat($this->subtotal),
            'discount'                  => AppLibrary::convertAmountFormat($this->discount),
            'total_tax'                 => AppLibrary::convertAmountFormat($this->total_tax),
            'total'                     => AppLibrary::convertAmountFormat($this->total),
            'subtotal_currency_price'   => AppLibrary::currencyAmountFormat($this->subtotal),
            'discount_currency_price'   => AppLibrary::currencyAmountFormat($this->discount),
            'total_tax_currency_price'  => AppLibrary::currencyAmountFormat($this->total_tax),
            'total_currency_price'      => AppLibrary::currencyAmountFormat($this->total),
            'order_type'                => $this->order_type,
            'order_datetime'            => AppLibrary::datetime($this->order_datetime),
            'payment_method'            => $this->payment_method,
            'payment_status'            => $this->payment_status,
            'status'                    => $this->status,
            'status_name'               => trans('order_status.' . $this->status),
            'active'                    => $this->active,
            'is_draft'                  => $isDraft,
            'order_note'                => $this->order_note,
            'source'                    => $this->source,
            'updated_at'                => optional($this->updated_at)?->toIso8601String(),
            'table'                     => $this->whenLoaded('diningTable', function () {
                return [
                    'id'           => $this->diningTable?->id,
                    'name'         => $this->diningTable?->name,
                    'table_number' => $this->diningTable?->table_number,
                    'zone'         => $this->diningTable?->zone,
                    'status'       => $this->diningTable?->status,
                ];
            }),
            'waiter'                    => $this->whenLoaded('waiter', function () {
                return [
                    'id'   => $this->waiter?->id,
                    'name' => $this->waiter?->name,
                ];
            }),
            'restaurant'                => $this->whenLoaded('restaurant', function () {
                return [
                    'id'           => $this->restaurant?->id,
                    'name'         => $this->restaurant?->name,
                    'address'      => $this->restaurant?->address,
                    'phone'        => $this->restaurant?->phone,
                    'country_code' => $this->restaurant?->country_code,
                    'logo'         => $this->restaurant?->logo,
                ];
            }),
            'order_items'               => $this->whenLoaded('orderItems', function () {
                return $this->orderItems->map(function ($item) {
                    return [
                        'id'                   => $item->id,
                        'item_id'              => $item->item_id,
                        'item_name'            => $item->orderItem?->name,
                        'name'                 => $item->orderItem?->name,
                        'quantity'             => $item->quantity,
                        'discount'             => (float) $item->discount,
                        'price'                => (float) $item->price,
                        'item_price'           => (float) $item->price,
                        'item_variations'      => json_decode($item->item_variations, true),
                        'item_extras'          => json_decode($item->item_extras, true),
                        'item_variation_total' => (float) $item->item_variation_total,
                        'item_extra_total'     => (float) $item->item_extra_total,
                        'total_price'          => (float) $item->total_price,
                        'total_currency_price' => AppLibrary::currencyAmountFormat($item->total_price),
                        'instruction'          => $item->instruction,
                        'tax_name'             => $item->tax_name,
                        'tax_rate'             => (float) $item->tax_rate,
                        'tax_type'             => $item->tax_type,
                        'tax_amount'           => (float) $item->tax_amount,
                    ];
                });
            }),
        ];
    }
}
