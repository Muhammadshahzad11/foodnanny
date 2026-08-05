<?php

namespace App\Http\Resources;

use App\Enums\DiscountType;
use App\Libraries\AppLibrary;
use Illuminate\Http\Resources\Json\JsonResource;

class SimpleItemAdminResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request): array
    {

        $discountedPrice = 0;
        if ($this->discount_type > 0 && $this->discount > 0) {
            if ($this->discount_type == DiscountType::PERCENTAGE) {
                $discountedPrice = $this->price - ($this->price / 100 * $this->discount);
            } else {
                $discountedPrice = $this->price - $this->discount;
            }
        }

        return [
            "id"                           => $this->id,
            "restaurant_id"                => $this->restaurant_id,
            "name"                         => $this->resource->getRawOriginal('name'),
            "slug"                         => $this->slug,
            "item_category_id"             => $this->item_category_id,
            "tax_id"                       => $this->tax_id,
            "flat_price"                   => AppLibrary::flatAmountFormat($this->price),
            "convert_price"                => AppLibrary::convertAmountFormat($this->price),
            "currency_price"               => AppLibrary::currencyAmountFormat($this->price),
            "price"                        => $this->price,
            "item_type"                    => $this->item_type,
            "status"                       => $this->status,
            "description"                  => $this->resource->getRawOriginal('description') === null ? '' : $this->resource->getRawOriginal('description'),
            "caution"                      => $this->caution === null ? '' : $this->caution,
            "order"                        => $this->orders->count(),
            "thumb"                        => $this->thumb,
            "cover"                        => $this->cover,
            "preview"                      => $this->preview,
            "category_name"                => optional($this->category)->name,
            "halal"                        => $this->is_halal,
            "available_time_start"         => $this->available_time_start === null ? '' : $this->available_time_start,
            'convert_available_time_start' => $this->available_time_start === null ? '' : AppLibrary::time($this->available_time_start),
            "available_time_end"           => $this->available_time_end === null ? '' : $this->available_time_end,
            'convert_available_time_end'   => $this->available_time_end === null ? '' : AppLibrary::time($this->available_time_end),
            "discount_type"                => $this->discount_type === null ? '' : $this->discount_type,
            "discount"                     => $this->discount,
            "flat_discount"                => AppLibrary::flatAmountFormat($this->discount),
            "maximum_purchase_quantity"    => $this->maximum_purchase_quantity,
            'flat_discounted_price'        => AppLibrary::flatAmountFormat($discountedPrice),
            'convert_discounted_price'     => AppLibrary::convertAmountFormat($discountedPrice),
            'currency_discounted_price'    => AppLibrary::currencyAmountFormat($discountedPrice),
        ];
    }
}
