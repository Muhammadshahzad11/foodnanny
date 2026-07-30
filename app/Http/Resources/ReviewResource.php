<?php

namespace App\Http\Resources;

use App\Enums\ReviewType;
use App\Libraries\AppLibrary;
use App\Models\Restaurant;
use Illuminate\Http\Resources\Json\JsonResource;

class ReviewResource extends JsonResource
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
            'id'       => $this->id,
            'reviewer' => $this->user->name,
            'type'     => $this->model_type == Restaurant::class ? ReviewType::RESTAURANT : ReviewType::DELIVERY_BOY,
            'star'     => $this->star,
            'review'   => $this->review,
            'name'     => $this->model?->name,
            'profile'  => $this->model?->image,
            'date'     => AppLibrary::datetime($this->created_at)
        ];
    }
}
