<?php

namespace App\Http\Resources;

use App\Libraries\AppLibrary;
use Illuminate\Http\Resources\Json\JsonResource;

class CollectionResource extends JsonResource
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
            'id'                     => $this->id,
            'source_user_id'         => $this->source_user_id,
            'destination_user_id'    => $this->destination_user_id,
            'source_user_name'       => $this->user?->name,
            'destination_user_name'  => $this?->destinationUser?->name,
            'source_user_email'      => $this?->user?->email,
            'destination_user_email' => $this?->destinationUser?->email,
            'date'                   => AppLibrary::date($this->date),
            'time'                   => AppLibrary::time($this->date),
            'date_time'              => AppLibrary::datetime($this->date),
            'flat_amount'            => AppLibrary::flatAmountFormat($this->amount),
            'convert_amount'         => AppLibrary::convertAmountFormat($this->amount)
        ];
    }
}
