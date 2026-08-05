<template>
    <LoadingComponent :props="loading"/>
    <div class="db-card">
        <div class="db-card-header border-none">
            <h3 class="db-card-title">{{ $t("menu.offers") }}</h3>
            <nav class="flex flex-wrap gap-2 mobile:justify-center">
                <TableLimitComponent :method="list" :search="props.search" :page="paginationPage"/>
                <FilterComponent @click.prevent="handleSlide('offer-filter')"/>
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
        <div class="table-filter-div" id="offer-filter">
            <form class="p-4 sm:p-5 mb-5" @submit.prevent="search">
                <div class="row">
                    <div class="col-12 sm:col-6 md:col-4 xl:col-3">
                        <label for="searchTitle" class="db-field-title after:hidden">{{
                                $t("label.title")
                            }}</label>
                        <input id="searchTitle" v-model="props.search.title" type="text" class="db-field-control"/>
                    </div>

                    <div class="col-12 sm:col-6 md:col-4 xl:col-3">
                        <label for="searchAmount" class="db-field-title after:hidden">{{
                                $t("label.discount")
                            }}</label>
                        <input id="searchAmount" v-model="props.search.amount" v-on:keypress="floatNumber($event)"
                               type="text" class="db-field-control"/>
                    </div>

                    <div class="col-12 sm:col-6 md:col-4 xl:col-3">
                        <label for="searchStartDate" class="db-field-title after:hidden">
                            {{ $t('label.date') }}
                        </label>
                        <DatePickerComponent @update:modelValue="handleDate" inputStyle="filter" :range="true"
                                             :hideInputIcon="true"
                                             v-model="modelValue"/>
                    </div>

                    <div class="col-12 sm:col-6 md:col-4 xl:col-3">
                        <label for="searchStartDate" class="db-field-title after:hidden">
                            {{ $t('label.time') }}
                        </label>
                        <Datepicker @update:modelValue="handleTime" hideInputIcon v-model="timeValue"
                                    :time-picker="true" :time-picker-only="true" range :is24="false" utc="false"
                                    menuClassName="foodnanny-menu" :teleport="true"
                                    inputClassName="foodnanny-input filter">
                        </Datepicker>
                    </div>

                    <div class="col-12 sm:col-6 md:col-4 xl:col-3">
                        <label for="searchType" class="db-field-title after:hidden">{{
                                $t("label.type")
                            }}</label>
                        <vue-select class="db-field-control f-b-custom-select" id="searchType"
                                    v-model="props.search.type" :options="[
                                { id: enums.offerTypeEnum.REGULAR, name: $t('label.regular') },
                                { id: enums.offerTypeEnum.PREMIER, name: $t('label.premier') },
                            ]" label-by="name" value-by="id" :closeOnSelect="true" :searchable="true"
                                    :clearOnClose="true" placeholder="--"
                                    search-placeholder="--"/>
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
            <table class="db-table stripe" id="print-offer">
                <thead class="db-table-head">
                <tr class="db-table-head-tr">
                    <th class="db-table-head-th">{{ $t("label.title") }}</th>
                    <th class="db-table-head-th">{{ $t("label.discount") }}</th>
                    <th class="db-table-head-th">{{ $t("label.date") }}</th>
                    <th class="db-table-head-th">{{ $t("label.time") }}</th>
                    <th class="db-table-head-th">{{ $t("label.status") }}</th>
                    <th class="db-table-head-th hidden-print" v-if="permissionChecker('campaigns-and-offers')">
                        {{ $t("label.action") }}
                    </th>
                </tr>
                </thead>
                <tbody class="db-table-body" v-if="offers.length > 0">
                <tr class="db-table-body-tr" v-for="offer in offers" :key="offer">
                    <td class="db-table-body-td">
                        <div v-if="offer.title.length < 40">{{ offer.title }}</div>
                        <div v-else>{{ offer.title.substring(0, 40) + ".." }}</div>
                    </td>
                    <td class="db-table-body-td">{{ offer.flat_amount }} %</td>
                    <td class="db-table-body-td">{{ offer.convert_date }}</td>
                    <td class="db-table-body-td">{{ offer.convert_time }}</td>
                    <td class="db-table-body-td">
                            <span :class="statusClass(offer.status)">
                                {{ enums.statusEnumArray[offer.status] }}
                            </span>
                    </td>
                    <td class="db-table-body-td hidden-print" v-if="permissionChecker('campaigns-and-offers')">
                        <div class="flex justify-start items-center sm:items-start sm:justify-start gap-1.5">
                            <SmIconViewComponent :link="'admin.campaignsAndOffers.showOffer'" :id="offer.id"/>
                            <SmIconIconApplyComponent @click="apply(offer.id)"
                                                      v-if="offer.apply === enums.askEnum.NO"/>
                            <SmIconLeaveComponent @click="leave(offer.id)" v-else/>
                        </div>
                    </td>
                </tr>
                </tbody>
                <tbody class="db-table-body" v-else>
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
</template>

<script>
import LoadingComponent from "../../../components/common/LoadingComponent.vue";
import alertService from "../../../services/alertService.js";
import PaginationTextComponent from "../components/pagination/PaginationTextComponent.vue";
import PaginationBox from "../components/pagination/PaginationBox.vue";
import PaginationSMBox from "../components/pagination/PaginationSMBox.vue";
import appService from "../../../services/appService.js";
import statusEnum from "../../../enums/modules/statusEnum.js";
import askEnum from "../../../enums/modules/askEnum.js";
import offerTypeEnum from "../../../enums/modules/offerTypeEnum.js";
import TableLimitComponent from "../components/TableLimitComponent.vue";
import SmIconIconApplyComponent from "../components/buttons/SmIconIconApplyComponent.vue";
import SmIconLeaveComponent from "../components/buttons/SmIconLeaveComponent.vue";
import FilterComponent from "../components/buttons/collapse/FilterComponent.vue";
import ExportComponent from "../components/buttons/export/ExportComponent.vue";
import print from "vue3-print-nb";
import PrintComponent from "../components/buttons/export/PrintComponent.vue";
import ExcelComponent from "../components/buttons/export/ExcelComponent.vue";
import SmIconViewComponent from "../components/buttons/SmIconViewComponent.vue";
import {usePaper} from "../../../composables/paper.js";
import {useSlide} from "../../../composables/slide.js";
import DatePickerComponent from "../components/DatePickerComponent.vue";
import Datepicker from "@vuepic/vue-datepicker";
import "@vuepic/vue-datepicker/dist/main.css";
import {useCampaignAndOfferStore} from "../../../stores/campaignAndOffer.js";
import {useFrontendSettingStore} from "../../../stores/frontendSetting.js";
import VueSimpleAlert from "vue3-simple-alert";

export default {
    name: "OfferListComponent",
    components: {
        TableLimitComponent,
        PaginationSMBox,
        PaginationBox,
        PaginationTextComponent,
        LoadingComponent,
        SmIconIconApplyComponent,
        SmIconLeaveComponent,
        ExportComponent,
        FilterComponent,
        PrintComponent,
        ExcelComponent,
        DatePickerComponent,
        SmIconViewComponent,
        Datepicker
    },
    setup() {
        const frontendSettingStore  = useFrontendSettingStore();
        const campaignAndOfferStore = useCampaignAndOfferStore();
        return {
            campaignAndOfferStore,
            frontendSettingStore
        }
    },
    data() {
        return {
            loading: {
                isActive: false,
            },
            enums: {
                statusEnum: statusEnum,
                offerTypeEnum: offerTypeEnum,
                askEnum: askEnum,
                statusEnumArray: {
                    [statusEnum.ACTIVE]: this.$t("label.active"),
                    [statusEnum.INACTIVE]: this.$t("label.inactive"),
                },
                offerTypeEnumArray: {
                    [offerTypeEnum.REGULAR]: this.$t("label.regular"),
                    [offerTypeEnum.PREMIER]: this.$t("label.premier")
                },
            },
            printLoading: true,
            printObj: {
                id: "print-offer",
                popTitle: this.$t("menu.offers"),
            },
            props: {
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
                    type: null,
                },
                startTime: '',
                endTime: '',
            },
            modelValue: null,
            timeValue: null,
            handlePaper: usePaper().handlePaper,
            handleSlide: useSlide().handleSlide,
        };
    },
    mounted() {
        this.list();
    },
    computed: {
        setting: function () {
            return this.frontendSettingStore.lists;
        },
        offers: function () {
            return this.campaignAndOfferStore.offerLists;
        },
        pagination: function () {
            return this.campaignAndOfferStore.offerPagination;
        },
        paginationPage: function () {
            return this.campaignAndOfferStore.offerPage;
        },
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
                this.props.search.end_date   = e[1];
            } else {
                this.props.search.start_date = null;
                this.props.search.end_date   = null;
            }
        },
        handleTime: function (e) {
            if (e) {
                this.props.search.start_time = e[0].hours + ':' + e[0].minutes + ':' + e[0].seconds;
                this.props.search.end_time   = e[1].hours + ':' + e[1].minutes + ':' + e[1].seconds;
            } else {
                this.props.search.start_time = '';
                this.props.search.end_time   = '';
                this.timeValue               = null;
            }
        },
        clear: function () {
            this.props.search.paginate   = 1;
            this.props.search.page       = 1;
            this.props.search.title      = "";
            this.props.search.amount     = "";
            this.props.search.type       = null;
            this.props.search.status     = null;
            this.props.search.start_date = "";
            this.props.search.end_date   = "";
            this.props.search.start_time = "";
            this.props.search.end_time   = "";
            this.modelValue              = null;
            this.timeValue               = null;
            this.list();
        },
        list: function (page = 1) {
            this.loading.isActive  = true;
            this.props.search.page = page;
            this.campaignAndOfferStore.fetchOfferLists(this.props.search).then((res) => {
                this.loading.isActive = false;
            }).catch((err) => {
                this.loading.isActive = false;
            });
        },
        apply: function (id) {
            return new VueSimpleAlert.confirm(
                this.$t('message.apply_campaign_or_offer'),
                this.$t('message.are_you_sure'),
                "warning",
                {
                    confirmButtonText: this.$t('button.yes_apply'),
                    cancelButtonText: this.$t('button.no_cancel'),
                    confirmButtonColor: "#1AB759",
                    cancelButtonColor: "#E93C3C"
                }
            ).then((res) => {
                try {
                    this.loading.isActive = true;
                    this.campaignAndOfferStore.fetchApplyOffer({id: id, search: this.props.search}).then((res) => {
                        this.loading.isActive = false;
                        alertService.successInfo(null, this.$t("message.apply_offer"));
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
        leave: function (id) {
            return new VueSimpleAlert.confirm(
                this.$t('message.apply_campaign_or_offer'),
                this.$t('message.are_you_sure'),
                "warning",
                {
                    confirmButtonText: this.$t('button.yes_leave'),
                    cancelButtonText: this.$t('button.no_cancel'),
                    confirmButtonColor: "#1AB759",
                    cancelButtonColor: "#E93C3C"
                }
            ).then((res) => {
                try {
                    this.loading.isActive = true;
                    this.campaignAndOfferStore.fetchLeaveOffer({id: id, search: this.props.search}).then((res) => {
                        this.loading.isActive = false;
                        alertService.successInfo(null, this.$t("message.leave_offer"));
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
            this.campaignAndOfferStore.exportOffer(this.props.search).then((res) => {
                this.loading.isActive = false;
                const blob            = new Blob([res.data], {
                    type: "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet",
                });
                const link            = document.createElement("a");
                link.href             = URL.createObjectURL(blob);
                link.download         = this.$t("menu.offers");
                link.click();
                URL.revokeObjectURL(link.href);
            }).catch((err) => {
                this.loading.isActive = false;
                alertService.error(err.response.data.message);
            });
        },
    },
};
</script>
