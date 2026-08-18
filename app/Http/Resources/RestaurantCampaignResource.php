<?php

namespace App\Http\Resources;

use App\Enums\Ask;
use App\Libraries\AppLibrary;
use App\Traits\DefaultAccessModelTrait;
use Illuminate\Http\Resources\Json\JsonResource;

class RestaurantCampaignResource extends JsonResource
{
    use DefaultAccessModelTrait;
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
            'image'          => $this->cover,
            'apply'          => $this->restaurants()->where('restaurant_id', $this->restaurant())->exists() ? Ask::YES : Ask::NO,
            'apply_status'   => $this->campaignRestaurants()->where('restaurant_id', $this->restaurant())->value('status')
        ];
    }
}