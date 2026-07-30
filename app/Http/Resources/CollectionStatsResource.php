<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CollectionStatsResource extends JsonResource
{
    public array $info;

    public function __construct($info)
    {
        parent::__construct($info);
        $this->info = $info;
    }

    public function toArray($request): array
    {
        return [
            "total_collection"       => $this->info['total_collection'],
            "avg_per_day_collection" => $this->info['avg_per_day_collection'],
            "per_day_collections"    => $this->info['per_day_collections'],
        ];
    }
}
