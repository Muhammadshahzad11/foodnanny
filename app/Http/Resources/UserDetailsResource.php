<?php

namespace App\Http\Resources;


use App\Libraries\AppLibrary;
use Illuminate\Http\Resources\Json\JsonResource;

class UserDetailsResource extends JsonResource
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
            "id"                  => $this->id,
            "restaurant_id"       => $this->restaurant_id,
            "name"                => $this->name,
            "first_name"          => $this->FirstName,
            "last_name"           => $this->LastName,
            "phone"               => $this->phone,
            "email"               => $this->email,
            'username'            => $this->username,
            "balance"             => AppLibrary::flatAmountFormat($this->balance),
            "currency_balance"    => AppLibrary::currencyAmountFormat($this->balance),
            "collection"          => AppLibrary::flatAmountFormat($this->collection),
            "currency_collection" => AppLibrary::currencyAmountFormat($this->collection),
            "image"               => $this->image,
            "role_id"             => $this->myRole,
            "role_name"           => optional($this->roles[0])->name,
            "country_code"        => $this->country_code,
            "order"               => $this->orders->count(),
            'create_date'         => AppLibrary::date($this->created_at),
            'update_date'         => AppLibrary::date($this->updated_at),
            "status"              => $this->status,
            "address"             => AddressResource::collection(optional($this->addresses)),
        ];
    }
}
