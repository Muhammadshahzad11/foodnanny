<template>
    <LoadingComponent :props="loading"/>
    <div class="col-12">
        <div class="db-card overflow-hidden">
            <div class="db-card-header border-none !items-start gap-3">
                <div>
                    <h3 class="db-card-title text-2xl">{{ $t('menu.kitchen') }}</h3>
                    <p class="text-sm text-[#6E7191] mt-1">{{ $t('label.overview') }}</p>
                </div>
                <div class="flex flex-wrap gap-2">
                    <button type="button" class="db-btn py-2 text-heading border border-[#EFF0F6]" @click="load">
                        {{ $t('button.refresh') }}
                    </button>
                    <router-link :to="{name: 'admin.kitchen.queue'}" class="db-btn py-2 text-white bg-primary">
                        {{ $t('label.kitchen_queue') }}
                    </router-link>
                </div>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-4 gap-3 px-4 pb-4">
                <button
                    v-for="card in cards"
                    :key="card.key"
                    type="button"
                    class="rounded-2xl border-2 p-4 text-left transition hover:shadow-md focus:outline-none focus:ring-2 focus:ring-primary/30"
                    :class="card.tile"
                    @click="goQueue(card.status)"
                >
                    <div class="flex items-center justify-between gap-2 mb-3">
                        <span
                            class="w-10 h-10 rounded-xl flex items-center justify-center"
                            :class="card.iconWrap"
                        >
                            <svg
                                class="w-5 h-5"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="2"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                aria-hidden="true"
                            >
                                <template v-if="card.key === 'today'">
                                    <rect x="3" y="4" width="18" height="18" rx="2"/>
                                    <path d="M16 2v4M8 2v4M3 10h18"/>
                                </template>
                                <template v-else-if="card.key === 'pending'">
                                    <circle cx="12" cy="12" r="10"/>
                                    <path d="M12 6v6l4 2"/>
                                </template>
                                <template v-else-if="card.key === 'accepted'">
                                    <path d="M20 6 9 17l-5-5"/>
                                </template>
                                <template v-else-if="card.key === 'preparing'">
                                    <path d="M6 15a6 6 0 0 0 12 0V5a3 3 0 0 0-6 0v10"/>
                                    <path d="M6 5a3 3 0 0 1 6 0"/>
                                    <path d="M12 19v2"/>
                                </template>
                                <template v-else-if="card.key === 'ready'">
                                    <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10"/>
                                    <path d="m9 12 2 2 4-4"/>
                                </template>
                                <template v-else-if="card.key === 'completed'">
                                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                                    <path d="m9 11 3 3L22 4"/>
                                </template>
                                <template v-else-if="card.key === 'cancelled'">
                                    <circle cx="12" cy="12" r="10"/>
                                    <path d="m15 9-6 6M9 9l6 6"/>
                                </template>
                                <template v-else>
                                    <path d="M8 6h13M8 12h13M8 18h13M3 6h.01M3 12h.01M3 18h.01"/>
                                </template>
                            </svg>
                        </span>
                        <span
                            v-if="card.badge && Number(card.value) > 0"
                            class="inline-flex items-center gap-1 text-[11px] font-bold uppercase tracking-wide px-2 py-1 rounded-full text-white"
                            :class="card.badge"
                        >
                            <span
                                v-if="card.pulse"
                                class="w-1.5 h-1.5 rounded-full bg-white animate-pulse"
                            ></span>
                            {{ card.badgeLabel }}
                        </span>
                    </div>
                    <p class="text-xs font-semibold uppercase tracking-wide text-[#6E7191] mb-1">{{ card.label }}</p>
                    <p class="text-3xl font-bold text-heading">{{ card.value }}</p>
                </button>
            </div>

            <div class="mx-4 mb-4 rounded-2xl border-2 border-violet-200 bg-violet-50 px-4 py-4 flex flex-wrap items-center justify-between gap-4">
                <div class="flex items-center gap-3 min-w-0">
                    <span class="w-12 h-12 flex-shrink-0 rounded-xl flex items-center justify-center bg-violet-100 text-violet-700">
                        <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <circle cx="12" cy="12" r="10"/>
                            <path d="M12 6v6l4 2"/>
                        </svg>
                    </span>
                    <div class="min-w-0">
                        <p class="text-sm text-[#6E7191]">{{ $t('label.kitchen_performance') }}</p>
                        <p class="text-lg font-semibold text-heading">
                            {{ $t('label.avg_ready_time') }}: {{ formatSeconds(dashboard.avg_ready_seconds) }}
                        </p>
                        <p class="text-sm text-[#6E7191] mt-0.5">
                            {{ $t('label.preparation_queue') }}: {{ dashboard.preparation_queue || 0 }}
                        </p>
                    </div>
                </div>
                <router-link
                    :to="{name: 'admin.kitchen.queue'}"
                    class="db-btn py-2 text-white bg-primary"
                >
                    {{ $t('label.kitchen_queue') }}
                </router-link>
            </div>
        </div>
    </div>
</template>

<script>
import LoadingComponent from "../../common/LoadingComponent.vue";
import {useKitchenOrderStore} from "../../../stores/kitchenOrder.js";
import {useAuthStore} from "../../../stores/auth.js";
import {useDefaultAccessStore} from "../../../stores/defaultAccess.js";
import orderStatusEnum from "../../../enums/modules/orderStatusEnum.js";
import alertService from "../../../services/alertService.js";
import {apiErrorMessage} from "../../../services/apiError.js";
import {getAuthRestaurantId, subscribeRestaurantRealtime} from "../../../composables/useRealtime.js";
import roleEnum from "../../../enums/modules/roleEnum.js";

export default {
    name: "KitchenDashboardComponent",
    components: {LoadingComponent},
    setup() {
        return {
            kitchenOrderStore: useKitchenOrderStore(),
            authStore: useAuthStore(),
            defaultAccessStore: useDefaultAccessStore(),
            orderStatusEnum,
        };
    },
    data() {
        return {loading: {isActive: false}, unsubscribeRealtime: null};
    },
    computed: {
        dashboard() {
            return this.kitchenOrderStore.dashboard || {};
        },
        cards() {
            return [
                {
                    key: 'today',
                    label: this.$t('label.today_orders'),
                    value: this.dashboard.today_orders || 0,
                    status: '',
                    tile: 'border-sky-200 bg-sky-50/70',
                    iconWrap: 'bg-sky-100 text-sky-600',
                },
                {
                    key: 'pending',
                    label: this.$t('label.pending'),
                    value: this.dashboard.pending_orders || 0,
                    status: orderStatusEnum.PENDING,
                    tile: 'border-slate-200 bg-slate-50',
                    iconWrap: 'bg-slate-100 text-slate-600',
                    badge: 'bg-slate-500',
                    badgeLabel: this.$t('label.pending'),
                    pulse: true,
                },
                {
                    key: 'accepted',
                    label: this.$t('label.accepted'),
                    value: this.dashboard.accepted_orders || 0,
                    status: orderStatusEnum.ACCEPT,
                    tile: 'border-sky-300 bg-sky-50',
                    iconWrap: 'bg-sky-100 text-sky-600',
                    badge: 'bg-sky-500',
                    badgeLabel: this.$t('label.accepted'),
                    pulse: true,
                },
                {
                    key: 'preparing',
                    label: this.$t('label.preparing'),
                    value: this.dashboard.preparing_orders || 0,
                    status: orderStatusEnum.PREPARING,
                    tile: 'border-amber-400 bg-amber-50',
                    iconWrap: 'bg-amber-100 text-amber-600',
                    badge: 'bg-amber-500',
                    badgeLabel: this.$t('label.preparing'),
                    pulse: true,
                },
                {
                    key: 'ready',
                    label: this.$t('label.ready'),
                    value: this.dashboard.ready_orders || 0,
                    status: orderStatusEnum.PREPARED,
                    tile: 'border-emerald-400 bg-emerald-50',
                    iconWrap: 'bg-emerald-100 text-emerald-700',
                    badge: 'bg-emerald-600',
                    badgeLabel: this.$t('label.ready'),
                    pulse: true,
                },
                {
                    key: 'completed',
                    label: this.$t('label.completed'),
                    value: this.dashboard.completed_orders || 0,
                    status: orderStatusEnum.DELIVERED,
                    tile: 'border-emerald-200 bg-emerald-50/60',
                    iconWrap: 'bg-emerald-100 text-emerald-600',
                },
                {
                    key: 'cancelled',
                    label: this.$t('label.canceled'),
                    value: this.dashboard.cancelled_orders || 0,
                    status: orderStatusEnum.CANCELED,
                    tile: 'border-rose-200 bg-rose-50/70',
                    iconWrap: 'bg-rose-100 text-rose-600',
                },
                {
                    key: 'queue',
                    label: this.$t('label.preparation_queue'),
                    value: this.dashboard.preparation_queue || 0,
                    status: '',
                    tile: 'border-violet-300 bg-violet-50',
                    iconWrap: 'bg-violet-100 text-violet-700',
                },
            ];
        }
    },
    mounted() {
        this.load();
        this.bindRealtime();
    },
    methods: {
        bindRealtime() {
            const restaurantId = getAuthRestaurantId(this.authStore.info, this.defaultAccessStore.lists);
            const isAdmin = Number(this.authStore.info?.role_id) === roleEnum.ADMIN;
            this.unsubscribeRealtime = subscribeRestaurantRealtime({
                restaurantId: restaurantId || 1,
                roles: isAdmin ? ['admin'] : [],
                onKitchenOrder: () => this.loadQuiet(),
            });
        },
        loadQuiet() {
            this.kitchenOrderStore.fetchDashboard().catch(() => {});
        },
        load() {
            this.loading.isActive = true;
            this.kitchenOrderStore.fetchDashboard().then(() => {
                this.loading.isActive = false;
            }).catch((err) => {
                this.loading.isActive = false;
                alertService.error(apiErrorMessage(err, this.$t('message.something_wrong')));
            });
        },
        goQueue(status) {
            this.$router.push({
                name: 'admin.kitchen.queue',
                query: status === '' || status === undefined ? {} : {status: String(status)},
            });
        },
        formatSeconds(sec) {
            const s = Number(sec || 0);
            if (!s) return '—';
            const m = Math.floor(s / 60);
            const r = s % 60;
            return `${m}m ${r}s`;
        }
    },
    beforeUnmount() {
        if (typeof this.unsubscribeRealtime === 'function') {
            this.unsubscribeRealtime();
        }
    }
}
</script>
