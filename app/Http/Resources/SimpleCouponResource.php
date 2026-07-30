<?php

namespace App\Http\Resources;


use App\Enums\DiscountType;
use App\Libraries\AppLibrary;
use Illuminate\Http\Resources\Json\JsonResource;

class SimpleCouponResource extends JsonResource
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
            'id'                            => $this->id,
            'restaurant_id'                 => $this->restaurant_id,
            'name'                          => $this->name,
            'description'                   => $this->description === null ? '' : $this->description,
            'code'                          => $this->code,
            'type'                          => (int) $this->type,
            'discount_alt'                  => $this->discount_type === DiscountType::PERCENTAGE ? AppLibrary::flatAmountFormat($this->discount) . '%' : AppLibrary::currencyAmountFormat($this->discount),
            "flat_discount"                 => AppLibrary::flatAmountFormat($this->discount),
            "convert_discount"              => AppLibrary::convertAmountFormat($this->discount),
            "currency_discount"             => AppLibrary::currencyAmountFormat($this->discount),
            'discount_type'                 => (int) $this->discount_type,
            'minimum_order'                 => $this->minimum_order === null ? 0 : $this->minimum_order,
            "minimum_order_currency_amount" => AppLibrary::currencyAmountFormat($this->minimum_order),
            "maximum_discount"              => $this->maximum_discount,
        ];
    }
}
