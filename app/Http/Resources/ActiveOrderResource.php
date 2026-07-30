<?php

namespace App\Http\Resources;


use App\Libraries\AppLibrary;
use Illuminate\Http\Resources\Json\JsonResource;

class ActiveOrderResource extends JsonResource
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
            'id'                 => $this->id,
            'order_serial_no'    => $this->order_serial_no,
            'order_datetime'     => AppLibrary::datetime($this->order_datetime),
            'total_amount_price' => AppLibrary::flatAmountFormat($this->total),
            'status'             => $this->status,
        ];
    }
}
