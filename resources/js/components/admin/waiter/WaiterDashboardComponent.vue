<template>
    <LoadingComponent :props="loading"/>
    <div class="col-12">
        <div class="db-card overflow-hidden">
            <div class="db-card-header border-none !items-start gap-3">
                <div>
                    <h3 class="db-card-title text-2xl">{{ $t('menu.waiter') }}</h3>
                    <p class="text-sm text-[#6E7191] mt-1">{{ $t('label.overview') }}</p>
                </div>
                <div class="flex flex-wrap gap-2">
                    <button type="button" class="db-btn py-2 text-heading border border-[#EFF0F6]" @click="load">
                        {{ $t('button.refresh') }}
                    </button>
                    <router-link :to="{name: 'admin.waiter.tables'}" class="db-btn py-2 text-white bg-primary">
                        {{ $t('label.tables') }}
                    </router-link>
                </div>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-6 gap-3 px-4 pb-4">
                <button
                    type="button"
                    class="rounded-2xl border-2 p-4 text-left transition hover:shadow-md focus:outline-none focus:ring-2 focus:ring-primary/30"
                    :class="tileClass('tables')"
                    @click="goTables"
                >
                    <div class="flex items-center justify-between gap-2 mb-3">
                        <span class="w-10 h-10 rounded-xl flex items-center justify-center bg-sky-100 text-sky-600">
                            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="10" width="18" height="10" rx="2"/><path d="M5 10V7a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2v3"/><path d="M8 14h.01M12 14h.01M16 14h.01"/></svg>
                        </span>
                    </div>
                    <p class="text-xs font-semibold uppercase tracking-wide text-[#6E7191] mb-1">{{ $t('label.tables') }}</p>
                    <p class="text-3xl font-bold text-heading">{{ dashboard.tables_total || 0 }}</p>
                </button>

                <button
                    type="button"
                    class="rounded-2xl border-2 p-4 text-left transition hover:shadow-md focus:outline-none focus:ring-2 focus:ring-primary/30"
                    :class="tileClass('available')"
                    @click="goTables"
                >
                    <div class="flex items-center justify-between gap-2 mb-3">
                        <span class="w-10 h-10 rounded-xl flex items-center justify-center bg-emerald-100 text-emerald-600">
                            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M20 6 9 17l-5-5"/></svg>
                        </span>
                        <span
                            v-if="Number(dashboard.tables_available) > 0"
                            class="text-[11px] font-bold uppercase tracking-wide px-2 py-1 rounded-full text-white bg-emerald-500"
                        >{{ $t('label.available') }}</span>
                    </div>
                    <p class="text-xs font-semibold uppercase tracking-wide text-[#6E7191] mb-1">{{ $t('label.available') }}</p>
                    <p class="text-3xl font-bold text-heading">{{ dashboard.tables_available || 0 }}</p>
                </button>

                <button
                    type="button"
                    class="rounded-2xl border-2 p-4 text-left transition hover:shadow-md focus:outline-none focus:ring-2 focus:ring-primary/30"
                    :class="tileClass('occupied')"
                    @click="goTables"
                >
                    <div class="flex items-center justify-between gap-2 mb-3">
                        <span class="w-10 h-10 rounded-xl flex items-center justify-center bg-amber-100 text-amber-600">
                            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 12h18"/><path d="M5 12V7a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2v5"/><path d="M7 12v5M17 12v5"/><path d="M5 17h14"/></svg>
                        </span>
                        <span
                            v-if="Number(dashboard.tables_occupied) > 0"
                            class="inline-flex items-center gap-1 text-[11px] font-bold uppercase tracking-wide px-2 py-1 rounded-full text-white bg-amber-500"
                        >
                            <span class="w-1.5 h-1.5 rounded-full bg-white animate-pulse"></span>
                            {{ $t('label.occupied') }}
                        </span>
                    </div>
                    <p class="text-xs font-semibold uppercase tracking-wide text-[#6E7191] mb-1">{{ $t('label.occupied') }}</p>
                    <p class="text-3xl font-bold text-heading">{{ dashboard.tables_occupied || 0 }}</p>
                </button>

                <button
                    type="button"
                    class="rounded-2xl border-2 p-4 text-left transition hover:shadow-md focus:outline-none focus:ring-2 focus:ring-primary/30"
                    :class="tileClass('draft')"
                    @click="goTables"
                >
                    <div class="flex items-center justify-between gap-2 mb-3">
                        <span class="w-10 h-10 rounded-xl flex items-center justify-center bg-slate-100 text-slate-600">
                            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 20h9"/><path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4Z"/></svg>
                        </span>
                    </div>
                    <p class="text-xs font-semibold uppercase tracking-wide text-[#6E7191] mb-1">{{ $t('label.draft_orders') }}</p>
                    <p class="text-3xl font-bold text-heading">{{ dashboard.draft_orders || 0 }}</p>
                </button>

                <button
                    type="button"
                    class="rounded-2xl border-2 p-4 text-left transition hover:shadow-md focus:outline-none focus:ring-2 focus:ring-primary/30"
                    :class="tileClass('kitchen')"
                    @click="goTables"
                >
                    <div class="flex items-center justify-between gap-2 mb-3">
                        <span class="w-10 h-10 rounded-xl flex items-center justify-center bg-sky-100 text-sky-600">
                            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M6 15a6 6 0 0 0 12 0V5a3 3 0 0 0-6 0v10"/><path d="M6 5a3 3 0 0 1 6 0"/><path d="M12 19v2"/></svg>
                        </span>
                    </div>
                    <p class="text-xs font-semibold uppercase tracking-wide text-[#6E7191] mb-1">{{ $t('label.kitchen_orders') }}</p>
                    <p class="text-3xl font-bold text-heading">{{ dashboard.kitchen_orders || 0 }}</p>
                </button>

                <button
                    type="button"
                    class="rounded-2xl border-2 p-4 text-left transition hover:shadow-md focus:outline-none focus:ring-2 focus:ring-primary/30"
                    :class="tileClass('ready')"
                    @click="goTables"
                >
                    <div class="flex items-center justify-between gap-2 mb-3">
                        <span class="w-10 h-10 rounded-xl flex items-center justify-center bg-emerald-100 text-emerald-700">
                            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10"/><path d="m9 12 2 2 4-4"/></svg>
                        </span>
                        <span
                            v-if="Number(dashboard.ready_orders) > 0"
                            class="inline-flex items-center gap-1 text-[11px] font-bold uppercase tracking-wide px-2 py-1 rounded-full text-white bg-emerald-600"
                        >
                            <span class="w-1.5 h-1.5 rounded-full bg-white animate-pulse"></span>
                            {{ $t('label.ready') }}
                        </span>
                    </div>
                    <p class="text-xs font-semibold uppercase tracking-wide text-[#6E7191] mb-1">{{ $t('label.ready_orders') }}</p>
                    <p class="text-3xl font-bold text-heading">{{ dashboard.ready_orders || 0 }}</p>
                </button>
            </div>

            <div
                v-if="Number(dashboard.ready_orders) > 0"
                class="mx-4 mb-4 rounded-2xl border-2 border-emerald-300 bg-emerald-50 px-4 py-3 flex flex-wrap items-center justify-between gap-3"
            >
                <div class="flex items-center gap-3 min-w-0">
                    <span class="w-10 h-10 flex-shrink-0 rounded-xl flex items-center justify-center bg-emerald-100 text-emerald-700">
                        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10"/><path d="m9 12 2 2 4-4"/></svg>
                    </span>
                    <div class="min-w-0">
                        <p class="font-semibold text-heading">
                            {{ dashboard.ready_orders }} {{ $t('label.ready_orders') }}
                        </p>
                        <p class="text-sm text-[#6E7191]">{{ $t('button.view') }} {{ $t('label.tables') }}</p>
                    </div>
                </div>
                <router-link :to="{name: 'admin.waiter.tables'}" class="db-btn py-2 text-white bg-primary">
                    {{ $t('label.tables') }}
                </router-link>
            </div>
        </div>
    </div>
</template>

<script>
import LoadingComponent from "../../common/LoadingComponent.vue";
import {useWaiterTableStore} from "../../../stores/waiterTable.js";
import {useAuthStore} from "../../../stores/auth.js";
import {useDefaultAccessStore} from "../../../stores/defaultAccess.js";
import alertService from "../../../services/alertService.js";
import {apiErrorMessage} from "../../../services/apiError.js";
import {getAuthRestaurantId, subscribeRestaurantRealtime} from "../../../composables/useRealtime.js";
import roleEnum from "../../../enums/modules/roleEnum.js";

export default {
    name: "WaiterDashboardComponent",
    components: {LoadingComponent},
    setup() {
        return {
            waiterTableStore: useWaiterTableStore(),
            authStore: useAuthStore(),
            defaultAccessStore: useDefaultAccessStore(),
        };
    },
    data() {
        return {
            loading: {isActive: false},
            unsubscribeRealtime: null,
        };
    },
    computed: {
        dashboard() {
            return this.waiterTableStore.dashboard || {};
        },
    },
    mounted() {
        this.load();
        const restaurantId = getAuthRestaurantId(this.authStore.info, this.defaultAccessStore.lists);
        const isAdmin = Number(this.authStore.info?.role_id) === roleEnum.ADMIN;
        this.unsubscribeRealtime = subscribeRestaurantRealtime({
            restaurantId: restaurantId || 1,
            roles: isAdmin ? ['admin'] : [],
            onKitchenOrder: () => this.loadQuiet(),
            onTable: () => this.loadQuiet(),
        });
    },
    beforeUnmount() {
        if (typeof this.unsubscribeRealtime === 'function') {
            this.unsubscribeRealtime();
        }
    },
    methods: {
        tileClass(key) {
            const map = {
                tables: 'border-sky-200 bg-sky-50/70',
                available: 'border-emerald-300 bg-emerald-50',
                occupied: 'border-amber-400 bg-amber-50',
                draft: 'border-slate-200 bg-slate-50',
                kitchen: 'border-sky-300 bg-sky-50',
                ready: 'border-emerald-400 bg-emerald-50',
            };
            return map[key] || 'border-[#EFF0F6] bg-white';
        },
        goTables() {
            this.$router.push({name: 'admin.waiter.tables'});
        },
        load() {
            this.loading.isActive = true;
            this.waiterTableStore.fetchDashboard().then(() => {
                this.loading.isActive = false;
            }).catch((err) => {
                this.loading.isActive = false;
                alertService.error(apiErrorMessage(err, this.$t('message.failed_to_load_waiter_dashboard')));
            });
        },
        loadQuiet() {
            this.waiterTableStore.fetchDashboard().catch(() => {});
        }
    }
}
</script>
