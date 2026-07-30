<template>
    <LoadingComponent :props="loading"/>
    <div class="col-12 xl:col-6">
        <div class="db-card !rounded-lg">
            <div class="db-card-header border-0">
                <h3 class="db-card-title text-lg text-heading font-semibold">{{ $t('label.orders_summary') }}</h3>
                <div id="order-range" class="cursor-pointer flex items-center gap-3">
                    <DatePickerComponent @update:modelValue="handleDate" inputStyle="filter" :range="true" hideInputIcon
                                         v-model="date"/>
                </div>
            </div>
            <div class="db-card-body">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-5">
                    <apexchart height="250" v-if="options" :options="options" :series="options.series"></apexchart>
                    <ul class="flex flex-col gap-8 w-full sm:w-36">
                        <li class="w-full">
                            <span class="block capitalize mb-1 text-heading">
                                {{ $t("label.delivered") }} ({{ delivered }}%)
                            </span>
                            <span class="block w-full h-2 rounded bg-primary"></span>
                        </li>
                        <li class="w-full">
                            <span class="block capitalize mb-1 text-heading">
                                {{ $t("label.returned") }} ({{ returned }}%)
                            </span>
                            <span class="block w-full h-2 rounded bg-[#567DFF]"></span>
                        </li>
                        <li class="w-full">
                            <span class="block capitalize mb-1 text-heading">
                                {{ $t("label.canceled") }} ({{ canceled }}%)
                            </span>
                            <span class="block w-full h-2 rounded bg-[#A953FF]"></span>
                        </li>
                        <li class="w-full">
                            <span class="block capitalize mb-1 text-heading">
                                {{ $t("label.rejected") }} ({{ rejected }}%)
                            </span>
                            <span class="block w-full h-2 rounded bg-[#FB4E4E]"></span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import LoadingComponent from "../../../common/LoadingComponent.vue";
import DatePickerComponent from "../../components/DatePickerComponent.vue";
import {useDashboardStore} from "../../../../stores/dashboard";

export default {
    name: "OrdersSummaryComponent",
    components: {LoadingComponent, DatePickerComponent},
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
            },
            date: null,
            options: null,
            delivered: null,
            canceled: null,
            returned: null,
            rejected: null
        };
    },
    mounted() {
        const now       = new Date();
        const startDate = new Date(now);
        const endDate   = new Date(now);
        startDate.setDate(endDate.getDate() - 6);
        this.date = [startDate, endDate];

        this.ordersSummary({
            start_date: startDate,
            end_date: endDate,
        });
    },
    methods: {
        handleDate: function (e) {
            this.ordersSummary({
                start_date: e[0],
                end_date: e[1]
            });
        },
        ordersSummary: function (date = {}) {
            this.loading.isActive = true;
            this.dashboardStore.fetchAdminOrdersSummary(date).then((res) => {
                this.loading.isActive = false;
                this.delivered        = res.data.data.delivered;
                this.returned         = res.data.data.returned;
                this.canceled         = res.data.data.canceled;
                this.rejected         = res.data.data.rejected;

                this.options = {
                    series: [parseInt(this.delivered), parseInt(this.returned), parseInt(this.canceled), parseInt(this.rejected)],
                    chart: {
                        type: 'radialBar'
                    },
                    plotOptions: {
                        radialBar: {
                            hollow: {size: '25%'},
                            track: {margin: 10},
                            dataLabels: {
                                name: {
                                    fontSize: '14px',
                                    fontFamily: 'inherit'
                                },
                                value: {
                                    fontSize: '14px',
                                    fontFamily: 'inherit',
                                    fontWeight: 'bold',
                                    color: '#1F1F39',
                                    offsetY: 5
                                },
                                total: {
                                    show: true,
                                    label: this.$t('label.total'),
                                    formatter: function (w) {
                                        return w.config.series.reduce((a, b) => a + b, 0);
                                    }
                                }
                            }
                        }
                    },
                    stroke: {lineCap: 'round'},
                    colors: ['#FF8C39', '#567DFF', '#A953FF', '#FB4E4E'],
                    labels: [this.$t('label.delivered'), this.$t('label.returned'), this.$t('label.canceled'), this.$t('label.rejected')]
                }
            }).catch((err) => {
                this.loading.isActive = false;
            })
        }
    }
}
</script>
