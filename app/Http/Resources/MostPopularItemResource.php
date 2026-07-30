<?php

namespace App\Http\Resources;

use App\Libraries\AppLibrary;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MostPopularItemResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'name'        => $this->name,
            'category'    => $this->category?->name,
            'order_count' => $this->orders_count,
            'thumb'       => $this->thumb,
            'price'       => AppLibrary::currencyAmountFormat($this->price)
        ];
    }
}
