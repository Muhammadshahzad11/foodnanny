<?php

namespace App\Http\Resources;

use App\Enums\DeliveryChargeType;
use App\Libraries\AppLibrary;
use Illuminate\Http\Resources\Json\JsonResource;

class RestaurantDeliveryZoneResource extends JsonResource
{
    public function toArray($request): array
    {
        $polygon = is_array($this->polygon) ? $this->polygon : [];

        return [
            'id'                          => $this->id,
            'restaurant_id'               => $this->restaurant_id,
            'restaurant_name'             => $this->whenLoaded('restaurant', fn () => $this->restaurant?->name),
            'restaurant_latitude'         => $this->whenLoaded('restaurant', fn () => $this->restaurant?->latitude),
            'restaurant_longitude'        => $this->whenLoaded('restaurant', fn () => $this->restaurant?->longitude),
            'name'                        => $this->name,
            'display_name'                => $this->display_name,
            'polygon'                     => $polygon,
            'point_count'                 => max(0, count($polygon) - (count($polygon) > 1 && ($polygon[0]['lat'] ?? null) == ($polygon[count($polygon) - 1]['lat'] ?? null) ? 1 : 0)),
            'status'                      => $this->status,
            'charge_type'                 => $this->charge_type,
            'charge_type_label'           => DeliveryChargeType::LABELS[$this->charge_type] ?? 'per_km',
            'min_delivery_charge'         => $this->min_delivery_charge,
            'min_delivery_charge_flat'    => AppLibrary::flatAmountFormat($this->min_delivery_charge ?? 0),
            'max_delivery_charge'         => $this->max_delivery_charge,
            'max_delivery_charge_flat'    => AppLibrary::flatAmountFormat($this->max_delivery_charge ?? 0),
            'charge_per_km'               => $this->charge_per_km,
            'charge_per_km_flat'          => AppLibrary::flatAmountFormat($this->charge_per_km ?? 0),
            'max_cod_amount'              => $this->max_cod_amount,
            'max_cod_amount_flat'         => AppLibrary::flatAmountFormat($this->max_cod_amount ?? 0),
            'additional_delivery_charge'  => $this->additional_delivery_charge,
            'additional_delivery_charge_flat' => AppLibrary::flatAmountFormat($this->additional_delivery_charge ?? 0),
            'created_at'                  => $this->created_at?->toDateTimeString(),
            'updated_at'                  => $this->updated_at?->toDateTimeString(),
        ];
    }
}
