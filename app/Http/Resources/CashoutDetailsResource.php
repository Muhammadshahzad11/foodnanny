<?php

namespace App\Http\Resources;

use App\Libraries\AppLibrary;
use Illuminate\Http\Resources\Json\JsonResource;

class CashoutDetailsResource extends JsonResource
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
           'id'             => $this->id,
           'user_id'        => $this->user_id,
           'user_name'      => $this?->user?->name,
           'user_email'     => $this?->user?->email,
           'amount'         => $this->amount,
           'flat_amount'    => AppLibrary::flatAmountFormat($this->amount),
           'convert_amount' => AppLibrary::convertAmountFormat($this->amount),
           'date'           => $this->date,
           'convert_date'   => AppLibrary::dateTime($this->date),
           'transaction_id' => $this->transaction_id,
           'remarks'        => $this->remarks,
           'file'           => $this->file
        ];
    }
}
