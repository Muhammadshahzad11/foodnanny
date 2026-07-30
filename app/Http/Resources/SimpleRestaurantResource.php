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
            'name'              => $this->name,
            'slug'              => $this->slug,
            'thumb'             => $this->image,
            'preparation_time'  => $this->orderSetup?->food_preparation_time,
            'rating_star'       => $this->rating_star == null ? 0 : (double)$this->rating_star,
            'rating_star_count' => $this->rating_star_count == null ? 0 : $this->rating_star_count,
            'distance'          => $this->distance,
            'status'            => $this->status,
            'favorite'          => (bool)$this->favorite,
            'availability'      => AppLibrary::availability($this->timeSlots),
        ];
    }
}

