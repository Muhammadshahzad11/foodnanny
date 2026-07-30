<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RevenueChartResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'series' => [
                [
                    'name' => trans('all.label.total_sale'),
                    'data' => array_values($this['total_sale'])
                ],
                [
                    'name' => trans('all.label.admin_commission'),
                    'data' => array_values($this['admin_commission'])
                ]
            ]
        ];
    }
}
