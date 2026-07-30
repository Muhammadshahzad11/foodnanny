<?php

namespace App\Http\Resources;


use App\Enums\DiscountType;
use App\Libraries\AppLibrary;
use Illuminate\Http\Resources\Json\JsonResource;

class FrontendItemDetailsResource extends JsonResource
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
            'id'                        => $this->id,
            'name'                      => $this->name,
            'slug'                      => $this->slug,
            'thumb'                     => $this->thumb,
            'caution'                   => $this->caution,
            'description'               => $this->description === null ? '' : $this->description,
            'description_alt'           => $this->description === null ? '' : strip_tags($this->description),
            'restaurant_id'             => $this->restaurant_id,
            'flat_price'                => AppLibrary::flatAmountFormat($this->price),
            'convert_price'             => AppLibrary::convertAmountFormat($this->price),
            'currency_price'            => AppLibrary::currencyAmountFormat($this->price),
            'tax'                       => new TaxResource($this->tax),
            'item_type'                 => $this->item_type,
            'variations'                => $this?->variations->groupBy('item_attribute_id'),
            'item_attributes'           => ItemAttributeResource::collection($this->itemAttributeList($this?->variations)),
            'extras'                    => ItemExtraResource::collection($this?->extras),
            'addons'                    => ItemAddonResource::collection($this?->addons),
            'discount'                  => $this->discount,
            "maximum_purchase_quantity" => $this->maximum_purchase_quantity,
            'discount_type'             => $this->discount_type,
            'flat_discounted_price'     => AppLibrary::flatAmountFormat($discountedPrice),
            'convert_discounted_price'  => AppLibrary::convertAmountFormat($discountedPrice),
            'currency_discounted_price' => AppLibrary::currencyAmountFormat($discountedPrice)
        ];
    }

    private function itemAttributeList($variations): \Vanilla\Support\Collection|\IlluminateAgnostic\Str\Support\Collection|\IlluminateAgnostic\StrAgnostic\Str\Support\Collection|\IlluminateAgnostic\Collection\Support\Collection|\IlluminateAgnostic\ArrAgnostic\Arr\Support\Collection|\Illuminate\Support\Collection|\IlluminateAgnostic\Arr\Support\Collection
    {
        $array = [];
        foreach ($variations as $b) {
            if (!isset($array[$b->itemAttribute->id])) {
                $array[$b->itemAttribute->id] = (object)[
                    'id'            => $b->itemAttribute->id,
                    'restaurant_id' => $b->itemAttribute->restaurant_id,
                    'name'          => $b->itemAttribute->name,
                    'status'        => $b->itemAttribute->status
                ];
            }
        }
        return collect($array);
    }
}
