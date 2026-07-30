<template>
    <LoadingComponent :props="loading"/>
    <div class="col-12 md:col-6">
        <div class="flex items-center justify-between gap-3 mb-3">
            <h3 class="text-xl font-semibold capitalize text-heading">{{ $t('label.earning_statistics') }}</h3>
        </div>
        <div class="bg-earning bg-no-repeat bg-cover bg-center w-full p-6 rounded-lg">
            <div class="mb-12 flex items-start justify-between gap-2">
                <div class="flex-auto">
                    <span class="block text-base capitalize mb-1 text-white"> {{ $t('label.available_balance') }}</span>
                    <h3 class="text-3xl font-semibold text-white">{{ payoutBalance.total_payout_balance }}</h3>
                </div>
                <router-link v-if="payoutPermission" :to="{name : 'admin.payouts'}"
                             class="w-12 aspect-square rounded-full flex items-center justify-center border border-white backdrop-blur-sm bg-gradient-to-r from-white/25 to-white/50">
                    <i class="lab-line-arrow-right-2 text-2xl -rotate-45 text-white"></i>
                </router-link>
            </div>
            <ul class="grid grid-cols-3 gap-4">
                <li class="p-2 w-full rounded-lg backdrop-blur-sm bg-gradient-to-r from-white/25 to-white/50">
                    <span
                        class="block text-sm mb-1 capitalize whitespace-nowrap overflow-hidden text-ellipsis text-white">
                        {{ $t('label.today') }}
                    </span>
                    <h4 class="text-lg leading-none font-semibold text-white">{{
                            payoutBalance.today_payout_balance
                        }}</h4>
                </li>
                <li class="p-2 w-full rounded-lg backdrop-blur-sm bg-gradient-to-r from-white/25 to-white/50">
                    <span
                        class="block text-sm mb-1 capitalize whitespace-nowrap overflow-hidden text-ellipsis text-white">
                        {{ $t('label.this_week') }}
                    </span>
                    <h4 class="text-lg leading-none font-semibold text-white">{{
                            payoutBalance.this_week_payout_balance
                        }}</h4>
                </li>
                <li class="p-2 w-full rounded-lg backdrop-blur-sm bg-gradient-to-r from-white/25 to-white/50">
                    <span
                        class="block text-sm mb-1 capitalize whitespace-nowrap overflow-hidden text-ellipsis text-white">
                        {{ $t('label.this_month') }}
                    </span>
                    <h4 class="text-lg leading-none font-semibold text-white">{{
                            payoutBalance.this_month_payout_balance
                        }}</h4>
                </li>
            </ul>
        </div>
    </div>
</template>
<script>
import {useDashboardStore} from "../../../../stores/dashboard.js";
import appService from "../../../../services/appService.js";
import LoadingComponent from "../../../common/LoadingComponent.vue";

export default {
    name: "PayoutBalanceComponent",
    components: {LoadingComponent},
    setup() {
        const dashboardStore = useDashboardStore();

        return {
            dashboardStore
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
        payoutBalance: function () {
            return this.dashboardStore.deliveryBoyPayoutBalance;
        }
    },
    mounted() {
        this.loading.isActive = true;
        this.dashboardStore.fetchDeliveryBoyPayoutBalance().then((res) => {
            this.loading.isActive = false;
        }).catch((err) => {
            this.loading.isActive = false;
        })
    },
    methods: {
        payoutPermission: function () {
            return !!appService.permissionChecker('payouts');
        }
    }
}
</script>
