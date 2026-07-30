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
            'name'       => $this->name,
            'email'      => $this->email,
            'name_email' => $this->name . ' (' . $this->email . ')'
        ];
    }
}

