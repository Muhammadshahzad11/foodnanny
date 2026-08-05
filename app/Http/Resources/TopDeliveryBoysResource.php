<?php

namespace App\Http\Resources;
 
use Illuminate\Http\Resources\Json\JsonResource;

class TopDeliveryBoysResource extends JsonResource
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
            "id"     => $this->id,
            "name"   => $this->name,
            "image"  => $this->image,
            "orders" => $this->delivery_boy_orders_count
        ];
    }
}
