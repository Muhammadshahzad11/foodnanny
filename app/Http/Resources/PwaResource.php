<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PwaResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'splash' => $this->splash,
            'icon' => $this->icon,
            'name' => $this->name,
            'short_name' => $this->short_name,
            'description' => $this->description,
            'theme_color' => $this->theme_color,
            'background_color' => $this->background_color,
            'orientation' => $this->orientation,
            'display_mode' => $this->display_mode,
            'offline_mode' => (bool) $this->offline_mode,
            'auto_update' => (bool) $this->auto_update,
            'cache_strategy' => $this->cache_strategy,
            'enable_install_popup' => (bool) $this->enable_install_popup,
            'popup_delay_seconds' => (int) $this->popup_delay_seconds,
            'popup_frequency_hours' => (int) $this->popup_frequency_hours,
            'cache_version' => (int) $this->cache_version,
            'force_updated_at' => $this->force_updated_at,
        ];
    }
}
