<template>
    <LoadingComponent :props="loading"/>
    <div class="col-12">
        <div class="db-card">
            <div class="db-card-header border-none">
                <h3 class="db-card-title">{{ $t("menu.collections") }}</h3>
                <nav class="flex flex-wrap gap-2 mobile:justify-center">
                    <TableLimitComponent :method="list" :search="props.search" :page="paginationPage"/>
                    <FilterComponent @click.prevent="handleSlide('collection-filter')"/>
                    <div class="paper-group">
                        <ExportComponent @click.prevent="handlePaper"/>
                        <nav class="paper-content absolute top-9 right-1/2 translate-x-1/2 z-30 min-w-[80px] w-fit rounded-md shadow-paper bg-white">
                            <PrintComponent :props="printObj"/>
                            <ExcelComponent :method="xls"/>
                        </nav>
                    </div>
                    <router-link :to="{ name: 'admin.collections.create' }" v-if="permissionChecker('collections_create')" class="h-9 px-3 py-4 font-medium border border-primary flex items-center gap-1 text-sm tracking-wide capitalize rounded-md shadow text-white bg-primary">
                        <i class="lab lab-line-add-circle"></i>
                        <span>{{ $t("button.add_collection") }}</span>
                    </router-link>
                </nav>
            </div>

            <div class="table-filter-div" id="collection-filter">
                <form class="p-4 sm:p-5 mb-5" @submit.prevent="search">
                    <div class="row">
                        <div class="col-12 sm:col-6 md:col-4 xl:col-3">
                            <label for="searchDate" class="db-field-title">{{ $t("label.date") }}</label>
                            <DatePickerComponent @update:modelValue="handleDate" inputStyle="filter" :range="true" :hideInputIcon="true" v-model="modelValue"/>
                        </div>

                        <div class="col-12 sm:col-6 md:col-4 xl:col-3">
                            <label for="searchAmount" class="db-field-title after:hidden">
                                {{ $t("label.amount") }}
                            </label>
                            <input v-on:keypress="floatNumber($event)" id="searchAmount" v-model="props.search.amount"
                                   type="text" class="db-field-control"/>
                        </div>

                        <div v-if="roleId === this.enums.roleEnum.ADMIN" class="col-12 sm:col-6 md:col-4 xl:col-3">
                            <label for="searchType" class="db-field-title after:hidden">
                                {{ $t("label.type") }}
                            </label>
                            <vue-select class="db-field-control f-b-custom-select" id="searchType"
                                        v-model="props.search.type" @update:modelValue="changeType"
                                        :options="[{ id: enums.collectionTypeEnum.DELIVERY_BOY, name: $t('label.delivery_boy') }, { id: enums.collectionTypeEnum.Employee, name: $t('label.employee') }]"
                                        label-by="name" value-by="id" :closeOnSelect="true" :searchable="true"
                                        :clearOnClose="true" placeholder="--" search-placeholder="--"/>
                        </div>

                        <div class="col-12 sm:col-6 md:col-4 xl:col-3"
                             v-if="roleId === this.enums.roleEnum.ADMIN &&  props.search.type === enums.collectionTypeEnum.DELIVERY_BOY">
                            <label for="searchDeliveryBoy" class="db-field-title after:hidden">
                                {{ $t('label.delivery_boy') }}
                            </label>
                            <vue-select class="db-field-control f-b-custom-select" id="searchDeliveryBoy"
                                        v-model="props.search.user_id" :options="deliveryBoys" label-by="name_email"
                                        value-by="id" :closeOnSelect="true" :searchable="true" :clearOnClose="true"
                                        placeholder="--" search-placeholder="--"/>
                        </div>

                        <div class="col-12 sm:col-6 md:col-4 xl:col-3"
                             v-if="roleId === this.enums.roleEnum.ADMIN &&  props.search.type === enums.collectionTypeEnum.Employee">
                            <label for="searchEmployee" class="db-field-title after:hidden">
                                {{ $t('label.employee') }}
                            </label>
                            <vue-select class="db-field-control f-b-custom-select" id="searchEmployee"
                                        v-model="props.search.user_id" :options="employees" label-by="name_email"
                                        value-by="id" :closeOnSelect="true" :searchable="true" :clearOnClose="true"
                                        placeholder="--" search-placeholder="--"/>
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
                        <th class="db-table-head-th">{{ $t("label.from") }}</th>
                        <th class="db-table-head-th">{{ $t("label.to") }}</th>
                        <th class="db-table-head-th">{{ $t("label.date") }}</th>
                        <th class="db-table-head-th">{{ $t("label.amount") }}</th>
                        <th class="db-table-head-th hidden-print" v-if="permissionChecker('collections_delete')">
                            {{ $t("label.action") }}
                        </th>
                    </tr>
                    </thead>
                    <tbody class="db-table-body" v-if="collections.length > 0">
                    <tr class="db-table-body-tr" v-for="collection in collections" :key="collection">
                        <td class="db-table-body-td">
                            {{ collection.source_user_name }}
                            <br>
                            {{ collection.source_user_email }}
                        </td>
                        <td class="db-table-body-td">
                            {{ collection.destination_user_name }}
                            <br>
                            {{ collection.destination_user_email }}
                        </td>
                        <td class="db-table-body-td">
                            {{ collection.time }}
                            <br>
                            {{ collection.date }}
                        </td>
                        <td class="db-table-body-td">
                            {{ collection.flat_amount }}
                        </td>
                        <td class="db-table-body-td hidden-print" v-if="permissionChecker('collections_delete')">
                            <div class="flex justify-start items-center sm:items-start sm:justify-start gap-1.5">
                                <SmIconDeleteComponent @click="destroy(collection.id)" v-if="permissionChecker('collections_delete')"/>
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
import CollectionCreateComponent from "./CollectionCreateComponent.vue";
import alertService from "../../../services/alertService.js";
import PaginationTextComponent from "../components/pagination/PaginationTextComponent.vue";
import PaginationBox from "../components/pagination/PaginationBox.vue";
import PaginationSMBox from "../components/pagination/PaginationSMBox.vue";
import appService from "../../../services/appService.js";
import TableLimitComponent from "../components/TableLimitComponent.vue";
import SmIconDeleteComponent from "../components/buttons/SmIconDeleteComponent.vue";
import print from "vue3-print-nb";
import FilterComponent from "../components/buttons/collapse/FilterComponent.vue";
import ExportComponent from "../components/buttons/export/ExportComponent.vue";
import PrintComponent from "../components/buttons/export/PrintComponent.vue";
import ExcelComponent from "../components/buttons/export/ExcelComponent.vue";
import {usePaper} from "../../../composables/paper";
import {useSlide} from "../../../composables/slide";
import DatePickerComponent from "../components/DatePickerComponent.vue";
import roleEnum from "../../../enums/modules/roleEnum";
import {useFrontendSettingStore} from "../../../stores/frontendSetting";
import {useCollectionStore} from "../../../stores/collection";
import {useAuthStore} from "../../../stores/auth.js";
import collectionTypeEnum from "../../../enums/modules/collectionTypeEnum.js";
import {useDeliveryBoyStore} from "../../../stores/deliveryBoy.js";
import {useEmployeeStore} from "../../../stores/employee.js";
import VueSimpleAlert from "vue3-simple-alert";

export default {
    name: "CollectionListComponent",
    components: {
        TableLimitComponent,
        PaginationSMBox,
        PaginationBox,
        PaginationTextComponent,
        CollectionCreateComponent,
        LoadingComponent,
        SmIconDeleteComponent,
        FilterComponent,
        ExportComponent,
        PrintComponent,
        ExcelComponent,
        DatePickerComponent
    },
    setup() {
        const authStore            = useAuthStore();
        const employeeStore        = useEmployeeStore();
        const collectionStore      = useCollectionStore();
        const deliveryBoyStore     = useDeliveryBoyStore();
        const frontendSettingStore = useFrontendSettingStore();

        return {
            authStore,
            employeeStore,
            collectionStore,
            deliveryBoyStore,
            frontendSettingStore
        }
    },
    data() {
        return {
            loading: {
                isActive: false
            },
            enums: {
                roleEnum: roleEnum,
                collectionTypeEnum: collectionTypeEnum
            },
            printLoading: true,
            printObj: {
                id: "print",
                popTitle: this.$t("menu.collections")
            },
            props: {
                search: {
                    paginate: 1,
                    page: 1,
                    per_page: 10,
                    order_column: "id",
                    order_type: "desc",
                    type: null,
                    user_id: null,
                    amount: "",
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
        this.loading.isActive = false;
        this.list();

        if (this.authStore.info?.role_id === this.enums.roleEnum.ADMIN) {
            this.deliveryBoyStore.fetchAllDeliveryBoy();
            this.employeeStore.fetchAllEmployeeAndAdmin();
        }
    },
    computed: {
        roleId: function () {
            return this.authStore.info?.role_id;
        },
        setting: function () {
            return this.frontendSettingStore.lists;
        },
        collections: function () {
            return this.collectionStore.lists;
        },
        pagination: function () {
            return this.collectionStore.pagination;
        },
        paginationPage: function () {
            return this.collectionStore.page;
        },
        deliveryBoys: function () {
            return this.deliveryBoyStore.allDeliveryBoys;
        },
        employees: function () {
            return this.employeeStore.allEmployeeAndAdmins;
        },
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
        changeType: function (e) {
            this.props.search.user_id = null;
        },
        clear: function () {
            this.props.search.paginate     = 1;
            this.props.search.page         = 1;
            this.props.search.order_column = "id";
            this.props.search.order_type   = "desc";
            this.props.search.type         = null;
            this.props.search.user_id      = null;
            this.props.search.amount       = "";
            this.props.search.from_date    = "";
            this.props.search.to_date      = ""
            this.modelValue                = null;
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
            this.collectionStore.fetch(this.props.search).then((res) => {
                this.loading.isActive = false;
            }).catch((err) => {
                this.loading.isActive = false;
            })
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
                    this.collectionStore.destroy({id: id, search: this.props.search}).then((res) => {
                        this.loading.isActive = false;
                        alertService.successFlip(null, this.$t("menu.collections"));
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
            })
        },
        xls: function () {
            this.loading.isActive = true;
            this.collectionStore.export(this.props.search).then((res) => {
                this.loading.isActive = false;
                const blob            = new Blob([res.data], {
                    type: "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet",
                });
                const link            = document.createElement("a");
                link.href             = URL.createObjectURL(blob);
                link.download         = this.$t("menu.collections");
                link.click();
                URL.revokeObjectURL(link.href);
            }).catch((err) => {
                this.loading.isActive = false;
                alertService.error(err.response.data.message);
            })
        }
    }
};
</script>
