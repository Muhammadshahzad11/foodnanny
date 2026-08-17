import axios from "axios";
import {defineStore} from "pinia";
import ENV from "../config/env.js";
import alertService from "../services/alertService.js";
import {notificationOrderSerial, resolveNotificationUrl} from "../services/notificationLink.js";
import {
    isSoundEnabled,
    playNotificationSound,
    playOrderAlertSound,
    setSoundEnabled,
} from "../services/notificationSound.js";

const NEW_ORDER_TYPES = ['order.created'];
const SOUND_TYPES = [
    'order.created',
    'kitchen.ready',
    'kitchen.accepted',
    'kitchen.preparing',
    'order.rejected',
    'order.cancelled',
];

function notificationText(payload) {
    const title = payload?.title || 'Update';
    const message = payload?.message || '';
    return message ? `${title} — ${message}` : title;
}

function showStatusAlert(payload) {
    const color = payload?.color || payload?.data?.color || 'primary';
    const orderSerial = notificationOrderSerial(payload);
    const orderUrl = resolveNotificationUrl(payload);

    alertService.statusAlert(notificationText(payload), color, 2000, {
        orderSerial,
        orderUrl,
    });
}

function showNewOrderAlert(payload) {
    alertService.newOrderAlert(notificationText(payload), {
        orderSerial: notificationOrderSerial(payload),
        orderUrl: resolveNotificationUrl(payload),
    });
}

/**
 * The counter often has the POS on another tab; a system notification is the
 * only thing they will see there.
 */
function showDesktopNotification(payload) {
    try {
        if (typeof Notification === 'undefined' || Notification.permission !== 'granted') return;
        if (typeof document !== 'undefined' && !document.hidden) return;

        const url = resolveNotificationUrl(payload);
        const note = new Notification(payload?.title || 'New order', {
            body: payload?.message || '',
            icon: '/images/default/logo.png',
            tag: `fn-order-${payload?.id || Date.now()}`,
            requireInteraction: true,
        });
        note.onclick = () => {
            try {
                window.focus();
                if (url) window.location.href = url;
            } catch (e) {
                // ignore
            }
            note.close();
        };
    } catch (e) {
        // ignore unsupported browsers
    }
}


export const useInboxNotificationStore = defineStore('inboxNotification', {
    state: () => ({
        recent: [],
        unreadCount: 0,
        soundEnabled: isSoundEnabled(),
        subscribedUserId: null,
    }),
    actions: {
        toggleSound(on = null) {
            this.soundEnabled = on === null ? !this.soundEnabled : !!on;
            setSoundEnabled(this.soundEnabled);
            if (this.soundEnabled) {
                playNotificationSound({volume: 0.6, force: true});
            }
        },
        /** Ask once so background new-order alerts can surface outside the tab. */
        ensureDesktopPermission() {
            try {
                if (typeof Notification === 'undefined') return;
                if (Notification.permission === 'default') {
                    Notification.requestPermission().catch(() => {});
                }
            } catch (e) {
                // ignore
            }
        },
        fetchRecent() {
            return axios.get('admin/inbox-notifications/recent').then((res) => {
                this.recent = res.data.data || [];
                this.unreadCount = res.data.unread_count || 0;
                return res;
            });
        },
        fetchUnreadCount() {
            return axios.get('admin/inbox-notifications/unread-count').then((res) => {
                this.unreadCount = res.data.data?.unread_count || 0;
                return res;
            });
        },
        markRead(id) {
            return axios.post(`admin/inbox-notifications/${id}/read`).then((res) => {
                this.unreadCount = res.data.unread_count ?? this.unreadCount;
                const idx = this.recent.findIndex((n) => n.id === id);
                if (idx >= 0) {
                    this.recent[idx] = res.data.data;
                }
                return res;
            });
        },
        markAllRead() {
            return axios.post('admin/inbox-notifications/read-all').then((res) => {
                this.unreadCount = 0;
                this.recent = this.recent.map((n) => ({...n, is_unread: false, read_at: n.read_at || new Date().toISOString()}));
                return res;
            });
        },
        destroy(id) {
            return axios.delete(`admin/inbox-notifications/${id}`).then((res) => {
                this.recent = this.recent.filter((n) => n.id !== id);
                this.unreadCount = res.data.unread_count ?? this.unreadCount;
                return res;
            });
        },
        prependFromBroadcast(payload) {
            if (!payload?.id) return;
            if (this.recent.some((n) => n.id === payload.id)) return;
            this.recent.unshift({
                ...payload,
                is_unread: true,
            });
            this.recent = this.recent.slice(0, 15);
            this.unreadCount += 1;

            const isNewOrder = NEW_ORDER_TYPES.includes(payload.type);

            if (isNewOrder) {
                showNewOrderAlert(payload);
                showDesktopNotification(payload);
            } else {
                showStatusAlert(payload);
            }

            const play = payload.data?.play_sound || SOUND_TYPES.includes(payload.type);
            if (this.soundEnabled && play) {
                if (isNewOrder) {
                    playOrderAlertSound();
                } else {
                    playNotificationSound({volume: 0.7});
                }
            }
        },

        subscribeUserChannel(userId) {
            if (!window.Echo || !userId || !ENV.PUSHER_KEY) return;
            if (this.subscribedUserId === userId) return;

            this.ensureDesktopPermission();

            if (this.subscribedUserId) {
                window.Echo.leave(`user.${this.subscribedUserId}`);
            }

            this.subscribedUserId = userId;
            window.Echo.private(`user.${userId}`)
                .listen('.notification.created', (e) => {
                    this.prependFromBroadcast(e);
                });
        },
        unsubscribe() {
            if (window.Echo && this.subscribedUserId) {
                window.Echo.leave(`user.${this.subscribedUserId}`);
            }
            this.subscribedUserId = null;
        },
    },
});
