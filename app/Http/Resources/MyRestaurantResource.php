<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class MyRestaurantResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request): array
    {
        if ($this->resource === null) {
            return [
                "id"             => null,
                "name"           => '',
                "email"          => '',
                "phone"          => '',
                "latitude"       => '',
                "longitude"      => '',
                "city"           => null,
                "state"          => null,
                "zip_code"       => null,
                "address"        => null,
                "current_status" => null,
                "cover"          => null,
                "logo"           => null,
                "cuisine_id"     => [],
            ];
        }

        return [
            "id"             => $this->id,
            "name"           => $this->name,
            "email"          => $this->email === null ? '' : $this->email,
            "phone"          => $this->phone === null ? '' : $this->phone,
            "latitude"       => $this->latitude === null ? '' : $this->latitude,
            "longitude"      => $this->longitude === null ? '' : $this->longitude,
            "city"           => $this->city,
            "state"          => $this->state,
            "zip_code"       => $this->zip_code,
            "address"        => $this->address,
            "current_status" => $this->current_status,
            "cover"          => $this->image,
            "logo"           => $this->logo,
            "cuisine_id"     => RestaurantCuisineResource::collection($this?->cuisines)
        ];
    }
}
