<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AdminSimpleItemCategoryResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'            => $this->id,
            'restaurant_id' => $this->restaurant_id,
            'name'          => $this->resource->getRawOriginal('name'),
            'slug'          => $this->slug,
            'description'   => $this->resource->getRawOriginal('description') === null ? '' : $this->resource->getRawOriginal('description'),
            'status'        => $this->status,
            'sort'          => $this->sort,
            'thumb'         => $this->thumb,
            'cover'         => $this->cover,
        ];
    }
}
