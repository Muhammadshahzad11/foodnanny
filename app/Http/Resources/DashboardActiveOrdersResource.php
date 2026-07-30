<?php

namespace App\Http\Resources;

use App\Libraries\AppLibrary;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DashboardActiveOrdersResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'                 => $this->id,
            'serial_no'          => $this->order_serial_no,
            'date'               => AppLibrary::datetime($this->order_datetime),
            'status'             => $this->status,
            'status_name'        => trans('order_status.' . $this->status),
            'restaurant_address' => $this->restaurant?->address,
        ];
    }
}
