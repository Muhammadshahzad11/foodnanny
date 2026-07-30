<?php

namespace App\Http\Resources;


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
            'cuisine'           => AppLibrary::cuisineString($this->cuisinesWithCuisineRelation),
            'availability'      => AppLibrary::availability($this->timeSlots),
            'time_slots'        => SimpleTimeSlotResource::collection($this->timeSlots),
            'single_time_slots' => AppLibrary::timeSlots($this->timeSlots),
            'order_setup'       => new SimpleOrderSetupResource($this->orderSetup),
            'cuisines'          => SimpleCuisineResource::collection($this->cuisinesWithCuisineRelation),
            'reviews'           => SimpleReviewResource::collection($this->reviews),
            'favorite'          => (bool)$this->favorite
        ];
    }
}
