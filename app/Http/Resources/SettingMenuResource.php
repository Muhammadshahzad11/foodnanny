<?php

namespace App\Http\Resources;


use Illuminate\Http\Resources\Json\JsonResource;

class SettingMenuResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'       => $this->id,
            'name'     => $this->name,
            'language' => $this->language,
            'url'      => $this->url,
            'icon'     => $this->icon,
            'status'   => $this->status,
            'type'     => $this->type,
            'priority' => $this->priority,
            'addon'    => $this->addon
        ];
    }
}
