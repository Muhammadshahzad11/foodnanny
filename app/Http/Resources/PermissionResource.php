<?php

namespace App\Http\Resources;


use App\Enums\PermissionType;
use Illuminate\Http\Resources\Json\JsonResource;

class PermissionResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'     => $this->id,
            'title'  => $this->title,
            'name'   => $this->name,
            'url'    => $this->url,
            'type'   => $this->type == PermissionType::RESTAURANT_OWNER || $this->type == PermissionType::BOTH,
            'access' => $this->access ?? false
        ];
    }

}
