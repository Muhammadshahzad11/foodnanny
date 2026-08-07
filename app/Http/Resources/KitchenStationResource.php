<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class KitchenStationResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'            => $this->id,
            'restaurant_id' => $this->restaurant_id,
            'outlet'        => $this->restaurant?->name,
            'name'          => $this->name,
            'code'          => $this->code,
            'sort_order'    => $this->sort_order,
            'status'        => $this->status,
            'printer_id'    => $this->printer_id,
            'printer'       => $this->whenLoaded('printer', function () {
                return $this->printer ? [
                    'id'            => $this->printer->id,
                    'name'          => $this->printer->name,
                    'print_format'  => $this->printer->print_format,
                    'printing_choice' => $this->printer->printing_choice,
                ] : null;
            }),
            'category_ids'  => $this->whenLoaded('categories', fn () => $this->categories->pluck('id')->values()),
            'categories'    => $this->whenLoaded('categories', function () {
                return $this->categories->map(fn ($c) => [
                    'id'   => $c->id,
                    'name' => $c->name,
                ])->values();
            }),
        ];
    }
}
