import appService from "./appService.js";

/**
 * Resolve a deep-link for an inbox / kitchen notification.
 */
export function resolveNotificationUrl(item = {}) {
    const data = item?.data || {};
    const orderId = data.order_id || item.order_id;
    const tableId = data.table_id || item.table_id;
    const type = item.type || data.type || '';

    const canKitchen = appService.permissionChecker('kitchen')
        || appService.permissionChecker('kitchen_queue')
        || appService.permissionChecker('kitchen_orders');
    const canWaiter = appService.permissionChecker('waiter')
        || appService.permissionChecker('waiter_orders')
        || appService.permissionChecker('waiter_tables');

    if (type.startsWith('kitchen.') || type.startsWith('order.') || orderId) {
        if (canKitchen) {
            return orderId ? `/admin/kitchen/orders/${orderId}` : '/admin/kitchen/queue';
        }
        if (canWaiter) {
            if (orderId) return `/admin/waiter/orders/${orderId}`;
            if (tableId) return `/admin/waiter/tables/${tableId}`;
            return '/admin/waiter/tables';
        }
    }

    if (type.startsWith('table.')) {
        if (canWaiter) return '/admin/waiter/tables';
        if (appService.permissionChecker('tables')) return '/admin/tables';
    }

    if (type.startsWith('employee.') && appService.permissionChecker('employees')) {
        return '/admin/employees';
    }

    return item.url || data.url || null;
}

export function notificationOrderSerial(item = {}) {
    const data = item?.data || {};
    return data.order_serial_no || item.order_serial_no || null;
}

/**
 * Split text so an order serial (e.g. 05082629 / #05082629) can be linked.
 * Returns [{type:'text', value}, {type:'order', value, serial}]
 */
export function splitOrderNumberParts(text, serial) {
    const raw = String(text || '');
    if (!raw) return [{type: 'text', value: ''}];

    const needle = serial ? String(serial) : null;
    let match = null;

    if (needle) {
        const re = new RegExp(`(Order\\s*)?#?${needle.replace(/[.*+?^${}()|[\]\\]/g, '\\$&')}`, 'i');
        match = raw.match(re);
    }
    if (!match) {
        match = raw.match(/#?\d{6,}/);
    }
    if (!match || match.index == null) {
        return [{type: 'text', value: raw}];
    }

    const start = match.index;
    const end = start + match[0].length;
    const parts = [];
    if (start > 0) parts.push({type: 'text', value: raw.slice(0, start)});
    parts.push({
        type: 'order',
        value: match[0],
        serial: needle || match[0].replace(/^#/, ''),
    });
    if (end < raw.length) parts.push({type: 'text', value: raw.slice(end)});
    return parts;
}
