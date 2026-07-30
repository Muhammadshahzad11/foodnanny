<?php

namespace App\Http\Resources;


use Illuminate\Http\Resources\Json\JsonResource;

class RestaurantCuisineResource extends JsonResource
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
            "id"            => $this->id,
            "restaurant_id" => $this->restaurant_id,
            "cuisine_id"    => $this->cuisine_id,
        ];
    }
}
