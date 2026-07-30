<template>
    <LoadingComponent :props="loading" />
    <div class="col-12">
        <div class="db-card db-tab-div active">
            <div class="db-card-header border-none">
                <h3 class="db-card-title">{{ $t('menu.sales_report') }}</h3>
                <nav class="flex flex-wrap gap-2 mobile:justify-center">
                    <TableLimitComponent :method="list" :search="props.search" :page="paginationPage" />
                    <FilterComponent @click.prevent="handleSlide('sales-report-filter')" />
                    <div class="paper-group">
                        <ExportComponent @click.prevent="handlePaper" />
                        <nav
                            class="paper-content absolute top-9 right-1/2 translate-x-1/2 z-30 min-w-[80px] w-fit rounded-md shadow-paper bg-white">
                            <PrintComponent :props="printObj" />
                            <ExcelComponent :method="xls" />
                        </nav>
                    </div>
                </nav>
            </div>
            <div class="table-filter-div" id="sales-report-filter">
                <form class="p-4 sm:p-5 mb-5" @submit.prevent="search">
                    <div class="row">
                        <div class="col-12 sm:col-6 md:col-4 xl:col-3">
                            <label for="order_id" class="db-field-title after:hidden">{{ $t('label.order_id') }}</label>
                            <input id="order_id" v-model="props.search.order_serial_no" type="text"
                                class="db-field-control">
                        </div>
                        <div class="col-12 sm:col-6 md:col-4 xl:col-3">
                            <label for="searchStatus" class="db-field-title after:hidden">{{$t('label.status')}}</label>
                            <vue-select class="db-field-control f-b-custom-select" id="searchStatus"
                                v-model="props.search.status" :options="[
                                    { id: enums.orderStatusEnum.PENDING, name: $t('label.pending') },
                                    { id: enums.orderStatusEnum.ACCEPT, name: $t('label.accept') },
                                    { id: enums.orderStatusEnum.PREPARING, name: $t('label.preparing') },
                                    { id: enums.orderStatusEnum.OUT_FOR_DELIVERY, name: $t('label.out_for_delivery') },
                                    { id: enums.orderStatusEnum.DELIVERED, name: $t('label.delivered') },
                                    { id: enums.orderStatusEnum.CANCELED, name: $t('label.canceled') },
                                    { id: enums.orderStatusEnum.REJECTED, name: $t('label.rejected') },
                                    { id: enums.orderStatusEnum.RETURNED, name: $t('label.returned') }
                                ]" label-by="name" value-by="id" :closeOnSelect="true" :searchable="true"
                                :clearOnClose="true" placeholder="--" search-placeholder="--" />
                        </div>

                        <div class="col-12 sm:col-6 md:col-4 xl:col-3">
                            <label for="searchStartDate" class="db-field-title after:hidden">{{$t('label.date')}}</label>
                            <DatePickerComponent @update:modelValue="handleDate" inputStyle="filter" :range="true" hideInputIcon
                                v-model="modelValue" />
                        </div>

                        <div class="col-12 sm:col-6 md:col-4 xl:col-3">
                            <label for="searchStatus" class="db-field-title after:hidden">{{$t('label.paid_status')}}</label>
                            <vue-select class="db-field-control f-b-custom-select" id="searchStatus"
                                v-model="props.search.payment_status" :options="[
                                    { id: enums.paymentStatusEnum.PAID, name: $t('label.paid') },
                                    { id: enums.paymentStatusEnum.UNPAID, name: $t('label.unpaid') }
                                ]" label-by="name" value-by="id" :closeOnSelect="true" :searchable="true"
                                :clearOnClose="true" placeholder="--" search-placeholder="--" />
                        </div>

                        <div class="col-12 sm:col-6 md:col-4 xl:col-3">
                            <label for="searchStatus" class="db-field-title after:hidden">{{$t('label.payment_type')}}</label>
                            <vue-select class="db-field-control f-b-custom-select" id="searchStatus"
                                v-model="props.search.payment_method" :options="paymentGateways" label-by="name"
                                value-by="id" :closeOnSelect="true" :searchable="true" :clearOnClose="true" placeholder="--"
                                search-placeholder="--" />
                        </div>
                        <div class="col-12 sm:col-6 md:col-4 xl:col-3">
                            <label for="source" class="db-field-title after:hidden">{{$t('label.source')}}</label>
                            <vue-select class="db-field-control f-b-custom-select" id="source" v-model="props.search.source"
                                :options="enums.sourceObject" label-by="name" value-by="value" :closeOnSelect="true"
                                :searchable="true" :clearOnClose="true" placeholder="--" search-placeholder="--" />
                        </div>
                        <div class="col-12">
                            <div class="flex flex-wrap gap-3 mt-4">
                                <button class="db-btn py-2 text-white bg-primary">
                                    <i class="lab lab-line-search lab-font-size-16"></i>
                                    <span>{{ $t('button.search') }}</span>
                                </button>
                                <button class="db-btn py-2 text-white bg-gray-600" @click="clear">
                                    <i class="lab lab-line-cross lab-font-size-22"></i>
                                    <span>{{ $t('button.clear') }}</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="db-table-responsive">
                <table class="db-table stripe" id="print">
                    <thead class="db-table-head">
                        <tr class="db-table-head-tr">
                            <th class="db-table-head-th">{{ $t('label.order_id') }}</th>
                            <th class="db-table-head-th">{{ $t('label.date') }}</th>
                            <th class="db-table-head-th">{{ $t('label.total') }}</th>
                            <th class="db-table-head-th">{{ $t('label.discount') }}</th>
                            <th class="db-table-head-th">{{ $t('label.delivery_fee') }}</th>
                            <th class="db-table-head-th">{{ $t('label.payment_type') }}</th>
                            <th class="db-table-head-th">{{ $t('label.payment_status') }}</th>
                        </tr>
                    </thead>
                    <tbody class="db-table-body" v-if="salesReports.length > 0">
                        <tr class="db-table-body-tr" v-for="salesReport in salesReports" :key="salesReport">
                            <td class="db-table-body-td">{{ salesReport.order_serial_no }}</td>
                            <td class="db-table-body-td">{{ salesReport.order_datetime }}</td>
                            <td class="db-table-body-td">{{ salesReport.total_amount_price }}</td>
                            <td class="db-table-body-td">{{ salesReport.discount_amount_price }}</td>
                            <td class="db-table-body-td">{{ salesReport.delivery_fee_amount_price }}</td>
                            <td class="db-table-body-td">
                                <span v-if="salesReport.transaction">
                                    {{ salesReport.transaction.payment_method }}
                                </span>
                                <span v-else>
                                    {{ enums.paymentTypeEnumArray[salesReport.payment_method] }}
                                </span>
                            </td>
                            <td class="db-table-body-td">
                                <span :class="statusClass(salesReport.payment_status)">
                                    {{ enums.paymentStatusEnumArray[salesReport.payment_status] }}
                                </span>
                            </td>
                        </tr>
                    </tbody>
                    <tbody class="db-table-body" v-else>
                        <tr class="db-table-body-tr">
                            <td class="db-table-body-td" colspan="7">
                                <div class="p-4">
                                    <img class="m-auto not-found max-w-[300px]" :src="setting.data_not_found" alt="Not Found">
                                    <span class="block mt-3 text-center text-lg">{{ $t('message.no_data_found') }}</span>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="flex items-center justify-between border-t border-gray-200 bg-white px-4 py-6">
                <PaginationSMBox :pagination="pagination" :method="list" />
                <div class="hidden sm:flex sm:flex-1 sm:items-center sm:justify-between">
                    <PaginationTextComponent :props="{ page: paginationPage }" />
                    <PaginationBox :pagination="pagination" :method="list" />
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import LoadingComponent from "../../common/LoadingComponent.vue";
import alertService from "../../../services/alertService.js";
import PaginationTextComponent from "../components/pagination/PaginationTextComponent.vue";
import PaginationBox from "../components/pagination/PaginationBox.vue";
import PaginationSMBox from "../components/pagination/PaginationSMBox.vue";
import appService from "../../../services/appService.js";
import paymentStatusEnum from "../../../enums/modules/paymentStatusEnum.js";
import orderStatusEnum from "../../../enums/modules/orderStatusEnum.js";
import paymentTypeEnum from "../../../enums/modules/posPaymentMethodEnum.js";
import TableLimitComponent from "../components/TableLimitComponent.vue";
import FilterComponent from "../components/buttons/collapse/FilterComponent.vue";
import ExportComponent from "../components/buttons/export/ExportComponent.vue";
import PrintComponent from "../components/buttons/export/PrintComponent.vue";
import ExcelComponent from "../components/buttons/export/ExcelComponent.vue";
import statusEnum from "../../../enums/modules/statusEnum.js";
import sourceEnum from "../../../enums/modules/sourceEnum.js";
import { usePaper } from "../../../composables/paper.js";
import { useSlide } from "../../../composables/slide.js";
import DatePickerComponent from "../components/DatePickerComponent.vue";
import {useSalesReportStore} from "../../../stores/salesReport.js";
import {usePaymentGatewayStore} from "../../../stores/paymentGateway.js";
import { useFrontendSettingStore } from "../../../stores/frontendSetting.js";

export default {
    name: "SalesReportListComponent",
    components: {
        TableLimitComponent,
        PaginationSMBox,
        PaginationBox,
        PaginationTextComponent,
        LoadingComponent,
        ExportComponent,
        FilterComponent,
        PrintComponent,
        ExcelComponent,
        DatePickerComponent
    },
    setup() {
        const salesReportStore = useSalesReportStore();
        const paymentGatewayStore = usePaymentGatewayStore();
        const frontendSettingStore = useFrontendSettingStore();

        return {
            salesReportStore,
            paymentGatewayStore,
            frontendSettingStore
        }
    },
    data() {
        return {
            loading: {
                isActive: false
            },
            enums: {
                paymentStatusEnum: paymentStatusEnum,
                paymentTypeEnum: paymentTypeEnum,
                orderStatusEnum: orderStatusEnum,
                sourceEnum: sourceEnum,
                paymentStatusEnumArray: {
                    [paymentStatusEnum.PAID]: this.$t("label.paid"),
                    [paymentStatusEnum.UNPAID]: this.$t("label.unpaid")
                },
                paymentTypeEnumArray: {
                    [paymentTypeEnum.CASH]          : this.$t("label.cash") +'/'+this.$t("label.cash_on_delivery"),
                    [paymentTypeEnum.CREDIT]        : this.$t("label.credit"),
                    [paymentTypeEnum.CARD]          : this.$t("label.card"),
                    [paymentTypeEnum.MOBILE_BANKING]: this.$t("label.mobile_banking"),
                    [paymentTypeEnum.OTHER]         : this.$t("label.other")
                },
                orderStatusEnumArray: {
                    [orderStatusEnum.PENDING]: this.$t("label.pending"),
                    [orderStatusEnum.ACCEPT]: this.$t("label.accept"),
                    [orderStatusEnum.PREPARING]: this.$t("label.preparing"),
                    [orderStatusEnum.OUT_FOR_DELIVERY]: this.$t("label.out_for_delivery"),
                    [orderStatusEnum.DELIVERED]: this.$t("label.delivered"),
                    [orderStatusEnum.CANCELED]: this.$t("label.canceled"),
                    [orderStatusEnum.REJECTED]: this.$t("label.rejected"),
                    [orderStatusEnum.RETURNED]: this.$t("label.returned")
                },
                sourceObject: [
                    {
                        name: this.$t("label.web"),
                        value: sourceEnum.WEB
                    },
                    {
                        name: this.$t("label.app"),
                        value: sourceEnum.APP
                    },
                    {
                        name: this.$t("label.pos"),
                        value: sourceEnum.POS
                    }
                ]
            },
            printLoading: true,
            printObj: {
                id: "print",
                popTitle: this.$t('menu.sales_report')
            },
            props: {
                search: {
                    paginate: 1,
                    page: 1,
                    per_page: 10,
                    order_column: 'id',
                    payment_status: null,
                    payment_method: null,
                    order_serial_no: "",
                    status: null,
                    from_date: "",
                    to_date: "",
                    source: null
                }
            },
            modelValue: null,
            handlePaper: usePaper().handlePaper,
            handleSlide: useSlide().handleSlide
        }
    },
    mounted() {
        this.list();
        this.paymentGatewayStore.fetch({
            order_column: 'id',
            order_type: 'asc',
            status: statusEnum.ACTIVE
        });
    },
    computed: {
        setting: function () {
            return this.frontendSettingStore.lists;
        },
        salesReports: function () {
            return this.salesReportStore.lists;
        },
        pagination: function () {
            return this.salesReportStore.pagination;
        },
        paginationPage: function () {
            return this.salesReportStore.page;
        },
        paymentGateways: function () {
            return this.paymentGatewayStore.lists;
        }
    },
    methods: {
        floatNumber(e) {
            return appService.floatNumber(e);
        },
        statusClass: function (status) {
            return appService.statusClass(status);
        },
        search: function () {
            this.list();
        },
        handleDate: function (e) {
            if (e) {
                this.props.search.from_date = e[0];
                this.props.search.to_date = e[1];
            } else {
                this.props.search.from_date = null;
                this.props.search.to_date = null;
            }
        },
        clear: function () {
            this.props.search.paginate = 1;
            this.props.search.page = 1;
            this.props.search.order_serial_no = "";
            this.props.search.payment_status = null;
            this.props.search.payment_method = null;
            this.props.search.status = null;
            this.props.search.from_date = "";
            this.props.search.to_date = "";
            this.props.search.source = null;
            this.modelValue = null;
            this.list();
        },
        list: function (page = 1) {
            this.loading.isActive = true;
            this.props.search.page = page;
            this.salesReportStore.fetch(this.props.search).then(res => {
                this.loading.isActive = false;
            }).catch((err) => {
                this.loading.isActive = false;
            });
        },
        xls: function () {
            this.loading.isActive = true;
            this.salesReportStore.export(this.props.search).then(res => {
                this.loading.isActive = false;
                const blob = new Blob([res.data], { type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' });
                const link = document.createElement('a');
                link.href = URL.createObjectURL(blob);
                link.download = this.$t("menu.sales_report");
                link.click();
                URL.revokeObjectURL(link.href);
            }).catch((err) => {
                this.loading.isActive = false;
                alertService.error(err.response.data.message);
            });
        }
    }
}
</script>
