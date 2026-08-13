<?php

namespace App\Http\Resources;

use App\Libraries\AppLibrary;
use Illuminate\Http\Resources\Json\JsonResource;

class ZoneResource extends JsonResource
{
    public function toArray($request): array
    {
        $polygon = is_array($this->polygon) ? $this->polygon : [];

        return [
            'id'                          => $this->id,
            'name'                        => $this->name,
            'display_name'                => $this->display_name,
            'polygon'                     => $polygon,
            'point_count'                 => max(0, count($polygon) - (count($polygon) > 1 && ($polygon[0]['lat'] ?? null) == ($polygon[count($polygon) - 1]['lat'] ?? null) ? 1 : 0)),
            'status'                      => $this->status,
            'base_delivery_fee'           => $this->base_delivery_fee,
            'base_delivery_fee_flat'      => AppLibrary::flatAmountFormat($this->base_delivery_fee ?? 0),
            'min_order_amount'            => $this->min_order_amount,
            'free_delivery_above'         => $this->free_delivery_above,
            'free_delivery_km'            => $this->free_delivery_km,
            'extra_distance_charge'       => $this->extra_distance_charge,
            'peak_enabled'                => (int) $this->peak_enabled,
            'peak_charge'                 => $this->peak_charge,
            'restaurants_count'           => $this->restaurants_count ?? $this->restaurants()->count(),
            'restaurants'                 => $this->whenLoaded('restaurants', fn () => $this->restaurants->map(fn ($r) => [
                'id'   => $r->id,
                'name' => $r->name,
            ])->values()),
            'admins'                      => $this->whenLoaded('admins', fn () => $this->admins->map(fn ($u) => [
                'id'    => $u->id,
                'name'  => $u->name,
                'email' => $u->email,
                'phone' => $u->phone,
            ])->values()),
            'admin_name'                  => $this->whenLoaded('admins', fn () => $this->admins->first()?->name),
            'delivery_boys'               => $this->whenLoaded('deliveryBoys', fn () => $this->deliveryBoys->map(fn ($u) => [
                'id'    => $u->id,
                'name'  => $u->name,
                'email' => $u->email,
                'phone' => $u->phone,
            ])->values()),
            'created_at'                  => $this->created_at?->toDateTimeString(),
            'updated_at'                  => $this->updated_at?->toDateTimeString(),
        ];
    }
}
