<?php

namespace App\Services;

use App\Models\Restaurant;
use Illuminate\Support\Facades\Auth;

class RestaurantModuleService
{
    public const POS = 'pos';
    public const KITCHEN = 'kitchen';
    public const WAITER = 'waiter';

    public const MODULES = [self::POS, self::KITCHEN, self::WAITER];

    public function currentRestaurantId(): int
    {
        $id = 0;
        try {
            $access = app(DefaultAccessService::class)->show();
            if (isset($access['restaurant_id'])) {
                $id = (int) $access['restaurant_id'];
            }
        } catch (\Exception) {
        }

        if ($id === 0) {
            $id = (int) (Auth::user()?->restaurant_id ?? 0);
        }

        return $id;
    }

    public function currentRestaurant(): ?Restaurant
    {
        $id = $this->currentRestaurantId();
        if ($id <= 0) {
            return null;
        }

        return Restaurant::withoutGlobalScopes()->find($id);
    }

    public function disabledModules(?Restaurant $restaurant = null): array
    {
        $restaurant ??= $this->currentRestaurant();
        if (!$restaurant) {
            return [];
        }

        $disabled = [];
        foreach (self::MODULES as $module) {
            if (!$restaurant->moduleEnabled($module)) {
                $disabled[] = $module;
            }
        }

        return $disabled;
    }

    public function permissionMatchesModule(string $name, string $module): bool
    {
        return $name === $module
            || str_starts_with($name, $module . '_')
            || str_starts_with($name, $module . '-');
    }

    public function denyDisabledModules($permissions, ?Restaurant $restaurant = null)
    {
        $disabled = $this->disabledModules($restaurant);
        if ($disabled === [] || !$permissions) {
            return $permissions;
        }

        foreach ($permissions as $permission) {
            $name = (string) ($permission->name ?? '');
            if ($name === '') {
                continue;
            }
            foreach ($disabled as $module) {
                if ($this->permissionMatchesModule($name, $module)) {
                    $permission->access = false;
                    break;
                }
            }
        }

        return $permissions;
    }

    public function filterMenus(array $menus, ?Restaurant $restaurant = null): array
    {
        $disabled = $this->disabledModules($restaurant);
        if ($disabled === []) {
            return $menus;
        }

        $blockedUrls = $this->blockedMenuUrls($disabled);
        $out = [];
        foreach ($menus as $menu) {
            $url = (string) ($menu['url'] ?? '');
            if ($url !== '#' && in_array($url, $blockedUrls, true)) {
                continue;
            }
            if (!empty($menu['children']) && is_array($menu['children'])) {
                $menu['children'] = array_values(array_filter(
                    $menu['children'],
                    fn ($child) => !in_array((string) ($child['url'] ?? ''), $blockedUrls, true)
                ));
                if ($url === '#' && $menu['children'] === []) {
                    continue;
                }
            }
            $out[] = $menu;
        }

        return $out;
    }

    public function assertEnabled(string $module): void
    {
        $restaurant = $this->currentRestaurant();
        if (!$restaurant) {
            return;
        }

        if (!$restaurant->moduleEnabled($module)) {
            abort(403, trans('all.message.restaurant_module_disabled'));
        }
    }

    private function blockedMenuUrls(array $disabledModules): array
    {
        $urls = [];
        foreach ($disabledModules as $module) {
            $urls[] = $module;
            if ($module === self::POS) {
                $urls[] = 'pos-orders';
            }
        }

        return $urls;
    }
}
