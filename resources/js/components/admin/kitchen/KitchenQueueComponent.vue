<template>
    <LoadingComponent :props="loading"/>
    <KitchenTicketPrintSheet :payload="printPayload"/>

    <div class="col-12">
        <div class="db-card">
            <div class="db-card-header border-none">
                <div>
                    <router-link :to="{name: 'admin.kitchen.dashboard'}" class="text-sm text-primary">
                        ← {{ $t('menu.kitchen') }}
                    </router-link>
                    <h3 class="db-card-title mt-1">{{ $t('label.kitchen_queue') }}</h3>
                </div>
                <button type="button" class="db-btn py-2 text-white bg-primary" @click="refresh">
                    {{ $t('button.refresh') }}
                </button>
            </div>

            <div class="px-4 pb-3 flex flex-wrap gap-2">
                <button
                    v-for="tab in statusTabs"
                    :key="tab.value === '' ? 'all' : tab.value"
                    type="button"
                    class="px-4 py-2 rounded-lg text-sm font-medium border transition"
                    :class="String(filters.status) === String(tab.value)
                        ? 'bg-primary border-primary text-white'
                        : 'bg-white border-[#EFF0F6] text-heading hover:border-primary'"
                    @click="setStatus(tab.value)"
                >
                    {{ tab.label }}
                </button>
            </div>

            <form class="px-4 pb-4 grid grid-cols-1 md:grid-cols-4 gap-3" @submit.prevent="refresh">
                <input v-model="filters.search" type="text" class="db-field-control"
                       :placeholder="$t('label.search_order_table_waiter')"/>
                <select v-model="filters.sort" class="db-field-control">
                    <option value="order_time">{{ $t('label.sort_order_time') }}</option>
                    <option value="priority">{{ $t('label.sort_priority') }}</option>
                    <option value="table">{{ $t('label.sort_table') }}</option>
                    <option value="waiter">{{ $t('label.sort_waiter') }}</option>
                    <option value="order_number">{{ $t('label.sort_order_number') }}</option>
                </select>
                <button type="submit" class="db-btn py-2 text-white bg-primary">
                    {{ $t('button.search') }}
                </button>
            </form>

            <div class="px-4 pb-4 grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
                <article
                    v-for="order in orders"
                    :key="order.id"
                    class="rounded-xl border p-4 flex flex-col gap-3 bg-white"
                    :class="cardClass(order.status)"
                >
                    <div class="flex items-start justify-between gap-2">
                        <div>
                            <p class="text-xl font-semibold text-heading">#{{ order.order_serial_no }}</p>
                            <p class="text-sm text-[#6E7191]">{{ order.order_time }}</p>
                        </div>
                        <span class="text-xs font-semibold uppercase px-2 py-1 rounded bg-[#F7F7FC] text-heading">
                            {{ order.status_name }}
                        </span>
                    </div>

                    <div class="text-sm space-y-1 text-heading">
                        <p v-if="order.table">
                            <span class="text-[#6E7191]">{{ $t('label.table') }}:</span>
                            {{ order.table.table_number }} · {{ order.table.name }}
                        </p>
                        <p v-if="order.waiter">
                            <span class="text-[#6E7191]">{{ $t('label.waiter') }}:</span>
                            {{ order.waiter.name }}
                        </p>
                        <p v-if="order.customer">
                            <span class="text-[#6E7191]">{{ $t('label.customer') }}:</span>
                            {{ order.customer.name }}
                        </p>
                        <p v-if="order.accepted_by">
                            <span class="text-[#6E7191]">{{ $t('label.accepted_by') }}:</span>
                            {{ order.accepted_by.name }}
                        </p>
                        <p v-if="order.order_note" class="text-primary font-medium">
                            {{ order.order_note }}
                        </p>
                    </div>

                    <ul class="space-y-2 border-t border-[#EFF0F6] pt-3">
                        <li v-for="item in order.order_items" :key="item.id" class="text-sm">
                            <span class="font-semibold text-heading">{{ item.quantity }}× {{ item.item_name }}</span>
                            <p v-if="item.instruction" class="text-primary text-xs mt-0.5">{{ item.instruction }}</p>
                            <p v-if="variationText(item)" class="text-[#6E7191] text-xs">{{ variationText(item) }}</p>
                            <p v-if="extraText(item)" class="text-[#6E7191] text-xs">{{ extraText(item) }}</p>
                        </li>
                    </ul>

                    <div class="mt-auto grid grid-cols-2 gap-2 pt-2">
                        <button
                            v-if="canAccept(order)"
                            type="button"
                            class="h-12 rounded-lg text-sm font-semibold text-white bg-sky-500"
                            @click="accept(order)"
                        >
                            {{ $t('button.accept') }}
                        </button>
                        <button
                            v-if="canPrepare(order)"
                            type="button"
                            class="h-12 rounded-lg text-sm font-semibold text-white bg-amber-500"
                            @click="preparing(order)"
                        >
                            {{ $t('button.preparing') }}
                        </button>
                        <button
                            v-if="canReady(order)"
                            type="button"
                            class="h-12 rounded-lg text-sm font-semibold text-white bg-[#1AB759]"
                            @click="ready(order)"
                        >
                            {{ $t('button.ready') }}
                        </button>
                        <button
                            type="button"
                            class="h-12 rounded-lg text-sm font-semibold text-white bg-primary"
                            @click="printTicket(order)"
                        >
                            {{ $t('button.print_kot') }}
                        </button>
                        <router-link
                            :to="{name: 'admin.kitchen.orders.show', params: {id: order.id}}"
                            class="h-12 rounded-lg text-sm font-semibold border border-[#EFF0F6] text-heading flex items-center justify-center col-span-2 hover:border-primary"
                        >
                            {{ $t('button.view') }}
                        </router-link>
                    </div>
                </article>
            </div>

            <div v-if="!orders.length" class="p-8 text-center text-[#6E7191]">
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
import orderStatusEnum from "../../../enums/modules/orderStatusEnum.js";
import alertService from "../../../services/alertService.js";
import appService from "../../../services/appService.js";

export default {
    name: "KitchenQueueComponent",
    components: {LoadingComponent, KitchenTicketPrintSheet},
    setup() {
        return {
            kitchenOrderStore: useKitchenOrderStore(),
            commonStore: useCommonStore(),
            orderStatusEnum
        };
    },
    data() {
        return {
            loading: {isActive: false},
            printPayload: null,
            filters: {
                period: 'today',
                status: '',
                search: '',
                sort: 'order_time',
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
    },
    methods: {
        permissionChecker(permission) {
            return appService.permissionChecker(permission);
        },
        setStatus(status) {
            this.filters.status = status;
            this.refresh();
        },
        refresh() {
            this.loading.isActive = true;
            this.kitchenOrderStore.fetch({...this.filters}).then(() => {
                this.loading.isActive = false;
            }).catch((err) => {
                this.loading.isActive = false;
                alertService.error(err.response?.data?.message || this.$t('message.something_wrong'));
            });
        },
        cardClass(status) {
            if (status === orderStatusEnum.PENDING) return 'border-sky-300';
            if (status === orderStatusEnum.ACCEPT) return 'border-sky-400';
            if (status === orderStatusEnum.PREPARING) return 'border-amber-400';
            if (status === orderStatusEnum.PREPARED) return 'border-emerald-400';
            return 'border-[#EFF0F6]';
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
        variationText(item) {
            const vars = item.item_variations;
            if (!vars) return '';
            if (Array.isArray(vars)) {
                return vars.map((v) => v.name || v.variation_name).filter(Boolean).join(', ');
            }
            if (vars.names) {
                return Object.values(vars.names).join(', ');
            }
            return '';
        },
        extraText(item) {
            const extras = item.item_extras;
            if (!extras) return '';
            if (Array.isArray(extras)) {
                return extras.map((e) => e.name).filter(Boolean).join(', ');
            }
            if (extras.names) {
                return (extras.names || []).join(', ');
            }
            return '';
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
        async printTicket(order) {
            try {
                if (!this.permissionChecker('kitchen_print')) {
                    alertService.error(this.$t('message.access_denied'));
                    return;
                }
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
