import ENV from "../config/env.js";
import alertService from "../services/alertService.js";
import orderStatusEnum from "../enums/modules/orderStatusEnum.js";

const SOUND_PREF_KEY = 'customer_order_sound_enabled';
const DEDUPE_PREFIX = 'customer_order_status_alert:';

function isSoundEnabled() {
    try {
        const raw = localStorage.getItem(SOUND_PREF_KEY);
        if (raw === null) return true;
        return raw === '1' || raw === 'true';
    } catch (e) {
        return true;
    }
}

export function setCustomerOrderSoundEnabled(enabled) {
    try {
        localStorage.setItem(SOUND_PREF_KEY, enabled ? '1' : '0');
    } catch (e) {
        // ignore
    }
}

export function getCustomerOrderSoundEnabled() {
    return isSoundEnabled();
}

function playNotificationSound() {
    if (!isSoundEnabled()) return;
    try {
        const audio = new Audio('/audio/notification.mp3');
        audio.volume = 0.7;
        audio.play().catch(() => {});
    } catch (e) {
        // ignore autoplay blocks
    }
}

function dedupeKey(orderId, status) {
    return `${DEDUPE_PREFIX}${orderId}:${status}`;
}

function alreadyAlerted(orderId, status) {
    try {
        const key = dedupeKey(orderId, status);
        const last = sessionStorage.getItem(key);
        if (last && Date.now() - Number(last) < 15000) {
            return true;
        }
        sessionStorage.setItem(key, String(Date.now()));
        return false;
    } catch (e) {
        return false;
    }
}

export function customerStatusMessage(status, t) {
    const s = Number(status);
    if (s === orderStatusEnum.ACCEPT) return t('message.order_status_popup_accepted');
    if (s === orderStatusEnum.PREPARING) return t('message.order_status_popup_preparing');
    if (s === orderStatusEnum.PREPARED) return t('message.order_status_popup_ready');
    if (s === orderStatusEnum.OUT_FOR_DELIVERY) return t('message.order_status_popup_on_the_way');
    if (s === orderStatusEnum.DELIVERED) return t('message.order_status_popup_completed');
    if (s === orderStatusEnum.REJECTED) return t('message.order_status_popup_rejected');
    if (s === orderStatusEnum.CANCELED) return t('message.order_status_popup_canceled');
    if (s === orderStatusEnum.PENDING) return t('message.order_status_popup_received');
    return t('message.order_status_popup_updated');
}

function toneForStatus(status) {
    const s = Number(status);
    if (s === orderStatusEnum.REJECTED || s === orderStatusEnum.CANCELED) return 'rose';
    if (s === orderStatusEnum.DELIVERED || s === orderStatusEnum.PREPARED) return 'emerald';
    if (s === orderStatusEnum.PREPARING || s === orderStatusEnum.OUT_FOR_DELIVERY) return 'amber';
    return 'sky';
}

/**
 * Subscribe the logged-in customer to private-user.{id} for order.status.updated.
 * Returns unsubscribe fn. Does not leave the channel (may be shared).
 */
export function subscribeCustomerOrderRealtime({userId, orderId = null, onUpdate, t = (k) => k}) {
    if (!window.Echo || !ENV.PUSHER_KEY || !userId) {
        return () => {};
    }

    const channelName = `user.${userId}`;
    const channel = window.Echo.private(channelName);

    const handler = (payload) => {
        if (!payload?.order_id) return;
        if (orderId && Number(payload.order_id) !== Number(orderId)) return;

        const status = Number(payload.status);
        const previous = payload.previous_status != null ? Number(payload.previous_status) : null;
        if (previous !== null && previous === status) return;

        if (!alreadyAlerted(payload.order_id, status)) {
            const message = customerStatusMessage(status, t);
            alertService.statusAlert(message, toneForStatus(status), 3500, {
                orderSerial: payload.order_serial_no ? `#${payload.order_serial_no}` : null,
                orderUrl: `/my-orders/${payload.order_id}`,
            });
            playNotificationSound();
        }

        if (typeof onUpdate === 'function') {
            onUpdate(payload);
        }
    };

    channel.listen('.order.status.updated', handler);

    return () => {
        try {
            channel.stopListening('.order.status.updated');
        } catch (e) {
            // ignore
        }
    };
}
