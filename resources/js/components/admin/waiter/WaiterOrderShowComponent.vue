<template>
    <LoadingComponent :props="loading"/>
    <KitchenTicketPrintSheet :payload="printPayload"/>
    <div class="col-12">
        <div class="db-card" v-if="order.id">
            <div class="db-card-header border-none">
                <div>
                    <h3 class="db-card-title">{{ order.order_serial_no }}</h3>
                    <p class="text-sm text-[#6E7191]">
                        {{ order.table?.table_number }} · {{ order.table?.name }}
                        <span v-if="order.is_draft"> · {{ $t('label.draft') }}</span>
                        <span v-else> · {{ order.status_name }}</span>
                    </p>
                </div>
                <div class="flex gap-2 flex-wrap">
                    <button
                        v-if="!order.is_draft"
                        type="button"
                        @click.prevent="printKot"
                        class="db-btn py-2 text-white bg-amber-600"
                    >
                        {{ $t('button.print_kot') }}
                    </button>
                    <router-link
                        v-if="order.table_id"
                        :to="{name: 'admin.waiter.table.order', params: {id: order.table_id}}"
                        class="db-btn py-2 text-white bg-primary"
                    >
                        {{ $t('button.edit') }}
                    </router-link>
                    <router-link :to="{name: 'admin.waiter.tables'}" class="db-btn py-2 text-white bg-slate-700">
                        {{ $t('label.tables') }}
                    </router-link>
                </div>
            </div>

            <div class="p-4 grid md:grid-cols-2 gap-4">
                <div>
                    <p class="text-sm text-[#6E7191] mb-1">{{ $t('label.waiter') }}</p>
                    <p class="font-medium">{{ order.waiter?.name || '-' }}</p>
                </div>
                <div>
                    <p class="text-sm text-[#6E7191] mb-1">{{ $t('label.order_note') }}</p>
                    <p class="font-medium">{{ order.order_note || '-' }}</p>
                </div>
                <div>
                    <p class="text-sm text-[#6E7191] mb-1">{{ $t('label.payment_status') }}</p>
                    <p class="font-medium">{{ order.payment_status === 5 ? $t('label.paid') : $t('label.unpaid') }}</p>
                </div>
                <div>
                    <p class="text-sm text-[#6E7191] mb-1">{{ $t('label.total') }}</p>
                    <p class="font-medium">{{ order.total_currency_price }}</p>
                </div>
            </div>

            <div class="px-4 pb-4">
                <table class="w-full">
                    <thead class="bg-primary/10">
                    <tr class="h-9">
                        <th class="px-3 text-left text-xs">{{ $t('label.item') }}</th>
                        <th class="px-3 text-left text-xs">{{ $t('label.qty') }}</th>
                        <th class="px-3 text-left text-xs">{{ $t('label.price') }}</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="item in order.order_items" :key="item.id" class="border-b border-[#EFF0F6]">
                        <td class="px-3 py-3 text-sm">
                            <p>{{ item.item_name || item.name || ('#' + item.item_id) }}</p>
                            <p class="text-xs text-[#6E7191]" v-if="item.instruction">{{ item.instruction }}</p>
                        </td>
                        <td class="px-3 py-3 text-sm">{{ item.quantity }}</td>
                        <td class="px-3 py-3 text-sm">{{ item.total_currency_price || item.total_price }}</td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</template>

<script>
import LoadingComponent from "../../common/LoadingComponent.vue";
import KitchenTicketPrintSheet from "../kitchen/KitchenTicketPrintSheet.vue";
import {useWaiterOrderStore} from "../../../stores/waiterOrder.js";
import alertService from "../../../services/alertService.js";
import {apiErrorMessage} from "../../../services/apiError.js";

export default {
    name: "WaiterOrderShowComponent",
    components: {LoadingComponent, KitchenTicketPrintSheet},
    setup() {
        return {
            waiterOrderStore: useWaiterOrderStore(),
        };
    },
    data() {
        return {
            loading: {isActive: false},
            printPayload: null,
        };
    },
    computed: {
        order() {
            return this.waiterOrderStore.show || {};
        }
    },
    async mounted() {
        try {
            this.loading.isActive = true;
            await this.waiterOrderStore.view(this.$route.params.id);
            this.loading.isActive = false;
        } catch (err) {
            this.loading.isActive = false;
            alertService.error(apiErrorMessage(err, this.$t('message.something_wrong')));
        }
    },
    methods: {
        async printKot() {
            if (!this.order.id || this.order.is_draft) {
                return;
            }
            try {
                this.loading.isActive = true;
                const res = await this.waiterOrderStore.printData(this.order.id);
                this.printPayload = res.data.data.payload;
                this.loading.isActive = false;
                await this.$nextTick();
                window.print();
            } catch (err) {
                this.loading.isActive = false;
                alertService.error(apiErrorMessage(err, this.$t('message.something_wrong')));
            }
        }
    }
}
</script>
