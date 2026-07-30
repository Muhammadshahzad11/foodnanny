<?php

namespace App\Http\Resources;

use App\Libraries\AppLibrary;
use Illuminate\Http\Resources\Json\JsonResource;

class RiderTipResource extends JsonResource
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
            "id"              => $this->id,
            "label"           => $this->label,
            "amount"          => $this->amount,
            "convert_amount"  => AppLibrary::convertAmountFormat($this->amount),
            "flat_amount"     => AppLibrary::flatAmountFormat($this->amount),
            "currency_amount" => AppLibrary::currencyAmountFormat($this->amount)
        ];
    }
}
