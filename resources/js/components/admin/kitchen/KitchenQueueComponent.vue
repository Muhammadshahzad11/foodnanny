<template>
    <LoadingComponent :props="loading"/>
    <KitchenTicketPrintSheet :payload="printPayload"/>

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
                <button type="button" class="db-btn py-3 px-5 text-base text-white bg-primary" @click="refresh">
                    {{ $t('button.refresh') }}
                </button>
            </div>

            <div class="px-4 pb-3 flex flex-wrap gap-2">
                <button
                    v-for="tab in statusTabs"
                    :key="tab.value === '' ? 'all' : tab.value"
                    type="button"
                    class="min-h-12 px-4 py-2.5 rounded-xl text-base font-semibold border transition"
                    :class="String(filters.status) === String(tab.value)
                        ? 'bg-primary border-primary text-white'
                        : 'bg-white border-[#EFF0F6] text-heading hover:border-primary'"
                    @click="setStatus(tab.value)"
                >
                    {{ tab.label }}
                </button>
            </div>

            <form class="px-4 pb-4 grid grid-cols-1 md:grid-cols-2 xl:grid-cols-6 gap-3" @submit.prevent="refresh">
                <input v-model="filters.search" type="text" class="db-field-control xl:col-span-2"
                       :placeholder="$t('label.search_order_table_waiter')"/>
                <select v-model="filters.sort" class="db-field-control">
                    <option value="priority">{{ $t('label.sort_priority') }}</option>
                    <option value="oldest">{{ $t('label.sort_oldest') }}</option>
                    <option value="preparation_time">{{ $t('label.sort_preparation_time') }}</option>
                    <option value="table">{{ $t('label.sort_table') }}</option>
                    <option value="waiter">{{ $t('label.sort_waiter') }}</option>
                    <option value="order_number">{{ $t('label.sort_order_number') }}</option>
                </select>
                <select v-model="filters.kitchen_priority" class="db-field-control">
                    <option value="">{{ $t('label.all_priorities') }}</option>
                    <option :value="kitchenPriorityEnum.NORMAL">{{ $t('label.priority_normal') }}</option>
                    <option :value="kitchenPriorityEnum.HIGH">{{ $t('label.priority_high') }}</option>
                    <option :value="kitchenPriorityEnum.URGENT">{{ $t('label.priority_urgent') }}</option>
                    <option :value="kitchenPriorityEnum.VIP">{{ $t('label.priority_vip') }}</option>
                </select>
                <input v-model="filters.from_date" type="date" class="db-field-control"/>
                <input v-model="filters.to_date" type="date" class="db-field-control"/>
                <button type="submit" class="db-btn py-3 text-base text-white bg-primary md:col-span-2 xl:col-span-1">
                    {{ $t('button.search') }}
                </button>
            </form>

            <div class="px-4 pb-4 grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
                <article
                    v-for="order in orders"
                    :key="order.id"
                    class="rounded-2xl border-2 p-5 flex flex-col gap-4 bg-white shadow-sm"
                    :class="cardClass(order)"
                >
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <p class="text-3xl font-bold tracking-tight text-heading">#{{ order.order_serial_no }}</p>
                            <p class="text-base text-[#6E7191] mt-1">{{ order.order_time }}</p>
                            <p class="text-lg font-semibold mt-1" :class="elapsedClass(order)">
                                ⏱ {{ elapsedLabel(order) }}
                                <span v-if="isTimerFrozen(order)" class="text-xs font-normal text-[#6E7191] ml-1">
                                    ({{ $t('label.final') }})
                                </span>
                            </p>
                        </div>
                        <div class="flex flex-col items-end gap-2">
                            <span class="text-xs font-bold uppercase tracking-wide px-2.5 py-1.5 rounded-lg"
                                  :class="statusChipClass(order.status)">
                                {{ order.status_name }}
                            </span>
                            <span class="text-xs font-bold uppercase px-2.5 py-1.5 rounded-lg"
                                  :class="priorityChipClass(order.kitchen_priority)">
                                {{ priorityLabel(order) }}
                            </span>
                        </div>
                    </div>

                    <div class="text-base space-y-1.5 text-heading">
                        <p v-if="order.restaurant" class="font-medium">
                            <span class="text-[#6E7191]">{{ $t('label.restaurant') }}:</span>
                            {{ order.restaurant.name }}
                        </p>
                        <p v-if="order.table" class="text-xl font-semibold">
                            <span class="text-[#6E7191] text-base font-normal">{{ $t('label.table') }}:</span>
                            {{ order.table.table_number }}
                            <span v-if="order.table.name"> · {{ order.table.name }}</span>
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
                         class="rounded-xl px-3 py-2.5 bg-amber-50 border border-amber-200 text-amber-900 text-base font-semibold">
                        ⚠ {{ order.order_note }}
                    </div>

                    <ul class="space-y-3 border-t border-[#EFF0F6] pt-3">
                        <li v-for="item in order.order_items" :key="item.id" class="text-base">
                            <p class="font-bold text-heading text-lg leading-snug">
                                {{ item.quantity }}× {{ item.item_name }}
                            </p>
                            <p v-for="(line, i) in (item.variation_lines || [])" :key="'v'+i"
                               class="text-[#4E4B66] text-sm mt-0.5 pl-1">• {{ line }}</p>
                            <p v-for="(line, i) in (item.extra_lines || [])" :key="'e'+i"
                               class="text-emerald-700 text-sm mt-0.5 pl-1 font-medium">+ {{ line }}</p>
                            <p v-if="item.instruction"
                               class="mt-1 inline-flex rounded-lg bg-rose-50 text-rose-700 px-2 py-1 text-sm font-semibold">
                                {{ item.instruction }}
                            </p>
                        </li>
                    </ul>

                    <div class="mt-auto grid grid-cols-2 gap-2.5 pt-2">
                        <button
                            v-if="canAccept(order)"
                            type="button"
                            class="min-h-14 rounded-xl text-base font-bold text-white bg-sky-500 active:scale-[0.98]"
                            @click="accept(order)"
                        >
                            {{ $t('button.accept') }}
                        </button>
                        <button
                            v-if="canPrepare(order)"
                            type="button"
                            class="min-h-14 rounded-xl text-base font-bold text-white bg-amber-500 active:scale-[0.98]"
                            @click="preparing(order)"
                        >
                            {{ $t('button.preparing') }}
                        </button>
                        <button
                            v-if="canReady(order)"
                            type="button"
                            class="min-h-14 rounded-xl text-base font-bold text-white bg-emerald-600 active:scale-[0.98]"
                            @click="ready(order)"
                        >
                            {{ $t('button.ready') }}
                        </button>
                        <button
                            v-if="canComplete(order)"
                            type="button"
                            class="min-h-14 rounded-xl text-base font-bold text-white bg-emerald-700 active:scale-[0.98]"
                            @click="complete(order)"
                        >
                            {{ $t('button.mark_completed') }}
                        </button>
                        <button
                            v-if="permissionChecker('kitchen_print')"
                            type="button"
                            class="min-h-14 rounded-xl text-base font-bold text-white bg-primary active:scale-[0.98]"
                            @click="printTicket(order)"
                        >
                            {{ $t('button.print_kot') }}
                        </button>
                        <button
                            v-if="canReject(order)"
                            type="button"
                            class="min-h-14 rounded-xl text-base font-bold text-white bg-rose-500 active:scale-[0.98]"
                            @click="reject(order)"
                        >
                            {{ $t('button.reject') }}
                        </button>
                        <button
                            v-if="canCancel(order)"
                            type="button"
                            class="min-h-14 rounded-xl text-base font-bold border-2 border-rose-300 text-rose-700 bg-white active:scale-[0.98]"
                            @click="cancel(order)"
                        >
                            {{ $t('button.cancel') }}
                        </button>
                        <router-link
                            :to="{name: 'admin.kitchen.orders.show', params: {id: order.id}}"
                            class="min-h-14 rounded-xl text-base font-bold border-2 border-[#EFF0F6] text-heading flex items-center justify-center col-span-2 hover:border-primary"
                        >
                            {{ $t('button.view') }}
                        </router-link>
                    </div>
                </article>
            </div>

            <div v-if="!orders.length" class="p-10 text-center text-lg text-[#6E7191]">
                {{ $t('message.no_data_found') }}
            </div>
        </div>
    </div>
</template>

<script>
import LoadingComponent from "../../common/LoadingComponent.vue";
import KitchenTicketPrintSheet from "./KitchenTicketPrintSheet.vue";
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

export default {
    name: "KitchenQueueComponent",
    components: {LoadingComponent, KitchenTicketPrintSheet},
    setup() {
        return {
            kitchenOrderStore: useKitchenOrderStore(),
            commonStore: useCommonStore(),
            authStore: useAuthStore(),
            defaultAccessStore: useDefaultAccessStore(),
            orderStatusEnum,
            kitchenPriorityEnum,
        };
    },
    data() {
        return {
            loading: {isActive: false},
            printPayload: null,
            nowTick: Date.now(),
            tickTimer: null,
            pollTimer: null,
            unsubscribeRealtime: null,
            filters: {
                period: 'today',
                status: this.$route.query.status || '',
                search: '',
                sort: 'priority',
                kitchen_priority: '',
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
    mounted() {
        this.commonStore.update({top_sidebar: false});
        this.refresh();
        this.tickTimer = setInterval(() => {
            this.nowTick = Date.now();
        }, 1000);
        // Safety net if realtime is missed (every 30s)
        this.pollTimer = setInterval(() => this.refreshQuiet(), 30 * 1000);
        this.bindRealtime();
    },
    beforeUnmount() {
        if (this.tickTimer) {
            clearInterval(this.tickTimer);
        }
        if (this.pollTimer) {
            clearInterval(this.pollTimer);
        }
        if (typeof this.unsubscribeRealtime === 'function') {
            this.unsubscribeRealtime();
        }
    },
    methods: {
        bindRealtime() {
            const restaurantId = getAuthRestaurantId(this.authStore.info, this.defaultAccessStore.lists);
            const isAdmin = Number(this.authStore.info?.role_id) === roleEnum.ADMIN;
            this.unsubscribeRealtime = subscribeRestaurantRealtime({
                restaurantId: restaurantId || 1,
                roles: isAdmin ? ['admin'] : [],
                onKitchenOrder: () => {
                    this.refreshQuiet();
                },
            });
        },
        refreshQuiet() {
            const payload = {...this.filters};
            if (payload.from_date || payload.to_date) {
                payload.period = 'custom';
            }
            this.kitchenOrderStore.fetch(payload).catch(() => {});
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
            const payload = {...this.filters};
            if (payload.from_date || payload.to_date) {
                payload.period = 'custom';
            }
            this.kitchenOrderStore.fetch(payload).then(() => {
                this.loading.isActive = false;
            }).catch((err) => {
                this.loading.isActive = false;
                alertService.error(err.response?.data?.message || this.$t('message.something_wrong'));
            });
        },
        cardClass(order) {
            if (order.status === orderStatusEnum.PENDING) return 'border-sky-400';
            if (order.status === orderStatusEnum.ACCEPT) return 'border-sky-500';
            if (order.status === orderStatusEnum.PREPARING) return 'border-amber-500';
            if (order.status === orderStatusEnum.PREPARED) return 'border-emerald-500';
            if ([orderStatusEnum.CANCELED, orderStatusEnum.REJECTED].includes(order.status)) return 'border-rose-300 opacity-80';
            return 'border-[#EFF0F6]';
        },
        statusChipClass(status) {
            if (status === orderStatusEnum.PREPARING) return 'bg-amber-100 text-amber-800';
            if (status === orderStatusEnum.PREPARED) return 'bg-emerald-100 text-emerald-800';
            if (status === orderStatusEnum.DELIVERED) return 'bg-slate-200 text-slate-700';
            if ([orderStatusEnum.CANCELED, orderStatusEnum.REJECTED].includes(status)) return 'bg-rose-100 text-rose-800';
            return 'bg-sky-100 text-sky-800';
        },
        priorityChipClass(priority) {
            if (priority >= kitchenPriorityEnum.VIP) return 'bg-violet-100 text-violet-800';
            if (priority >= kitchenPriorityEnum.URGENT) return 'bg-rose-100 text-rose-800';
            if (priority >= kitchenPriorityEnum.HIGH) return 'bg-orange-100 text-orange-800';
            return 'bg-slate-100 text-slate-700';
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

            // Completed / canceled / rejected: freeze at finish time (do not keep counting)
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
            if (this.isTimerFrozen(order)) {
                return 'text-[#6E7191]';
            }
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
        },
        async printTicket(order) {
            try {
                this.loading.isActive = true;
                const res = await this.kitchenOrderStore.printData(order.id);
                this.printPayload = res.data.data.payload;
                this.loading.isActive = false;
                this.$nextTick(() => window.print());
            } catch (err) {
                this.loading.isActive = false;
                alertService.error(err.response?.data?.message || this.$t('message.something_wrong'));
            }
        }
    }
}
</script>
