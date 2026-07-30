<?php

namespace App\Http\Resources;

use App\Libraries\AppLibrary;
use Illuminate\Http\Resources\Json\JsonResource;

class PayoutDetailsResource extends JsonResource
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
            'country_code' => $this->model?->country_code,
            'phone'        => $this->model?->phone,
            'date'         => AppLibrary::datetime($this->date),
            'created_at'   => $this->created_at
        ];
    }
}
