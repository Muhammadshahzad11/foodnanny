<?php

namespace App\Http\Resources;

use App\Libraries\AppLibrary;
use Illuminate\Http\Resources\Json\JsonResource;

class CampaignResource extends JsonResource
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
            'title'          => $this->title,
            'tag'            => $this->tag,
            "slug"           => $this->slug,
            'amount'         => $this->amount,
            "flat_amount"    => AppLibrary::flatAmountFormat($this->amount),
            "convert_amount" => AppLibrary::convertAmountFormat($this->amount),
            'description'    => $this->description === null ? '' : $this->description,
            'start_date'     => $this->start_date,
            'end_date'       => $this->end_date,
            'convert_date'   => AppLibrary::date($this->start_date) . ' - ' . AppLibrary::date($this->end_date),
            'start_time'     => $this->start_time,
            'end_time'       => $this->end_time,
            'convert_time'   => AppLibrary::time($this->start_time) . ' - ' . AppLibrary::time($this->end_time),
            'type'           => $this->type,
            'status'         => $this->status,
            'thumbnail'      => $this->thumb,
            'cover'          => $this->cover,
            'restaurants'    => RestaurantResource::collection($this->restaurants)
        ];
    }
}
