<?php

namespace App\Http\Resources;

use App\Libraries\AppLibrary;
use Illuminate\Http\Resources\Json\JsonResource;

class AdminCampaignResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'             => $this->id,
            'title'          => $this->resource->getRawOriginal('title'),
            'tag'            => $this->tag,
            'slug'           => $this->slug,
            'amount'         => $this->amount,
            'flat_amount'    => AppLibrary::flatAmountFormat($this->amount),
            'convert_amount' => AppLibrary::convertAmountFormat($this->amount),
            'description'    => $this->resource->getRawOriginal('description') === null ? '' : $this->resource->getRawOriginal('description'),
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
            'restaurants'    => RestaurantResource::collection($this->whenLoaded('restaurants', $this->restaurants)),
            'translations'   => $this->whenLoaded('translations', $this->translations),
        ];
    }
}
