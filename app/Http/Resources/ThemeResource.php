<?php

namespace App\Http\Resources;

use App\Models\ThemeSetting;
use Illuminate\Http\Resources\Json\JsonResource;

class ThemeResource extends JsonResource
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
    public function toArray($request) : array
    {
        return [
            "theme_logo"            => $this->themeImage('theme_logo')->logo,
            "theme_favicon_logo"    => $this->themeImage('theme_favicon_logo')->faviconLogo,
            "theme_footer_logo"     => $this->themeImage('theme_footer_logo')->footerLogo,
            "theme_primary_color"   => $this->themeColor('theme_primary_color', '#148A3C'),
            "theme_secondary_color" => $this->themeColor('theme_secondary_color', '#0A3D28')
        ];
    }

    public function themeImage($key)
    {
        return ThemeSetting::where(['key' => $key])->first();
    }

    public function themeColor($key, $default)
    {
        $row = ThemeSetting::where(['group' => 'theme', 'key' => $key])->first();
        return ($row && $row->color) ? $row->color : $default;
    }
}
