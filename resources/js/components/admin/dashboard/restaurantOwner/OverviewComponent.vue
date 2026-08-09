<template>
    <LoadingComponent :props="loading"/>
    <div class="mb-9">
        <div class="flex items-center justify-between gap-3 mb-3">
            <h3 class="text-xl font-semibold capitalize text-heading">{{ $t('label.overview') }}</h3>
        </div>

        <div class="row">
            <div class="col-12 sm:col-6 xl:col-3">
                <div class="p-4 rounded-lg flex items-center gap-4 bg-admin-orange">
                    <div class="w-12 h-12 flex-shrink-0 rounded-full flex items-center justify-center bg-white">
                        <i class="lab-fill-dollar text-admin-orange text-2xl"></i>
                    </div>
                    <dl class="flex-auto overflow-hidden">
                        <dt class="font-medium tracking-wide capitalize whitespace-nowrap text-ellipsis overflow-hidden text-white">
                            {{ $t('label.total_sales') }}
                        </dt>
                        <dd class="font-semibold text-[22px] leading-[34px] text-white">{{ overview.total_sales }}</dd>
                    </dl>
                </div>
            </div>
            <div class="col-12 sm:col-6 xl:col-3" v-if="overview.filter_total_sales">
                <div class="p-4 rounded-lg flex items-center gap-4 bg-admin-orange/90">
                    <div class="w-12 h-12 flex-shrink-0 rounded-full flex items-center justify-center bg-white">
                        <i class="lab-fill-dollar text-admin-orange text-2xl"></i>
                    </div>
                    <dl class="flex-auto overflow-hidden">
                        <dt class="font-medium tracking-wide capitalize whitespace-nowrap text-ellipsis overflow-hidden text-white">
                            {{ $t('label.sales') || 'Period sales' }}
                        </dt>
                        <dd class="font-semibold text-[22px] leading-[34px] text-white">{{ overview.filter_total_sales }}</dd>
                    </dl>
                </div>
            </div>
            <div class="col-12 sm:col-6 xl:col-3">
                <div class="p-4 rounded-lg flex items-center gap-4 bg-admin-indigo">
                    <div class="w-12 h-12 flex-shrink-0 rounded-full flex items-center justify-center bg-white">
                        <i class="lab-fill-box text-admin-indigo text-2xl"></i>
                    </div>
                    <dl class="flex-auto overflow-hidden">
                        <dt class="font-medium tracking-wide capitalize whitespace-nowrap text-ellipsis overflow-hidden text-white">
                            {{ $t('label.total_orders') }}
                        </dt>
                        <dd class="font-semibold text-[22px] leading-[34px] text-white">{{ overview.total_orders }}</dd>
                    </dl>
                </div>
            </div>
            <div class="col-12 sm:col-6 xl:col-3">
                <div class="p-4 rounded-lg flex items-center gap-4 bg-admin-blue">
                    <div class="w-12 h-12 flex-shrink-0 rounded-full flex items-center justify-center bg-white">
                        <i class="lab-fill-payout text-admin-blue text-2xl"></i>
                    </div>
                    <dl class="flex-auto overflow-hidden">
                        <dt class="font-medium tracking-wide capitalize whitespace-nowrap text-ellipsis overflow-hidden text-white">
                            {{ $t('label.available_balance') }}
                        </dt>
                        <dd class="font-semibold text-[22px] leading-[34px] text-white">{{ overview.available_balance }}</dd>
                    </dl>
                </div>
            </div>
            <div class="col-12 sm:col-6 xl:col-3">
                <div class="p-4 rounded-lg flex items-center gap-4 bg-admin-purple">
                    <div class="w-12 h-12 flex-shrink-0 rounded-full flex items-center justify-center bg-white">
                        <i class="lab-fill-menu-items text-admin-purple text-2xl"></i>
                    </div>
                    <dl class="flex-auto overflow-hidden">
                        <dt class="font-medium tracking-wide capitalize whitespace-nowrap text-ellipsis overflow-hidden text-white">
                            {{ $t('label.total_menu_items') }}
                        </dt>
                        <dd class="font-semibold text-[22px] leading-[34px] text-white">{{
                                overview.total_menu_items
                            }}
                        </dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>

    <div class="mb-6">
        <div class="flex items-center justify-between gap-3 mb-3">
            <h3 class="text-xl font-semibold capitalize text-heading">{{ $t('menu.order_statistics') }}</h3>
            <div class="relative cursor-pointer custom-datepicker">
                <DatePickerComponent @update:modelValue="handleDate" inputStyle="filter" :range="true" hideInputIcon v-model="date"/>
            </div>
        </div>
        <div class="row">
            <div class="col-12 sm:col-6 md:col-4 lg:col-6 xl:col-3">
                <div class="flex items-center gap-4 p-4 rounded-lg shadow-xs bg-white">
                    <div
                        class="bg-admin-orange/10 w-12 h-12 flex-shrink-0 rounded-full flex items-center justify-center">
                        <i class="lab-fill-box text-admin-orange text-2xl"></i>
                    </div>
                    <dl class="flex-auto overflow-hidden">
                        <dt class="font-medium text-sm capitalize tracking-wide whitespace-nowrap overflow-hidden text-ellipsis mb-1">
                            {{ $t('label.total_orders') }}
                        </dt>
                        <dd class="font-bold text-lg text-secondary">{{ overview.filter_total_orders }}</dd>
                    </dl>
                </div>
            </div>
            <div class="col-12 sm:col-6 md:col-4 lg:col-6 xl:col-3">
                <div class="flex items-center gap-4 p-4 rounded-lg shadow-xs bg-white">
                    <div
                        class="bg-admin-yellow/10 w-12 h-12 flex-shrink-0 rounded-full flex items-center justify-center">
                        <i class="lab-fill-pending text-admin-yellow text-2xl"></i>
                    </div>
                    <dl class="flex-auto overflow-hidden">
                        <dt class="font-medium text-sm capitalize tracking-wide whitespace-nowrap overflow-hidden text-ellipsis mb-1">
                            {{ $t('label.pending') }}
                        </dt>
                        <dd class="font-bold text-lg text-secondary">{{ overview.filter_total_pending }}</dd>
                    </dl>
                </div>
            </div>

            <div class="col-12 sm:col-6 md:col-4 lg:col-6 xl:col-3">
                <div class="flex items-center gap-4 p-4 rounded-lg shadow-xs bg-white">
                    <div
                        class="bg-admin-green/10 w-12 h-12 flex-shrink-0 rounded-full flex items-center justify-center">
                        <i class="lab-fill-preparing text-admin-green text-2xl"></i>
                    </div>
                    <dl class="flex-auto overflow-hidden">
                        <dt class="font-medium text-sm capitalize tracking-wide whitespace-nowrap overflow-hidden text-ellipsis mb-1">
                            {{ $t('label.preparing') }}
                        </dt>
                        <dd class="font-bold text-lg text-secondary">{{ overview.filter_total_preparing }}</dd>
                    </dl>
                </div>
            </div>

            <div class="col-12 sm:col-6 md:col-4 lg:col-6 xl:col-3">
                <div class="flex items-center gap-4 p-4 rounded-lg shadow-xs bg-white">
                    <div class="bg-admin-sky/10 w-12 h-12 flex-shrink-0 rounded-full flex items-center justify-center">
                        <i class="lab-fill-on-the-way text-admin-sky text-2xl"></i>
                    </div>
                    <dl class="flex-auto overflow-hidden">
                        <dt class="font-medium text-sm capitalize tracking-wide whitespace-nowrap overflow-hidden text-ellipsis mb-1">
                            {{ $t('label.out_for_delivery') }}
                        </dt>
                        <dd class="font-bold text-lg text-secondary">{{ overview.filter_total_out_for_delivery }}</dd>
                    </dl>
                </div>
            </div>

            <div class="col-12 sm:col-6 md:col-4 lg:col-6 xl:col-3">
                <div class="flex items-center gap-4 p-4 rounded-lg shadow-xs bg-white">
                    <div
                        class="bg-admin-purple/10 w-12 h-12 flex-shrink-0 rounded-full flex items-center justify-center">
                        <i class="lab-fill-delivered text-admin-purple text-2xl"></i>
                    </div>
                    <dl class="flex-auto overflow-hidden">
                        <dt class="font-medium text-sm capitalize tracking-wide whitespace-nowrap overflow-hidden text-ellipsis mb-1">
                            {{ $t('label.delivered') }}
                        </dt>
                        <dd class="font-bold text-lg text-secondary">{{ overview.filter_total_delivered }}</dd>
                    </dl>
                </div>
            </div>

            <div class="col-12 sm:col-6 md:col-4 lg:col-6 xl:col-3">
                <div class="flex items-center gap-4 p-4 rounded-lg shadow-xs bg-white">
                    <div class="bg-admin-red/10 w-12 h-12 flex-shrink-0 rounded-full flex items-center justify-center">
                        <i class="lab-fill-cancel text-admin-red text-2xl"></i>
                    </div>
                    <dl class="flex-auto overflow-hidden">
                        <dt class="font-medium text-sm capitalize tracking-wide whitespace-nowrap overflow-hidden text-ellipsis mb-1">
                            {{ $t('label.canceled') }}
                        </dt>
                        <dd class="font-bold text-lg text-secondary">{{ overview.filter_total_canceled }}</dd>
                    </dl>
                </div>
            </div>

            <div class="col-12 sm:col-6 md:col-4 lg:col-6 xl:col-3">
                <div class="flex items-center gap-4 p-4 rounded-lg shadow-xs bg-white">
                    <div class="bg-admin-blue/10 w-12 h-12 flex-shrink-0 rounded-full flex items-center justify-center">
                        <i class="lab-fill-returns text-admin-blue text-2xl"></i>
                    </div>
                    <dl class="flex-auto overflow-hidden">
                        <dt class="font-medium text-sm capitalize tracking-wide whitespace-nowrap overflow-hidden text-ellipsis mb-1">
                            {{ $t('label.returned') }}
                        </dt>
                        <dd class="font-bold text-lg text-secondary">{{ overview.filter_total_returned }}</dd>
                    </dl>
                </div>
            </div>

            <div class="col-12 sm:col-6 md:col-4 lg:col-6 xl:col-3">
                <div class="flex items-center gap-4 p-4 rounded-lg shadow-xs bg-white">
                    <div class="bg-admin-red/10 w-12 h-12 flex-shrink-0 rounded-full flex items-center justify-center">
                        <i class="lab-fill-cancel text-admin-red text-2xl"></i>
                    </div>
                    <dl class="flex-auto overflow-hidden">
                        <dt class="font-medium text-sm capitalize tracking-wide whitespace-nowrap overflow-hidden text-ellipsis mb-1">
                            {{ $t('label.rejected') }}
                        </dt>
                        <dd class="font-bold text-lg text-secondary">{{ overview.filter_total_rejected }}</dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import {useDashboardStore} from "../../../../stores/dashboard.js";
import LoadingComponent from "../../../common/LoadingComponent.vue";
import DatePickerComponent from "../../components/DatePickerComponent.vue";

export default {
    name: "OverviewComponent",
    components: {
        DatePickerComponent,
        LoadingComponent
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
                isActive: false
            },
            date: null
        }
    },
    mounted() {
        const startDate = new Date();
        const endDate   = new Date();
        this.date       = [startDate, endDate];

        this.loading.isActive = true;
        this.dashboardStore.fetchRestaurantOwnerOverview({
            start_date: startDate,
            end_date: endDate
        }).then((res) => {
            this.loading.isActive = false;
        }).catch((err) => {
            this.loading.isActive = false;
        })
    },
    computed: {
        overview: function () {
            return this.dashboardStore.restaurantOwnerOverview;
        }
    },
    methods: {
        handleDate: function (e) {
            if (e) {
                this.loading.isActive = true;
                this.dashboardStore.fetchRestaurantOwnerOverview({
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
