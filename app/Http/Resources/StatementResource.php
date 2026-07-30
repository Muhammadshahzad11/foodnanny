<?php

namespace App\Http\Resources;

use App\Libraries\AppLibrary;
use Illuminate\Http\Resources\Json\JsonResource;

class StatementResource extends JsonResource
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
            'id'              => $this->id,
            'date'            => AppLibrary::datetime($this->date),
            'order_id'        => $this->order_id,
            'type'            => $this->type ?? '',
            'detail'          => $this->detail ?? '',
            'sign'            => $this->sign,
            'order_serial_no' => $this->order?->order_serial_no ?? 'N/A',
            'amount'          => AppLibrary::flatAmountFormat(abs($this->amount)),
            'info'            => json_decode($this->info)
        ];
    }
}
