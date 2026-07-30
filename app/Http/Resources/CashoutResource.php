<?php

namespace App\Http\Resources;

use App\Libraries\AppLibrary;
use Illuminate\Http\Resources\Json\JsonResource;

class CashoutResource extends JsonResource
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
            'id'           => $this->id,
            'user_name'    => $this?->user?->name,
            'user_email'   => $this?->user?->email,
            'flat_amount'  => AppLibrary::flatAmountFormat($this->amount),
            'convert_date' => AppLibrary::dateTime($this->date)
        ];
    }
}
