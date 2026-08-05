<?php

namespace App\Http\Resources;


use App\Enums\DiscountType;
use App\Libraries\AppLibrary;
use Illuminate\Http\Resources\Json\JsonResource;

class CouponDetailsResource extends JsonResource
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
            'id'                               => $this->id,
            'restaurant_id'                    => $this->restaurant_id,
            'name'                             => $this->name,
            'description'                      => $this->description === null ? '' : $this->description,
            'code'                             => $this->code,
            'discount_alt'                     => $this->discount_type === DiscountType::PERCENTAGE ? AppLibrary::flatAmountFormat($this->discount) . '%' : AppLibrary::currencyAmountFormat($this->discount),
            'discount_type'                    => (int) $this->discount_type,
            'type'                             => (int) $this->type,
            'convert_start_date'               => AppLibrary::date($this->start_date),
            'convert_end_date'                 => AppLibrary::date($this->end_date),
            'minimum_order'                    => $this->minimum_order === null ? 0 : $this->minimum_order,
            'maximum_discount'                 => $this->maximum_discount === null ? 0 : $this->maximum_discount,
            "minimum_order_currency_amount"    => AppLibrary::currencyAmountFormat($this->minimum_order),
            "maximum_discount_currency_amount" => AppLibrary::currencyAmountFormat($this->maximum_discount),
        ];
    }
}
