<?php

namespace App\Http\Resources;


use App\Libraries\AppLibrary;
use Illuminate\Http\Resources\Json\JsonResource;

class SimpleCampaignResource extends JsonResource
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
            'slug'        => $this->slug,
            'type'        => 'campaign',
            'start_time'  => AppLibrary::time($this->start_time),
            'end_time'    => AppLibrary::time($this->end_time),
            'start_date'  => AppLibrary::date($this->start_date),
            'end_date'    => AppLibrary::date($this->end_date),
            'thumb'       => $this->thumb,
            'cover'       => $this->cover,
            'restaurants' => SimpleOfferRestaurantResource::collection($this->campaignRestaurants),
            'description' => $this->description
        ];
    }
}
