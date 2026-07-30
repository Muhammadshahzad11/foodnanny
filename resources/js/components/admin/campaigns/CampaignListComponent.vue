<template>
    <LoadingComponent :props="loading" />
    <div class="col-12">
        <div class="db-card db-tab-div active">
            <div class="db-card-header border-none">
                <h3 class="db-card-title">{{ $t("menu.campaigns") }}</h3>
                <nav class="flex flex-wrap gap-2 mobile:justify-center">
                    <TableLimitComponent :method="list" :search="props.search" :page="paginationPage" />
                    <FilterComponent @click.prevent="handleSlide('campaign-filter')" />
                    <div class="paper-group">
                        <ExportComponent @click.prevent="handlePaper" />
                        <nav class="paper-content absolute top-9 right-1/2 translate-x-1/2 z-30 min-w-[80px] w-fit rounded-md shadow-paper bg-white">
                            <PrintComponent :props="printObj" />
                            <ExcelComponent :method="xls" />
                        </nav>
                    </div>
                    <CampaignCreateComponent :props="props" v-if="permissionChecker('campaigns_create')" />
                </nav>
            </div>

            <div class="table-filter-div" id="campaign-filter">
                <form class="p-4 sm:p-5 mb-5" @submit.prevent="search">
                    <div class="row">
                        <div class="col-12 sm:col-6 md:col-4 xl:col-3">
                            <label for="searchTitle" class="db-field-title after:hidden">{{$t("label.title") }}</label>
                            <input id="searchTitle" v-model="props.search.title" type="text" class="db-field-control" />
                        </div>
                        <div class="col-12 sm:col-6 md:col-4 xl:col-3">
                            <label for="searchStartDate" class="db-field-title after:hidden"> {{ $t('label.date') }} </label>
                            <DatePickerComponent @update:modelValue="handleDate" inputStyle="filter" :range="true" v-model="modelValue"  :hideInputIcon="true" />
                        </div>
                        <div class="col-12 sm:col-6 md:col-4 xl:col-3">
                            <label for="searchTime" class="db-field-title after:hidden">{{ $t('label.time') }} </label>
                            <TimePickerComponent @update:modelValue="handleTime" inputStyle="filter" :range="true" v-model="timeValue" :hideInputIcon="true" />
                        </div>
                        <div class="col-12 sm:col-6 md:col-4 xl:col-3">
                            <label for="searchStatus" class="db-field-title after:hidden">{{$t("label.status")}}</label>
                            <vue-select class="db-field-control f-b-custom-select" id="searchStatus"
                                v-model="props.search.status" :options="[
                                    { id: enums.statusEnum.ACTIVE, name: $t('label.active') },
                                    { id: enums.statusEnum.INACTIVE, name: $t('label.inactive') },
                                ]" label-by="name" value-by="id" :closeOnSelect="true" :searchable="true"
                                :clearOnClose="true" placeholder="--" search-placeholder="--" />
                        </div>
                        <div class="col-12 sm:col-6 md:col-4 xl:col-3">
                            <label for="searchType" class="db-field-title after:hidden">{{$t("label.type")}}</label>
                            <vue-select class="db-field-control f-b-custom-select" id="searchType"
                                v-model="props.search.type" :options="[
                                    { id: enums.campaignTypeEnum.FREE, name: $t('label.free') },
                                    { id: enums.campaignTypeEnum.PAID, name: $t('label.paid') },
                                ]" label-by="name" value-by="id" :closeOnSelect="true" :searchable="true"
                                :clearOnClose="true" placeholder="--" search-placeholder="--" />
                        </div>
                        <div class="col-12 sm:col-6 md:col-4 xl:col-3"
                            v-if="props.search.type === enums.campaignTypeEnum.PAID">
                            <label for="searchAmount" class="db-field-title after:hidden">{{$t("label.amount")}}</label>
                            <input id="searchAmount" v-model="props.search.amount" v-on:keypress="floatNumber($event)" type="text" class="db-field-control" />
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
                            <th class="db-table-head-th">{{ $t("label.title") }}</th>
                            <th class="db-table-head-th">{{ $t("label.date") }}</th>
                            <th class="db-table-head-th">{{ $t("label.time") }}</th>
                            <th class="db-table-head-th">{{ $t("label.type") }}</th>
                            <th class="db-table-head-th">{{ $t("label.amount") }}</th>
                            <th class="db-table-head-th">{{ $t("label.status") }}</th>
                            <th class="db-table-head-th hidden-print"
                                v-if="permissionChecker('campaigns_show') || permissionChecker('campaigns_edit') || permissionChecker('campaigns_delete')">
                                {{ $t("label.action") }}
                            </th>
                        </tr>
                    </thead>
                    <tbody class="db-table-body" v-if="campaigns.length > 0">
                        <tr class="db-table-body-tr" v-for="campaign in campaigns" :key="campaign">
                            <td class="db-table-body-td"> {{ textShortener(campaign.title) }} </td>
                            <td class="db-table-body-td">{{ campaign.convert_date }}</td>
                            <td class="db-table-body-td">{{ campaign.convert_time }}</td>
                            <td class="db-table-body-td">{{ enums.campaignTypeEnumArray[campaign.type] }}</td>
                            <td class="db-table-body-td">{{ campaign.flat_amount }}</td>
                            <td class="db-table-body-td">
                                <span :class="statusClass(campaign.status)">
                                    {{ enums.statusEnumArray[campaign.status] }}
                                </span>
                            </td>
                            <td class="db-table-body-td hidden-print"
                                v-if="permissionChecker('campaigns_show') || permissionChecker('campaigns_edit') || permissionChecker('campaigns_delete')">
                                <div class="flex justify-start items-center sm:items-start sm:justify-start gap-1.5">
                                    <SmIconViewComponent :link="'admin.campaign.show'" :id="campaign.id" v-if="permissionChecker('campaigns_show')" />
                                    <SmIconSidebarModalEditComponent @click="edit(campaign)" v-if="permissionChecker('campaigns_edit')" />
                                    <SmIconDeleteComponent @click="destroy(campaign.id)" v-if="permissionChecker('campaigns_delete')" />
                                </div>
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
import CampaignCreateComponent from "./CampaignCreateComponent.vue";
import alertService from "../../../services/alertService.js";
import PaginationTextComponent from "../components/pagination/PaginationTextComponent.vue";
import PaginationBox from "../components/pagination/PaginationBox.vue";
import PaginationSMBox from "../components/pagination/PaginationSMBox.vue";
import appService from "../../../services/appService.js";
import statusEnum from "../../../enums/modules/statusEnum.js";
import campaignTypeEnum from "../../../enums/modules/campaignTypeEnum.js";
import TableLimitComponent from "../components/TableLimitComponent.vue";
import SmIconDeleteComponent from "../components/buttons/SmIconDeleteComponent.vue";
import SmIconSidebarModalEditComponent from "../components/buttons/SmIconSidebarModalEditComponent.vue";
import FilterComponent from "../components/buttons/collapse/FilterComponent.vue";
import ExportComponent from "../components/buttons/export/ExportComponent.vue";
import print from "vue3-print-nb";
import PrintComponent from "../components/buttons/export/PrintComponent.vue";
import ExcelComponent from "../components/buttons/export/ExcelComponent.vue";
import SmIconViewComponent from "../components/buttons/SmIconViewComponent.vue";
import { usePaper } from "../../../composables/paper.js";
import { useSlide } from "../../../composables/slide.js";
import DatePickerComponent from "../components/DatePickerComponent.vue";
import TimePickerComponent from "../components/TimePickerComponent.vue";
import { useCampaignStore } from "../../../stores/campaign.js";
import { useFrontendSettingStore } from "../../../stores/frontendSetting.js";
import VueSimpleAlert from "vue3-simple-alert";

export default {
    name: "CampaignListComponent",
    components: {
        TableLimitComponent,
        PaginationSMBox,
        PaginationBox,
        PaginationTextComponent,
        CampaignCreateComponent,
        LoadingComponent,
        SmIconDeleteComponent,
        SmIconSidebarModalEditComponent,
        ExportComponent,
        FilterComponent,
        PrintComponent,
        ExcelComponent,
        DatePickerComponent,
        SmIconViewComponent,
        TimePickerComponent
    },
    setup() {
        const frontendSettingStore = useFrontendSettingStore();
        const campaignStore = useCampaignStore();
        return {
            campaignStore,
            frontendSettingStore
        }
    },
    data() {
        return {
            loading: {
                isActive: false
            },
            enums: {
                statusEnum: statusEnum,
                campaignTypeEnum: campaignTypeEnum,
                statusEnumArray: {
                    [statusEnum.ACTIVE]: this.$t("label.active"),
                    [statusEnum.INACTIVE]: this.$t("label.inactive")
                },
                campaignTypeEnumArray: {
                    [campaignTypeEnum.FREE]: this.$t("label.free"),
                    [campaignTypeEnum.PAID]: this.$t("label.paid")
                }
            },
            printLoading: true,
            printObj: {
                id: "print",
                popTitle: this.$t("menu.campaigns")
            },
            props: {
                form: {
                    title: "",
                    amount: "",
                    start_date: "",
                    end_date: "",
                    start_time: "",
                    end_time: "",
                    type: campaignTypeEnum.FREE,
                    status: statusEnum.ACTIVE,
                    description: ""
                },
                search: {
                    paginate: 1,
                    page: 1,
                    per_page: 10,
                    order_column: "id",
                    order_type: "desc",
                    title: "",
                    amount: "",
                    start_date: "",
                    end_date: "",
                    start_time: "",
                    end_time: "",
                    status: null,
                    type: null
                },
                startTime: '',
                endTime: ''
            },
            modelValue: null,
            timeValue: null,
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
        campaigns: function () {
            return this.campaignStore.lists;
        },
        pagination: function () {
            return this.campaignStore.pagination;
        },
        paginationPage: function () {
            return this.campaignStore.page;
        }
    },
    methods: {
        permissionChecker(e) {
            return appService.permissionChecker(e);
        },
        floatNumber(e) {
            return appService.floatNumber(e);
        },
        statusClass: function (status) {
            return appService.statusClass(status);
        },
        textShortener: function (text, number = 30) {
            return appService.textShortener(text, number);
        },
        search: function () {
            this.list();
        },
        handleDate: function (e) {
            if (e) {
                this.props.search.start_date = e[0];
                this.props.search.end_date = e[1];
            } else {
                this.props.search.start_date = null;
                this.props.search.end_date = null;
            }
        },
        handleTime: function (e) {
            if (e) {
                this.props.search.start_time = e[0].hours + ':' + e[0].minutes + ':' + e[0].seconds;
                this.props.search.end_time = e[1].hours + ':' + e[1].minutes + ':' + e[1].seconds;
            } else {
                this.props.search.start_time = '';
                this.props.search.end_time = '';
                this.timeValue = null;
            }
        },
        clear: function () {
            this.props.search.paginate = 1;
            this.props.search.page = 1;
            this.props.search.title = "";
            this.props.search.amount = "";
            this.props.search.status = null;
            this.props.search.type = null;
            this.props.search.start_date = "";
            this.props.search.end_date = "";
            this.props.search.start_time = "";
            this.props.search.end_time = "";
            this.modelValue = null;
            this.timeValue = null;
            this.list();
        },
        list: function (page = 1) {
            this.loading.isActive = true;
            this.props.search.page = page;
            this.campaignStore.fetch(this.props.search).then((res) => {
                this.loading.isActive = false;
            }).catch((err) => {
                this.loading.isActive = false;
            });
        },
        edit: function (campaign) {
            appService.sideDrawerShow();
            this.loading.isActive = true;
            this.campaignStore.edit(campaign.id).then((res) => {
                    this.loading.isActive = false;
                    this.props.errors = {};
                    this.props.startTime = this.parseTime(campaign.start_time);
                    this.props.endTime = this.parseTime(campaign.end_time);
                    this.props.form = {
                        title: campaign.title,
                        amount: campaign.flat_amount,
                        start_date: campaign.start_date,
                        end_date: campaign.end_date,
                        start_time: campaign.start_time,
                        end_time: campaign.end_time,
                        type: campaign.type,
                        status: campaign.status,
                        description: campaign.description
                    };
                }).catch((err) => {
                    alertService.error(err.response.data.message);
                });
        },
        parseTime(time) {
            if (time === "") {
                return "";
            } else {
                const [hours, minutes, seconds] = time.split(':').map(Number);
                return { hours, minutes, seconds };
            }
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
                        this.campaignStore.destroy({ id: id, search: this.props.search }).then((res) => {
                            this.loading.isActive = false;
                            alertService.successFlip(null, this.$t("menu.campaigns"));
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
            this.campaignStore.export(this.props.search).then((res) => {
                this.loading.isActive = false;
                const blob = new Blob([res.data], {
                    type: "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet",
                });
                const link = document.createElement("a");
                link.href = URL.createObjectURL(blob);
                link.download = this.$t("menu.campaigns");
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
