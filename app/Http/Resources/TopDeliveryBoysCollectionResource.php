<?php

namespace App\Http\Resources;


use App\Libraries\AppLibrary;
use Illuminate\Http\Resources\Json\JsonResource;

class TopDeliveryBoysCollectionResource extends JsonResource
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
            'id'         => $this->id,
            'name'       => $this->name,
            'image'      => $this->file_name == null ? asset('images/required/profile.png') : asset('storage/'."{$this->media_id}/".$this->file_name),
            'collection' => AppLibrary::currencyAmountFormat($this->total_collection)
        ];
    }
}
