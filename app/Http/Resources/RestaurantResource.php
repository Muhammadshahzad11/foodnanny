<?php

namespace App\Http\Resources;

use App\Libraries\AppLibrary;
use Illuminate\Http\Resources\Json\JsonResource;

class RestaurantResource extends JsonResource
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
            "id"                     => $this->id,
            "name"                   => $this->name,
            "email"                  => $this->email === null ? '' : $this->email,
            "country_code"           => $this->country_code ?? '',
            "phone"                  => $this->phone === null ? '' : $this->phone,
            "latitude"               => $this->latitude === null ? '' : $this->latitude,
            "longitude"              => $this->longitude === null ? '' : $this->longitude,
            "city"                   => $this->city,
            "state"                  => $this->state,
            "zip_code"               => $this->zip_code,
            "address"                => $this->address,
            "status"                 => $this->status,
            "current_status"         => $this->current_status,
            "apply"                  => $this->apply,
            "logo"                   => $this->logo,
            "image"                  => $this->image,
            "online_commission"      => $this->online_commission === null ? '' : $this->online_commission,
            "flat_online_commission" => $this->online_commission === null ? '' : AppLibrary::flatAmountFormat($this->online_commission),
            "pos_commission"         => $this->pos_commission === null ? '' : $this->pos_commission,
            "flat_pos_commission"    => $this->pos_commission === null ? '' : AppLibrary::flatAmountFormat($this->pos_commission),
            "cuisine_id"             => RestaurantCuisineResource::collection($this->cuisines),
            "cuisine"                => AppLibrary::cuisineString($this->cuisines)
        ];
    }
}
