<template>
    <LoadingComponent :props="loading"/>
    <div class="col-12">
        <div class="db-card mb-4">
            <div class="db-card-header border-none">
                <h3 class="db-card-title">{{ $t('menu.kitchen') }}</h3>
                <router-link :to="{name: 'admin.kitchen.queue'}" class="db-btn py-2 text-white bg-primary">
                    {{ $t('label.kitchen_queue') }}
                </router-link>
            </div>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-3 p-4">
                <div v-for="card in cards" :key="card.key" class="rounded-xl p-4 bg-[#F7F7FC]">
                    <p class="text-xs text-[#6E7191] mb-1">{{ card.label }}</p>
                    <p class="text-2xl font-semibold text-heading">{{ card.value }}</p>
                </div>
            </div>
            <div class="px-4 pb-4">
                <div class="rounded-xl p-4 bg-[#F7F7FC]">
                    <p class="text-sm text-[#6E7191] mb-1">{{ $t('label.kitchen_performance') }}</p>
                    <p class="text-lg font-semibold text-heading">
                        {{ $t('label.avg_ready_time') }}: {{ formatSeconds(dashboard.avg_ready_seconds) }}
                    </p>
                    <p class="text-sm text-[#6E7191] mt-1">
                        {{ $t('label.preparation_queue') }}: {{ dashboard.preparation_queue || 0 }}
                    </p>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import LoadingComponent from "../../common/LoadingComponent.vue";
import {useKitchenOrderStore} from "../../../stores/kitchenOrder.js";
import alertService from "../../../services/alertService.js";

export default {
    name: "KitchenDashboardComponent",
    components: {LoadingComponent},
    setup() {
        return {kitchenOrderStore: useKitchenOrderStore()};
    },
    data() {
        return {loading: {isActive: false}};
    },
    computed: {
        dashboard() {
            return this.kitchenOrderStore.dashboard || {};
        },
        cards() {
            return [
                {key: 'today', label: this.$t('label.today_orders'), value: this.dashboard.today_orders || 0},
                {key: 'pending', label: this.$t('label.pending'), value: this.dashboard.pending_orders || 0},
                {key: 'accepted', label: this.$t('label.accepted'), value: this.dashboard.accepted_orders || 0},
                {key: 'preparing', label: this.$t('label.preparing'), value: this.dashboard.preparing_orders || 0},
                {key: 'ready', label: this.$t('label.ready'), value: this.dashboard.ready_orders || 0},
                {key: 'completed', label: this.$t('label.completed'), value: this.dashboard.completed_orders || 0},
                {key: 'cancelled', label: this.$t('label.canceled'), value: this.dashboard.cancelled_orders || 0},
                {key: 'queue', label: this.$t('label.preparation_queue'), value: this.dashboard.preparation_queue || 0},
            ];
        }
    },
    mounted() {
        this.loading.isActive = true;
        this.kitchenOrderStore.fetchDashboard().then(() => {
            this.loading.isActive = false;
        }).catch((err) => {
            this.loading.isActive = false;
            alertService.error(err.response?.data?.message || this.$t('message.something_wrong'));
        });
    },
    methods: {
        formatSeconds(sec) {
            const s = Number(sec || 0);
            if (!s) return '—';
            const m = Math.floor(s / 60);
            const r = s % 60;
            return `${m}m ${r}s`;
        }
    }
}
</script>
