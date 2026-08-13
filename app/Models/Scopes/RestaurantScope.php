<?php

namespace App\Models\Scopes;

use App\Traits\DefaultAccessModelTrait;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;

class RestaurantScope implements Scope
{
    use DefaultAccessModelTrait;

    public function apply(Builder $builder, Model $model): void
    {
        if (App::runningInConsole() || !Auth::check()) {
            return;
        }

        if ($this->restaurant() > 0) {
            $field = sprintf('%s.%s', $builder->getQuery()->from, 'restaurant_id');
            $builder->where($field, '=', $this->restaurant());
            return;
        }

        $zoneId = $this->zone();
        if ($zoneId > 0) {
            $field = sprintf('%s.%s', $builder->getQuery()->from, 'restaurant_id');
            $builder->whereIn($field, function ($query) use ($zoneId) {
                $query->select('id')->from('restaurants')->where('zone_id', $zoneId);
            });
        }
    }
}
