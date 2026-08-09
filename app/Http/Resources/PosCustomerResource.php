<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PosCustomerResource extends JsonResource
{
    public function toArray($request): array
    {
        $address = $this->addresses?->first();

        return [
            'id'      => $this->id,
            'name'    => $this->name,
            'email'   => $this->email,
            'phone'   => $this->phone,
            'address' => $address?->address
                ?? $address?->apartment
                ?? null,
        ];
    }
}
