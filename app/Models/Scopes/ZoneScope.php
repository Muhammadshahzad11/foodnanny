<?php

namespace App\Models\Scopes;

use App\Traits\DefaultAccessModelTrait;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;

class ZoneScope implements Scope
{
    use DefaultAccessModelTrait;

    public function apply(Builder $builder, Model $model): void
    {
        if (App::runningInConsole() || !Auth::check()) {
            return;
        }

        $zoneId = $this->zone();
        if ($zoneId <= 0 || $this->restaurant() > 0) {
            return;
        }

        $table = $builder->getQuery()->from;

        if ($model instanceof \App\Models\Zone) {
            $builder->where($table . '.id', $zoneId);
            return;
        }

        $builder->where($table . '.zone_id', $zoneId);
    }
}
