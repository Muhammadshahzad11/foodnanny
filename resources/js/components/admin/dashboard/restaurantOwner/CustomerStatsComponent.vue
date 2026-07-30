<template>
    <LoadingComponent :props="loading" />
    <div class="col-12 xl:col-6">
        <div class="db-card !rounded-lg">
            <div class="db-card-header border-0">
                <h3 class="db-card-title text-lg text-heading font-semibold">{{ $t('label.customer_stats') }}</h3>
                <div id="customer-range" class="cursor-pointer flex items-center gap-3">
                    <DatePickerComponent @update:modelValue="handleDate" inputStyle="filter" :range="true" hideInputIcon v-model="date"/>
                </div>
            </div>
            <div class="db-card-body">
                <apexchart height="270" v-if="options" :options="options" :series="options.series"></apexchart>
            </div>
        </div>
    </div>
</template>

<script>

import LoadingComponent from "../../../common/LoadingComponent.vue";
import DatePickerComponent from "../../components/DatePickerComponent.vue";
import {useDashboardStore} from "../../../../stores/dashboard.js";

export default {
    name: "CustomerStatsComponent",
    components: { LoadingComponent, DatePickerComponent },
    setup() {
        const dashboardStore = useDashboardStore();
        return {
            dashboardStore
        }
    },
    data() {
        return {
            loading: {
                isActive: false,
            },
            date: null,
            options: null
        };
    },
    mounted() {
        const now       = new Date();
        const startDate = new Date(now.getFullYear(), now.getMonth(), 1);
        const endDate   = new Date(now.getFullYear(), now.getMonth() + 1, 0);
        this.date       = [startDate, endDate];

        this.customerStats({
            start_date: startDate,
            end_date: endDate,
        });
    },
    methods: {
        handleDate: function (e) {
            this.customerStats({
                start_date: e[0],
                end_date: e[1]
            });
        },
        customerStats: function (date = {}) {
            this.loading.isActive = true;
            this.dashboardStore.fetchRestaurantOwnerCustomerStats(date).then((res) => {
                this.loading.isActive = false;
                this.options = {
                    series: [{
                        name: this.$t('menu.customers'),
                        data: res.data.data.total_customers,
                    }],
                    chart: {
                        type: 'bar',
                        height: 276,
                        parentHeightOffset: 0,
                        zoom: { enabled: false },
                        toolbar: { show: false },
                    },
                    plotOptions: {
                        bar: {
                            horizontal: false,
                            columnWidth: '30%',
                            borderRadius: 2
                        },
                    },
                    stroke: {
                        show: true,
                        width: 2,
                        colors: ['#567DFF']
                    },
                    xaxis: {
                        categories: res.data.data.times,
                    },
                    fill: {
                        opacity: 1
                    },
                    tooltip: {
                        style: {
                            fontSize: '14px',
                            fontFamily: 'inherit',
                        }
                    },
                    colors: ['#567DFF'],
                    grid: { show: false, },
                    yaxis: { show: false },
                    dataLabels: { enabled: false },
                }
            }).catch((err) => {
                this.loading.isActive = false;
            })
        }
    }
}
</script>
