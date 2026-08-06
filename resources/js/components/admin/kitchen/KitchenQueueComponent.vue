<template>
    <LoadingComponent :props="loading"/>

    <div class="col-12">
        <div class="db-card overflow-hidden">
            <div class="db-card-header border-none !items-start gap-3">
                <div>
                    <router-link :to="{name: 'admin.kitchen.dashboard'}" class="text-sm text-primary">
                        ← {{ $t('menu.kitchen') }}
                    </router-link>
                    <h3 class="db-card-title mt-1 text-2xl">{{ $t('label.kitchen_queue') }}</h3>
                    <p class="text-sm text-[#6E7191] mt-1">{{ $t('message.kitchen_queue_hint') }}</p>
                </div>
                <div class="flex flex-wrap items-center gap-2">
                    <span class="text-sm text-[#6E7191]">
                        {{ orders.length }} {{ $t('label.tickets') }}
                    </span>
                    <button type="button" class="db-btn py-2.5 px-4 text-sm text-heading border border-[#EFF0F6]" @click="filtersOpen = !filtersOpen">
                        {{ $t('button.filter') }}
                    </button>
                    <button type="button" class="db-btn py-2.5 px-4 text-sm text-white bg-primary" @click="refresh">
                        {{ $t('button.refresh') }}
                    </button>
                </div>
            </div>

            <div class="px-4 pb-3 flex flex-wrap gap-2">
                <button
                    v-for="tab in statusTabs"
                    :key="tab.value === '' ? 'all' : tab.value"
                    type="button"
                    class="min-h-11 px-4 py-2 rounded-lg text-sm font-semibold border transition"
                    :class="String(filters.status) === String(tab.value)
                        ? 'bg-primary border-primary text-white'
                        : 'bg-white border-[#EFF0F6] text-heading hover:border-primary'"
                    @click="setStatus(tab.value)"
                >
                    {{ tab.label }}
                </button>
            </div>

            <div class="px-4 pb-3">
                <div class="relative">
                    <input
                        v-model="filters.search"
                        type="search"
                        class="db-field-control w-full"
                        :placeholder="$t('label.search_order_table_waiter')"
                        autocomplete="off"
                    />
                    <button
                        v-if="filters.search"
                        type="button"
                        class="absolute right-3 top-1/2 -translate-y-1/2 text-[#6E7191] hover:text-heading text-lg leading-none"
                        @click="clearSearch"
                        :aria-label="$t('button.clear')"
                    >×</button>
                    <p v-if="searching" class="mt-1 text-xs text-primary">
                        {{ $t('label.searching') }}
                    </p>
                </div>
            </div>

            <form v-show="filtersOpen" class="px-4 pb-4 grid grid-cols-1 md:grid-cols-2 xl:grid-cols-6 gap-3" @submit.prevent="refresh">
                <select v-model="filters.period" class="db-field-control" @change="refresh">
                    <option value="today">{{ $t('label.today') }}</option>
                    <option value="yesterday">{{ $t('label.yesterday') }}</option>
                    <option value="week">{{ $t('label.this_week') }}</option>
                    <option value="month">{{ $t('label.last_30_days') }}</option>
                    <option value="all">{{ $t('label.all_time') }}</option>
                </select>
                <select v-model="filters.source" class="db-field-control" @change="refresh">
                    <option value="">{{ $t('label.all_sources') }}</option>
                    <option value="online">{{ $t('label.online') }}</option>
                    <option value="qr">{{ $t('label.qr_order') }}</option>
                    <option :value="sourceEnum.POS">{{ $t('label.pos') }}</option>
                    <option :value="sourceEnum.WAITER">{{ $t('label.waiter') }}</option>
                </select>
                <select v-model="filters.sort" class="db-field-control" @change="refresh">
                    <option value="priority">{{ $t('label.sort_priority') }}</option>
                    <option value="newest">{{ $t('label.sort_newest') }}</option>
                    <option value="oldest">{{ $t('label.sort_oldest') }}</option>
                    <option value="longest_waiting">{{ $t('label.sort_longest_waiting') }}</option>
                    <option value="table">{{ $t('label.sort_table') }}</option>
                    <option value="order_number">{{ $t('label.sort_order_number') }}</option>
                </select>
                <select v-model="filters.kitchen_priority" class="db-field-control" @change="refresh">
                    <option value="">{{ $t('label.all_priorities') }}</option>
                    <option :value="kitchenPriorityEnum.NORMAL">{{ $t('label.priority_normal') }}</option>
                    <option :value="kitchenPriorityEnum.HIGH">{{ $t('label.priority_high') }}</option>
                    <option :value="kitchenPriorityEnum.URGENT">{{ $t('label.priority_urgent') }}</option>
                    <option :value="kitchenPriorityEnum.VIP">{{ $t('label.priority_vip') }}</option>
                </select>
                <input v-model="filters.from_date" type="date" class="db-field-control" @change="refresh"/>
                <input v-model="filters.to_date" type="date" class="db-field-control" @change="refresh"/>
            </form>

            <div class="px-4 pb-4 grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
                <article
                    v-for="order in orders"
                    :key="order.id"
                    class="rounded-2xl border bg-white p-4 flex flex-col gap-3 transition hover:shadow-md"
                    :class="cardClass(order)"
                >
                    <div class="flex items-start justify-between gap-3">
                        <div class="min-w-0">
                            <div class="flex flex-wrap items-center gap-1.5 mb-1.5">
                                <span class="text-[10px] font-bold uppercase tracking-wide px-2 py-0.5 rounded"
                                      :class="channelChipClass(order)">
                                    {{ channelLabel(order) }}
                                </span>
                                <span class="text-[10px] font-bold uppercase tracking-wide px-2 py-0.5 rounded"
                                      :class="priorityChipClass(order.kitchen_priority)">
                                    {{ priorityLabel(order) }}
                                </span>
                            </div>
                            <p class="text-2xl font-bold tracking-tight text-heading">#{{ order.order_serial_no }}</p>
                            <p class="text-sm text-[#6E7191] mt-0.5">{{ order.order_time }}</p>
                        </div>
                        <div class="text-right shrink-0">
                            <span class="inline-block text-[11px] font-bold uppercase tracking-wide px-2 py-1 rounded"
                                  :class="statusChipClass(order.status)">
                                {{ order.status_name }}
                            </span>
                            <p class="mt-2 text-lg font-bold tabular-nums" :class="elapsedClass(order)">
                                {{ elapsedLabel(order) }}
                                <span v-if="isTimerFrozen(order)" class="block text-[10px] font-medium text-[#6E7191]">
                                    {{ $t('label.final') }}
                                </span>
                            </p>
                        </div>
                    </div>

                    <div v-if="order.table" class="rounded-xl bg-[#F7F7FC] px-3 py-2.5">
                        <p class="text-xs text-[#6E7191]">{{ $t('label.table') }}</p>
                        <p class="text-lg font-semibold text-heading">
                            {{ order.table.table_number }}
                            <span v-if="order.table.name" class="text-sm font-medium text-[#6E7191]">· {{ order.table.name }}</span>
                        </p>
                    </div>

                    <div class="text-sm space-y-1 text-heading">
                        <p v-if="order.restaurant">
                            <span class="text-[#6E7191]">{{ $t('label.restaurant') }}:</span>
                            {{ order.restaurant.name }}
                        </p>
                        <p v-if="order.waiter">
                            <span class="text-[#6E7191]">{{ $t('label.waiter') }}:</span>
                            {{ order.waiter.name }}
                        </p>
                        <p v-if="order.customer">
                            <span class="text-[#6E7191]">{{ $t('label.customer') }}:</span>
                            {{ order.customer.name }}
                        </p>
                        <p v-if="order.preparation_time" class="text-[#6E7191]">
                            {{ $t('label.preparation_time') }}: {{ order.preparation_time }} min
                        </p>
                    </div>

                    <div v-if="order.order_note"
                         class="rounded-lg px-3 py-2 bg-amber-50 border border-amber-100 text-amber-900 text-sm font-medium">
                        {{ order.order_note }}
                    </div>

                    <ul class="space-y-2.5 border-t border-[#EFF0F6] pt-3">
                        <li v-for="item in order.order_items" :key="item.id" class="text-sm">
                            <p class="font-semibold text-heading text-base leading-snug">
                                {{ item.quantity }}× {{ item.item_name }}
                            </p>
                            <p v-for="(line, i) in (item.variation_lines || [])" :key="'v'+i"
                               class="text-[#6E7191] text-xs mt-0.5 pl-1">{{ line }}</p>
                            <p v-for="(line, i) in (item.extra_lines || [])" :key="'e'+i"
                               class="text-primary text-xs mt-0.5 pl-1 font-medium">+ {{ line }}</p>
                            <p v-if="item.instruction"
                               class="mt-1 inline-flex rounded bg-rose-50 text-rose-700 px-2 py-0.5 text-xs font-medium">
                                {{ item.instruction }}
                            </p>
                        </li>
                    </ul>

                    <div class="mt-auto grid grid-cols-2 gap-2 pt-1">
                        <button
                            v-if="canAccept(order)"
                            type="button"
                            class="col-span-2 min-h-12 rounded-xl text-sm font-semibold text-white bg-primary active:scale-[0.99]"
                            @click="accept(order)"
                        >
                            {{ $t('button.accept') }}
                        </button>
                        <button
                            v-if="canPrepare(order)"
                            type="button"
                            class="col-span-2 min-h-12 rounded-xl text-sm font-semibold text-white bg-amber-500 active:scale-[0.99]"
                            @click="preparing(order)"
                        >
                            {{ $t('button.preparing') }}
                        </button>
                        <button
                            v-if="canReady(order)"
                            type="button"
                            class="col-span-2 min-h-12 rounded-xl text-sm font-semibold text-white bg-emerald-600 active:scale-[0.99]"
                            @click="ready(order)"
                        >
                            {{ $t('button.ready') }}
                        </button>
                        <button
                            v-if="canComplete(order)"
                            type="button"
                            class="col-span-2 min-h-12 rounded-xl text-sm font-semibold text-white bg-emerald-700 active:scale-[0.99]"
                            @click="complete(order)"
                        >
                            {{ $t('button.mark_completed') }}
                        </button>
                        <button
                            v-if="canReject(order)"
                            type="button"
                            class="min-h-11 rounded-xl text-sm font-semibold text-white bg-rose-500 active:scale-[0.99]"
                            :class="canCancel(order) ? '' : 'col-span-2'"
                            @click="reject(order)"
                        >
                            {{ $t('button.reject') }}
                        </button>
                        <button
                            v-if="canCancel(order)"
                            type="button"
                            class="min-h-11 rounded-xl text-sm font-semibold border border-rose-200 text-rose-700 bg-white active:scale-[0.99]"
                            :class="canReject(order) ? '' : 'col-span-2'"
                            @click="cancel(order)"
                        >
                            {{ $t('button.cancel') }}
                        </button>
                        <router-link
                            :to="{name: 'admin.kitchen.orders.show', params: {id: order.id}}"
                            class="col-span-2 min-h-10 rounded-xl text-sm font-semibold border border-[#EFF0F6] text-heading flex items-center justify-center hover:border-primary"
                        >
                            {{ $t('button.view') }}
                        </router-link>
                    </div>
                </article>
            </div>

            <div v-if="!orders.length && !loading.isActive && !searching" class="p-10 text-center text-[#6E7191]">
                {{ filters.search ? $t('label.no_search_results') : $t('message.no_data_found') }}
            </div>
        </div>
    </div>
</template>

<script>
import LoadingComponent from "../../common/LoadingComponent.vue";
import {useKitchenOrderStore} from "../../../stores/kitchenOrder.js";
import {useCommonStore} from "../../../stores/common.js";
import {useAuthStore} from "../../../stores/auth.js";
import {useDefaultAccessStore} from "../../../stores/defaultAccess.js";
import orderStatusEnum from "../../../enums/modules/orderStatusEnum.js";
import kitchenPriorityEnum from "../../../enums/modules/kitchenPriorityEnum.js";
import alertService from "../../../services/alertService.js";
import appService from "../../../services/appService.js";
import {getAuthRestaurantId, subscribeRestaurantRealtime} from "../../../composables/useRealtime.js";
import roleEnum from "../../../enums/modules/roleEnum.js";
import sourceEnum from "../../../enums/modules/sourceEnum.js";
import orderTypeEnum from "../../../enums/modules/orderTypeEnum.js";

export default {
    name: "KitchenQueueComponent",
    components: {LoadingComponent},
    setup() {
        return {
            kitchenOrderStore: useKitchenOrderStore(),
            commonStore: useCommonStore(),
            authStore: useAuthStore(),
            defaultAccessStore: useDefaultAccessStore(),
            orderStatusEnum,
            kitchenPriorityEnum,
            sourceEnum,
            orderTypeEnum,
        };
    },
    data() {
        return {
            loading: {isActive: false},
            searching: false,
            filtersOpen: false,
            nowTick: Date.now(),
            tickTimer: null,
            pollTimer: null,
            searchTimer: null,
            unsubscribeRealtime: null,
            filters: {
                period: 'today',
                status: this.$route.query.status || '',
                search: '',
                sort: 'priority',
                kitchen_priority: '',
                source: '',
                from_date: '',
                to_date: '',
                paginate: 0,
            }
        };
    },
    computed: {
        orders() {
            return this.kitchenOrderStore.lists || [];
        },
        statusTabs() {
            return [
                {value: '', label: this.$t('label.queue')},
                {value: orderStatusEnum.PENDING, label: this.$t('label.pending')},
                {value: orderStatusEnum.ACCEPT, label: this.$t('label.accepted')},
                {value: orderStatusEnum.PREPARING, label: this.$t('label.preparing')},
                {value: orderStatusEnum.PREPARED, label: this.$t('label.ready')},
                {value: orderStatusEnum.DELIVERED, label: this.$t('label.completed')},
                {value: orderStatusEnum.CANCELED, label: this.$t('label.canceled')},
            ];
        }
    },
    watch: {
        'filters.search'() {
            if (this.searchTimer) {
                clearTimeout(this.searchTimer);
            }
            this.searching = true;
            this.searchTimer = setTimeout(() => {
                this.refreshQuiet().finally(() => {
                    this.searching = false;
                });
            }, 300);
        },
    },
    mounted() {
        this.commonStore.update({top_sidebar: false});
        this.refresh();
        this.tickTimer = setInterval(() => {
            this.nowTick = Date.now();
        }, 1000);
        this.pollTimer = setInterval(() => this.refreshQuiet(), 30 * 1000);
        this.bindRealtime();
    },
    beforeUnmount() {
        if (this.tickTimer) clearInterval(this.tickTimer);
        if (this.pollTimer) clearInterval(this.pollTimer);
        if (this.searchTimer) clearTimeout(this.searchTimer);
        if (typeof this.unsubscribeRealtime === 'function') this.unsubscribeRealtime();
    },
    methods: {
        bindRealtime() {
            const restaurantId = getAuthRestaurantId(this.authStore.info, this.defaultAccessStore.lists);
            const isAdmin = Number(this.authStore.info?.role_id) === roleEnum.ADMIN;
            this.unsubscribeRealtime = subscribeRestaurantRealtime({
                restaurantId: restaurantId || 1,
                roles: isAdmin ? ['admin'] : [],
                onKitchenOrder: () => this.refreshQuiet(),
            });
        },
        buildPayload() {
            const payload = {...this.filters};
            if (payload.from_date || payload.to_date) {
                payload.period = 'custom';
            }
            Object.keys(payload).forEach((key) => {
                if (payload[key] === '' || payload[key] === null || payload[key] === undefined) {
                    delete payload[key];
                }
            });
            return payload;
        },
        refreshQuiet() {
            return this.kitchenOrderStore.fetch(this.buildPayload()).catch(() => {});
        },
        clearSearch() {
            this.filters.search = '';
            this.refreshQuiet();
        },
        permissionChecker(permission) {
            return appService.permissionChecker(permission);
        },
        setStatus(status) {
            this.filters.status = status;
            this.refresh();
        },
        refresh() {
            this.loading.isActive = true;
            this.kitchenOrderStore.fetch(this.buildPayload()).then(() => {
                this.loading.isActive = false;
            }).catch((err) => {
                this.loading.isActive = false;
                alertService.error(err.response?.data?.message || this.$t('message.something_wrong'));
            });
        },
        cardClass(order) {
            if (order.status === orderStatusEnum.PENDING) return 'border-sky-300';
            if (order.status === orderStatusEnum.ACCEPT) return 'border-sky-400';
            if (order.status === orderStatusEnum.PREPARING) return 'border-amber-400';
            if (order.status === orderStatusEnum.PREPARED) return 'border-emerald-400';
            if ([orderStatusEnum.CANCELED, orderStatusEnum.REJECTED].includes(order.status)) return 'border-rose-200 opacity-80';
            return 'border-[#EFF0F6]';
        },
        statusChipClass(status) {
            if (status === orderStatusEnum.PREPARING) return 'bg-amber-100 text-amber-800';
            if (status === orderStatusEnum.PREPARED) return 'bg-emerald-100 text-emerald-800';
            if (status === orderStatusEnum.DELIVERED) return 'bg-slate-100 text-slate-700';
            if ([orderStatusEnum.CANCELED, orderStatusEnum.REJECTED].includes(status)) return 'bg-rose-100 text-rose-700';
            return 'bg-sky-100 text-sky-800';
        },
        priorityChipClass(priority) {
            if (priority >= kitchenPriorityEnum.VIP) return 'bg-violet-100 text-violet-800';
            if (priority >= kitchenPriorityEnum.URGENT) return 'bg-rose-100 text-rose-700';
            if (priority >= kitchenPriorityEnum.HIGH) return 'bg-orange-100 text-orange-800';
            return 'bg-[#F7F7FC] text-[#6E7191]';
        },
        channelKey(order) {
            if (order.channel_key) return order.channel_key;
            if (Number(order.order_type) === orderTypeEnum.DINING_TABLE && Number(order.table?.id || order.table_id) > 0) {
                const src = Number(order.source);
                if (src === sourceEnum.WAITER) return 'waiter';
                if (src === sourceEnum.POS) return 'pos';
                return 'qr';
            }
            const src = Number(order.source);
            if (src === sourceEnum.POS) return 'pos';
            if (src === sourceEnum.WAITER) return 'waiter';
            if (src === sourceEnum.APP) return 'app';
            return 'online';
        },
        channelLabel(order) {
            const key = this.channelKey(order);
            const map = {
                qr: this.$t('label.qr_order'),
                waiter: this.$t('label.waiter'),
                pos: this.$t('label.pos'),
                app: this.$t('label.app'),
                online: this.$t('label.online'),
            };
            return map[key] || key;
        },
        channelChipClass(order) {
            const key = this.channelKey(order);
            if (key === 'qr') return 'bg-sky-100 text-sky-800';
            if (key === 'waiter') return 'bg-indigo-100 text-indigo-800';
            if (key === 'pos') return 'bg-amber-100 text-amber-800';
            return 'bg-[#F7F7FC] text-[#6E7191]';
        },
        priorityLabel(order) {
            const map = {
                [kitchenPriorityEnum.NORMAL]: this.$t('label.priority_normal'),
                [kitchenPriorityEnum.HIGH]: this.$t('label.priority_high'),
                [kitchenPriorityEnum.URGENT]: this.$t('label.priority_urgent'),
                [kitchenPriorityEnum.VIP]: this.$t('label.priority_vip'),
            };
            return map[order.kitchen_priority] || order.priority_label || this.$t('label.priority_normal');
        },
        elapsedSeconds(order) {
            void this.nowTick;
            const from = order.elapsed_from || order.order_datetime_iso;
            if (!from) return 0;
            const start = new Date(from).getTime();
            if (Number.isNaN(start)) return 0;
            let end = Date.now();
            if (this.isTimerFrozen(order)) {
                const to = order.elapsed_to || order.updated_at;
                const parsed = to ? new Date(to).getTime() : NaN;
                end = Number.isNaN(parsed) ? start : parsed;
            }
            return Math.max(0, Math.floor((end - start) / 1000));
        },
        isTimerFrozen(order) {
            if (order.timer_frozen) return true;
            return [
                orderStatusEnum.DELIVERED,
                orderStatusEnum.CANCELED,
                orderStatusEnum.REJECTED,
                orderStatusEnum.RETURNED,
            ].includes(Number(order.status));
        },
        elapsedLabel(order) {
            const s = this.elapsedSeconds(order);
            const m = Math.floor(s / 60);
            const r = s % 60;
            return `${m}:${String(r).padStart(2, '0')}`;
        },
        elapsedClass(order) {
            if (this.isTimerFrozen(order)) return 'text-[#6E7191]';
            const m = Math.floor(this.elapsedSeconds(order) / 60);
            const eta = Number(order.preparation_time || 0);
            if (eta && m >= eta) return 'text-rose-600';
            if (eta && m >= Math.max(1, eta - 5)) return 'text-amber-600';
            return 'text-heading';
        },
        canAccept(order) {
            return this.permissionChecker('kitchen_accept')
                && [orderStatusEnum.PENDING, orderStatusEnum.ACCEPT].includes(order.status)
                && !order.accepted_by;
        },
        canPrepare(order) {
            return this.permissionChecker('kitchen_prepare')
                && order.status === orderStatusEnum.ACCEPT;
        },
        canReady(order) {
            return this.permissionChecker('kitchen_ready')
                && order.status === orderStatusEnum.PREPARING;
        },
        canComplete(order) {
            return this.permissionChecker('kitchen_ready')
                && order.status === orderStatusEnum.PREPARED;
        },
        canReject(order) {
            return this.permissionChecker('kitchen_reject')
                && [orderStatusEnum.PENDING, orderStatusEnum.ACCEPT, orderStatusEnum.PREPARING].includes(order.status);
        },
        canCancel(order) {
            return this.permissionChecker('kitchen_cancel')
                && [orderStatusEnum.PENDING, orderStatusEnum.ACCEPT, orderStatusEnum.PREPARING, orderStatusEnum.PREPARED].includes(order.status);
        },
        async accept(order) {
            try {
                this.loading.isActive = true;
                await this.kitchenOrderStore.accept(order.id, order.updated_at);
                await this.refresh();
            } catch (err) {
                this.loading.isActive = false;
                alertService.error(err.response?.data?.message || this.$t('message.something_wrong'));
            }
        },
        async preparing(order) {
            try {
                this.loading.isActive = true;
                await this.kitchenOrderStore.preparing(order.id, order.updated_at);
                await this.refresh();
            } catch (err) {
                this.loading.isActive = false;
                alertService.error(err.response?.data?.message || this.$t('message.something_wrong'));
            }
        },
        async ready(order) {
            try {
                this.loading.isActive = true;
                await this.kitchenOrderStore.ready(order.id, order.updated_at);
                await this.refresh();
            } catch (err) {
                this.loading.isActive = false;
                alertService.error(err.response?.data?.message || this.$t('message.something_wrong'));
            }
        },
        async complete(order) {
            try {
                this.loading.isActive = true;
                await this.kitchenOrderStore.complete(order.id, order.updated_at);
                await this.refresh();
                alertService.success(this.$t('message.kitchen_order_completed'));
            } catch (err) {
                this.loading.isActive = false;
                alertService.error(err.response?.data?.message || this.$t('message.something_wrong'));
            }
        },
        async reject(order) {
            const reason = window.prompt(this.$t('message.kitchen_reject_reason'), '');
            if (reason === null) return;
            try {
                this.loading.isActive = true;
                await this.kitchenOrderStore.reject(order.id, reason || null, order.updated_at);
                await this.refresh();
            } catch (err) {
                this.loading.isActive = false;
                alertService.error(err.response?.data?.message || this.$t('message.something_wrong'));
            }
        },
        async cancel(order) {
            const reason = window.prompt(this.$t('message.kitchen_cancel_reason'), '');
            if (reason === null) return;
            try {
                this.loading.isActive = true;
                await this.kitchenOrderStore.cancel(order.id, reason || null, order.updated_at);
                await this.refresh();
            } catch (err) {
                this.loading.isActive = false;
                alertService.error(err.response?.data?.message || this.$t('message.something_wrong'));
            }
        }
    }
}
</script>
