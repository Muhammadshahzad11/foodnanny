<?php

namespace App\Http\Resources;

use App\Libraries\AppLibrary;
use Illuminate\Http\Resources\Json\JsonResource;

class CampaignRestaurantResource extends JsonResource
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
            'id'                       => $this->id,
            'restaurant_id'            => $this->restaurant_id,
            'campaign_id'              => $this->campaign_id,
            'status'                   => $this->status,
            'apply'                    => $this->apply,
            'date'                     => AppLibrary::datetime($this->created_at),
            'campaign_title'           => optional($this->campaign)->title,
            'campaign_restaurant_name' => optional($this->restaurant)->name,
        ];
    }
}
