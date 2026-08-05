<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class MessageUserResource extends JsonResource
{

    public function toArray($request): array
    {
        return [
            "id"              => $this->id,
            "user_id"         => $this->user_id,
            "name"            => $this->user->name,
            "image"           => $this->user->image,
            "order_serial_no" => $this->order_serial_no
        ];
    }
}
