<?php

namespace App\Http\Resources;


use App\Libraries\AppLibrary;
use Illuminate\Http\Resources\Json\JsonResource;

class PosDetailsResource extends JsonResource
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
            'payment_method'           => $this->payment_method,
            'payment_note'             => $this->payment_note == null ? "" : $this->payment_note,
            'received_currency_amount' => AppLibrary::currencyAmountFormat($this->received_amount)
        ];
    }
}
