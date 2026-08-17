<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CuisineResource extends JsonResource
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
            "id"          => $this->id,
            "name"        => $this->name,
            "slug"        => $this->slug,
            "description" => $this->description === null ? '' : $this->description,
            "status"        => $this->status,
            "show_on_home"  => $this->show_on_home,
            'image'         => $this->image
        ];
    }
}