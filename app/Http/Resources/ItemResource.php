<?php

namespace App\Http\Resources;

use App\Libraries\AppLibrary;
use Illuminate\Http\Resources\Json\JsonResource;

class ItemResource extends JsonResource
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
            "id"                           => $this->id,
            "restaurant_id"                => $this->restaurant_id,
            "name"                         => $this->name,
            "slug"                         => $this->slug,
            "item_category_id"             => $this->item_category_id,
            "tax_id"                       => $this->tax_id,
            "flat_price"                   => AppLibrary::flatAmountFormat($this->price),
            "currency_price"               => AppLibrary::currencyAmountFormat($this->price),
            "price"                        => $this->price,
            "item_type"                    => $this->item_type,
            "status"                       => $this->status,
            "description"                  => $this->description === null ? '' : $this->description,
            "caution"                      => $this->caution === null ? '' : $this->caution,
            "order"                        => $this->orders->sum('quantity'),
            "thumb"                        => $this->thumb,
            "cover"                        => $this->cover,
            "preview"                      => $this->preview,
            "category_name"                => optional($this->category)->name,
            "tax_name"                     => optional($this->tax)->name,
            "category"                     => new ItemCategoryResource($this?->category),
            "tax"                          => new TaxResource($this?->tax),
            "variations"                   => $this?->variations->groupBy('item_attribute_id'),
            "itemAttributes"               => ItemAttributeResource::collection($this->itemAttributeList($this?->variations)),
            "extras"                       => ItemExtraResource::collection($this?->extras),
            "addons"                       => ItemAddonResource::collection($this?->addons),
            "halal"                        => $this->is_halal,
            "available_time_start"         => $this->available_time_start === null ? '' :  $this->available_time_start,
            'convert_available_time_start' => $this->available_time_start === null ? '' :  AppLibrary::time($this->available_time_start),
            "available_time_end"           => $this->available_time_end === null ? '' :  $this->available_time_end,
            'convert_available_time_end'   => $this->available_time_end === null ? '' : AppLibrary::time($this->available_time_end),
            "discount_type"                => $this->discount_type === null ? '' :  $this->discount_type,
            "discount"                     => $this->discount,
            "flat_discount"                => AppLibrary::flatAmountFormat($this->discount),
            "maximum_purchase_quantity"    => $this->maximum_purchase_quantity
        ];
    }

    private function itemAttributeList($variations): \Vanilla\Support\Collection|\IlluminateAgnostic\Str\Support\Collection|\IlluminateAgnostic\Collection\Support\Collection|\IlluminateAgnostic\StrAgnostic\Str\Support\Collection|\IlluminateAgnostic\ArrAgnostic\Arr\Support\Collection|\Illuminate\Support\Collection|\IlluminateAgnostic\Arr\Support\Collection
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
