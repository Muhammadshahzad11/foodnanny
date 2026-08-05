import ENV from "../config/env.js";

/**
 * Subscribe to restaurant private channels for Module 7 realtime.
 * Returns an unsubscribe function.
 */
export function subscribeRestaurantRealtime({restaurantId, roles = [], onKitchenOrder, onTable, onEmployee}) {
    if (!window.Echo || !ENV.PUSHER_KEY || !restaurantId) {
        return () => {};
    }

    const channels = [];
    const leave = [];

    const bind = (name, event, handler) => {
        if (!handler) return;
        const channel = window.Echo.private(name);
        channel.listen(event, handler);
        channels.push(name);
        leave.push(() => {
            try {
                window.Echo.leave(name);
            } catch (e) {
                // ignore
            }
        });
    };

    // Always try kitchen/waiter/owner — auth will reject unauthorized
    bind(`restaurant.${restaurantId}.kitchen`, '.kitchen.order.updated', onKitchenOrder);
    bind(`restaurant.${restaurantId}.waiter`, '.kitchen.order.updated', onKitchenOrder);
    bind(`restaurant.${restaurantId}.waiter`, '.table.status.updated', onTable);
    bind(`restaurant.${restaurantId}.owner`, '.kitchen.order.updated', onKitchenOrder);
    bind(`restaurant.${restaurantId}.owner`, '.table.status.updated', onTable);
    bind(`restaurant.${restaurantId}.owner`, '.employee.updated', onEmployee);
    bind(`restaurant.${restaurantId}.kitchen`, '.table.status.updated', onTable);

    // Admin cross-restaurant
    if (roles.includes('admin') || roles.includes(1)) {
        bind('admin.ops', '.kitchen.order.updated', onKitchenOrder);
        bind('admin.ops', '.table.status.updated', onTable);
        bind('admin.ops', '.employee.updated', onEmployee);
    }

    return () => leave.forEach((fn) => fn());
}

export function getAuthRestaurantId(authInfo, defaultAccess) {
    if (defaultAccess?.restaurant_id) {
        return Number(defaultAccess.restaurant_id);
    }
    if (authInfo?.restaurant_id) {
        return Number(authInfo.restaurant_id);
    }
    return 0;
}
