<?php

namespace App\Http\Resources;

use App\Libraries\AppLibrary;
use Illuminate\Http\Resources\Json\JsonResource;

class PayoutResource extends JsonResource
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
            'amount'       => AppLibrary::flatAmountFormat($this->amount),
            'name'         => $this->model?->name,
            'email'        => $this->model?->email,
            'phone'        => $this->model?->phone,
            'country_code' => $this->model?->country_code,
            'date'         => AppLibrary::datetime($this->date)
        ];
    }
}
