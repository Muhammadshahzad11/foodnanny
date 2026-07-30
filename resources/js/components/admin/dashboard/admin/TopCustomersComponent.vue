<template>
    <LoadingComponent :props="loading"/>
    <div class="col-12 xl:col-6">
        <div class="db-card !rounded-lg">
            <div class="db-card-header border-0">
                <h3 class="db-card-title text-lg text-heading font-semibold">{{ $t('label.top_customers') }}</h3>
            </div>
            <div class="db-card-body lg:h-[308px]">
                <ul v-if="customers.length > 0" class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                    <li class="w-full rounded-xl pt-3 border border-[#D9DBE9]" v-for="customer in customers"
                        :key="customer">
                        <img class="w-12 mx-auto rounded-full mb-2 aspect-square object-cover flex-shrink-0" :src="customer.image" alt="avatar">
                        <h4 class="text-sm px-3 text-center font-medium capitalize mb-4 whitespace-nowrap overflow-hidden text-ellipsis">
                            {{ customer.name }}
                        </h4>
                        <p class="text-xs w-full tracking-wide text-center py-1 rounded-t rounded-b-[11px] text-white bg-[#008BBA]">
                            {{ customer.orders }} {{ $t('label.orders') }}
                        </p>
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
import {useDashboardStore} from "../../../../stores/dashboard";
import LoadingComponent from "../../../common/LoadingComponent.vue";
import {useFrontendSettingStore} from "../../../../stores/frontendSetting.js";

export default {
    name: "TopCustomersComponent",
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
                isActive: false,
            }
        }
    },
    computed: {
        setting: function () {
            return this.frontendSettingStore.lists;
        },
        customers: function () {
            return this.dashboardStore.adminTopCustomers;
        }
    },
    mounted() {
        this.loading.isActive = true;
        this.dashboardStore.fetchAdminTopCustomers().then(res => {
            this.loading.isActive = false;
        }).catch((err) => {
            this.loading.isActive = false;
        });
    }
}
</script>
