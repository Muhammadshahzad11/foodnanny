<?php

namespace App\Http\Resources;


use App\Libraries\AppLibrary;
use Illuminate\Http\Resources\Json\JsonResource;

class TableRestaurantDetailsResource extends JsonResource
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
            'id'        => $this->id,
            'name'      => $this->name,
            'slug'      => $this->slug,
            'logo'      => $this->logo,
            'cover'     => $this->image,
            'latitude'  => $this->latitude,
            'longitude' => $this->longitude,
            'cuisine'   => AppLibrary::cuisineString($this->cuisinesWithCuisineRelation, true),
            'fssai_number' => $this->fssai_number ?: '',
        ];
    }
}
