<template>
    <div class="mb-9">
        <div class="flex items-center justify-between gap-3 mb-3">
            <h3 class="text-xl font-semibold capitalize text-heading">{{ $t('label.overview') }}</h3>
            <div class="relative cursor-pointer custom-datepicker">
                <DatePickerComponent @update:modelValue="handleDate" inputStyle="filter" :range="true" hideInputIcon v-model="date"/>
            </div>
        </div>
        <div class="row">
            <div class="col-12 sm:col-6 xl:col-3">
                <div class="p-4 rounded-lg flex items-center gap-4 bg-admin-orange">
                    <div class="w-12 h-12 flex-shrink-0 rounded-full flex items-center justify-center bg-white">
                        <i class="lab-fill-box text-admin-orange text-2xl"></i>
                    </div>
                    <dl class="flex-auto overflow-hidden">
                        <dt class="font-medium tracking-wide capitalize whitespace-nowrap text-ellipsis overflow-hidden text-white">
                            {{ $t('label.total_orders') }}
                        </dt>
                        <dd class="font-semibold text-[22px] leading-[34px] text-white">
                            {{ overview.total_orders }}
                        </dd>
                    </dl>
                </div>
            </div>
            <div class="col-12 sm:col-6 xl:col-3">
                <div class="p-4 rounded-lg flex items-center gap-4 bg-admin-indigo">
                    <div class="w-12 h-12 flex-shrink-0 rounded-full flex items-center justify-center bg-white">
                        <i class="lab-fill-dollar text-admin-indigo text-2xl"></i>
                    </div>
                    <dl class="flex-auto overflow-hidden">
                        <dt class="font-medium tracking-wide capitalize whitespace-nowrap text-ellipsis overflow-hidden text-white">
                            {{ $t('label.sales_volume') }}
                        </dt>
                        <dd class="font-semibold text-[22px] leading-[34px] text-white">
                            {{ overview.sales_volume }}
                        </dd>
                    </dl>
                </div>
            </div>
            <div class="col-12 sm:col-6 xl:col-3">
                <div class="p-4 rounded-lg flex items-center gap-4 bg-admin-blue">
                    <div class="w-12 h-12 flex-shrink-0 rounded-full flex items-center justify-center bg-white">
                        <i class="lab-fill-restaurants text-admin-blue text-2xl"></i>
                    </div>
                    <dl class="flex-auto overflow-hidden">
                        <dt class="font-medium tracking-wide capitalize whitespace-nowrap text-ellipsis overflow-hidden text-white">
                            {{ $t('label.no_of_restaurants') }}
                        </dt>
                        <dd class="font-semibold text-[22px] leading-[34px] text-white">
                            {{ overview.no_of_restaurants }}
                        </dd>
                    </dl>
                </div>
            </div>
            <div class="col-12 sm:col-6 xl:col-3">
                <div class="p-4 rounded-lg flex items-center gap-4 bg-admin-purple">
                    <div class="w-12 h-12 flex-shrink-0 rounded-full flex items-center justify-center bg-white">
                        <i class="lab-fill-commission text-admin-purple text-2xl"></i>
                    </div>
                    <dl class="flex-auto overflow-hidden">
                        <dt class="font-medium tracking-wide capitalize whitespace-nowrap text-ellipsis overflow-hidden text-white">
                            {{ $t('label.commission') }}
                        </dt>
                        <dd class="font-semibold text-[22px] leading-[34px] text-white">
                            {{ overview.commission }}
                        </dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>

    <div class="mb-6">
        <div class="row">
            <div class="col-12 sm:col-6 md:col-4 lg:col-6 xl:col-3">
                <div class="flex items-center gap-4 p-4 rounded-lg shadow-xs bg-white">
                    <div class="bg-admin-purple/10 w-12 h-12 flex-shrink-0 rounded-full flex items-center justify-center">
                        <i class="lab-fill-delivered text-admin-purple text-2xl"></i>
                    </div>
                    <dl class="flex-auto overflow-hidden">
                        <dt class="font-medium text-sm capitalize tracking-wide whitespace-nowrap overflow-hidden text-ellipsis mb-1"> {{ $t('label.delivered_orders') }}</dt>
                        <dd class="font-bold text-lg text-secondary">{{ overview.delivered_orders }}</dd>
                    </dl>
                </div>
            </div>

            <div class="col-12 sm:col-6 md:col-4 lg:col-6 xl:col-3">
                <div class="flex items-center gap-4 p-4 rounded-lg shadow-xs bg-white">
                    <div class="bg-admin-red/10 w-12 h-12 flex-shrink-0 rounded-full flex items-center justify-center">
                        <i class="lab-fill-cancel text-admin-red text-2xl"></i>
                    </div>
                    <dl class="flex-auto overflow-hidden">
                        <dt class="font-medium text-sm capitalize tracking-wide whitespace-nowrap overflow-hidden text-ellipsis mb-1"> {{ $t('label.canceled_orders') }}</dt>
                        <dd class="font-bold text-lg text-secondary">{{ overview.canceled_orders }}</dd>
                    </dl>
                </div>
            </div>

            <div class="col-12 sm:col-6 md:col-4 lg:col-6 xl:col-3">
                <div class="flex items-center gap-4 p-4 rounded-lg shadow-xs bg-white">
                    <div class="bg-admin-blue/10 w-12 h-12 flex-shrink-0 rounded-full flex items-center justify-center">
                        <i class="lab-fill-returns text-admin-blue text-2xl"></i>
                    </div>
                    <dl class="flex-auto overflow-hidden">
                        <dt class="font-medium text-sm capitalize tracking-wide whitespace-nowrap overflow-hidden text-ellipsis mb-1"> {{ $t('label.returned_orders') }}</dt>
                        <dd class="font-bold text-lg text-secondary">{{ overview.returned_orders }}</dd>
                    </dl>
                </div>
            </div>

            <div class="col-12 sm:col-6 md:col-4 lg:col-6 xl:col-3">
                <div class="flex items-center gap-4 p-4 rounded-lg shadow-xs bg-white">
                    <div class="bg-admin-pink/10 w-12 h-12 flex-shrink-0 rounded-full flex items-center justify-center">
                        <i class="lab-fill-cancel text-admin-pink text-2xl"></i>
                    </div>
                    <dl class="flex-auto overflow-hidden">
                        <dt class="font-medium text-sm capitalize tracking-wide whitespace-nowrap overflow-hidden text-ellipsis mb-1"> {{ $t('label.rejected_orders') }}</dt>
                        <dd class="font-bold text-lg text-secondary">{{ overview.rejected_orders }}</dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>

</template>

<script>
import DatePickerComponent from "../../components/DatePickerComponent.vue";
import {useDashboardStore} from "../../../../stores/dashboard.js";

export default {
    name: "OverviewComponent",
    components: {DatePickerComponent},
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
            date: null
        }
    },
    mounted() {
        const now       = new Date();
        const startDate = new Date(now.getFullYear(), now.getMonth(), 1);
        const endDate   = new Date(now.getFullYear(), now.getMonth() + 1, 0);
        this.date       = [startDate, endDate];

        this.dashboardStore.fetchAdminOverview({
            start_date: startDate,
            end_date: endDate
        });
    },
    computed: {
        overview: function () {
            return this.dashboardStore.adminOverview;
        }
    },
    methods: {
        handleDate: function (e) {
            if (e) {
                this.loading.isActive = true;
                this.dashboardStore.fetchAdminOverview({
                    start_date: e[0],
                    end_date: e[1]
                }).then((res) => {
                    this.loading.isActive = false;
                }).catch((err) => {
                    this.loading.isActive = false;
                });
            } else {
                this.date = null;
            }
        }
    }
}
</script>
