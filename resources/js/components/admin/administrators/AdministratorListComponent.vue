<template>
    <LoadingComponent :props="loading" />
    <div class="col-12">
        <div class="db-card">
            <div class="db-card-header border-none">
                <h3 class="db-card-title">{{ $t("menu.administrators") }}</h3>
                <nav class="flex flex-wrap gap-2 mobile:justify-center">
                    <TableLimitComponent :method="list" :search="props.search" :page="paginationPage" />
                    <FilterComponent @click.prevent="handleSlide('administrator-filter')" />
                    <div class="paper-group">
                        <ExportComponent @click.prevent="handlePaper" />
                        <nav
                            class="paper-content absolute top-9 right-1/2 translate-x-1/2 z-30 min-w-[80px] w-fit rounded-md shadow-paper bg-white">
                            <PrintComponent :props="printObj" />
                            <ExcelComponent :method="xls" />
                        </nav>
                    </div>
                    <AdministratorCreateComponent :props="props" v-if="permissionChecker('administrators_create')" />
                </nav>
            </div>
            <div class="table-filter-div" id="administrator-filter">
                <form class="p-4 sm:p-5 mb-5" @submit.prevent="search">
                    <div class="row">
                        <div class="col-12 sm:col-6 md:col-4 xl:col-3">
                            <label for="searchName" class="db-field-title after:hidden">{{ $t('label.name') }}</label>
                            <input id="searchName" v-model="props.search.name" type="text" class="db-field-control">
                        </div>
                        <div class="col-12 sm:col-6 md:col-4 xl:col-3">
                            <label for="searchEmail" class="db-field-title after:hidden">{{ $t('label.email') }}</label>
                            <input id="searchEmail" v-model="props.search.email" type="text" class="db-field-control">
                        </div>
                        <div class="col-12 sm:col-6 md:col-4 xl:col-3">
                            <label for="searchPhone" class="db-field-title after:hidden">{{ $t('label.phone') }}</label>
                            <input id="searchPhone" v-model="props.search.phone" v-on:keypress="phoneNumber($event)" type="text" class="db-field-control">
                        </div>

                        <div class="col-12 sm:col-6 md:col-4 xl:col-3">
                            <label for="searchStatus" class="db-field-title after:hidden">{{ $t('label.status') }}</label>
                            <vue-select class="db-field-control f-b-custom-select" id="searchStatus"
                                v-model="props.search.status" :options="[
                                    { id: enums.statusEnum.ACTIVE, name: $t('label.active') },
                                    { id: enums.statusEnum.INACTIVE, name: $t('label.inactive') }
                                ]" label-by="name" value-by="id" :closeOnSelect="true" :searchable="true"
                                :clearOnClose="true" placeholder="--" search-placeholder="--" />
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
                            <th class="db-table-head-th">
                                {{ $t("label.name") }}
                            </th>
                            <th class="db-table-head-th">
                                {{ $t("label.email") }}
                            </th>
                            <th class="db-table-head-th">
                                {{ $t("label.phone") }}
                            </th>
                            <th class="db-table-head-th">
                                {{ $t("label.status") }}
                            </th>
                            <th class="db-table-head-th hidden-print" v-if="permissionChecker('administrators_show') || permissionChecker('administrators_edit') || permissionChecker('administrators_delete')">
                                {{ $t("label.action") }}
                            </th>
                        </tr>
                    </thead>
                    <tbody class="db-table-body" v-if="administrators.length > 0">
                        <tr class="db-table-body-tr" v-for="administrator in administrators" :key="administrator">
                            <td class="db-table-body-td">
                                {{ textShortener(administrator.name, 20) }}
                            </td>
                            <td class="db-table-body-td">
                                {{ administrator.email }}
                            </td>
                            <td class="db-table-body-td">
                                <span v-if="administrator.phone"> {{ administrator.country_code + '' +
                                    administrator.phone }} </span>
                            </td>
                            <td class="db-table-body-td ">
                                <span :class="statusClass(administrator.status)">
                                    {{
                                        enums.statusEnumArray[administrator.status]
                                    }}
                                </span>
                            </td>
                            <td class="db-table-body-td hidden-print" v-if="permissionChecker('administrators_show') || permissionChecker('administrators_edit') || permissionChecker('administrators_delete')">
                                <div class="flex justify-start items-center sm:items-start sm:justify-start gap-1.5">
                                    <SmIconViewComponent :link="'admin.administrators.show'" :id="administrator.id" v-if="permissionChecker('administrators_show')" />
                                    <SmIconSidebarModalEditComponent @click="edit(administrator)" v-if="permissionChecker('administrators_edit')" />
                                    <SmIconDeleteComponent @click="destroy(administrator.id)" v-if="administrator.id !== 1 && permissionChecker('administrators_delete')" />
                                </div>
                            </td>
                        </tr>
                    </tbody>
                    <tbody class="db-table-body" v-else>
                        <tr class="db-table-body-tr">
                            <td class="db-table-body-td" colspan="5">
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
import LoadingComponent from "../../../components/common/LoadingComponent.vue";
import AdministratorCreateComponent from "./AdministratorCreateComponent.vue";
import alertService from "../../../services/alertService.js";
import PaginationTextComponent from "../components/pagination/PaginationTextComponent.vue";
import PaginationBox from "../components/pagination/PaginationBox.vue";
import PaginationSMBox from "../components/pagination/PaginationSMBox.vue";
import appService from "../../../services/appService.js";
import statusEnum from "../../../enums/modules/statusEnum.js";
import TableLimitComponent from "../components/TableLimitComponent.vue";
import SmIconDeleteComponent from "../components/buttons/SmIconDeleteComponent.vue";
import SmIconViewComponent from "../components/buttons/SmIconViewComponent.vue";
import SmIconSidebarModalEditComponent from "../components/buttons/SmIconSidebarModalEditComponent.vue";
import FilterComponent from "../components/buttons/collapse/FilterComponent.vue";
import ExportComponent from "../components/buttons/export/ExportComponent.vue";
import print from 'vue3-print-nb';
import PrintComponent from "../components/buttons/export/PrintComponent.vue";
import ExcelComponent from "../components/buttons/export/ExcelComponent.vue";
import { usePaper } from "../../../composables/paper.js";
import { useSlide } from "../../../composables/slide.js";
import { useAdministratorStore } from "../../../stores/administrator.js";
import { useCountryCodeStore } from "../../../stores/countryCode.js";
import { useFrontendSettingStore } from "../../../stores/frontendSetting.js";
import { useCompanyStore } from "../../../stores/company.js";
import VueSimpleAlert from "vue3-simple-alert";

export default {
    name: "AdministratorListComponent",
    components: {
        SmIconSidebarModalEditComponent,
        TableLimitComponent,
        PaginationSMBox,
        PaginationBox,
        PaginationTextComponent,
        AdministratorCreateComponent,
        LoadingComponent,
        SmIconDeleteComponent,
        SmIconViewComponent,
        FilterComponent,
        ExportComponent,
        print,
        PrintComponent,
        ExcelComponent,
    },
    setup() {
        const administratorStore   = useAdministratorStore();
        const countryCodeStore     = useCountryCodeStore();
        const frontendSettingStore = useFrontendSettingStore();
        const companyStore         = useCompanyStore();
        return {
            administratorStore,
            countryCodeStore,
            frontendSettingStore,
            companyStore
        }
    },
    data() {
        return {
            loading: {
                isActive: false
            },
            enums: {
                statusEnum: statusEnum,
                statusEnumArray: {
                    [statusEnum.ACTIVE]: this.$t("label.active"),
                    [statusEnum.INACTIVE]: this.$t("label.inactive")
                }
            },
            printLoading: true,
            printObj: {
                id: "print",
                popTitle: this.$t('menu.administrators')
            },
            props: {
                form: {
                    name                 : "",
                    email                : "",
                    phone                : "",
                    password             : "",
                    password_confirmation: "",
                    status               : statusEnum.ACTIVE,
                    country_code         : "",
                    flag                 : ""
                },
                search: {
                    paginate    : 1,
                    page        : 1,
                    per_page    : 10,
                    order_column: "id",
                    order_type  : "desc",
                    name        : "",
                    email       : "",
                    phone       : "",
                    status      : null
                }
            },
            handlePaper: usePaper().handlePaper,
            handleSlide: useSlide().handleSlide,
        };
    },
    computed: {
        setting: function () {
            return this.frontendSettingStore.lists;
        },
        administrators: function () {
            return this.administratorStore.lists;
        },
        pagination: function () {
            return this.administratorStore.pagination;
        },
        paginationPage: function () {
            return this.administratorStore.page;
        }
    },
    async mounted() {
        await this.list();
    },
    methods: {
        phoneNumber(e) {
            return appService.phoneNumber(e);
        },
        permissionChecker(e) {
            return appService.permissionChecker(e);
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
        clear: function () {
            this.props.search.paginate = 1;
            this.props.search.page     = 1;
            this.props.search.name     = "";
            this.props.search.email    = "";
            this.props.search.phone    = "";
            this.props.search.status   = null;
            this.list();
        },
        list: async function (page = 1) {
            this.loading.isActive  = true;
            this.props.search.page = page;
            await this.administratorStore.fetch(this.props.search).then((res) => {
                this.loading.isActive = false;
            }).catch((err) => {
                this.loading.isActive = false;
            });
        },
        edit: function (administrator) {
            this.loading.isActive = true;
            this.administratorStore.edit(administrator.id);
            this.loading.isActive = false;
            this.props.errors     = {};
            this.props.form       = {
                name        : administrator.name,
                email       : administrator.email,
                phone       : administrator.phone,
                password    : administrator.password,
                status      : administrator.status,
                country_code: administrator.country_code
            };
            this.countryCodeStore.fetchFind({country_code: administrator.country_code}).then(res => {
                this.props.form.flag = res.data.data.flag_emoji;
            }).catch()
        },
        destroy: function (id) {
            return new VueSimpleAlert.confirm(
                this.$t("message.delete_record"),
                this.$t("message.are_you_sure"),
                "warning",
                {
                    confirmButtonText : this.$t("button.yes_delete"),
                    cancelButtonText  : this.$t("button.no_cancel"),
                    confirmButtonColor: "#1AB759",
                    cancelButtonColor : "#E93C3C"
                }
            ).then((res) => {
                try {
                    this.loading.isActive = true;
                    this.administratorStore.destroy({
                        id: id,
                        search: this.props.search
                    }).then((res) => {
                            this.loading.isActive = false;
                            alertService.successFlip(
                                null,
                                this.$t("menu.administrators")
                            );
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
            this.administratorStore.export(this.props.search).then(res => {
                this.loading.isActive = false;
                const blob = new Blob([res.data], {
                    type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
                });
                const link = document.createElement('a');
                link.href = URL.createObjectURL(blob);
                link.download = this.$t("menu.administrators");
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
