<?php

namespace App\Http\Resources;


use App\Libraries\AppLibrary;
use Illuminate\Http\Resources\Json\JsonResource;

class SimpleOfferRestaurantResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id'                => $this->restaurant?->id ?? 0,
            'name'              => $this->restaurant?->name,
            'slug'              => $this->restaurant?->slug,
            'description'       => $this->restaurant?->description ?? '',
            'cuisine'           => AppLibrary::cuisineString($this->restaurant?->cuisines),
            'thumb'             => $this->restaurant?->image,
            'preparation_time'  => $this->restaurant?->orderSetup?->food_preparation_time,
            'rating_star'       => $this->restaurant?->rating_star == null ? 0 : (double)$this->restaurant?->rating_star,
            'rating_star_count' => $this->restaurant?->rating_star_count == null ? 0 : $this->restaurant->rating_star_count,
            'distance'          => $this->restaurant?->distance,
            'status'            => $this->restaurant?->status,
            'favorite'          => (bool)$this->restaurant?->favorite,
            'availability'      => AppLibrary::availability($this->restaurant?->timeSlots)
        ];
    }
}

