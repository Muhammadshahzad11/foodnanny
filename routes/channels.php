<?php

use App\Enums\Role as EnumRole;
use App\Models\DefaultAccess;
use App\Models\User;
use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('chat.{orderId}.{channelType}.{userId}', function ($user, $orderId, $channelType, $userId) {
    return (bool) $user->id == $userId;
});

Broadcast::channel('user.{userId}', function (User $user, int $userId) {
    return (int) $user->id === (int) $userId;
});

Broadcast::channel('admin.ops', function (User $user) {
    return (int) ($user->myrole ?? 0) === EnumRole::ADMIN
        || $user->can('kitchen_view')
        || $user->can('kitchen');
});

Broadcast::channel('restaurant.{restaurantId}.kitchen', function (User $user, int $restaurantId) {
    return canAccessRestaurantChannel($user, $restaurantId, ['kitchen', 'kitchen_view', 'kitchen_dashboard']);
});

Broadcast::channel('restaurant.{restaurantId}.waiter', function (User $user, int $restaurantId) {
    return canAccessRestaurantChannel($user, $restaurantId, ['waiter', 'waiter_dashboard', 'waiter_orders']);
});

Broadcast::channel('restaurant.{restaurantId}.owner', function (User $user, int $restaurantId) {
    $roleId = (int) ($user->myrole ?? 0);
    if ($roleId === EnumRole::ADMIN) {
        return true;
    }
    if (!in_array($roleId, [EnumRole::RESTAURANT_OWNER, EnumRole::MANAGER], true)) {
        // Managers/owners; also allow kitchen managers via permission
        if (!$user->can('kitchen_dashboard') && !$user->can('waiter_dashboard')) {
            return false;
        }
    }

    return belongsToRestaurant($user, $restaurantId);
});

if (!function_exists('belongsToRestaurant')) {
    function belongsToRestaurant(User $user, int $restaurantId): bool
    {
        if ((int) ($user->myrole ?? 0) === EnumRole::ADMIN) {
            return true;
        }

        if ((int) $user->restaurant_id === (int) $restaurantId) {
            return true;
        }

        $default = DefaultAccess::query()
            ->where('user_id', $user->id)
            ->where('name', 'restaurant_id')
            ->value('default_id');

        return (int) $default === (int) $restaurantId;
    }
}

if (!function_exists('canAccessRestaurantChannel')) {
    function canAccessRestaurantChannel(User $user, int $restaurantId, array $permissions): bool
    {
        if (!belongsToRestaurant($user, $restaurantId)) {
            return false;
        }

        $roleId = (int) ($user->myrole ?? 0);
        if (in_array($roleId, [
            EnumRole::ADMIN,
            EnumRole::RESTAURANT_OWNER,
            EnumRole::MANAGER,
            EnumRole::CHEF,
            EnumRole::WAITER,
        ], true)) {
            return true;
        }

        foreach ($permissions as $permission) {
            if ($user->can($permission)) {
                return true;
            }
        }

        return false;
    }
}
