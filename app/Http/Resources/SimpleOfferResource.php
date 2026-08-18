<?php

namespace App\Http\Resources;


use App\Enums\Ask;
use App\Libraries\AppLibrary;
use Illuminate\Http\Resources\Json\JsonResource;

class SimpleOfferResource extends JsonResource
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
            'id'          => $this->id,
            'title'       => $this->title,
            'tag'         => $this->tag,
            'slug'        => $this->slug,
            'amount'      => (float)$this->amount,
            'percentage'  => ((float) $this->amount > 0) ? AppLibrary::convertAmountFormat($this->amount) . '%' : '',
            'option'      => $this->is_single == Ask::YES ? 'single' : 'multi',
            'type'        => 'offer',
            'start_time'  => AppLibrary::time($this->start_time),
            'end_time'    => AppLibrary::time($this->end_time),
            'start_date'  => AppLibrary::date($this->start_date),
            'end_date'    => AppLibrary::date($this->end_date),
            'thumb'       => $this->thumb,
            'cover'       => $this->cover,
            'restaurants' => SimpleOfferRestaurantResource::collection($this->offerRestaurants),
            'description' => $this->description
        ];
    }
}
