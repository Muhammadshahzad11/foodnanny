<?php

namespace App\Http\Resources;

use App\Enums\OrderType;
use App\Enums\Source;
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
            'order_type'                  => $this->order_type,
            'source'                      => $this->source,
            'source_name'                 => trans('source.' . $this->source),
            'channel_key'                 => $this->channelKey(),
            'is_scan_menu_order'          => $this->resource->isScanMenuOrder(),
            'table_id'                    => $this->table_id,
            'table'                       => $this->when((int) $this->table_id > 0, function () {
                return [
                    'id'           => $this->diningTable?->id,
                    'name'         => $this->diningTable?->name,
                    'table_number' => $this->diningTable?->table_number,
                ];
            }),
            'payment_method'              => $this->payment_method,
            'payment_status'              => $this->payment_status,
            'preparation_time'            => $this->preparation_time,
            'order_datetime'              => AppLibrary::datetime($this->order_datetime),
            'status'                      => $this->status,
            'requires_delivery_otp'       => (int) $this->order_type === \App\Enums\OrderType::DELIVERY
                && (int) $this->status === \App\Enums\OrderStatus::OUT_FOR_DELIVERY,
            'is_advance_order'            => $this->is_advance_order,
            'status_name'                 => trans('order_status.' . $this->status),
            'customer'                    => new UserResource($this->user),
            'transaction'                 => new TransactionResource($this->transaction),
            'reason'                      => $this->reason,
        ];
    }

    protected function channelKey(): string
    {
        if ((int) $this->order_type === OrderType::DINING_TABLE && (int) $this->table_id > 0) {
            $source = (int) $this->source;
            if (in_array($source, [Source::WEB, Source::APP], true)) {
                return 'qr';
            }
            if ($source === Source::WAITER) {
                return 'waiter';
            }
            if ($source === Source::POS) {
                return 'pos';
            }
        }

        return match ((int) $this->source) {
            Source::POS => 'pos',
            Source::WAITER => 'waiter',
            Source::APP => 'app',
            default => 'online',
        };
    }
}
