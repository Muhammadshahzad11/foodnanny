<template>
    <div class="relative" ref="root">
        <button
            type="button"
            class="relative w-9 h-9 leading-9 text-center rounded-lg bg-primary/10"
            @click.stop.prevent="toggle"
            :aria-label="$t('label.notifications')"
            :aria-expanded="open ? 'true' : 'false'"
        >
            <svg class="w-[18px] h-[18px] text-primary mx-auto" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <path d="M6 8a6 6 0 0 1 12 0c0 7 3 9 3 9H3s3-2 3-9"/>
                <path d="M10.3 21a1.94 1.94 0 0 0 3.4 0"/>
            </svg>
            <span
                v-if="unreadCount > 0"
                class="absolute -top-1 -right-1 min-w-[18px] h-[18px] px-1 rounded-full bg-rose-500 text-white text-[10px] font-bold leading-[18px] animate-pulse"
            >
                {{ unreadCount > 99 ? '99+' : unreadCount }}
            </span>
        </button>

        <div
            v-if="open"
            class="w-[min(100vw-1.5rem,22rem)] max-h-[70vh] overflow-hidden flex flex-col absolute top-12 ltr:right-0 rtl:left-0 z-[80] rounded-xl border border-gray-200 shadow-xl bg-white"
            @click.stop
        >
            <div class="flex items-center justify-between gap-2 px-4 py-3 border-b border-gray-100">
                <h3 class="text-sm font-semibold text-heading">{{ $t('label.notifications') }}</h3>
                <div class="flex items-center gap-3">
                    <button
                        type="button"
                        class="w-7 h-7 rounded-lg flex items-center justify-center transition"
                        :class="soundEnabled ? 'bg-primary/10 text-primary' : 'bg-slate-100 text-[#A0A3BD]'"
                        :title="soundEnabled ? $t('label.notification_sound_on') : $t('label.notification_sound_off')"
                        @click.stop.prevent="toggleSound"
                    >
                        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="M11 5 6 9H2v6h4l5 4V5z"/>
                            <template v-if="soundEnabled">
                                <path d="M15.5 8.5a5 5 0 0 1 0 7"/>
                                <path d="M18.5 5.5a9 9 0 0 1 0 13"/>
                            </template>
                            <template v-else>
                                <path d="M22 9l-6 6"/>
                                <path d="M16 9l6 6"/>
                            </template>
                        </svg>
                    </button>
                    <button
                        v-if="unreadCount > 0"
                        type="button"
                        class="text-xs font-medium text-primary"
                        @click.stop.prevent="markAll"
                    >
                        {{ $t('button.mark_all_read') }}
                    </button>
                </div>
            </div>

            <ul class="flex-auto overflow-y-auto thin-scrolling max-h-[50vh]">
                <li
                    v-for="item in recent"
                    :key="item.id"
                    class="px-4 py-3 border-b border-gray-50 cursor-pointer hover:bg-slate-50 transition"
                    :class="item.is_unread ? 'bg-primary/5' : ''"
                    @click.stop.prevent="openItem(item)"
                >
                    <div class="flex gap-3">
                        <span
                            class="flex-shrink-0 w-9 h-9 rounded-lg flex items-center justify-center"
                            :class="iconWrap(item.color)"
                        >
                            <i :class="['lab', item.icon || 'lab-line-notification']" class="text-base"></i>
                        </span>
                        <div class="min-w-0 flex-auto">
                            <p class="text-sm font-semibold text-heading truncate">{{ item.title }}</p>
                            <p class="text-xs text-[#6E7191] mt-0.5 line-clamp-3">
                                <template v-for="(part, idx) in messageParts(item)" :key="idx">
                                    <a
                                        v-if="part.type === 'order'"
                                        href="#"
                                        class="font-bold text-primary underline underline-offset-2"
                                        @click.stop.prevent="openOrder(item)"
                                    >{{ part.value }}</a>
                                    <span v-else>{{ part.value }}</span>
                                </template>
                            </p>
                            <p class="text-[11px] text-[#A0A3BD] mt-1">{{ relative(item.created_at) }}</p>
                        </div>
                        <span v-if="item.is_unread" class="w-2 h-2 rounded-full bg-primary mt-2 flex-shrink-0"></span>
                    </div>
                </li>
                <li v-if="!recent.length" class="px-4 py-8 text-center text-sm text-[#6E7191]">
                    {{ $t('message.no_notifications') }}
                </li>
            </ul>
        </div>
    </div>
</template>

<script>
import {useInboxNotificationStore} from "../../../stores/inboxNotification.js";
import alertService from "../../../services/alertService.js";
import {
    notificationOrderSerial,
    resolveNotificationUrl,
    splitOrderNumberParts,
} from "../../../services/notificationLink.js";

export default {
    name: "NotificationBellComponent",
    setup() {
        return {inboxStore: useInboxNotificationStore()};
    },
    data() {
        return {open: false};
    },
    computed: {
        recent() {
            return this.inboxStore.recent || [];
        },
        unreadCount() {
            return this.inboxStore.unreadCount || 0;
        },
        soundEnabled() {
            return !!this.inboxStore.soundEnabled;
        }
    },
    mounted() {
        document.addEventListener('click', this.onDocClick);
    },
    beforeUnmount() {
        document.removeEventListener('click', this.onDocClick);
    },
    methods: {
        toggleSound() {
            this.inboxStore.toggleSound();
        },
        toggle() {
            this.open = !this.open;
            if (this.open) {
                this.inboxStore.fetchRecent().catch(() => {});
            }
        },
        onDocClick(e) {
            const root = this.$refs.root;
            if (root && !root.contains(e.target)) {
                this.open = false;
            }
        },
        iconWrap(color) {
            const map = {
                sky: 'bg-sky-100 text-sky-700',
                amber: 'bg-amber-100 text-amber-700',
                emerald: 'bg-emerald-100 text-emerald-700',
                rose: 'bg-rose-100 text-rose-700',
                violet: 'bg-violet-100 text-violet-700',
                primary: 'bg-primary/10 text-primary',
            };
            return map[color] || map.primary;
        },
        relative(iso) {
            if (!iso) return '';
            const t = new Date(iso).getTime();
            if (Number.isNaN(t)) return '';
            const sec = Math.max(0, Math.floor((Date.now() - t) / 1000));
            if (sec < 60) return `${sec}s`;
            if (sec < 3600) return `${Math.floor(sec / 60)}m`;
            if (sec < 86400) return `${Math.floor(sec / 3600)}h`;
            return `${Math.floor(sec / 86400)}d`;
        },
        messageParts(item) {
            return splitOrderNumberParts(item.message || '', notificationOrderSerial(item));
        },
        async markAll() {
            try {
                await this.inboxStore.markAllRead();
            } catch (e) {
                alertService.error(this.$t('message.something_wrong'));
            }
        },
        resolveUrl(item) {
            return resolveNotificationUrl(item);
        },
        openOrder(item) {
            const target = this.resolveUrl(item);
            if (!target) return;
            this.open = false;
            this.$router.push(target).catch(() => {
                window.location.href = target;
            });
        },
        async openItem(item) {
            try {
                if (item.is_unread) {
                    await this.inboxStore.markRead(item.id).catch(() => {});
                }
            } catch (e) {
                // still navigate even if mark-read fails
            }

            this.open = false;
            const target = this.resolveUrl(item);
            if (!target) {
                alertService.info(`${item.title}: ${item.message}`);
                return;
            }

            this.$router.push(target).catch(() => {
                window.location.href = target;
            });
        }
    }
}
</script>
