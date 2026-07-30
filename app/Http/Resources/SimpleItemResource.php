<?php

namespace App\Http\Resources;


use App\Enums\DiscountType;
use App\Libraries\AppLibrary;
use Illuminate\Http\Resources\Json\JsonResource;

class SimpleItemResource extends JsonResource
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
        $discountOption  = '';
        if ($this->discount_type > 0 && $this->discount > 0) {
            if ($this->discount_type == DiscountType::PERCENTAGE) {
                $discountedPrice = $this->price - ($this->price / 100 * $this->discount);
                $discountOption  = AppLibrary::flatAmountFormat($this->discount) . '%';
            } else {
                $discountedPrice = $this->price - $this->discount;
                $discountOption  = AppLibrary::currencyAmountFormat($this->discount);
            }
        }

        return [
            'id'                        => $this->id,
            'name'                      => $this->name,
            'slug'                      => $this->slug,
            'item_type'                 => $this->item_type,
            'flat_price'                => AppLibrary::flatAmountFormat($this->price),
            'convert_price'             => AppLibrary::convertAmountFormat($this->price),
            'currency_price'            => AppLibrary::currencyAmountFormat($this->price),
            'description'               => $this->description === null ? '' : $this->description,
            'description_alt'           => $this->description === null ? '' : strip_tags($this->description),
            'caution'                   => $this->caution === null ? '' : $this->caution,
            'thumb'                     => $this->thumb,
            'cover'                     => $this->cover,
            'halal'                     => $this->is_halal,
            'discount'                  => AppLibrary::flatAmountFormat($this->discount),
            'discount_option'           => $discountOption,
            'discount_type'             => $this->discount_type,
            'flat_discounted_price'     => AppLibrary::flatAmountFormat($discountedPrice),
            'convert_discounted_price'  => AppLibrary::convertAmountFormat($discountedPrice),
            'currency_discounted_price' => AppLibrary::currencyAmountFormat($discountedPrice),
        ];
    }
}
