<?php

namespace App\Traits;

use App\Models\User;
use Illuminate\Support\Facades\Auth;

trait HasModelMeta
{
    public static function bootHasModelMeta(): void
    {
        static::creating(function ($model) {
            if (Auth::check()) {
                $model->creator_type = get_class(Auth::user());
                $model->creator_id   = Auth::id();
                $model->editor_type  = get_class(Auth::user());
                $model->editor_id    = Auth::id();
            } else {
                $model->creator_type = User::class;
                $model->creator_id   = 1;
                $model->editor_type  = User::class;
                $model->editor_id    = 1;
            }
        });

        static::updating(function ($model) {
            if (Auth::check()) {
                $model->editor_type = get_class(Auth::user());
                $model->editor_id   = Auth::id();
            }
        });
    }
}
