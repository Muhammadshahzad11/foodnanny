<?php

namespace App\Http\Resources;



use Illuminate\Http\Resources\Json\JsonResource;

class SimpleAdminRestaurantResource extends JsonResource
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
            'id'         => $this->id,
            'zone_id'    => $this->zone_id,
            'name'       => $this->name,
            'email'      => $this->email,
            'name_email' => $this->name . ' (' . $this->email . ')',
            'latitude'   => $this->latitude === null ? '' : $this->latitude,
            'longitude'  => $this->longitude === null ? '' : $this->longitude,
        ];
    }
}

