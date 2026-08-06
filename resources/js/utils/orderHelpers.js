import orderTypeEnum from "../enums/modules/orderTypeEnum.js";
import paymentTypeEnum from "../enums/modules/paymentTypeEnum.js";

/**
 * Restaurant table QR / scan-menu dine-in order.
 * Matches backend Order::isScanMenuOrder() / is_scan_menu_order.
 */
export function isScanMenuOrder(order = {}) {
    const orderType = Number(order?.order_type ?? 0);
    const tableId = Number(order?.table_id ?? order?.table?.id ?? 0);
    if (order?.is_scan_menu_order === true || order?.is_scan_menu_order === 1) {
        return true;
    }
    return orderType === orderTypeEnum.DINING_TABLE && tableId > 0;
}

/**
 * Dynamic payment label: Pay at Counter for QR dine-in COD, else Cash On Delivery / gateway name.
 * Prefer API payment_method_label when present.
 */
export function resolvePaymentMethodLabel(order = {}, t = (k) => k) {
    if (order?.payment_method_label) {
        return order.payment_method_label;
    }

    const method = Number(order?.payment_method ?? 0);
    const transactionName = order?.transaction?.payment_method
        ? String(order.transaction.payment_method).trim()
        : '';

    if (method === paymentTypeEnum.CASH_ON_DELIVERY || isCodTransactionName(transactionName)) {
        return isScanMenuOrder(order)
            ? t('label.pay_at_counter')
            : t('label.cash_on_delivery');
    }

    if (transactionName) {
        return transactionName;
    }

    return '';
}

function isCodTransactionName(name) {
    if (!name) return false;
    const n = name.toLowerCase().replace(/[\s_-]+/g, '');
    return n.includes('cashondelivery') || n === 'cod';
}
