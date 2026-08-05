import axios from "axios";
import {defineStore} from "pinia";
import ENV from "../config/env.js";
import alertService from "../services/alertService.js";
import {notificationOrderSerial, resolveNotificationUrl} from "../services/notificationLink.js";

function playNotificationSound() {
    try {
        const audio = new Audio('/audio/notification.mp3');
        audio.volume = 0.7;
        audio.play().catch(() => {});
    } catch (e) {
        // ignore autoplay blocks
    }
}

function showStatusAlert(payload) {
    const title = payload?.title || 'Update';
    const message = payload?.message || '';
    const text = message ? `${title} — ${message}` : title;
    const color = payload?.color || payload?.data?.color || 'primary';
    const orderSerial = notificationOrderSerial(payload);
    const orderUrl = resolveNotificationUrl(payload);

    alertService.statusAlert(text, color, 30000, {
        orderSerial,
        orderUrl,
    });
}


export const useInboxNotificationStore = defineStore('inboxNotification', {
    state: () => ({
        recent: [],
        unreadCount: 0,
        soundEnabled: true,
        subscribedUserId: null,
    }),
    actions: {
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

            // Live alert bar for every status / inbox update
            showStatusAlert(payload);

            const play = payload.data?.play_sound
                || ['order.created', 'kitchen.ready', 'kitchen.accepted', 'kitchen.preparing', 'order.rejected', 'order.cancelled'].includes(payload.type);
            if (this.soundEnabled && play) {
                playNotificationSound();
            }
        },

        subscribeUserChannel(userId) {
            if (!window.Echo || !userId || !ENV.PUSHER_KEY) return;
            if (this.subscribedUserId === userId) return;

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
