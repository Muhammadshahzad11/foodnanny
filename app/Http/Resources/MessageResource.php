<?php

namespace App\Http\Resources;


use App\Libraries\AppLibrary;
use Illuminate\Http\Resources\Json\JsonResource;

class MessageResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            "id"         => $this->id,
            "order_id"   => $this->order_id,
            "user_id"    => $this->user_id,
            'text'       => $this->text,
            "image"      => $this->user?->image,
            'self'       => $this->user_id == auth()->id(),
            'created_at' => AppLibrary::datetime($this->created_at)
        ];
    }
}
