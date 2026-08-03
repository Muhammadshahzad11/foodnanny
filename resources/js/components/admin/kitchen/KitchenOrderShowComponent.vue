<template>
    <LoadingComponent :props="loading"/>
    <KitchenTicketPrintSheet :payload="printPayload"/>

    <div class="col-12" v-if="order.id">
        <div class="db-card mb-4">
            <div class="db-card-header border-none">
                <div>
                    <router-link :to="{name: 'admin.kitchen.queue'}" class="text-sm text-primary">
                        ← {{ $t('label.kitchen_queue') }}
                    </router-link>
                    <h3 class="db-card-title mt-1">#{{ order.order_serial_no }}</h3>
                    <p class="text-sm text-[#6E7191]">{{ order.status_name }} · {{ order.order_datetime }}</p>
                </div>
                <div class="flex flex-wrap gap-2">
                    <button v-if="canAccept" type="button" class="db-btn py-2 text-white bg-sky-500"
                            @click="accept">{{ $t('button.accept') }}</button>
                    <button v-if="canPrepare" type="button" class="db-btn py-2 text-white bg-amber-500"
                            @click="preparing">{{ $t('button.preparing') }}</button>
                    <button v-if="canReady" type="button" class="db-btn py-2 text-white bg-[#1AB759]"
                            @click="ready">{{ $t('button.ready') }}</button>
                    <button type="button" class="db-btn py-2 text-white bg-primary"
                            @click="printTicket">{{ $t('button.print_kot') }}</button>
                </div>
            </div>

            <div class="p-4 grid md:grid-cols-2 gap-4">
                <div class="rounded-xl p-4 bg-[#F7F7FC] space-y-2 text-sm text-heading">
                    <p v-if="order.table"><span class="text-[#6E7191]">{{ $t('label.table') }}:</span> {{ order.table.table_number }} · {{ order.table.name }}</p>
                    <p v-if="order.waiter"><span class="text-[#6E7191]">{{ $t('label.waiter') }}:</span> {{ order.waiter.name }}</p>
                    <p v-if="order.customer"><span class="text-[#6E7191]">{{ $t('label.customer') }}:</span> {{ order.customer.name }}</p>
                    <p v-if="order.accepted_by"><span class="text-[#6E7191]">{{ $t('label.accepted_by') }}:</span> {{ order.accepted_by.name }}</p>
                    <p v-if="order.preparing_by"><span class="text-[#6E7191]">{{ $t('label.preparing_by') }}:</span> {{ order.preparing_by.name }}</p>
                    <p v-if="order.ready_by"><span class="text-[#6E7191]">{{ $t('label.ready_by') }}:</span> {{ order.ready_by.name }}</p>
                    <p v-if="order.order_note" class="text-primary font-medium">{{ order.order_note }}</p>
                </div>
                <div class="rounded-xl p-4 bg-[#F7F7FC]">
                    <p class="text-sm text-[#6E7191] mb-2">{{ $t('label.priority') }}</p>
                    <div class="flex gap-2">
                        <input v-model.number="priority" type="number" min="0" max="100" class="db-field-control w-24"/>
                        <button type="button" class="db-btn py-2 text-white bg-primary" @click="savePriority">
                            {{ $t('button.save') }}
                        </button>
                    </div>
                </div>
            </div>

            <div class="px-4 pb-4">
                <h4 class="text-base font-semibold text-heading mb-3">{{ $t('label.items') }}</h4>
                <div v-for="item in order.order_items" :key="item.id" class="py-3 border-b border-[#EFF0F6] last:border-0">
                    <p class="text-heading font-semibold text-lg">{{ item.quantity }}× {{ item.item_name }}</p>
                    <p v-if="item.instruction" class="text-primary text-sm mt-1">{{ item.instruction }}</p>
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
import alertService from "../../../services/alertService.js";
import appService from "../../../services/appService.js";

export default {
    name: "KitchenOrderShowComponent",
    components: {LoadingComponent, KitchenTicketPrintSheet},
    setup() {
        return {kitchenOrderStore: useKitchenOrderStore(), orderStatusEnum};
    },
    data() {
        return {
            loading: {isActive: false},
            printPayload: null,
            priority: 0,
        };
    },
    computed: {
        order() {
            return this.kitchenOrderStore.show || {};
        },
        canAccept() {
            return appService.permissionChecker('kitchen_accept')
                && [orderStatusEnum.PENDING, orderStatusEnum.ACCEPT].includes(this.order.status)
                && !this.order.accepted_by;
        },
        canPrepare() {
            return appService.permissionChecker('kitchen_prepare')
                && this.order.status === orderStatusEnum.ACCEPT;
        },
        canReady() {
            return appService.permissionChecker('kitchen_ready')
                && this.order.status === orderStatusEnum.PREPARING;
        }
    },
    mounted() {
        this.load();
    },
    methods: {
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
