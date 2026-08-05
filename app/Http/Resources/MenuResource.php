<?php

namespace App\Http\Resources;


use Illuminate\Http\Resources\Json\JsonResource;

class MenuResource extends JsonResource
{
    public function toArray($request): array|\JsonSerializable|\Illuminate\Contracts\Support\Arrayable
    {
        $array = [
            'id'       => $this['id'],
            'name'     => $this['name'],
            'language' => $this['language'],
            'url'      => $this['url'],
            'icon'     => $this['icon'],
            'status'   => $this['status'],
            'parent'   => $this['parent'],
            'type'     => $this['type'],
            'priority' => $this['priority'],
            'addon'    => $this['addon']
        ];

        if (isset($this['children'])) {
            $array['children'] = MenuResource::collection($this['children']);
        }
        return $array;
    }
}
