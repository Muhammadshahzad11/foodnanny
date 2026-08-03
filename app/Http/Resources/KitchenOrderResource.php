<?php

namespace App\Http\Resources;

use App\Libraries\AppLibrary;
use Illuminate\Http\Resources\Json\JsonResource;

class KitchenOrderResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'                        => $this->id,
            'order_serial_no'           => $this->order_serial_no,
            'token'                     => $this->token,
            'order_type'                => $this->order_type,
            'status'                    => $this->status,
            'status_name'               => trans('order_status.' . $this->status),
            'active'                    => $this->active,
            'order_note'                => $this->order_note,
            'kitchen_priority'          => $this->kitchen_priority,
            'preparation_time'          => $this->preparation_time,
            'order_datetime'            => AppLibrary::datetime($this->order_datetime),
            'order_time'                => AppLibrary::time($this->order_datetime),
            'updated_at'                => optional($this->updated_at)?->toIso8601String(),
            'kitchen_accepted_at'       => optional($this->kitchen_accepted_at)?->toIso8601String(),
            'table'                     => $this->whenLoaded('diningTable', function () {
                return $this->diningTable ? [
                    'id'           => $this->diningTable->id,
                    'name'         => $this->diningTable->name,
                    'table_number' => $this->diningTable->table_number,
                    'zone'         => $this->diningTable->zone,
                ] : null;
            }),
            'waiter'                    => $this->whenLoaded('waiter', function () {
                return $this->waiter ? [
                    'id'   => $this->waiter->id,
                    'name' => $this->waiter->name,
                ] : null;
            }),
            'customer'                  => $this->whenLoaded('user', function () {
                return $this->user ? [
                    'id'   => $this->user->id,
                    'name' => $this->user->name,
                ] : null;
            }),
            'restaurant'                => $this->whenLoaded('restaurant', function () {
                return [
                    'id'   => $this->restaurant?->id,
                    'name' => $this->restaurant?->name,
                ];
            }),
            'station'                   => $this->whenLoaded('kitchenStation', function () {
                return $this->kitchenStation ? [
                    'id'   => $this->kitchenStation->id,
                    'name' => $this->kitchenStation->name,
                    'code' => $this->kitchenStation->code,
                ] : null;
            }),
            'accepted_by'               => $this->whenLoaded('kitchenAcceptedBy', function () {
                return $this->kitchenAcceptedBy ? [
                    'id'   => $this->kitchenAcceptedBy->id,
                    'name' => $this->kitchenAcceptedBy->name,
                ] : null;
            }),
            'preparing_by'              => $this->whenLoaded('kitchenPreparingBy', function () {
                return $this->kitchenPreparingBy ? [
                    'id'   => $this->kitchenPreparingBy->id,
                    'name' => $this->kitchenPreparingBy->name,
                ] : null;
            }),
            'ready_by'                  => $this->whenLoaded('kitchenReadyBy', function () {
                return $this->kitchenReadyBy ? [
                    'id'   => $this->kitchenReadyBy->id,
                    'name' => $this->kitchenReadyBy->name,
                ] : null;
            }),
            'order_items'               => $this->whenLoaded('orderItems', function () {
                return $this->orderItems->map(function ($item) {
                    return [
                        'id'               => $item->id,
                        'item_id'          => $item->item_id,
                        'item_name'        => $item->orderItem?->name,
                        'quantity'         => $item->quantity,
                        'instruction'      => $item->instruction,
                        'item_variations'  => json_decode($item->item_variations, true),
                        'item_extras'      => json_decode($item->item_extras, true),
                        'kitchen_status'   => $item->kitchen_status,
                        'kitchen_station_id' => $item->kitchen_station_id,
                    ];
                });
            }),
        ];
    }
}
