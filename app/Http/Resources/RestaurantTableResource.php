<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class RestaurantTableResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'            => $this->id,
            'restaurant_id' => $this->restaurant_id,
            'restaurant'    => $this->whenLoaded('restaurant', function () {
                return [
                    'id'   => $this->restaurant?->id,
                    'name' => $this->restaurant?->name,
                ];
            }),
            'table_number'  => $this->table_number,
            'name'          => $this->name,
            'capacity'      => $this->capacity,
            'zone'          => $this->zone,
            'status'        => $this->status,
            'notes'         => $this->notes,
            'created_by'    => $this->whenLoaded('creator', function () {
                return [
                    'id'   => $this->creator?->id,
                    'name' => $this->creator?->name,
                ];
            }),
            'updated_by'    => $this->whenLoaded('editor', function () {
                return [
                    'id'   => $this->editor?->id,
                    'name' => $this->editor?->name,
                ];
            }),
            'creator_id'    => $this->creator_id,
            'editor_id'     => $this->editor_id,
            'created_at'    => $this->created_at,
            'updated_at'    => $this->updated_at,
        ];
    }
}
