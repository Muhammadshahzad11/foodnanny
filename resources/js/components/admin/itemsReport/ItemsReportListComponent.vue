<template>
    <LoadingComponent :props="loading" />
    <div class="col-12">
        <div class="db-card db-tab-div active">
            <div class="db-card-header border-none">
                <h3 class="db-card-title">{{ $t('menu.items_report') }}</h3>
                <nav class="flex flex-wrap gap-2 mobile:justify-center">
                    <TableLimitComponent :method="list" :search="props.search" :page="paginationPage" />
                    <FilterComponent @click.prevent="handleSlide('items-reports')" />
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
            <div class="table-filter-div" id="items-reports">
                <form class="p-4 sm:p-5 mb-5" @submit.prevent="search">
                    <div class="row">
                        <div class="col-12 sm:col-6 md:col-4 xl:col-3">
                            <label for="item_category_id" class="db-field-title">{{$t("label.category")}}</label>
                            <vue-select class="db-field-control f-b-custom-select" id="item_category_id"
                                v-model="props.search.item_category_id" :options="itemCategories" label-by="name"
                                value-by="id" :closeOnSelect="true" :searchable="true" :clearOnClose="true" placeholder="--"
                                search-placeholder="--" />
                        </div>

                        <div class="col-12 sm:col-6 md:col-4 xl:col-3">
                            <label for="item_type" class="db-field-title after:hidden">{{$t('label.type')}}</label>
                            <vue-select class="db-field-control f-b-custom-select" id="item_type"
                                v-model="props.search.item_type" :options="[
                                    { id: enums.itemTypeEnum.VEG, name: $t('label.veg') },
                                    { id: enums.itemTypeEnum.NON_VEG, name: $t('label.non_veg') }
                                ]" label-by="name" value-by="id" :closeOnSelect="true" :searchable="true"
                                :clearOnClose="true" placeholder="--" search-placeholder="--" />
                        </div>

                        <div class="col-12 sm:col-6 md:col-4 xl:col-3">
                            <label for="name" class="db-field-title">{{ $t("label.name")}}</label>
                            <vue-select class="db-field-control f-b-custom-select" id="name" v-model="props.search.name"
                                :options="items" label-by="name" value-by="name" :closeOnSelect="true" :searchable="true"
                                :clearOnClose="true" placeholder="--" search-placeholder="--" />
                        </div>

                        <div class="col-12 sm:col-6 md:col-4 xl:col-3">
                            <label for="searchStartDate" class="db-field-title after:hidden">{{$t('label.date')}}</label>
                            <DatePickerComponent @update:modelValue="handleDate" inputStyle="filter" :range="range" hideInputIcon
                                v-model="modelValue" />
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
                            <th class="db-table-head-th">{{ $t('label.name') }}</th>
                            <th class="db-table-head-th">{{ $t('label.category') }}</th>
                            <th class="db-table-head-th">{{ $t('label.type') }}</th>
                            <th class="db-table-head-th">{{ $t('label.quantity') }}</th>
                        </tr>
                    </thead>
                    <tbody class="db-table-body" v-if="itemsReports.length > 0">
                        <tr class="db-table-body-tr" v-for="itemsReport in itemsReports" :key="itemsReport">
                            <td class="db-table-body-td">{{ textShortener(itemsReport.name)  }}</td>
                            <td class="db-table-body-td">{{ itemsReport.category_name }}</td>
                            <td class="db-table-body-td">
                                <span v-if="itemsReport.item_type !== null"> {{ enums.itemTypeEnumArray[itemsReport.item_type] }}</span>
                                <span v-else>N/A</span>
                            </td>
                            <td class="db-table-body-td">{{ itemsReport.order }}</td>
                        </tr>
                    </tbody>
                    <tfoot class="db-table-foot border-t" v-if="itemsReports.length > 0">
                        <tr>
                        <td class="db-table-body-td">{{ $t('label.total') }}</td>
                        <td></td>
                        <td></td>
                        <td class="db-table-body-td"> {{ subTotal(itemsReports) }}</td>
                       </tr>
                    </tfoot>
                    <tbody class="db-table-body" v-else>
                        <tr class="db-table-body-tr">
                            <td class="db-table-body-td" colspan="4">
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
import itemTypeEnum from "../../../enums/modules/itemTypeEnum.js";
import paymentTypeEnum from "../../../enums/modules/paymentTypeEnum.js";
import TableLimitComponent from "../components/TableLimitComponent.vue";
import FilterComponent from "../components/buttons/collapse/FilterComponent.vue";
import ExportComponent from "../components/buttons/export/ExportComponent.vue";
import print from 'vue3-print-nb';
import PrintComponent from "../components/buttons/export/PrintComponent.vue";
import ExcelComponent from "../components/buttons/export/ExcelComponent.vue";
import { usePaper } from "../../../composables/paper.js";
import { useSlide } from "../../../composables/slide.js";
import DatePickerComponent from "../components/DatePickerComponent.vue";
import { useFrontendSettingStore } from "../../../stores/frontendSetting.js";
import {useItemStore} from "../../../stores/item.js";
import {useItemCategoryStore} from "../../../stores/itemCategory.js";
import {useItemsReportStore} from "../../../stores/itemsReport.js";

export default {
    name: "ItemsReportListComponent",
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
        const itemCategoryStore    = useItemCategoryStore();
        const itemStore            = useItemStore();
        const frontendSettingStore = useFrontendSettingStore();
        const itemsReportStore = useItemsReportStore();

        return {
            frontendSettingStore,
            itemStore,
            itemCategoryStore,
            itemsReportStore
        }
    },
    data() {
        return {
            loading: {
                isActive: false
            },
            enums: {
                itemTypeEnum: itemTypeEnum,
                paymentTypeEnum: paymentTypeEnum,
                itemTypeEnumArray: {
                    [itemTypeEnum.VEG]: this.$t("label.veg"),
                    [itemTypeEnum.NON_VEG]: this.$t("label.non_veg")
                }
            },
            printLoading: true,
            printObj: {
                id: "print",
                popTitle: this.$t('menu.items_report')
            },
            props: {
                form: {
                    date: null
                },
                search: {
                    paginate: 1,
                    page: 1,
                    per_page: 10,
                    order_column: 'id',
                    name: null,
                    item_category_id: null,
                    item_type: null,
                    from_date: "",
                    to_date: ""
                }
            },
            modelValue: null,
            range: true,
            handlePaper: usePaper().handlePaper,
            handleSlide: useSlide().handleSlide
        }
    },
    mounted() {
        this.list();
        this.loading.isActive = true;
        this.props.search.page = 1;
        this.itemStore.fetch(this.props.search).then(res => {
            this.loading.isActive = false;
        }).catch((err) => {
            this.loading.isActive = false;
        });
        this.itemCategoryStore.fetch(this.props.search).then(res => {
            this.loading.isActive = false;
        }).catch((err) => {
            this.loading.isActive = false;
        });
    },
    computed: {
        setting: function () {
            return this.frontendSettingStore.lists;
        },
        itemsReports: function () {
            return this.itemsReportStore.lists;
        },
        items: function () {
            return this.itemStore.lists;
        },
        itemCategories: function () {
            return this.itemCategoryStore.lists;
        },
        pagination: function () {
            return this.itemsReportStore.pagination;
        },
        paginationPage: function () {
            return this.itemsReportStore.page;
        }
    },
    methods: {
        textShortener: function (text, number = 30) {
            return appService.textShortener(text, number);
        },
        search: function () {
            this.list();
        },
        handleDate: function (e) {
            if (e) {
                this.props.search.from_date = e[0];
                this.props.search.to_date = e[1];
            } else {
                this.props.form.date = null;
                this.props.search.from_date = null;
                this.props.search.to_date = null;
            }
        },
        subTotal(items) {
            return items.reduce((acc, ele) => {
                return acc + parseInt(ele.order);
            }, 0);
        },
        clear: function () {
            this.props.search.paginate = 1;
            this.props.search.page = 1;
            this.props.search.name = null;
            this.props.search.item_category_id = null;
            this.props.search.item_type = null;
            this.props.search.from_date = "";
            this.props.search.to_date = "";
            this.modelValue = null;
            this.list();
        },
        list: function (page = 1) {
            this.loading.isActive = true;
            this.props.search.page = page;
            this.itemsReportStore.fetch(this.props.search).then(res => {
                this.loading.isActive = false;
            }).catch((err) => {
                this.loading.isActive = false;
            });
        },
        xls: function () {
            this.loading.isActive = true;
            this.itemsReportStore.export(this.props.search).then(res => {
                this.loading.isActive = false;
                const blob = new Blob([res.data], { type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' });
                const link = document.createElement('a');
                link.href = URL.createObjectURL(blob);
                link.download = this.$t("menu.items_report");
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
