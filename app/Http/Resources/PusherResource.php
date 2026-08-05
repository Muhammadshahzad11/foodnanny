<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PusherResource extends JsonResource
{
    public array $info;

    public function __construct($info)
    {
        parent::__construct($info);
        $this->info = $info;
    }

    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request): array
    {
        return [
            "pusher_app_id"      => $this->info['pusher_app_id'],
            "pusher_app_key"     => $this->info['pusher_app_key'],
            "pusher_app_secret"  => $this->info['pusher_app_secret'],
            "pusher_app_cluster" => $this->info['pusher_app_cluster']
        ];
    }
}
