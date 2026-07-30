<?php

namespace App\Http\Resources;

use App\Libraries\AppLibrary;
use Illuminate\Http\Resources\Json\JsonResource;

class OfferRestaurantResource extends JsonResource
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
            'id'                    => $this->id,
            'restaurant_id'         => $this->restaurant_id,
            'offer_id'              => $this->offer_id,
            'status'                => $this->status,
            'apply'                 => $this->apply,
            'date'                  => AppLibrary::datetime($this->created_at),
            'offer_title'           => optional($this->offer)->title,
            'offer_restaurant_name' => optional($this->restaurant)->name
        ];
    }
}