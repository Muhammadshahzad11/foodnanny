<?php

namespace App\Http\Resources;


use App\Enums\DiscountType;
use App\Libraries\AppLibrary;
use Illuminate\Http\Resources\Json\JsonResource;

class ItemAddonResource extends JsonResource
{

    public object $variation;

    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */


    public function toArray($request): array
    {

        $this->variation = $this->variationTotal();
        $discountedPrice = 0;
        if ($this->addonItem?->discount_type > 0 && $this->addonItem?->discount > 0) {
            if ($this->addonItem?->discount_type == DiscountType::PERCENTAGE) {
                $discountedPrice = $this->addonItem->price - ($this->addonItem->price / 100 * $this->addonItem->discount);
            } else {
                $discountedPrice = $this->addonItem->price - $this->addonItem->discount;
            }
        }

        $total = $this->variation?->price + ($this->addonItem?->discount > 0 ? $discountedPrice : $this->addonItem?->price);
        return [
            'id'                                   => $this->id,
            'restaurant_id'                        => $this->restaurant_id,
            'item_id'                              => $this->item_id,
            'item_addon_id'                        => $this->addon_item_id,
            'item_name'                            => $this->item?->name,
            'addon_item_name'                      => $this->addonItem?->name,
            'addon_item_price'                     => $this->addonItem?->price,
            'addon_item_flat_price'                => AppLibrary::flatAmountFormat($this->addonItem?->price),
            'addon_item_convert_price'             => AppLibrary::convertAmountFormat($this->addonItem?->price),
            'addon_item_currency_price'            => AppLibrary::currencyAmountFormat($this->addonItem?->price),
            'addon_item_tax'                       => new TaxResource($this->addonItem?->tax),
            'addon_item_flat_discounted_price'     => AppLibrary::flatAmountFormat($discountedPrice),
            'addon_item_currency_discounted_price' => AppLibrary::currencyAmountFormat($discountedPrice),
            'addon_item_convert_discounted_price'  => AppLibrary::convertAmountFormat($discountedPrice),
            'addon_item_discount_type'             => $this->addonItem?->discount_type,
            'addon_item_discount'                  => $this->addonItem?->discount,
            'addon_item_maximum_purchase_quantity' => $this->addonItem?->maximum_purchase_quantity,
            'addon_item_status'                    => $this->addonItem?->status,
            'variations'                           => json_decode($this->addon_item_variation),
            'variation_total'                      => $this->variation?->price,
            'variation_total_flat_price'           => AppLibrary::flatAmountFormat($this->variation?->price),
            'variation_total_convert_price'        => AppLibrary::convertAmountFormat($this->variation?->price),
            'variation_total_currency_price'       => AppLibrary::currencyAmountFormat($this->variation?->price),
            'total'                                => $total,
            'total_flat_price'                     => AppLibrary::flatAmountFormat($total),
            'total_convert_price'                  => AppLibrary::convertAmountFormat($total),
            'total_currency_price'                 => AppLibrary::currencyAmountFormat($total),
            'variation_names'                      => $this->variation?->name,
            'thumb'                                => $this->addonItem?->thumb,
            'cover'                                => $this->addonItem?->cover,
            'preview'                              => $this->addonItem?->preview,
            'caution'                              => $this->addonItem?->caution == null ? '' : $this->addonItem?->caution
        ];
    }

    private function variationTotal(): object
    {
        $variationArray = $this->addonItem?->variations?->mapWithKeys(function ($variation) {
            return [$variation->id => $variation];
        });

        if ($this->addon_item_variation) {
            $variations = (object)json_decode($this->addon_item_variation, true);
            $price      = 0;
            $name       = [];
            foreach ($variations as $variation) {
                if (isset($variationArray[$variation])) {
                    $name[] = [
                        'id'             => $variationArray[$variation]->id,
                        'name'           => $variationArray[$variation]->name,
                        'attribute_name' => $variationArray[$variation]->itemAttribute->name
                    ];
                    $price  += $variationArray[$variation]->price;
                }
            }
            return (object)[
                'price' => $price,
                'name'  => $name
            ];
        }
        return (object)[
            'price' => 0,
            'name'  => []
        ];
    }
}
