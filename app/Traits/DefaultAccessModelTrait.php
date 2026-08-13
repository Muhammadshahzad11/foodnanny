<?php

namespace App\Traits;

use App\Enums\Role as EnumRole;
use App\Models\DefaultAccess;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;

trait DefaultAccessModelTrait
{
    public function restaurant()
    {
        if (!App::runningInConsole() && Auth::check()) {
            $access = DefaultAccess::where(['user_id' => Auth::id()])->get()->pluck('default_id', 'name');
            if (count($access)) {
                if($access['restaurant_id']) {
                    return $access['restaurant_id'];
                }
            }
        }
        return 0;
    }

    /**
     * Assigned platform zone for Zone Admins. Super Admin returns 0 (all zones).
     */
    public function zone(): int
    {
        if (App::runningInConsole() || !Auth::check()) {
            return 0;
        }

        $user = Auth::user();
        if ((int) ($user->myrole ?? 0) !== EnumRole::ZONE_ADMIN) {
            return 0;
        }

        return (int) ($user->zone_id ?? 0);
    }

    public function setRestaurant($restaurantId)
    {
        if (!App::runningInConsole() && Auth::check()) {
            if($restaurantId == 0 || $restaurantId == '' && $restaurantId == null) {
                return $this->restaurant();
            }
        }
        return $restaurantId;
    }
}
