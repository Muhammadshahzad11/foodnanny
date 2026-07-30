<template>
    <LoadingComponent :props="loading"/>
    <div class="col-12">
        <div class="db-card">
            <div class="db-card-header border-none">
                <h3 class="db-card-title">{{ $t('menu.pos_orders') }}</h3>
                <nav class="flex flex-wrap gap-2 mobile:justify-center">
                    <TableLimitComponent :method="list" :search="props.search" :page="paginationPage"/>
                    <FilterComponent @click.prevent="handleSlide('pos-order')"/>
                    <div class="paper-group">
                        <ExportComponent @click.prevent="handlePaper"/>
                        <nav
                            class="paper-content absolute top-9 right-1/2 translate-x-1/2 z-30 min-w-[80px] w-fit rounded-md shadow-paper bg-white">
                            <PrintComponent :props="printObj"/>
                            <ExcelComponent :method="xls"/>
                        </nav>
                    </div>
                </nav>
            </div>

            <div class="table-filter-div" id="pos-order">
                <form class="p-4 sm:p-5 mb-5" @submit.prevent="search">
                    <div class="row">
                        <div class="col-12 sm:col-6 md:col-4 xl:col-3">
                            <label for="order_id" class="db-field-title after:hidden">{{ $t('label.order_id') }}</label>
                            <input id="order_id" v-model="props.search.order_serial_no" type="text"
                                   class="db-field-control">
                        </div>

                        <div class="col-12 sm:col-6 md:col-4 xl:col-3">
                            <label for="searchStatus" class="db-field-title after:hidden">
                                {{ $t('label.status') }}
                            </label>
                            <vue-select class="db-field-control f-b-custom-select" id="searchStatus"
                                        v-model="props.search.status"
                                        :options="[{ id: enums.orderStatusEnum.ACCEPT, name: $t('label.accept') }, { id: enums.orderStatusEnum.PREPARING, name: $t('label.preparing') }, { id: enums.orderStatusEnum.PREPARED, name: $t('label.prepared') }, { id: enums.orderStatusEnum.DELIVERED, name: $t('label.delivered') }]"
                                        label-by="name" value-by="id" :closeOnSelect="true" :searchable="true"
                                        :clearOnClose="true" placeholder="--" search-placeholder="--"/>
                        </div>

                        <div class="col-12 sm:col-6 md:col-4 xl:col-3">
                            <label for="searchUserId" class="db-field-title">
                                {{ $t("label.customer") }}
                            </label>
                            <vue-select class="db-field-control f-b-custom-select" id="searchUserId"
                                        v-model="props.search.user_id" :options="customers" label-by="name"
                                        value-by="id" :closeOnSelect="true" :searchable="true" :clearOnClose="true"
                                        placeholder="--" search-placeholder="--"/>
                        </div>

                        <div class="col-12 sm:col-6 md:col-4 xl:col-3">
                            <label for="searchDate" class="db-field-title after:hidden">
                                {{ $t('label.date') }}
                            </label>
                            <DatePickerComponent id="searchDate" :hideInputIcon="true" @update:modelValue="handleDate"
                                                 inputStyle="filter" :range="true" v-model="modelValue"/>
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
                        <th class="db-table-head-th">{{ $t('label.customer') }}</th>
                        <th class="db-table-head-th">{{ $t('label.amount') }}</th>
                        <th class="db-table-head-th">{{ $t('label.date') }}</th>
                        <th class="db-table-head-th">{{ $t('label.status') }}</th>
                        <th v-if="permissionChecker('pos-orders_show') || permissionChecker('pos-orders_delete')"
                            class="db-table-head-th hidden-print">
                            {{ $t('label.action') }}
                        </th>
                    </tr>
                    </thead>
                    <tbody class="db-table-body" v-if="orders.length > 0">
                    <tr class="db-table-body-tr" v-for="order in orders" :key="order">
                        <td class="db-table-body-td">
                            {{ order.order_serial_no }}
                        </td>
                        <td class="db-table-body-td">
                            {{ order.customer.name }}
                        </td>
                        <td class="db-table-body-td">{{ order.total_amount_price }}</td>
                        <td class="db-table-body-td">{{ order.order_datetime }}</td>
                        <td class="db-table-body-td">
                            <span :class="orderStatusClassForTable(order.status)">
                                {{ enums.orderStatusEnumArray[order.status] }}
                            </span>
                        </td>
                        <td class="db-table-body-td hidden-print"
                            v-if="permissionChecker('pos-orders_show') || permissionChecker('pos-orders_delete')">
                            <div class="flex justify-start items-center sm:items-start sm:justify-start gap-1.5">
                                <SmIconViewComponent :link="'admin.pos.orders.show'" :id="order.id"
                                                     v-if="permissionChecker('pos-orders_show')"/>
                                <SmIconDeleteComponent @click="destroy(order.id)"
                                                       v-if="permissionChecker('pos-orders_delete')"/>
                            </div>
                        </td>
                    </tr>
                    </tbody>

                    <tbody v-else class="db-table-body">
                    <tr class="db-table-body-tr">
                        <td class="db-table-body-td" colspan="6">
                            <div class="p-4">
                                <img class="m-auto not-found max-w-[300px]" :src="setting.data_not_found"
                                     alt="Not Found">
                                <span class="block mt-3 text-center text-lg">{{ $t('message.no_data_found') }}</span>
                            </div>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>

            <div class="flex items-center justify-between border-t border-gray-200 bg-white px-4 py-6">
                <PaginationSMBox :pagination="pagination" :method="list"/>
                <div class="hidden sm:flex sm:flex-1 sm:items-center sm:justify-between">
                    <PaginationTextComponent :props="{ page: paginationPage }"/>
                    <PaginationBox :pagination="pagination" :method="list"/>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import LoadingComponent from "../../common/LoadingComponent.vue";
import {usePaper} from "../../../composables/paper.js";
import ExportComponent from "../components/buttons/export/ExportComponent.vue";
import PrintComponent from "../components/buttons/export/PrintComponent.vue";
import ExcelComponent from "../components/buttons/export/ExcelComponent.vue";
import TableLimitComponent from "../components/TableLimitComponent.vue";
import FilterComponent from "../components/buttons/collapse/FilterComponent.vue";
import {useSlide} from "../../../composables/slide.js";
import {useUserStore} from "../../../stores/user.js";
import {usePosOrderStore} from "../../../stores/posOrder.js";
import orderStatusEnum from "../../../enums/modules/orderStatusEnum.js";
import DatePickerComponent from "../components/DatePickerComponent.vue";
import appService from "../../../services/appService.js";
import PaginationSMBox from "../components/pagination/PaginationSMBox.vue";
import PaginationTextComponent from "../components/pagination/PaginationTextComponent.vue";
import PaginationBox from "../components/pagination/PaginationBox.vue";
import {useFrontendSettingStore} from "../../../stores/frontendSetting.js";
import SmIconViewComponent from "../components/buttons/SmIconViewComponent.vue";
import SmIconDeleteComponent from "../components/buttons/SmIconDeleteComponent.vue";
import VueSimpleAlert from "vue3-simple-alert";
import orderTypeEnum from "../../../enums/modules/orderTypeEnum.js";
import alertService from "../../../services/alertService.js";


export default {
    name: "PosOrderListComponent",
    components: {
        SmIconDeleteComponent,
        SmIconViewComponent,
        PaginationBox,
        PaginationTextComponent,
        PaginationSMBox,
        FilterComponent,
        ExcelComponent,
        PrintComponent,
        ExportComponent,
        LoadingComponent,
        TableLimitComponent,
        DatePickerComponent
    },
    setup() {
        const userStore            = useUserStore();
        const {handlePaper}        = usePaper();
        const {handleSlide}        = useSlide();
        const posOrderStore        = usePosOrderStore();
        const frontendSettingStore = useFrontendSettingStore();

        return {
            userStore,
            handlePaper,
            handleSlide,
            posOrderStore,
            frontendSettingStore,
        }
    },
    data() {
        return {
            loading: {
                isActive: false
            },
            enums: {
                orderStatusEnum: orderStatusEnum,
                orderStatusEnumArray: {
                    [orderStatusEnum.ACCEPT]: this.$t("label.accept"),
                    [orderStatusEnum.PREPARING]: this.$t("label.preparing"),
                    [orderStatusEnum.PREPARED]: this.$t("label.prepared"),
                    [orderStatusEnum.DELIVERED]: this.$t("label.delivered"),
                }
            },
            printObj: {
                id: "print",
                popTitle: this.$t("menu.pos_orders"),
            },
            props: {
                search: {
                    paginate: 1,
                    page: 1,
                    per_page: 10,
                    order_column: 'id',
                    order_by: "desc",
                    order_serial_no: "",
                    excepts: orderTypeEnum.DELIVERY + '|' + orderTypeEnum.TAKEAWAY,
                    user_id: null,
                    status: null,
                    from_date: "",
                    to_date: "",
                }
            },
            modelValue: null,
        }
    },
    mounted() {
        this.list();
        this.userStore.fetch();
    },
    computed: {
        setting: function () {
            return this.frontendSettingStore.lists;
        },
        orders: function () {
            return this.posOrderStore.lists;
        },
        customers: function () {
            return this.userStore.lists;
        },
        pagination: function () {
            return this.posOrderStore.pagination;
        },
        paginationPage: function () {
            return this.posOrderStore.page;
        }
    },
    methods: {
        permissionChecker(e) {
            return appService.permissionChecker(e);
        },
        orderStatusClassForTable: function (status) {
            return appService.orderStatusClassForTable(status);
        },
        search: function () {
            this.list();
        },
        handleDate: function (e) {
            if (e) {
                this.props.search.from_date = e[0];
                this.props.search.to_date   = e[1];
            } else {
                this.props.search.from_date = null;
                this.props.search.to_date   = null;
            }
        },
        clear: function () {
            this.props.search.paginate        = 1;
            this.props.search.page            = 1;
            this.props.search.order_serial_no = "";
            this.props.search.status          = null;
            this.props.search.user_id         = null;
            this.props.search.from_date       = "";
            this.props.search.to_date         = "";
            this.modelValue                   = null;
            this.list();
        },
        list: function (page = 1) {
            this.loading.isActive  = true;
            this.props.search.page = page;
            this.posOrderStore.fetch(this.props.search).then(res => {
                this.loading.isActive = false;
            }).catch((err) => {
                this.loading.isActive = false;
            });
        },
        destroy: function (id) {
            return new VueSimpleAlert.confirm(
                this.$t("message.delete_record"),
                this.$t("message.are_you_sure"),
                "warning",
                {
                    confirmButtonText: this.$t("button.yes_delete"),
                    cancelButtonText: this.$t("button.no_cancel"),
                    confirmButtonColor: "#1AB759",
                    cancelButtonColor: "#E93C3C"
                }
            ).then((res) => {
                try {
                    this.loading.isActive = true;
                    this.posOrderStore.destroy({id: id, search: this.props.search}).then((res) => {
                        this.loading.isActive = false;
                        alertService.successFlip(null, this.$t('menu.pos_orders'));
                    }).catch((err) => {
                        this.loading.isActive = false;
                        alertService.error(err.response.data.message);
                    })
                } catch (err) {
                    this.loading.isActive = false;
                    alertService.error(err.response.data.message);
                }
            }).catch((err) => {
                this.loading.isActive = false;
            })
        },
        xls: function () {
            this.loading.isActive = true;
            this.posOrderStore.export(this.props.search).then((res) => {
                this.loading.isActive = false;
                const blob            = new Blob([res.data], {
                    type: "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet",
                });
                const link            = document.createElement("a");
                link.href             = URL.createObjectURL(blob);
                link.download         = this.$t("menu.pos_orders");
                link.click();
                URL.revokeObjectURL(link.href);
            }).catch((err) => {
                this.loading.isActive = false;
                alertService.error(err.response.data.message);
            });
        },
    }
}
</script>
