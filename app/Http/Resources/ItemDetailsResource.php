<?php

namespace App\Http\Resources;


use App\Enums\DiscountType;
use App\Libraries\AppLibrary;
use Illuminate\Http\Resources\Json\JsonResource;

class ItemDetailsResource extends JsonResource
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
            'id'                           => $this->id,
            'name'                         => $this->resource->getRawOriginal('name'),
            'slug'                         => $this->slug,
            'thumb'                        => $this->thumb,
            'caution'                      => $this->caution === null ? '' : $this->caution,
            'description'                  => $this->resource->getRawOriginal('description') === null ? '' : $this->resource->getRawOriginal('description'),
            'description_alt'              => $this->resource->getRawOriginal('description') === null ? '' : strip_tags($this->resource->getRawOriginal('description')),
            'restaurant_id'                => $this->restaurant_id,
            'flat_price'                   => AppLibrary::flatAmountFormat($this->price),
            'convert_price'                => AppLibrary::convertAmountFormat($this->price),
            'currency_price'               => AppLibrary::currencyAmountFormat($this->price),
            'tax'                          => new TaxResource($this->tax),
            'tax_name'                     => $this->tax?->name,
            'item_type'                    => $this->item_type,
            'variations'                   => $this?->variations->groupBy('item_attribute_id'),
            'item_attributes'              => ItemAttributeResource::collection($this->itemAttributeList($this?->variations)),
            'extras'                       => ItemExtraResource::collection($this?->extras),
            'addons'                       => ItemAddonResource::collection($this?->addons),
            'discount'                     => $this->discount,
            "discount_type"                => $this->discount_type === null ? '' : $this->discount_type,
            "item_category_id"             => $this->item_category_id,
            "tax_id"                       => $this->tax_id,
            "price"                        => $this->price,
            "status"                       => $this->status,
            "cover"                        => $this->cover,
            "preview"                      => $this->preview,
            "category_name"                => optional($this->category)->name,
            "halal"                        => $this->is_halal, 
            "available_time_start"         => $this->available_time_start === null ? '' : $this->available_time_start,
            'convert_available_time_start' => $this->available_time_start === null ? '' : AppLibrary::time($this->available_time_start),
            "available_time_end"           => $this->available_time_end === null ? '' : $this->available_time_end,
            'convert_available_time_end'   => $this->available_time_end === null ? '' : AppLibrary::time($this->available_time_end),
            "flat_discount"                => AppLibrary::flatAmountFormat($this->discount),
            "maximum_purchase_quantity"    => $this->maximum_purchase_quantity,
            'flat_discounted_price'        => AppLibrary::flatAmountFormat($discountedPrice),
            'convert_discounted_price'     => AppLibrary::convertAmountFormat($discountedPrice),
            'currency_discounted_price'    => AppLibrary::currencyAmountFormat($discountedPrice),
            'translations'                 => $this->whenLoaded('translations', $this->translations)
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
