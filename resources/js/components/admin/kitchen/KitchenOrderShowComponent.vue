<template>
    <LoadingComponent :props="loading"/>
    <KitchenTicketPrintSheet :payload="printPayload"/>

    <div class="col-12" v-if="order.id">
        <div class="db-card mb-4">
            <div class="db-card-header border-none !items-start gap-3">
                <div>
                    <router-link :to="{name: 'admin.kitchen.queue'}" class="text-sm text-primary">
                        ← {{ $t('label.kitchen_queue') }}
                    </router-link>
                    <h3 class="db-card-title mt-1 text-3xl">#{{ order.order_serial_no }}</h3>
                    <p class="text-base text-[#6E7191]">{{ order.status_name }} · {{ order.order_datetime }}</p>
                    <p class="text-xl font-semibold mt-1">⏱ {{ elapsedLabel }}</p>
                </div>
                <div class="flex flex-wrap gap-2">
                    <button v-if="canAccept" type="button" class="db-btn py-3 px-4 text-base text-white bg-sky-500"
                            @click="accept">{{ $t('button.accept') }}</button>
                    <button v-if="canPrepare" type="button" class="db-btn py-3 px-4 text-base text-white bg-amber-500"
                            @click="preparing">{{ $t('button.preparing') }}</button>
                    <button v-if="canReady" type="button" class="db-btn py-3 px-4 text-base text-white bg-emerald-600"
                            @click="ready">{{ $t('button.ready') }}</button>
                    <button v-if="canReject" type="button" class="db-btn py-3 px-4 text-base text-white bg-rose-500"
                            @click="reject">{{ $t('button.reject') }}</button>
                    <button v-if="canCancel" type="button" class="db-btn py-3 px-4 text-base border border-rose-300 text-rose-700 bg-white"
                            @click="cancel">{{ $t('button.cancel') }}</button>
                    <button v-if="permissionChecker('kitchen_print')" type="button" class="db-btn py-3 px-4 text-base text-white bg-primary"
                            @click="printTicket">{{ $t('button.print_kot') }}</button>
                </div>
            </div>

            <div class="p-4 grid md:grid-cols-2 gap-4">
                <div class="rounded-2xl p-4 bg-[#F7F7FC] space-y-2 text-base text-heading">
                    <p v-if="order.restaurant"><span class="text-[#6E7191]">{{ $t('label.restaurant') }}:</span> {{ order.restaurant.name }}</p>
                    <p v-if="order.table" class="text-xl font-semibold">
                        <span class="text-[#6E7191] text-base font-normal">{{ $t('label.table') }}:</span>
                        {{ order.table.table_number }} · {{ order.table.name }}
                    </p>
                    <p v-if="order.waiter"><span class="text-[#6E7191]">{{ $t('label.waiter') }}:</span> {{ order.waiter.name }}</p>
                    <p v-if="order.customer"><span class="text-[#6E7191]">{{ $t('label.customer') }}:</span> {{ order.customer.name }}</p>
                    <p v-if="order.accepted_by"><span class="text-[#6E7191]">{{ $t('label.accepted_by') }}:</span> {{ order.accepted_by.name }}</p>
                    <p v-if="order.preparing_by"><span class="text-[#6E7191]">{{ $t('label.preparing_by') }}:</span> {{ order.preparing_by.name }}</p>
                    <p v-if="order.ready_by"><span class="text-[#6E7191]">{{ $t('label.ready_by') }}:</span> {{ order.ready_by.name }}</p>
                    <p v-if="order.order_note" class="rounded-xl px-3 py-2 bg-amber-50 border border-amber-200 text-amber-900 font-semibold">
                        ⚠ {{ order.order_note }}
                    </p>
                    <p v-if="order.reason" class="text-rose-600 font-medium">{{ order.reason }}</p>
                </div>
                <div class="rounded-2xl p-4 bg-[#F7F7FC]">
                    <p class="text-sm text-[#6E7191] mb-3">{{ $t('label.priority') }}</p>
                    <div class="grid grid-cols-2 gap-2">
                        <button
                            v-for="opt in priorityOptions"
                            :key="opt.value"
                            type="button"
                            class="min-h-12 rounded-xl text-sm font-bold border-2 transition"
                            :class="Number(priority) === opt.value
                                ? 'border-primary bg-primary text-white'
                                : 'border-[#EFF0F6] bg-white text-heading'"
                            @click="priority = opt.value; savePriority()"
                        >
                            {{ opt.label }}
                        </button>
                    </div>
                </div>
            </div>

            <div class="px-4 pb-4">
                <h4 class="text-lg font-semibold text-heading mb-3">{{ $t('label.items') }}</h4>
                <div v-for="item in order.order_items" :key="item.id" class="py-4 border-b border-[#EFF0F6] last:border-0">
                    <p class="text-heading font-bold text-xl">{{ item.quantity }}× {{ item.item_name }}</p>
                    <p v-for="(line, i) in (item.variation_lines || [])" :key="'v'+i" class="text-[#4E4B66] mt-1">• {{ line }}</p>
                    <p v-for="(line, i) in (item.extra_lines || [])" :key="'e'+i" class="text-emerald-700 font-medium mt-1">+ {{ line }}</p>
                    <p v-if="item.instruction" class="mt-2 inline-flex rounded-lg bg-rose-50 text-rose-700 px-2 py-1 font-semibold">
                        {{ item.instruction }}
                    </p>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import LoadingComponent from "../../common/LoadingComponent.vue";
import KitchenTicketPrintSheet from "./KitchenTicketPrintSheet.vue";
import {useKitchenOrderStore} from "../../../stores/kitchenOrder.js";
import orderStatusEnum from "../../../enums/modules/orderStatusEnum.js";
import kitchenPriorityEnum from "../../../enums/modules/kitchenPriorityEnum.js";
import alertService from "../../../services/alertService.js";
import appService from "../../../services/appService.js";

export default {
    name: "KitchenOrderShowComponent",
    components: {LoadingComponent, KitchenTicketPrintSheet},
    setup() {
        return {kitchenOrderStore: useKitchenOrderStore(), orderStatusEnum, kitchenPriorityEnum};
    },
    data() {
        return {
            loading: {isActive: false},
            printPayload: null,
            priority: 0,
            nowTick: Date.now(),
            tickTimer: null,
        };
    },
    computed: {
        order() {
            return this.kitchenOrderStore.show || {};
        },
        priorityOptions() {
            return [
                {value: kitchenPriorityEnum.NORMAL, label: this.$t('label.priority_normal')},
                {value: kitchenPriorityEnum.HIGH, label: this.$t('label.priority_high')},
                {value: kitchenPriorityEnum.URGENT, label: this.$t('label.priority_urgent')},
                {value: kitchenPriorityEnum.VIP, label: this.$t('label.priority_vip')},
            ];
        },
        elapsedLabel() {
            void this.nowTick;
            const from = this.order.elapsed_from || this.order.order_datetime_iso;
            if (!from) return '0:00';
            const start = new Date(from).getTime();
            if (Number.isNaN(start)) return '0:00';
            const s = Math.max(0, Math.floor((Date.now() - start) / 1000));
            const m = Math.floor(s / 60);
            const r = s % 60;
            return `${m}:${String(r).padStart(2, '0')}`;
        },
        canAccept() {
            return this.permissionChecker('kitchen_accept')
                && [orderStatusEnum.PENDING, orderStatusEnum.ACCEPT].includes(this.order.status)
                && !this.order.accepted_by;
        },
        canPrepare() {
            return this.permissionChecker('kitchen_prepare')
                && this.order.status === orderStatusEnum.ACCEPT;
        },
        canReady() {
            return this.permissionChecker('kitchen_ready')
                && this.order.status === orderStatusEnum.PREPARING;
        },
        canReject() {
            return this.permissionChecker('kitchen_reject')
                && [orderStatusEnum.PENDING, orderStatusEnum.ACCEPT, orderStatusEnum.PREPARING].includes(this.order.status);
        },
        canCancel() {
            return this.permissionChecker('kitchen_cancel')
                && [orderStatusEnum.PENDING, orderStatusEnum.ACCEPT, orderStatusEnum.PREPARING, orderStatusEnum.PREPARED].includes(this.order.status);
        }
    },
    mounted() {
        this.load();
        this.tickTimer = setInterval(() => {
            this.nowTick = Date.now();
        }, 1000);
    },
    beforeUnmount() {
        if (this.tickTimer) clearInterval(this.tickTimer);
    },
    methods: {
        permissionChecker(permission) {
            return appService.permissionChecker(permission);
        },
        load() {
            this.loading.isActive = true;
            this.kitchenOrderStore.view(this.$route.params.id).then((res) => {
                this.priority = res.data.data.kitchen_priority || 0;
                this.loading.isActive = false;
            }).catch((err) => {
                this.loading.isActive = false;
                alertService.error(err.response?.data?.message || this.$t('message.something_wrong'));
            });
        },
        async accept() {
            try {
                this.loading.isActive = true;
                await this.kitchenOrderStore.accept(this.order.id, this.order.updated_at);
                this.loading.isActive = false;
            } catch (err) {
                this.loading.isActive = false;
                alertService.error(err.response?.data?.message || this.$t('message.something_wrong'));
            }
        },
        async preparing() {
            try {
                this.loading.isActive = true;
                await this.kitchenOrderStore.preparing(this.order.id, this.order.updated_at);
                this.loading.isActive = false;
            } catch (err) {
                this.loading.isActive = false;
                alertService.error(err.response?.data?.message || this.$t('message.something_wrong'));
            }
        },
        async ready() {
            try {
                this.loading.isActive = true;
                await this.kitchenOrderStore.ready(this.order.id, this.order.updated_at);
                this.loading.isActive = false;
            } catch (err) {
                this.loading.isActive = false;
                alertService.error(err.response?.data?.message || this.$t('message.something_wrong'));
            }
        },
        async reject() {
            const reason = window.prompt(this.$t('message.kitchen_reject_reason'), '');
            if (reason === null) return;
            try {
                this.loading.isActive = true;
                await this.kitchenOrderStore.reject(this.order.id, reason || null, this.order.updated_at);
                this.loading.isActive = false;
            } catch (err) {
                this.loading.isActive = false;
                alertService.error(err.response?.data?.message || this.$t('message.something_wrong'));
            }
        },
        async cancel() {
            const reason = window.prompt(this.$t('message.kitchen_cancel_reason'), '');
            if (reason === null) return;
            try {
                this.loading.isActive = true;
                await this.kitchenOrderStore.cancel(this.order.id, reason || null, this.order.updated_at);
                this.loading.isActive = false;
            } catch (err) {
                this.loading.isActive = false;
                alertService.error(err.response?.data?.message || this.$t('message.something_wrong'));
            }
        },
        async savePriority() {
            try {
                this.loading.isActive = true;
                await this.kitchenOrderStore.priority(this.order.id, this.priority);
                this.loading.isActive = false;
                alertService.success(this.$t('message.kitchen_priority_updated'));
            } catch (err) {
                this.loading.isActive = false;
                alertService.error(err.response?.data?.message || this.$t('message.something_wrong'));
            }
        },
        async printTicket() {
            try {
                this.loading.isActive = true;
                const res = await this.kitchenOrderStore.printData(this.order.id);
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
