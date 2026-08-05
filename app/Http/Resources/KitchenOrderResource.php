<?php

namespace App\Http\Resources;

use App\Enums\KitchenPriority;
use App\Libraries\AppLibrary;
use Illuminate\Http\Resources\Json\JsonResource;

class KitchenOrderResource extends JsonResource
{
    public function toArray($request): array
    {
        $priority = (int) $this->kitchen_priority;

        return [
            'id'                        => $this->id,
            'order_serial_no'           => $this->order_serial_no,
            'token'                     => $this->token,
            'order_type'                => $this->order_type,
            'status'                    => $this->status,
            'status_name'               => trans('order_status.' . $this->status),
            'active'                    => $this->active,
            'order_note'                => $this->order_note,
            'reason'                    => $this->reason,
            'kitchen_priority'          => $priority,
            'priority_label'            => KitchenPriority::LABELS[$priority] ?? 'normal',
            'preparation_time'          => $this->preparation_time,
            'order_datetime'            => AppLibrary::datetime($this->order_datetime),
            'order_datetime_iso'        => optional($this->order_datetime)?->toIso8601String(),
            'order_time'                => AppLibrary::time($this->order_datetime),
            'updated_at'                => optional($this->updated_at)?->toIso8601String(),
            'kitchen_accepted_at'       => optional($this->kitchen_accepted_at)?->toIso8601String(),
            'elapsed_from'              => optional($this->kitchen_accepted_at ?? $this->order_datetime)?->toIso8601String(),
            // Freeze timer end for finished tickets (completed / canceled / rejected)
            'elapsed_to'                => $this->timerIsFrozen()
                ? optional($this->updated_at)?->toIso8601String()
                : null,
            'timer_frozen'              => $this->timerIsFrozen(),
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
                    $variations = json_decode($item->item_variations, true);
                    $extras     = json_decode($item->item_extras, true);

                    return [
                        'id'                 => $item->id,
                        'item_id'            => $item->item_id,
                        'item_name'          => $item->orderItem?->name,
                        'quantity'           => $item->quantity,
                        'instruction'        => $item->instruction,
                        'item_variations'    => $variations,
                        'item_extras'        => $extras,
                        'variation_lines'    => $this->formatVariationLines($variations),
                        'extra_lines'        => $this->formatExtraLines($extras),
                        'kitchen_status'     => $item->kitchen_status,
                        'kitchen_station_id' => $item->kitchen_station_id,
                    ];
                });
            }),
        ];
    }

    protected function timerIsFrozen(): bool
    {
        return in_array((int) $this->status, [
            \App\Enums\OrderStatus::DELIVERED,
            \App\Enums\OrderStatus::CANCELED,
            \App\Enums\OrderStatus::REJECTED,
            \App\Enums\OrderStatus::RETURNED,
        ], true);
    }

    protected function formatVariationLines($variations): array
    {
        if (!is_array($variations)) {
            return [];
        }
        if (isset($variations['names']) && is_array($variations['names'])) {
            return array_values(array_filter($variations['names']));
        }
        if (array_is_list($variations)) {
            return array_values(array_filter(array_map(
                fn ($v) => is_array($v) ? ($v['name'] ?? $v['variation_name'] ?? null) : null,
                $variations
            )));
        }

        return [];
    }

    protected function formatExtraLines($extras): array
    {
        if (!is_array($extras)) {
            return [];
        }
        if (isset($extras['names']) && is_array($extras['names'])) {
            return array_values(array_filter($extras['names']));
        }
        if (array_is_list($extras)) {
            return array_values(array_filter(array_map(
                fn ($e) => is_array($e) ? ($e['name'] ?? null) : null,
                $extras
            )));
        }

        return [];
    }
}
