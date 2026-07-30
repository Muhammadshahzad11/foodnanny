<?php

namespace App\Traits;

use App\Models\Translation;

trait Translatable
{
    public function translations(): \Illuminate\Database\Eloquent\Relations\MorphMany
    {
        return $this->morphMany(Translation::class, 'translatable');
    }

    public function getTranslation(string $key, ?string $locale = null): mixed
    {
        $locale = $locale ?: request()->header('x-localization');

        if (!$locale || !$this->relationLoaded('translations')) {
            return $this->attributes[$key] ?? null;
        }

        $translation = $this->translations
            ->where('locale', $locale)
            ->where('key', $key)
            ->first();

        if ($translation && !empty($translation->value)) {
            return $translation->value;
        }

        return $this->attributes[$key] ?? null;
    }
}
