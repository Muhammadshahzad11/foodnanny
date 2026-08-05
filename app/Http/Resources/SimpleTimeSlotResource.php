<?php

namespace App\Http\Resources;


use App\Libraries\AppLibrary;
use Illuminate\Http\Resources\Json\JsonResource;

class SimpleTimeSlotResource extends JsonResource
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
            "id"            => $this->id,
            "restaurant_id" => $this->restaurant_id,
            "opening_time"  => $this->opening_time === null ? '' : AppLibrary::time($this->opening_time),
            "closing_time"  => $this->closing_time === null ? '' : AppLibrary::time($this->closing_time),
            "day"           => $this->day === null ? '' : $this->day,
        ];
    }
}
