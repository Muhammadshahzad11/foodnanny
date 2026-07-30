<?php

namespace App\Http\Resources;
use Illuminate\Http\Resources\Json\JsonResource;

class BenefitResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray($request): array
    {
        return [
            'id'          => $this->id,
            'title'       => $this->title,
            'description' => $this->description,
            'status'      => $this->status,
            'sort'        => $this->sort,
            'thumb'       => $this->thumb,
            'cover'       => $this->cover
        ];
    }
}
