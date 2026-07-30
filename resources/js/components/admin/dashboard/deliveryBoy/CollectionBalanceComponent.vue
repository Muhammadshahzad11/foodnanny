<template>
    <LoadingComponent :props="loading"/>
    <div class="col-12 md:col-6">
        <div class="flex items-center justify-between gap-3 mb-3">
            <h3 class="text-xl font-semibold capitalize text-heading">{{ $t('label.collection_statistics')}}</h3>
        </div>
        <div class="bg-gradient-to-r from-[#FFEDE0] to-[#ECECFF] w-full p-6 rounded-lg">
            <div class="flex-auto mb-12">
                <span class="block text-base capitalize mb-1 text-[#1F1F39]">{{ $t('label.collection_balance') }}</span>
                <h3 class="text-3xl font-semibold">{{ collectionBalance.total_collection_balance }}</h3>
            </div>

            <ul class="grid grid-cols-3 gap-4">
                <li class="p-2 w-full rounded-lg bg-white">
                    <span class="block text-sm mb-1 capitalize whitespace-nowrap overflow-hidden text-ellipsis text-[#1F1F39]">{{ $t('label.today') }}</span>
                    <h4 class="text-lg leading-none font-semibold">{{ collectionBalance.today_collection_balance }}</h4>
                </li>
                <li class="p-2 w-full rounded-lg bg-white">
                    <span class="block text-sm mb-1 capitalize whitespace-nowrap overflow-hidden text-ellipsis text-[#1F1F39]">{{ $t('label.last_week') }}</span>
                    <h4 class="text-lg leading-none font-semibold">{{ collectionBalance.last_week_collection_balance }}</h4>
                </li>
                <li class="p-2 w-full rounded-lg bg-white">
                    <span class="block text-sm mb-1 capitalize whitespace-nowrap overflow-hidden text-ellipsis text-[#1F1F39]">{{ $t('label.this_month') }}</span>
                    <h4 class="text-lg leading-none font-semibold">{{ collectionBalance.this_month_collection_balance }}</h4>
                </li>
            </ul>
        </div>
    </div>
</template>
<script>
import LoadingComponent from "../../../common/LoadingComponent.vue";
import {useDashboardStore} from "../../../../stores/dashboard.js";

export default {
    name: "CollectionBalanceComponent",
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
        collectionBalance: function () {
            return this.dashboardStore.deliveryBoyCollectionBalance;
        }
    },
    mounted() {
        this.loading.isActive = true;
        this.dashboardStore.fetchDeliveryBoyCollectionBalance().then((res) => {
            this.loading.isActive = false;
        }).catch((err) => {
            this.loading.isActive = false;
        })
    }
}
</script>
