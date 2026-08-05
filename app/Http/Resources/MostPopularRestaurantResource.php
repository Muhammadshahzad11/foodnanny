<?php

namespace App\Http\Resources;

use App\Libraries\AppLibrary;
use Illuminate\Http\Resources\Json\JsonResource;

class MostPopularRestaurantResource extends JsonResource
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
            "id"      => $this->id,
            "name"    => $this->name,
            "image"   => $this->image,
            "cuisine" => AppLibrary::cuisineString($this->cuisines),
            "orders"  => $this->orders_count,
        ];
    }
}
