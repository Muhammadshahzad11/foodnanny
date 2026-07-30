<template>
    <LoadingComponent :props="loading"/>
    <div class="col-12">
        <div class="db-card">
            <div class="db-card-header border-none">
                <h3 class="db-card-title">{{ $t('menu.refunds') }}</h3>
                <nav class="flex flex-wrap gap-2 mobile:justify-center">
                    <TableLimitComponent :method="list" :search="props.search" :page="paginationPage"/>
                    <FilterComponent @click.prevent="handleSlide('refunds')"/>
                    <div class="paper-group">
                        <ExportComponent @click.prevent="handlePaper"/>
                        <nav
                            class="paper-content absolute top-9 right-1/2 translate-x-1/2 z-30 min-w-[80px] w-fit rounded-md shadow-paper bg-white">
                            <PrintComponent :props="printObj"/>
                            <ExcelComponent :method="xls"/>
                        </nav>
                    </div>
                    <RefundCreateComponent :props="props" v-if="permissionChecker('refunds_create')"/>
                </nav>
            </div>
            <div class="table-filter-div" id="refunds">
                <form class="p-4 sm:p-5 mb-5" @submit.prevent="search">
                    <div class="row">
                        <div class="col-12 sm:col-6 md:col-4 xl:col-3">
                            <label for="searchOrderId"
                                   class="db-field-title after:hidden">{{ $t('label.order_id') }}</label>
                            <input id="searchOrderId" v-model="props.search.order_serial_no" type="text"
                                   class="db-field-control">
                        </div>
                        <div class="col-12 sm:col-6 md:col-4 xl:col-3">
                            <label for="refund_amount" class="db-field-title after:hidden">{{
                                    $t('label.refund_amount')
                                }}</label>
                            <input id="refund_amount" v-on:keypress="floatNumber($event)"
                                   v-model="props.search.refund_amount"
                                   type="text" class="db-field-control">
                        </div>

                        <div class="col-12 sm:col-6 md:col-4 xl:col-3">
                            <label for="deduction_amount"
                                   class="db-field-title after:hidden">{{ $t('label.deduction_amount') }}</label>
                            <input id="deduction_amount" v-on:keypress="floatNumber($event)"
                                   v-model="props.search.deduction_amount"
                                   type="text" class="db-field-control">
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
                        <th class="db-table-head-th">{{ $t('label.refund_amount') }}</th>
                        <th class="db-table-head-th">{{ $t('label.deduction_amount') }}</th>
                        <th class="db-table-head-th">{{ $t('label.date') }}</th>
                        <th class="db-table-head-th">{{ $t('label.responsible') }}</th>
                        <th class="db-table-head-th hidden-print"
                            v-if="permissionChecker('refunds_show') || permissionChecker('refunds_delete')">
                            {{ $t('label.action') }}
                        </th>
                    </tr>
                    </thead>
                    <tbody class="db-table-body" v-if="refunds.length > 0">
                    <tr class="db-table-body-tr" v-for="refund in refunds" :key="refund">
                        <td class="db-table-body-td">
                            {{ refund.order_serial_no }}
                        </td>
                        <td class="db-table-body-td">
                            {{ refund.refund_amount }}
                        </td>
                        <td class="db-table-body-td">
                            {{ refund.deduction_amount }}
                        </td>
                        <td class="db-table-body-td">{{ refund.datetime }}</td>
                        <td class="db-table-body-td">{{ enums.modelTypeEnumArray[refund.responsible] }}</td>
                        <td class="db-table-body-td hidden-print"
                            v-if="permissionChecker('refunds_show') || permissionChecker('refunds_delete')">
                            <div class="flex justify-start items-center sm:items-start sm:justify-start gap-1.5">
                                <button class="db-table-action view" @click="showModal(refund)"
                                        v-if="permissionChecker('refunds_show')">
                                    <i class="lab lab-line-eye"></i>
                                    <span class="db-tooltip">{{ $t('button.view') }}</span>
                                </button>
                                <SmIconDeleteComponent @click="destroy(refund.id)"
                                                       v-if="permissionChecker('refunds_delete')"/>
                            </div>
                        </td>
                    </tr>
                    </tbody>
                    <tbody class="db-table-body" v-else>
                    <tr class="db-table-body-tr">
                        <td class="db-table-body-td" colspan="7">
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

    <div id="refund" class="modal">
        <div class="modal-dialog max-w-xl">
            <div class="modal-header">
                <h3 class="modal-title">{{ $t("label.refund") }}</h3>
                <button class="modal-close lab-line-close font-bold text-base text-slate-400 hover:text-red-500"
                        @click="hideModal"></button>
            </div>
            <div class="modal-body">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-5">
                    <div class="text-heading">
                        <h4 class="text-sm font-medium mb-2 leading-6 text-heading">{{ $t("label.order_info") }}</h4>
                        <p class="text-xs leading-6">
                            <span class="font-medium">{{ $t("label.order_id") }}:&nbsp;</span>
                            <span class="font-light">#{{ modalInfo.order_id }}</span>
                        </p>
                        <p class="text-xs leading-6">
                            <span class="font-medium">{{ $t("label.order_date") }}:&nbsp;</span>
                            <span class="font-light">{{ modalInfo.order_date }}</span>
                        </p>
                        <p class="text-xs leading-6">
                            <span class="font-medium">{{ $t("label.restaurant") }}:&nbsp;</span>
                            <span class="font-light">{{ modalInfo.restaurant_name }}</span>
                        </p>
                        <p class="text-xs leading-6">
                            <span class="font-medium">{{ $t("label.order_amount") }}:&nbsp;</span>
                            <span class="font-light"> {{ modalInfo.order_amount }}</span>
                        </p>
                        <p class="text-xs leading-6">
                            <span class="font-medium">{{ $t("label.deducted_amount") }}:&nbsp;</span>
                            <span class="font-light"> {{ modalInfo.deducted_amount }}</span>
                        </p>
                    </div>
                    <div class="text-heading">
                        <h4 class="text-sm font-medium mb-2 leading-6">{{ $t("label.refund_info") }}</h4>
                        <p class="text-xs leading-6">
                            <span class="font-medium">{{ $t("label.refund_date") }}:&nbsp;</span>
                            <span class="font-light">{{ modalInfo.refund_date }}</span>
                        </p>
                        <p class="text-xs leading-6">
                            <span class="font-medium">{{ $t("label.refund_amount") }}:&nbsp;</span>
                            <span class="font-light">{{ modalInfo.refund_amount }}</span>
                        </p>
                        <p class="text-xs leading-6">
                            <span class="font-medium">{{ $t("label.refund_mode") }}:&nbsp;</span>
                            <span class="font-light">{{ $t("label.credit_wallet") }}</span>
                        </p>
                        <p class="text-xs leading-6">
                            <span class="font-medium">{{ $t("label.refund_responsible") }}:&nbsp;</span>
                            <span class="font-light">{{ modalInfo.deducted_type }}</span>
                        </p>
                    </div>

                    <div class="bg-[#F7F7FC] p-3 rounded-lg text-heading">
                        <h4 class="text-sm font-medium mb-2 leading-6 text-heading">{{
                                $t("label.refund_deducted_from")
                            }}</h4>
                        <p class="text-xs leading-6">
                            <span class="font-medium">{{ $t("label.name") }}:&nbsp;</span>
                            <span class="font-light">{{ modalInfo.deducted_name }}</span>
                        </p>
                        <p class="text-xs leading-6">
                            <span class="font-medium">{{ $t("label.phone") }}:&nbsp;</span>
                            <span class="font-light">{{ modalInfo.deducted_phone }}</span>
                        </p>
                        <p class="text-xs leading-6">
                            <span class="font-medium">{{ $t("label.email") }}:&nbsp;</span>
                            <span class="font-light">{{ modalInfo.deducted_email }}</span>
                        </p>
                    </div>

                    <div class="bg-[#F7F7FC] p-3 rounded-lg text-heading">
                        <h4 class="text-sm font-medium mb-2 leading-6 text-heading">{{ $t("label.refunded_to") }}</h4>
                        <p class="text-xs leading-6">
                            <span class="font-medium">{{ $t("label.name") }}:&nbsp;</span>
                            <span class="font-light">{{
                                    modalInfo.refunded_to_name === 'N/A' ? $t('label.company') : modalInfo.refunded_to_name
                                }}</span>
                        </p>
                        <p class="text-xs leading-6">
                            <span class="font-medium">{{ $t("label.phone") }}:&nbsp;</span>
                            <span class="font-light">{{ modalInfo.refunded_to_phone }}</span>
                        </p>
                        <p class="text-xs leading-6">
                            <span class="font-medium">{{ $t("label.email") }}:&nbsp;</span>
                            <span class="font-light"> {{ modalInfo.refunded_to_email }}</span>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import LoadingComponent from "../../common/LoadingComponent.vue";
import RefundCreateComponent from "./RefundCreateComponent.vue";
import alertService from "../../../services/alertService.js";
import PaginationTextComponent from "../components/pagination/PaginationTextComponent.vue";
import PaginationBox from "../components/pagination/PaginationBox.vue";
import PaginationSMBox from "../components/pagination/PaginationSMBox.vue";
import appService from "../../../services/appService.js";
import TableLimitComponent from "../components/TableLimitComponent.vue";
import SmIconDeleteComponent from "../components/buttons/SmIconDeleteComponent.vue";
import FilterComponent from "../components/buttons/collapse/FilterComponent.vue";
import ExportComponent from "../components/buttons/export/ExportComponent.vue";
import PrintComponent from "../components/buttons/export/PrintComponent.vue";
import ExcelComponent from "../components/buttons/export/ExcelComponent.vue";
import {usePaper} from "../../../composables/paper.js";
import {useSlide} from "../../../composables/slide.js";
import modelTypeEnum from "../../../enums/modules/modelTypeEnum.js";
import {useFrontendSettingStore} from "../../../stores/frontendSetting.js";
import VueSimpleAlert from "vue3-simple-alert";
import DatePickerComponent from "../components/DatePickerComponent.vue";
import {useRefundStore} from "../../../stores/refund.js";
import {useModal} from "../../../composables/modal.js";
import SmIconViewComponent from "../components/buttons/SmIconViewComponent.vue";

export default {
    name: "RefundListComponent",
    components: {
        TableLimitComponent,
        PaginationSMBox,
        PaginationBox,
        PaginationTextComponent,
        RefundCreateComponent,
        LoadingComponent,
        SmIconDeleteComponent,
        FilterComponent,
        ExportComponent,
        PrintComponent,
        DatePickerComponent,
        ExcelComponent,
        SmIconViewComponent
    },
    setup() {
        const {openModal, closeModal} = useModal();
        const refundStore             = useRefundStore();
        const frontendSettingStore    = useFrontendSettingStore();
        return {
            openModal,
            closeModal,
            refundStore,
            frontendSettingStore
        }
    },
    data() {
        return {
            loading: {
                isActive: false
            },
            enums: {
                modelTypeEnum: modelTypeEnum,
                modelTypeEnumArray: {
                    [modelTypeEnum.RESTAURANT]: this.$t("label.restaurant"),
                    [modelTypeEnum.DELIVERY_BOY]: this.$t("label.delivery_boy")
                }
            },
            printLoading: true,
            printObj: {
                id: "print",
                popTitle: this.$t("menu.refunds")
            },
            props: {
                form: {
                    order_serial_no: "",
                    refund_amount: "",
                    deduction_amount: "",
                    responsible: null
                },
                search: {
                    paginate: 1,
                    page: 1,
                    per_page: 10,
                    order_column: 'id',
                    order_by: "desc",
                    order_serial_no: "",
                    refund_amount: "",
                    deduction_amount: ""
                }
            },
            modalInfo: {},
            handlePaper: usePaper().handlePaper,
            handleSlide: useSlide().handleSlide
        };
    },
    mounted() {
        this.list();
    },
    computed: {
        setting: function () {
            return this.frontendSettingStore.lists;
        },
        refunds: function () {
            return this.refundStore.lists;
        },
        pagination: function () {
            return this.refundStore.pagination;
        },
        paginationPage: function () {
            return this.refundStore.page;
        }
    },
    methods: {
        permissionChecker(e) {
            return appService.permissionChecker(e);
        },
        floatNumber(e) {
            return appService.floatNumber(e);
        },
        search: function () {
            this.list();
        },
        showModal: function (refund) {
            this.modalInfo = refund.info;
            this.openModal("refund");
        },
        hideModal: function () {
            this.modalInfo = {};
            this.closeModal('refund');
        },
        clear: function () {
            this.props.search.paginate         = 1;
            this.props.search.page             = 1;
            this.props.search.order_by         = "desc";
            this.props.search.order_serial_no  = "";
            this.props.search.refund_amount    = "";
            this.props.search.deduction_amount = "";
            this.props.search.responsible      = null;
            this.list();
        },
        list: function (page = 1) {
            this.loading.isActive  = true;
            this.props.search.page = page;
            this.refundStore.fetch(this.props.search).then((res) => {
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
                    this.refundStore.destroy({id: id, search: this.props.search}).then((res) => {
                        this.loading.isActive = false;
                        alertService.successFlip(null, this.$t("label.refund"));
                    }).catch((err) => {
                        this.loading.isActive = false;
                        alertService.error(err.response.data.message);
                    });
                } catch (err) {
                    this.loading.isActive = false;
                    alertService.error(err.response.data.message);
                }
            }).catch((err) => {
                this.loading.isActive = false;
            });
        },
        xls: function () {
            this.loading.isActive = true;
            this.refundStore.export(this.props.search).then((res) => {
                this.loading.isActive = false;
                const blob            = new Blob([res.data], {
                    type: "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet"
                });
                const link            = document.createElement("a");
                link.href             = URL.createObjectURL(blob);
                link.download         = this.$t("label.refund");
                link.click();
                URL.revokeObjectURL(link.href);
            }).catch((err) => {
                this.loading.isActive = false;
                alertService.error(err.response.data.message);
            });
        }
    }
};
</script>
