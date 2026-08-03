<template>
    <LoadingComponent :props="loading"/>
    <div class="col-12">
        <div class="db-card mb-4">
            <div class="db-card-header border-none">
                <h3 class="db-card-title">{{ $t('menu.waiter') }}</h3>
                <router-link :to="{name: 'admin.waiter.tables'}" class="db-btn py-2 text-white bg-primary">
                    {{ $t('label.tables') }}
                </router-link>
            </div>
            <div class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-6 gap-3 p-4">
                <div v-for="card in cards" :key="card.key" class="rounded-xl p-4 bg-[#F7F7FC]">
                    <p class="text-xs text-[#6E7191] mb-1">{{ card.label }}</p>
                    <p class="text-2xl font-semibold text-heading">{{ card.value }}</p>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import LoadingComponent from "../../common/LoadingComponent.vue";
import {useWaiterTableStore} from "../../../stores/waiterTable.js";
import alertService from "../../../services/alertService.js";

export default {
    name: "WaiterDashboardComponent",
    components: {LoadingComponent},
    setup() {
        const waiterTableStore = useWaiterTableStore();
        return {waiterTableStore};
    },
    data() {
        return {
            loading: {isActive: false},
        };
    },
    computed: {
        dashboard() {
            return this.waiterTableStore.dashboard || {};
        },
        cards() {
            return [
                {key: 'tables_total', label: this.$t('label.tables'), value: this.dashboard.tables_total || 0},
                {key: 'tables_available', label: this.$t('label.available'), value: this.dashboard.tables_available || 0},
                {key: 'tables_occupied', label: this.$t('label.occupied'), value: this.dashboard.tables_occupied || 0},
                {key: 'draft_orders', label: this.$t('label.draft_orders'), value: this.dashboard.draft_orders || 0},
                {key: 'kitchen_orders', label: this.$t('label.kitchen_orders'), value: this.dashboard.kitchen_orders || 0},
                {key: 'ready_orders', label: this.$t('label.ready_orders'), value: this.dashboard.ready_orders || 0},
            ];
        }
    },
    mounted() {
        this.loading.isActive = true;
        this.waiterTableStore.fetchDashboard().then(() => {
            this.loading.isActive = false;
        }).catch((err) => {
            this.loading.isActive = false;
            alertService.error(err.response?.data?.message || this.$t('message.something_wrong'));
        });
    }
}
</script>
