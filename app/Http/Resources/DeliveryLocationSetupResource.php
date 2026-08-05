<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class DeliveryLocationSetupResource extends JsonResource
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
            "id"              => $this->id,
            "delivery_boy_id" => $this->delivery_boy_id,
            "address"         => $this->address,
            "latitude"        => $this->latitude,
            "longitude"       => $this->longitude
        ];
    }
}
