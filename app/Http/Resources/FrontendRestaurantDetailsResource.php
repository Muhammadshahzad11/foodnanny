<?php

namespace App\Http\Resources;


use App\Enums\Ask;
use App\Libraries\AppLibrary;
use Illuminate\Http\Resources\Json\JsonResource;

class FrontendRestaurantDetailsResource extends JsonResource
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
            'id'                => $this->id,
            'name'              => $this->name,
            'slug'              => $this->slug,
            "email"             => $this->email === null ? '' : $this->email,
            'country_code'      => $this->country_code ?? '',
            'phone'             => $this->phone,
            'address'           => $this->address,
            'logo'              => $this->logo,
            'cover'             => $this->image,
            'latitude'          => $this->latitude,
            'longitude'         => $this->longitude,
            'today'             => date('D, M d'),
            'distance'          => $this->distance,
            'rating_star'       => (int)$this->rating_star,
            'rating_star_count' => (int)$this->rating_star_count,
            'cuisine'           => AppLibrary::cuisineString($this->cuisinesWithCuisineRelation, true),
            'availability'      => AppLibrary::availability($this->timeSlots),
            'time_slots'        => SimpleTimeSlotResource::collection($this->timeSlots),
            'single_time_slots' => AppLibrary::timeSlots($this->timeSlots),
            'order_setup'       => new SimpleOrderSetupResource($this->orderSetup),
            'zone_id'           => $this->zone_id,
            'zone'              => $this->whenLoaded('zone', function () {
                if (!$this->zone) {
                    return null;
                }
                return [
                    'id'                    => $this->zone->id,
                    'name'                  => $this->zone->name,
                    'display_name'          => $this->zone->display_name,
                    'base_delivery_fee'     => $this->zone->base_delivery_fee,
                    'min_order_amount'      => $this->zone->min_order_amount,
                    'free_delivery_above'   => $this->zone->free_delivery_above,
                    'free_delivery_km'      => $this->zone->free_delivery_km,
                    'extra_distance_charge' => $this->zone->extra_distance_charge,
                    'peak_enabled'          => (int) $this->zone->peak_enabled,
                    'peak_charge'           => $this->zone->peak_charge,
                ];
            }),
            'delivery_zones'    => RestaurantDeliveryZoneResource::collection($this->whenLoaded('activeDeliveryZones')),
            'cuisines'          => SimpleCuisineResource::collection(
                $this->cuisinesWithCuisineRelation->filter(
                    fn ($row) => (int) ($row->cuisine?->show_on_home ?? Ask::YES) === Ask::YES
                )->values()
            ),
            'reviews'           => SimpleReviewResource::collection($this->reviews),
            'favorite'          => (bool)$this->favorite,
            'show_important_notice' => (int) ($this->show_important_notice ?? Ask::YES) === Ask::YES,
            'important_notice'  => $this->important_notice ?: '',
            'important_notice_emphasis' => $this->important_notice_emphasis ?: '',
            'show_highlights'   => (int) ($this->show_highlights ?? Ask::YES) === Ask::YES,
            'highlights'        => $this->resource->resolvedHighlights(),
        ];
    }
}
