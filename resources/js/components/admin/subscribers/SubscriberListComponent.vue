<template>
    <LoadingComponent :props="loading" />

    <div class="db-card db-tab-div active">
        <div class="db-card-header border-none">
            <h3 class="db-card-title">{{ $t('menu.subscribers') }}</h3>
            <nav class="flex flex-wrap gap-2 mobile:justify-center">
                <TableLimitComponent :method="list" :search="props.search" :page="paginationPage" />
                <FilterComponent @click.prevent="handleSlide('subscriber-filter')" />
                <div class="paper-group">
                    <ExportComponent @click.prevent="handlePaper" />
                    <nav class="paper-content absolute top-9 right-1/2 translate-x-1/2 z-30 min-w-[80px] w-fit rounded-md shadow-paper bg-white">
                        <PrintComponent :props="printObj" />
                        <ExcelComponent :method="xls" />
                    </nav>
                </div>
                <SubscriberMailComponent :props="props" v-if="permissionChecker('subscribers')" />
            </nav>
        </div>
        <div class="table-filter-div" id="subscriber-filter">
            <form class="p-4 sm:p-5 mb-5 w-full d-block" @submit.prevent="search">
                <div class="row">
                    <div class="col-12 sm:col-6 md:col-4 xl:col-3">
                        <label for="searchEmail" class="db-field-title after:hidden">{{ $t('label.email') }}</label>
                        <input id="searchEmail" v-model="props.search.email" type="text" class="db-field-control">
                    </div>
                    <div class="col-12 sm:col-6 md:col-4 xl:col-3">
                        <label for="searchStartDate" class="db-field-title after:hidden"> {{ $t('label.date') }} </label>
                        <DatePickerComponent @update:modelValue="handleDate" inputStyle="filter" :range="true" :hideInputIcon="true" v-model="modelValue" />
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
                        <th class="db-table-head-th">{{ $t("label.email") }}</th>
                        <th class="db-table-head-th">{{ $t("label.date") }}</th>
                        <th class="db-table-head-th hidden-print" v-if="permissionChecker('subscribers')"> {{ $t("label.action") }}</th>
                    </tr>
                </thead>
                <tbody class="db-table-body" v-if="subscribers.length > 0">
                    <tr class="db-table-body-tr" v-for="subscriber in subscribers" :key="subscriber">
                        <td class="db-table-body-td">{{ subscriber.email }}</td>
                        <td class="db-table-body-td">{{ subscriber.date_time }}</td>
                        <td class="db-table-body-td hidden-print" v-if="permissionChecker('subscribers')">
                            <div class="flex justify-start items-center sm:items-start sm:justify-start gap-1.5">
                                <SmIconDeleteComponent @click="destroy(subscriber.id)" v-if="permissionChecker('subscribers')" />
                            </div>
                        </td>
                    </tr>
                </tbody>
                <tbody class="db-table-body" v-else>
                    <tr class="db-table-body-tr">
                        <td class="db-table-body-td" colspan="3">
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
</template>

<script>
import LoadingComponent from "../../common/LoadingComponent.vue";
import SubscriberMailComponent from "./SubscriberMailComponent.vue";
import alertService from "../../../services/alertService.js";
import PaginationTextComponent from "../components/pagination/PaginationTextComponent.vue";
import PaginationBox from "../components/pagination/PaginationBox.vue";
import PaginationSMBox from "../components/pagination/PaginationSMBox.vue";
import appService from "../../../services/appService.js";
import TableLimitComponent from "../components/TableLimitComponent.vue";
import SmIconDeleteComponent from "../components/buttons/SmIconDeleteComponent.vue";
import FilterComponent from "../components/buttons/collapse/FilterComponent.vue";
import ExportComponent from "../components/buttons/export/ExportComponent.vue";
import print from 'vue3-print-nb';
import PrintComponent from "../components/buttons/export/PrintComponent.vue";
import ExcelComponent from "../components/buttons/export/ExcelComponent.vue";
import DatePickerComponent from "../components/DatePickerComponent.vue";
import { usePaper } from "../../../composables/paper";
import { useSlide } from "../../../composables/slide";
import { useFrontendSettingStore } from "../../../stores/frontendSetting";
import { useSubscriberStore } from "../../../stores/subscriber";
import VueSimpleAlert from "vue3-simple-alert";

export default {
    name: "SubscriberListComponent",
    components: {
        TableLimitComponent,
        PaginationSMBox,
        PaginationBox,
        PaginationTextComponent,
        SubscriberMailComponent,
        LoadingComponent,
        SmIconDeleteComponent,
        ExportComponent,
        FilterComponent,
        PrintComponent,
        ExcelComponent,
        DatePickerComponent
    },
    setup() {
        const frontendSettingStore = useFrontendSettingStore();
        const subscriberStore = useSubscriberStore();
        return {
            frontendSettingStore,
            subscriberStore
        }
    },
    data() {
        return {
            loading: {
                isActive: false
            },
            printLoading: true,
            printObj: {
                id: "print",
                popTitle: this.$t('menu.subscribers')
            },
            props: {
                form: {
                    subject: "",
                    message: ""
                },
                search: {
                    paginate: 1,
                    page: 1,
                    per_page: 10,
                    order_column: "id",
                    order_type: "desc",
                    email: "",
                    from_date: "",
                    to_date: ""
                }
            },
            modelValue: null,
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
        subscribers: function () {
            return this.subscriberStore.lists;
        },
        pagination: function () {
            return this.subscriberStore.pagination;
        },
        paginationPage: function () {
            return this.subscriberStore.page;
        },
    },
    methods: {
        permissionChecker(e) {
            return appService.permissionChecker(e);
        },
        search: function () {
            this.list();
        },
        clear: function () {
            this.props.search.paginate  = 1;
            this.props.search.page      = 1;
            this.props.search.email     = "";
            this.props.search.from_date = "";
            this.props.search.to_date   = "";
            this.modelValue             = null;
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
        list: function (page = 1) {
            this.loading.isActive  = true;
            this.props.search.page = page;
            this.subscriberStore.fetch(this.props.search).then((res) => {
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
                    this.subscriberStore.destroy({id: id, search: this.props.search}).then((res) => {
                        this.loading.isActive = false;
                        alertService.successFlip(null, this.$t("menu.subscribers"));
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
            this.subscriberStore.export(this.props.search).then(res => {
                this.loading.isActive = false;
                const blob = new Blob([res.data], { type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' });
                const link = document.createElement('a');
                link.href = URL.createObjectURL(blob);
                link.download = this.$t("menu.subscribers");
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
