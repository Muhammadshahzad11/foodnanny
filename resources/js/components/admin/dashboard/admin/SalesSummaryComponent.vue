<template>
    <LoadingComponent :props="loading"/>
    <div class="col-12 xl:col-6">
        <div class="db-card !rounded-lg">
            <div class="db-card-header border-0">
                <h3 class="db-card-title text-lg text-heading font-semibold">{{ $t('label.sales_summary') }}</h3>
                <div class="relative cursor-pointer custom-datepicker">
                    <DatePickerComponent @update:modelValue="handleDate" inputStyle="filter" :range="true" hideInputIcon v-model="date"/>
                </div>
            </div>
            <div class="db-card-body">
                <ul class="flex gap-11">
                    <li>
                        <div class="flex items-center gap-2.5">
                            <i class="lab lab-line-bar-chart lab-font-size-20 lab-font-color-2"></i>
                            <h3 class="font-bold text-[22px] leading-[34px]">{{ totalSales }}</h3>
                        </div>
                        <p class="text-xs capitalize">{{ $t("label.total_sales") }}</p>
                    </li>
                    <li>
                        <div class="flex items-center gap-2.5">
                            <i class="lab lab-line-bar-chart lab-font-size-20 lab-font-color-2"></i>
                            <h3 class="font-bold text-[22px] leading-[34px]">{{ avgPerDay }}</h3>
                        </div>
                        <p class="text-xs capitalize">{{ $t("label.avg_sales_per_day") }}</p>
                    </li>
                </ul>
                <apexchart height="203" v-if="options" :options="options" :series="options.series"></apexchart>
            </div>
        </div>
    </div>
</template>

<script>
import {useDashboardStore} from "../../../../stores/dashboard.js";
import DatePickerComponent from "../../components/DatePickerComponent.vue";
import LoadingComponent from "../../../common/LoadingComponent.vue";

export default {
    name: "SalesSummaryComponent",
    components: {
        LoadingComponent,
        DatePickerComponent
    },
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
            options: null,
            date: null,
            totalSales: null,
            avgPerDay: null
        }
    },
    mounted() {
        const now       = new Date();
        const startDate = new Date(now.getFullYear(), now.getMonth(), 1);
        const endDate   = new Date(now.getFullYear(), now.getMonth() + 1, 0);
        this.date       = [startDate, endDate];

        this.salesSummary({
            start_date: startDate,
            end_date: endDate,
        });
    },
    methods: {
        handleDate: function (e) {
            this.salesSummary({
                start_date: e[0],
                end_date: e[1]
            });
        },
        salesSummary: function (date = {}) {
            this.loading.isActive = true;
            this.dashboardStore.fetchAdminSalesSummary(date).then((res) => {
                this.loading.isActive = false;
                this.totalSales       = res.data.data.total_sales;
                this.avgPerDay        = res.data.data.avg_per_day;
                this.options          = {
                    series: [{
                        name: this.$t('label.sales'),
                        data: res.data.data.per_day_sales.map(val => Number(val).toFixed(2))
                    }],
                    chart: {
                        type: 'area',
                        height: 250,
                        fontFamily: 'inherit',
                        parentHeightOffset: 0,
                        zoom: {enabled: false},
                        toolbar: {show: false}
                    },
                    xaxis: {
                        tooltip: {enabled: false},
                        axisBorder: {show: false}
                    },
                    stroke: {
                        width: 3,
                        lineCap: "round",
                        curve: "smooth"
                    },
                    colors: ["#F36805"],
                    grid: {show: false},
                    yaxis: {show: false},
                    dataLabels: {enabled: false}
                }
            }).catch((err) => {
                this.loading.isActive = false;
            })
        }
    }
}
</script>

