<template>
    <LoadingComponent :props="loading" />
    <div class="col-12">
        <div class="db-card">
            <div class="db-card-header border-none">
                <h3 class="db-card-title">{{ $t("menu.restaurant_owners") }}</h3>
                <nav class="flex flex-wrap gap-2 mobile:justify-center">
                    <TableLimitComponent :method="list" :search="props.search" :page="paginationPage" />
                    <FilterComponent @click.prevent="handleSlide('restaurant-owner-filter')" />
                    <div class="paper-group">
                        <ExportComponent @click.prevent="handlePaper" />
                        <nav class="paper-content absolute top-9 right-1/2 translate-x-1/2 z-30 min-w-[80px] w-fit rounded-md shadow-paper bg-white">
                            <PrintComponent :props="printObj" />
                            <ExcelComponent :method="xls" />
                        </nav>
                    </div>
                    <RestaurantOwnerCreateComponent :props="props" v-if="permissionChecker('restaurant-owners_create')" />
                </nav>
            </div>

            <div class="table-filter-div" id="restaurant-owner-filter">
                <form class="p-4 sm:p-5 mb-5" @submit.prevent="search">
                    <div class="row">
                        <div class="col-12 sm:col-6 md:col-4 xl:col-3">
                            <label for="searchName" class="db-field-title after:hidden">{{$t("label.name")}}</label>
                            <input id="searchName" v-model="props.search.name" type="text" class="db-field-control" />
                        </div>
                        <div class="col-12 sm:col-6 md:col-4 xl:col-3">
                            <label for="searchEmail" class="db-field-title after:hidden">{{$t("label.email")}}</label>
                            <input id="searchEmail" v-model="props.search.email" type="text" class="db-field-control" />
                        </div>
                        <div class="col-12 sm:col-6 md:col-4 xl:col-3">
                            <label for="searchPhone" class="db-field-title after:hidden">{{$t("label.phone")}}</label>
                            <input id="searchPhone" v-model="props.search.phone" v-on:keypress="phoneNumber($event)" type="text" class="db-field-control" />
                        </div>
                        <div class="col-12 sm:col-6 md:col-4 xl:col-3">
                            <label for="searchStatus" class="db-field-title after:hidden">{{$t("label.status")}}</label>
                            <vue-select class="db-field-control f-b-custom-select" id="searchStatus"
                                v-model="props.search.status"
                                :options="[{ id: enums.statusEnum.ACTIVE, name: $t('label.active') }, { id: enums.statusEnum.INACTIVE, name: $t('label.inactive') },]"
                                label-by="name" value-by="id" :closeOnSelect="true" :searchable="true" :clearOnClose="true"
                                placeholder="--" search-placeholder="--" />
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
                            <th class="db-table-head-th">{{ $t("label.name") }}</th>
                            <th class="db-table-head-th">{{ $t("label.email") }}</th>
                            <th class="db-table-head-th">{{ $t("label.phone") }}</th>
                            <th class="db-table-head-th">{{ $t("label.status") }}</th>
                            <th class="db-table-head-th hidden-print" v-if="permissionChecker('restaurant-owners_show') || permissionChecker('restaurant-owners_edit') || permissionChecker('restaurant-owners_delete')"> {{ $t("label.action") }}</th>
                        </tr>
                    </thead>
                    <tbody class="db-table-body" v-if="restaurantOwners.length > 0">
                        <tr class="db-table-body-tr" v-for="restaurantOwner in restaurantOwners" :key="restaurantOwner">
                            <td class="db-table-body-td">
                                {{ textShortener(restaurantOwner.name, 20) }}
                            </td>
                            <td class="db-table-body-td">
                                {{ restaurantOwner.email }}
                            </td>
                            <td class="db-table-body-td">
                                {{ restaurantOwner.country_code + '' + restaurantOwner.phone }}
                            </td>
                            <td class="db-table-body-td">
                                <span :class="statusClass(restaurantOwner.status)">
                                    {{ enums.statusEnumArray[restaurantOwner.status] }}
                                </span>
                            </td>
                            <td class="db-table-body-td hidden-print" v-if="permissionChecker('restaurant-owners_show') || permissionChecker('restaurant-owners_edit') || permissionChecker('restaurant-owners_delete')">
                                <div class="flex justify-start items-center sm:items-start sm:justify-start gap-1.5">
                                    <SmIconViewComponent :link="'admin.restaurantOwners.show'" :id="restaurantOwner.id" v-if="permissionChecker('restaurant-owners_show')" />
                                    <SmIconSidebarModalEditComponent @click="edit(restaurantOwner)" v-if="permissionChecker('restaurant-owners_edit')" />
                                    <SmIconDeleteComponent @click="destroy(restaurantOwner.id)" v-if="restaurantOwner.id !== 2 && permissionChecker('restaurant-owners_delete')" />
                                </div>
                            </td>
                        </tr>
                    </tbody>
                    <tbody class="db-table-body" v-else>
                        <tr class="db-table-body-tr">
                            <td class="db-table-body-td" colspan="5">
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
import RestaurantOwnerCreateComponent from "./RestaurantOwnerCreateComponent.vue";
import alertService from "../../../services/alertService";
import PaginationTextComponent from "../components/pagination/PaginationTextComponent.vue";
import PaginationBox from "../components/pagination/PaginationBox.vue";
import PaginationSMBox from "../components/pagination/PaginationSMBox.vue";
import appService from "../../../services/appService";
import statusEnum from "../../../enums/modules/statusEnum";
import TableLimitComponent from "../components/TableLimitComponent.vue";
import SmIconViewComponent from "../components/buttons/SmIconViewComponent.vue";
import SmIconSidebarModalEditComponent from "../components/buttons/SmIconSidebarModalEditComponent.vue";
import SmIconDeleteComponent from "../components/buttons/SmIconDeleteComponent.vue";
import print from "vue3-print-nb";
import FilterComponent from "../components/buttons/collapse/FilterComponent.vue";
import ExportComponent from "../components/buttons/export/ExportComponent.vue";
import PrintComponent from "../components/buttons/export/PrintComponent.vue";
import ExcelComponent from "../components/buttons/export/ExcelComponent.vue";
import { usePaper } from "../../../composables/paper";
import { useSlide } from "../../../composables/slide";
import { useRestaurantOwnerStore } from "../../../stores/restaurantOwner";
import { useCountryCodeStore } from "../../../stores/countryCode";
import { useFrontendSettingStore } from "../../../stores/frontendSetting";
import { useCompanyStore } from "../../../stores/company";
import VueSimpleAlert from "vue3-simple-alert";

export default {
    name: "RestaurantOwnerListComponent",
    components: {
        TableLimitComponent,
        PaginationSMBox,
        PaginationBox,
        PaginationTextComponent,
        RestaurantOwnerCreateComponent,
        LoadingComponent,
        SmIconViewComponent,
        SmIconSidebarModalEditComponent,
        SmIconDeleteComponent,
        FilterComponent,
        ExportComponent,
        PrintComponent,
        ExcelComponent
    },
    setup() {
        const restaurantOwnerStore = useRestaurantOwnerStore();
        const countryCodeStore = useCountryCodeStore();
        const frontendSettingStore = useFrontendSettingStore();
        const companyStore = useCompanyStore();
        return {
            restaurantOwnerStore,
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
                    [statusEnum.ACTIVE]  : this.$t("label.active"),
                    [statusEnum.INACTIVE]: this.$t("label.inactive")
                }
            },
            printLoading: true,
            printObj: {
                id: "print",
                popTitle: this.$t("menu.restaurant_owners")
            },
            props: {
                form: {
                    name                 : "",
                    email                : "",
                    phone                : "",
                    password             : "",
                    password_confirmation: "",
                    status               : statusEnum.ACTIVE,
                    restaurant_id        : null,
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
                },
            },
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
        restaurantOwners: function () {
            return this.restaurantOwnerStore.lists;
        },
        pagination: function () {
            return this.restaurantOwnerStore.pagination;
        },
        paginationPage: function () {
            return this.restaurantOwnerStore.page;
        }
    },
    methods: {
        permissionChecker(e) {
            return appService.permissionChecker(e);
        },
        statusClass: function (status) {
            return appService.statusClass(status);
        },
        phoneNumber(e) {
            return appService.phoneNumber(e);
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
        list: function (page = 1) {
            this.loading.isActive = true;
            this.props.search.page = page;
            this.restaurantOwnerStore.fetch(this.props.search).then((res) => {
                this.loading.isActive = false;
            }).catch((err) => {
                this.loading.isActive = false;
            });
        },
        edit: function (restaurantOwner) {
            this.loading.isActive = true;
            this.restaurantOwnerStore.edit(restaurantOwner.id).then((res) => {
                this.loading.isActive = false;
                this.props.errors = {};
                this.props.form = {
                    name         : restaurantOwner.name,
                    email        : restaurantOwner.email,
                    phone        : restaurantOwner.phone,
                    password     : restaurantOwner.password,
                    status       : restaurantOwner.status,
                    country_code : restaurantOwner.country_code,
                    restaurant_id: restaurantOwner.restaurant_id
                };
                this.countryCodeStore.fetchFind({country_code: restaurantOwner.country_code}).then(res => {
                    this.props.form.flag = res.data.data.flag_emoji;
                }).catch()
            }).catch((err) => {
                alertService.error(err.response.data.message);
            });
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
                    this.restaurantOwnerStore.destroy({id: id, search: this.props.search}).then((res) => {
                        this.loading.isActive = false;
                        alertService.successFlip(null, this.$t("menu.restaurantOwners"));
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
            this.restaurantOwnerStore.export(this.props.search).then((res) => {
                this.loading.isActive = false;
                const blob = new Blob([res.data], {
                    type: "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet"
                });
                const link = document.createElement("a");
                link.href = URL.createObjectURL(blob);
                link.download = this.$t("menu.restaurantOwners");
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
