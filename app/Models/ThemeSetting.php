<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class ThemeSetting extends Model implements HasMedia
{
    use InteractsWithMedia;

    protected $table = "settings";

    public function getLogoAttribute() : string
    {
        if (!empty($this->getFirstMediaUrl('theme-logo'))) {
            return asset($this->getFirstMediaUrl('theme-logo'));
        }
        return asset('images/default/theme/logo.png');
    }

    public function getFaviconLogoAttribute() : string
    {
        if (!empty($this->getFirstMediaUrl('theme-favicon-logo'))) {
            return asset($this->getFirstMediaUrl('theme-favicon-logo'));
        }
        return asset('images/default/theme/favicon.png');
    }

    public function getFooterLogoAttribute() : string
    {
        if (!empty($this->getFirstMediaUrl('theme-footer-logo'))) {
            return asset($this->getFirstMediaUrl('theme-footer-logo'));
        }
        return asset('images/default/theme/footer-logo.png');
    }

    /**
     * Plain settings payload value, read straight from the database so it is never
     * served stale from the settings package cache (which set() does not bust per key).
     */
    public function getColorAttribute() : ?string
    {
        $payload = json_decode((string) $this->getRawOriginal('payload'), true);
        if (is_array($payload) && array_key_exists('$value', $payload)) {
            return $payload['$value'];
        }
        return is_string($this->payload) ? $this->payload : null;
    }
}
