<template>
    <div class="col-12 mt-9 mb-9">
        <h3 class="text-xl font-semibold capitalize mb-3">{{ $t('label.active_orders') }}</h3>

        <div v-if="orders.length > 0" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
            <div v-for="order in orders" class="p-3 rounded-lg shadow-xs bg-white">
                <div class="flex flex-wrap items-start gap-2 mb-1.5">
                    <h3 class="text-sm font-normal whitespace-nowrap text-heading">{{ $t('label.order_id') }}:</h3>
                    <h4 class="text-sm font-medium whitespace-nowrap text-focus">#{{ order.serial_no }}</h4>
                    <h5 class="!h-5 !leading-5 !py-0" :class="orderStatusClass(order.status)">
                        {{ order.status_name }}
                    </h5>
                </div>
                <p class="text-xs mb-3">
                    {{ order.date }}
                </p>
                <div class="flex items-center gap-2 mb-3">
                    <i class="lab-line-restaurants flex-shrink-0 text-xl"></i>
                    <dl class="flex-auto">
                        <dd class="text-xs text-heading">{{ order.restaurant_address }}</dd>
                    </dl>
                </div>
                <router-link :to="{name: 'admin.activeOrder.show', params: { id: order.id }}" class="w-full h-9 leading-9 text-sm font-medium capitalize rounded-xl text-center border border-[#D9DBE9] text-heading hover:bg-primary/10 hover:border-primary/10">
                    {{ $t("label.see_order_details") }}
                </router-link>
            </div>
        </div>

        <div v-else class="py-20 px-3 text-center">
            <img class="w-44 mx-auto mb-6" :src="setting.image_delivery" alt="delivery">
            <h4 class="text-base font-semibold">{{ $t('message.no_order_for_delivery') }}</h4>
        </div>
    </div>
</template>

<script>
import SmIconViewComponent from "../../components/buttons/SmIconViewComponent.vue";
import {useDashboardStore} from "../../../../stores/dashboard.js";
import appService from "../../../../services/appService.js";
import {useFrontendSettingStore} from "../../../../stores/frontendSetting.js";

export default {
    name: "ActiveOrdersComponent",
    components: {SmIconViewComponent},
    setup() {
        const dashboardStore       = useDashboardStore();
        const frontendSettingStore = useFrontendSettingStore();

        return {
            dashboardStore,
            frontendSettingStore
        }
    },
    data() {
        return {
            loading: {
                isActive: false
            }
        }
    },
    computed: {
        orders: function () {
            return this.dashboardStore.deliveryBoyActiveOrders;
        },
        setting: function () {
            return this.frontendSettingStore.lists;
        }
    },
    mounted() {
        this.loading.isActive = true;
        this.dashboardStore.fetchDeliveryBoyActiveOrders().then((res) => {
            this.loading.isActive = false;
        }).catch((err) => {
            this.loading.isActive = false;
        })
    },
    methods: {
        orderStatusClass: function (status) {
            return appService.orderStatusClass(status);
        }
    }
}
</script>
