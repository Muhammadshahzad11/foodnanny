<template>
    <LoadingComponent :props="loading"/>
    <div class="col-12 xl:col-6">
        <div class="db-card !rounded-lg">
            <div class="db-card-header border-0">
                <h3 class="db-card-title text-lg text-heading font-semibold">{{ $t('label.revenue') }}</h3>
            </div>
            <div class="db-card-body">
                <apexchart type="bar" height="261" :options="options" :series="series"/>
            </div>
        </div>
    </div>
</template>

<script>
import LoadingComponent from "../../../common/LoadingComponent.vue";
import {useDashboardStore} from "../../../../stores/dashboard.js";
import appService from "../../../../services/appService.js";
import {useFrontendSettingStore} from "../../../../stores/frontendSetting.js";

export default {
    name: "RevenueComponent",
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
            },
            series: [],
            options: {}
        }
    },
    computed: {
        setting: function () {
            return this.frontendSettingStore.lists;
        }
    },
    mounted() {
        this.dashboardStore.fetchAdminRevenue().then((res) => {
            this.loading.isActive = false;
            this.series           = res.data.data.series;
            this.options          = {
                chart: {
                    type: 'bar',
                    toolbar: {show: false}
                },
                colors: ['#3B82F6', '#F97316'],
                plotOptions: {
                    bar: {
                        horizontal: false,
                        columnWidth: '50%',
                        borderRadius: 2
                    }
                },
                dataLabels: {enabled: false},
                stroke: {
                    show: true,
                    width: 2,
                    colors: ['transparent']
                },
                xaxis: {
                    categories: [
                        this.$t('months.january'),
                        this.$t('months.february'),
                        this.$t('months.march'),
                        this.$t('months.april'),
                        this.$t('months.may'),
                        this.$t('months.june'),
                        this.$t('months.july'),
                        this.$t('months.august'),
                        this.$t('months.september'),
                        this.$t('months.october'),
                        this.$t('months.november'),
                        this.$t('months.december'),
                    ]
                },
                legend: {
                    position: 'top',
                    horizontalAlign: 'left'
                },
                tooltip: {
                    y: {
                        formatter: (val) => this.currencyFormat(val, this.setting.site_digit_after_decimal_point, this.setting.site_default_currency_symbol, this.setting.site_currency_position)
                    }
                }
            }
        }).catch((err) => {
            this.loading.isActive = false;
        })
    },
    methods: {
        currencyFormat(amount, decimal, currency, position) {
            return appService.currencyFormat(amount, decimal, currency, position);
        }
    }
}
</script>
