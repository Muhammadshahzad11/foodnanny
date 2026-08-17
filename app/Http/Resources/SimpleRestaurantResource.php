<?php

namespace App\Http\Resources;


use App\Libraries\AppLibrary;
use Illuminate\Http\Resources\Json\JsonResource;

class SimpleRestaurantResource extends JsonResource
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
            'name'              => $this->name,
            'slug'              => $this->slug,
            'thumb'             => $this->image,
            'preparation_time'  => $this->orderSetup?->food_preparation_time,
            'rating_star'       => $this->rating_star == null ? 0 : (double)$this->rating_star,
            'rating_star_count' => $this->rating_star_count == null ? 0 : $this->rating_star_count,
            'distance'          => $this->distance !== null ? round((float) $this->distance, 1) : null,
            'status'            => $this->status,
            'favorite'          => (bool)$this->favorite,
            'availability'      => AppLibrary::availability($this->timeSlots),
        ];
    }
}

