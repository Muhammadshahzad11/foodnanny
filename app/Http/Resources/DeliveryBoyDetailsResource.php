<?php

namespace App\Http\Resources;


use App\Libraries\AppLibrary;
use Illuminate\Http\Resources\Json\JsonResource;

class DeliveryBoyDetailsResource extends JsonResource
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
            "id"                 => $this->id,
            "name"               => $this->name,
            "username"           => $this->username,
            "email"              => $this->email,
            "restaurant_id"      => $this->restaurant_id,
            "phone"              => $this->phone === null ? '' : $this->phone,
            "status"             => $this->status,
            "role_id"            => $this->roles[0]?->id,
            "role"               => $this->roles[0]?->name,
            "image"              => $this->image,
            "country_code"       => $this->country_code,
            "balance"            => AppLibrary::flatAmountFormat($this->balance),
            "convert_balance"    => AppLibrary::convertAmountFormat($this->balance),
            "collection"         => AppLibrary::flatAmountFormat($this->collection),
            "convert_collection" => AppLibrary::convertAmountFormat($this->collection),
            "address"            => AddressResource::collection(optional($this->addresses)),
            'create_date'        => AppLibrary::date($this->created_at),
            'update_date'        => AppLibrary::date($this->updated_at),
        ];
    }
}
