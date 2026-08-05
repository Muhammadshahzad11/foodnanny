<?php

namespace App\Http\Resources;
use Illuminate\Http\Resources\Json\JsonResource;

class AutoLocalizationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray($request): array
    {
        return [
            "id"           => $this->id,
            "name"         => $this->name,
            "code"         => $this->code,
            "display_mode" => $this->display_mode,
            "status"       => $this->status,
            'image'        => $this->image
        ];
    }
}
