<template>
    <LoadingComponent :props="loading"/>
    <div class="col-12 xl:col-6">
        <div class="db-card !rounded-lg">
            <div class="db-card-header border-0">
                <div class="db-card-title text-lg text-heading font-semibold">{{ $t('label.most_popular_items') }}</div>
            </div>
            <div class="db-card-body">
                <ul v-if="mostPopularItems.length > 0" class="grid grid-cols-1 sm:grid-cols-2 gap-[18px]">
                    <li class="w-full flex rounded-xl border border-[#D9DBE9]"
                        v-for="mostPopularItem in mostPopularItems" :key="mostPopularItem">
                        <img class="flex w-20 h-20 object-cover rounded-l-[11px]" :src="mostPopularItem.thumb"
                             alt="product">
                        <div class="py-2 px-3 flex flex-col justify-between overflow-hidden">
                            <h4 class="text-sm overflow-hidden whitespace-nowrap text-ellipsis font-medium capitalize">
                                {{ mostPopularItem.name }}
                            </h4>
                            <h5 class="text-xs font-medium capitalize text-[#008BBA]">
                                {{ mostPopularItem.category }}
                            </h5>
                            <h6 class="text-sm font-bold">{{ mostPopularItem.price }}</h6>
                        </div>
                    </li>
                </ul>
                <div v-else class="flex flex-col items-center justify-center h-full p-6">
                    <img class="m-auto not-found max-w-[300px]" :src="setting.data_not_found" alt="Not Found">
                    <span class="block mt-3 text-center text-lg">{{ $t('message.no_data_found') }}</span>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import LoadingComponent from "../../../common/LoadingComponent.vue";
import {useDashboardStore} from "../../../../stores/dashboard.js";
import {useFrontendSettingStore} from "../../../../stores/frontendSetting.js";

export default {
    name: "MostPopularItemsComponent",
    components: {LoadingComponent},
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
        setting: function () {
            return this.frontendSettingStore.lists;
        },
        mostPopularItems: function () {
            return this.dashboardStore.restaurantOwnerMostPopularItems;
        }
    },
    mounted() {
        this.loading.isActive = true;
        this.dashboardStore.fetchRestaurantOwnerMostPopularItems().then(res => {
            this.loading.isActive = false;
        }).catch((err) => {
            this.loading.isActive = false;
        })
    }
}
</script>
