<?php

namespace App\Http\Resources;

use App\Libraries\AppLibrary;
use Illuminate\Http\Resources\Json\JsonResource;

class WaiterTableResource extends JsonResource
{
    public function toArray($request): array
    {
        $openOrder = $this->relationLoaded('orders') ? $this->orders->first() : null;

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
            'open_order'    => $openOrder ? new WaiterOrderResource($openOrder) : null,
            'open_order_total_currency' => $openOrder
                ? AppLibrary::currencyAmountFormat($openOrder->total)
                : null,
            'created_at'    => $this->created_at,
            'updated_at'    => $this->updated_at,
        ];
    }
}
