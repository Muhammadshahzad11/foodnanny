<template>
    <LoadingComponent :props="loading"/>
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
                        <i class="lab-fill-dollar text-admin-orange text-2xl"></i>
                    </div>
                    <dl class="flex-auto overflow-hidden">
                        <dt class="font-medium tracking-wide capitalize whitespace-nowrap text-ellipsis overflow-hidden text-white">
                            {{ $t('label.total_earnings') }}
                        </dt>
                        <dd class="font-semibold text-[22px] leading-[34px] text-white">
                            {{
                                overview.total_earnings
                            }}
                        </dd>
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
                            {{ $t('label.accepted_orders') }}
                        </dt>
                        <dd class="font-semibold text-[22px] leading-[34px] text-white">
                            {{ overview.total_accepted_orders }}
                        </dd>
                    </dl>
                </div>
            </div>
            <div class="col-12 sm:col-6 xl:col-3">
                <div class="p-4 rounded-lg flex items-center gap-4 bg-admin-blue">
                    <div class="w-12 h-12 flex-shrink-0 rounded-full flex items-center justify-center bg-white">
                        <i class="lab-fill-delivered text-admin-blue text-2xl"></i>
                    </div>
                    <dl class="flex-auto overflow-hidden">
                        <dt class="font-medium tracking-wide capitalize whitespace-nowrap text-ellipsis overflow-hidden text-white">
                            {{ $t('label.complete_delivery') }}
                        </dt>
                        <dd class="font-semibold text-[22px] leading-[34px] text-white">{{
                                overview.completed_delivery
                            }}
                        </dd>
                    </dl>
                </div>
            </div>
            <div class="col-12 sm:col-6 xl:col-3">
                <div class="p-4 rounded-lg flex items-center gap-4 bg-admin-purple">
                    <div class="w-12 h-12 flex-shrink-0 rounded-full flex items-center justify-center bg-white">
                        <i class="lab-fill-returns text-admin-purple text-2xl"></i>
                    </div>
                    <dl class="flex-auto overflow-hidden">
                        <dt class="font-medium tracking-wide capitalize whitespace-nowrap text-ellipsis overflow-hidden text-white">
                            {{ $t('label.return_delivery') }}
                        </dt>
                        <dd class="font-semibold text-[22px] leading-[34px] text-white">{{
                                overview.return_delivery
                            }}
                        </dd>
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
    components: {DatePickerComponent, LoadingComponent},
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
    computed: {
        overview: function () {
            return this.dashboardStore.deliveryBoyOverview;
        }
    },
    mounted() {
        const startDate = new Date();
        const endDate   = new Date();
        this.date       = [startDate, endDate];

        this.loading.isActive = true;
        this.dashboardStore.fetchDeliveryBoyOverview({
            start_date: startDate,
            end_date: endDate
        }).then((res) => {
            this.loading.isActive = false;
        }).catch((err) => {
            this.loading.isActive = false;
        })
    },
    methods: {
        handleDate: function (e) {
            if (e) {
                this.loading.isActive = true;
                this.dashboardStore.fetchDeliveryBoyOverview({
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
