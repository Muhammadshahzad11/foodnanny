<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class RestaurantTableQrResource extends JsonResource
{
    public function toArray($request): array
    {
        $includeToken = $request->boolean('include_token');

        return [
            'id'              => $this->id,
            'uuid'            => $this->uuid,
            'restaurant_id'   => $this->restaurant_id,
            'restaurant'      => $this->whenLoaded('restaurant', function () {
                return [
                    'id'   => $this->restaurant?->id,
                    'name' => $this->restaurant?->name,
                    'slug' => $this->restaurant?->slug,
                ];
            }),
            'table_number'    => $this->table_number,
            'name'            => $this->name,
            'zone'            => $this->zone,
            'status'          => $this->status,
            'has_qr'          => $this->hasQr(),
            'qr_version'      => $this->qr_version,
            'qr_generated_at' => $this->qr_generated_at,
            'qr_url'          => $this->qr_url,
            'qr_image_url'    => $this->qrImageUrl(),
            'qr_token'        => $this->when($includeToken, $this->qr_token),
        ];
    }
}
