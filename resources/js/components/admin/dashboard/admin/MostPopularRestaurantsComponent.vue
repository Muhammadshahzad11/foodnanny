<template>
    <LoadingComponent :props="loading" />
    <div class="col-12 xl:col-6">
        <div class="db-card !rounded-lg">
            <div class="db-card-header border-0">
                <div class="db-card-title text-lg text-heading font-semibold">{{ $t('label.most_popular_restaurants') }}</div>
            </div>
            <div class="db-card-body lg:h-[308px]">
                <ul v-if="restaurants.length > 0" class="grid grid-cols-1 sm:grid-cols-2 gap-[18px]">
                    <li v-for="restaurant in restaurants" :key="restaurant" class="w-full h-20 p-2 flex items-center gap-3 rounded-xl border border-[#D9DBE9]">
                        <img class="w-16 h-16 object-cover flex-shrink-0 rounded-full" :src="restaurant.image" alt="menu">
                        <div class="flex-auto h-full flex flex-col justify-between overflow-hidden">
                            <h4 class="text-xs overflow-hidden whitespace-nowrap text-ellipsis font-medium capitalize">{{ restaurant.name }}</h4>
                            <h5 class="text-[10px] leading-none font-medium overflow-hidden whitespace-nowrap text-ellipsis capitalize text-[#008BBA]">
                                {{ restaurant.cuisine }}
                            </h5>
                            <h6 class="text-sm font-bold capitalize">
                                <span class="text-primary">{{restaurant.orders }}</span>
                                {{ $t('label.orders') }}
                            </h6>
                        </div>
                    </li>
                </ul>

                <div v-else class="flex flex-col items-center justify-center h-full p-4">
                    <img class="m-auto not-found max-w-[300px]" :src="setting.data_not_found" alt="Not Found">
                    <span class="block mt-3 text-center text-lg">{{ $t('message.no_data_found') }}</span>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import { useDashboardStore } from "../../../../stores/dashboard";
import LoadingComponent from "../../../common/LoadingComponent.vue";
import {useFrontendSettingStore} from "../../../../stores/frontendSetting.js";

export default {
    name: "MostPopularRestaurantsComponent",
    components: { LoadingComponent },
    setup() {
        const dashboardStore = useDashboardStore();
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
        restaurants: function () {
            return this.dashboardStore.adminMostPopularRestaurants;
        }
    },
    mounted() {
        this.loading.isActive = true;
        this.dashboardStore.fetchAdminMostPopularRestaurants().then(res => {
            this.loading.isActive = false;
        }).catch((err) => {
            this.loading.isActive = false;
        })
    }
}
</script>
