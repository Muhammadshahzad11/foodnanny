<template>
    <LoadingComponent :props="loading"/>
    <TableQrModalComponent
        :visible="qrModalVisible"
        :qr="activeQr"
        @close="closeQrModal"
        @generate="generateActiveQr"
        @regenerate="regenerateActiveQr"
        @download="downloadActiveQr"
        @print="printIds([activeQrId])"
    />
    <TableQrPrintSheetComponent :items="printItems" :instruction="printInstruction"/>

    <div class="col-12">
        <div class="db-card">
            <div class="db-card-header border-none">
                <h3 class="db-card-title">{{ $t("menu.tables") }}</h3>
                <nav class="flex flex-wrap gap-2 mobile:justify-center">
                    <TableLimitComponent :method="list" :search="props.search" :page="paginationPage"/>
                    <FilterComponent @click.prevent="handleSlide('table-filter')"/>
                    <button
                        v-if="permissionChecker('table_qr_generate')"
                        type="button"
                        class="db-btn py-2 text-white bg-emerald-600"
                        @click="generateMissing"
                    >
                        {{ $t('button.generate_missing_qr') }}
                    </button>
                    <button
                        v-if="permissionChecker('table_qr_download') && selectedIds.length"
                        type="button"
                        class="db-btn py-2 text-white bg-sky-600"
                        @click="bulkDownload"
                    >
                        {{ $t('button.download_zip') }}
                    </button>
                    <button
                        v-if="permissionChecker('table_qr_view') && selectedIds.length"
                        type="button"
                        class="db-btn py-2 text-white bg-slate-700"
                        @click="printIds(selectedIds)"
                    >
                        {{ $t('button.print_selected') }}
                    </button>
                    <button
                        v-if="permissionChecker('table_qr_regenerate') && selectedIds.length"
                        type="button"
                        class="db-btn py-2 text-white bg-amber-600"
                        @click="regenerateSelected"
                    >
                        {{ $t('button.regenerate_qr') }}
                    </button>
                    <TableCreateComponent
                        :props="props"
                        :restaurants="restaurants"
                        :statusOptions="statusOptions"
                        v-if="permissionChecker('tables_create')"
                    />
                </nav>
            </div>

            <div class="table-filter-div" id="table-filter">
                <form class="p-4 sm:p-5 mb-5" @submit.prevent="search">
                    <div class="row">
                        <div class="col-12 sm:col-6 md:col-4 xl:col-3">
                            <label for="searchTableNumber" class="db-field-title after:hidden">{{ $t("label.table_number") }}</label>
                            <input id="searchTableNumber" v-model="props.search.table_number" type="text" class="db-field-control"/>
                        </div>
                        <div class="col-12 sm:col-6 md:col-4 xl:col-3">
                            <label for="searchName" class="db-field-title after:hidden">{{ $t("label.table_name") }}</label>
                            <input id="searchName" v-model="props.search.name" type="text" class="db-field-control"/>
                        </div>
                        <div class="col-12 sm:col-6 md:col-4 xl:col-3">
                            <label for="searchZone" class="db-field-title after:hidden">{{ $t("label.zone") }}</label>
                            <input id="searchZone" v-model="props.search.zone" type="text" class="db-field-control"/>
                        </div>
                        <div class="col-12 sm:col-6 md:col-4 xl:col-3">
                            <label for="searchStatus" class="db-field-title after:hidden">{{ $t("label.status") }}</label>
                            <vue-select
                                class="db-field-control f-b-custom-select"
                                id="searchStatus"
                                v-model="props.search.status"
                                :options="statusOptions"
                                label-by="name"
                                value-by="id"
                                :closeOnSelect="true"
                                :searchable="true"
                                :clearOnClose="true"
                                placeholder="--"
                                search-placeholder="--"
                            />
                        </div>
                        <div class="col-12 sm:col-6 md:col-4 xl:col-3" v-if="isAdmin">
                            <label for="searchRestaurant" class="db-field-title after:hidden">{{ $t("label.restaurant") }}</label>
                            <vue-select
                                class="db-field-control f-b-custom-select"
                                id="searchRestaurant"
                                v-model="props.search.restaurant_id"
                                :options="restaurants"
                                label-by="name"
                                value-by="id"
                                :closeOnSelect="true"
                                :searchable="true"
                                :clearOnClose="true"
                                placeholder="--"
                                search-placeholder="--"
                            />
                        </div>
                        <div class="col-12">
                            <div class="flex flex-wrap gap-3 mt-4">
                                <button class="db-btn py-2 text-white bg-primary">
                                    <i class="lab lab-line-search lab-font-size-16"></i>
                                    <span>{{ $t("button.search") }}</span>
                                </button>
                                <button type="button" class="db-btn py-2 text-white bg-gray-600" @click="clear">
                                    <i class="lab lab-line-cross lab-font-size-22"></i>
                                    <span>{{ $t("button.clear") }}</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <div class="db-table-responsive">
                <table class="db-table stripe">
                    <thead class="db-table-head">
                    <tr class="db-table-head-tr">
                        <th class="db-table-head-th" v-if="canBulkQr">
                            <input type="checkbox" :checked="allSelected" @change="toggleSelectAll"/>
                        </th>
                        <th class="db-table-head-th">{{ $t("label.table_number") }}</th>
                        <th class="db-table-head-th">{{ $t("label.table_name") }}</th>
                        <th class="db-table-head-th">{{ $t("label.capacity") }}</th>
                        <th class="db-table-head-th">{{ $t("label.zone") }}</th>
                        <th class="db-table-head-th" v-if="isAdmin">{{ $t("label.restaurant") }}</th>
                        <th class="db-table-head-th">{{ $t("label.status") }}</th>
                        <th class="db-table-head-th">{{ $t("label.has_qr") }}</th>
                        <th class="db-table-head-th">{{ $t("label.action") }}</th>
                    </tr>
                    </thead>
                    <tbody class="db-table-body" v-if="tables.length > 0">
                    <tr class="db-table-body-tr" v-for="table in tables" :key="table.id">
                        <td class="db-table-body-td" v-if="canBulkQr">
                            <input type="checkbox" :value="table.id" v-model="selectedIds"/>
                        </td>
                        <td class="db-table-body-td">{{ table.table_number }}</td>
                        <td class="db-table-body-td">{{ table.name }}</td>
                        <td class="db-table-body-td">{{ table.capacity }}</td>
                        <td class="db-table-body-td">{{ table.zone || '-' }}</td>
                        <td class="db-table-body-td" v-if="isAdmin">{{ table.restaurant?.name || '-' }}</td>
                        <td class="db-table-body-td">
                            <span :class="tableStatusClass(table.status)">{{ statusLabel(table.status) }}</span>
                        </td>
                        <td class="db-table-body-td">
                            <span :class="table.has_qr ? 'text-green-600' : 'text-slate-400'">
                                {{ table.has_qr ? $t('label.active') : '-' }}
                            </span>
                        </td>
                        <td class="db-table-body-td">
                            <div class="flex justify-start items-center gap-1.5 flex-wrap">
                                <button
                                    v-if="permissionChecker('table_qr_view') || permissionChecker('table_qr_generate')"
                                    type="button"
                                    class="db-btn py-1 px-2 text-xs text-white bg-indigo-600"
                                    @click="openQr(table)"
                                >
                                    QR
                                </button>
                                <SmIconViewComponent
                                    :link="'admin.tables.show'"
                                    :id="table.id"
                                    v-if="permissionChecker('tables_show')"
                                />
                                <SmModalEditComponent @click="edit(table)" v-if="permissionChecker('tables_edit')"/>
                                <SmIconDeleteComponent @click="destroy(table.id)" v-if="permissionChecker('tables_delete')"/>
                            </div>
                        </td>
                    </tr>
                    </tbody>
                    <tbody class="db-table-body" v-else>
                    <tr class="db-table-body-tr">
                        <td class="db-table-body-td" :colspan="isAdmin ? 9 : 8">
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
import TableCreateComponent from "./TableCreateComponent.vue";
import TableQrModalComponent from "./TableQrModalComponent.vue";
import TableQrPrintSheetComponent from "./TableQrPrintSheetComponent.vue";
import alertService from "../../../services/alertService.js";
import PaginationTextComponent from "../components/pagination/PaginationTextComponent.vue";
import PaginationBox from "../components/pagination/PaginationBox.vue";
import PaginationSMBox from "../components/pagination/PaginationSMBox.vue";
import appService from "../../../services/appService.js";
import TableLimitComponent from "../components/TableLimitComponent.vue";
import FilterComponent from "../components/buttons/collapse/FilterComponent.vue";
import SmIconDeleteComponent from "../components/buttons/SmIconDeleteComponent.vue";
import SmIconViewComponent from "../components/buttons/SmIconViewComponent.vue";
import SmModalEditComponent from "../components/buttons/SmModalEditComponent.vue";
import tableStatusEnum from "../../../enums/modules/tableStatusEnum.js";
import roleEnum from "../../../enums/modules/roleEnum.js";
import {useModal} from "../../../composables/modal.js";
import {useSlide} from "../../../composables/slide.js";
import {useRestaurantTableStore} from "../../../stores/restaurantTable.js";
import {useFrontendSettingStore} from "../../../stores/frontendSetting.js";
import {useRestaurantStore} from "../../../stores/restaurant.js";
import {useAuthStore} from "../../../stores/auth.js";
import VueSimpleAlert from "vue3-simple-alert";

export default {
    name: "TableListComponent",
    components: {
        TableLimitComponent,
        FilterComponent,
        PaginationSMBox,
        PaginationBox,
        PaginationTextComponent,
        TableCreateComponent,
        TableQrModalComponent,
        TableQrPrintSheetComponent,
        LoadingComponent,
        SmIconDeleteComponent,
        SmIconViewComponent,
        SmModalEditComponent,
    },
    setup() {
        const restaurantTableStore = useRestaurantTableStore();
        const frontendSettingStore = useFrontendSettingStore();
        const restaurantStore = useRestaurantStore();
        const authStore = useAuthStore();
        return {restaurantTableStore, frontendSettingStore, restaurantStore, authStore};
    },
    data() {
        return {
            loading: {isActive: false},
            enums: {tableStatusEnum, roleEnum},
            selectedIds: [],
            qrModalVisible: false,
            activeQrId: null,
            activeQr: null,
            printItems: [],
            printInstruction: "",
            props: {
                form: {
                    restaurant_id: null,
                    table_number: "",
                    name: "",
                    capacity: 2,
                    zone: "",
                    status: tableStatusEnum.AVAILABLE,
                    notes: ""
                },
                search: {
                    paginate: 1,
                    page: 1,
                    per_page: 10,
                    order_column: "id",
                    order_type: "desc",
                    table_number: "",
                    name: "",
                    zone: "",
                    status: null,
                    restaurant_id: null,
                },
            },
        };
    },
    mounted() {
        if (this.isAdmin) {
            this.restaurantStore.fetchAllRestaurant().catch(() => {});
        }
        this.list();
    },
    computed: {
        setting() { return this.frontendSettingStore.lists; },
        tables() { return this.restaurantTableStore.lists; },
        pagination() { return this.restaurantTableStore.pagination; },
        paginationPage() { return this.restaurantTableStore.page; },
        restaurants() { return this.restaurantStore.allRestaurants || []; },
        isAdmin() { return Number(this.authStore.info?.role_id) === roleEnum.ADMIN; },
        canBulkQr() {
            return this.permissionChecker('table_qr_view')
                || this.permissionChecker('table_qr_download')
                || this.permissionChecker('table_qr_regenerate');
        },
        allSelected() {
            return this.tables.length > 0 && this.selectedIds.length === this.tables.length;
        },
        statusOptions() {
            return [
                {id: tableStatusEnum.AVAILABLE, name: this.$t("label.available")},
                {id: tableStatusEnum.OCCUPIED, name: this.$t("label.occupied")},
                {id: tableStatusEnum.RESERVED, name: this.$t("label.reserved")},
                {id: tableStatusEnum.CLEANING, name: this.$t("label.cleaning")},
                {id: tableStatusEnum.OUT_OF_SERVICE, name: this.$t("label.out_of_service")},
                {id: tableStatusEnum.INACTIVE, name: this.$t("label.inactive")},
            ];
        },
    },
    methods: {
        permissionChecker(permission) { return appService.permissionChecker(permission); },
        handleSlide: useSlide().handleSlide,
        statusLabel(status) {
            const map = {
                [tableStatusEnum.AVAILABLE]: this.$t("label.available"),
                [tableStatusEnum.OCCUPIED]: this.$t("label.occupied"),
                [tableStatusEnum.RESERVED]: this.$t("label.reserved"),
                [tableStatusEnum.CLEANING]: this.$t("label.cleaning"),
                [tableStatusEnum.OUT_OF_SERVICE]: this.$t("label.out_of_service"),
                [tableStatusEnum.INACTIVE]: this.$t("label.inactive"),
            };
            return map[status] || status;
        },
        tableStatusClass(status) {
            if (status === tableStatusEnum.AVAILABLE) return "db-table-badge text-green-600 bg-green-100";
            if (status === tableStatusEnum.OCCUPIED || status === tableStatusEnum.RESERVED) return "db-table-badge text-amber-600 bg-amber-100";
            if (status === tableStatusEnum.CLEANING) return "db-table-badge text-sky-600 bg-sky-100";
            return "db-table-badge text-red-600 bg-red-100";
        },
        toggleSelectAll(e) {
            this.selectedIds = e.target.checked ? this.tables.map(t => t.id) : [];
        },
        list(page = 1) {
            this.loading.isActive = true;
            this.props.search.page = page;
            this.restaurantTableStore.fetch(this.props.search).then(() => {
                this.loading.isActive = false;
                this.selectedIds = [];
            }).catch(() => { this.loading.isActive = false; });
        },
        search() { this.list(1); },
        clear() {
            this.props.search.table_number = "";
            this.props.search.name = "";
            this.props.search.zone = "";
            this.props.search.status = null;
            this.props.search.restaurant_id = null;
            this.list(1);
        },
        edit(table) {
            useModal().openModal('modal');
            this.restaurantTableStore.edit(table.id);
            this.props.form = {
                restaurant_id: table.restaurant_id,
                table_number: table.table_number,
                name: table.name,
                capacity: table.capacity,
                zone: table.zone || "",
                status: table.status,
                notes: table.notes || ""
            };
        },
        destroy(id) {
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
            ).then(() => {
                this.loading.isActive = true;
                this.restaurantTableStore.destroy({id, search: this.props.search}).then(() => {
                    this.loading.isActive = false;
                    alertService.successFlip(null, this.$t("menu.tables"));
                }).catch((err) => {
                    this.loading.isActive = false;
                    alertService.error(err.response?.data?.message || err.message);
                });
            }).catch(() => {});
        },
        openQr(table) {
            this.activeQrId = table.id;
            this.loading.isActive = true;
            this.restaurantTableStore.fetchQrPreview(table.id).then((res) => {
                this.activeQr = res.data.data?.has_qr ? res.data.data : null;
                this.qrModalVisible = true;
                this.loading.isActive = false;
            }).catch((err) => {
                this.loading.isActive = false;
                alertService.error(err.response?.data?.message || err.message);
            });
        },
        closeQrModal() {
            this.qrModalVisible = false;
            this.activeQr = null;
            this.activeQrId = null;
        },
        generateActiveQr() {
            this.loading.isActive = true;
            this.restaurantTableStore.generateQr(this.activeQrId).then((res) => {
                this.activeQr = res.data.data;
                this.list(this.props.search.page);
                this.loading.isActive = false;
                alertService.successFlip(0, this.$t('label.table_qr'));
            }).catch((err) => {
                this.loading.isActive = false;
                alertService.error(err.response?.data?.message || err.message);
            });
        },
        regenerateActiveQr() {
            return new VueSimpleAlert.confirm(
                this.$t("message.table_qr_regenerate_confirm"),
                this.$t("message.are_you_sure"),
                "warning",
                {
                    confirmButtonText: this.$t("button.regenerate_qr"),
                    cancelButtonText: this.$t("button.no_cancel"),
                    confirmButtonColor: "#1AB759",
                    cancelButtonColor: "#E93C3C"
                }
            ).then(() => {
                this.loading.isActive = true;
                this.restaurantTableStore.regenerateQr(this.activeQrId).then((res) => {
                    this.activeQr = res.data.data;
                    this.list(this.props.search.page);
                    this.loading.isActive = false;
                    alertService.successFlip(1, this.$t('label.table_qr'));
                }).catch((err) => {
                    this.loading.isActive = false;
                    alertService.error(err.response?.data?.message || err.message);
                });
            }).catch(() => {});
        },
        triggerBlobDownload(blob, filename) {
            const url = window.URL.createObjectURL(blob);
            const link = document.createElement('a');
            link.href = url;
            link.download = filename;
            link.click();
            window.URL.revokeObjectURL(url);
        },
        downloadActiveQr(format = 'png') {
            this.loading.isActive = true;
            this.restaurantTableStore.downloadQr(this.activeQrId, format).then((res) => {
                this.triggerBlobDownload(res.data, `table-qr.${format}`);
                this.loading.isActive = false;
            }).catch((err) => {
                this.loading.isActive = false;
                alertService.error(err.response?.data?.message || err.message);
            });
        },
        generateMissing() {
            this.loading.isActive = true;
            this.restaurantTableStore.generateMissingQr().then((res) => {
                this.list(this.props.search.page);
                this.loading.isActive = false;
                alertService.successFlip(0, this.$t('label.table_qr'));
            }).catch((err) => {
                this.loading.isActive = false;
                alertService.error(err.response?.data?.message || err.message);
            });
        },
        bulkDownload() {
            this.loading.isActive = true;
            this.restaurantTableStore.bulkDownloadQr(this.selectedIds).then((res) => {
                this.triggerBlobDownload(res.data, 'table-qr-codes.zip');
                this.loading.isActive = false;
            }).catch((err) => {
                this.loading.isActive = false;
                alertService.error(err.response?.data?.message || err.message);
            });
        },
        regenerateSelected() {
            return new VueSimpleAlert.confirm(
                this.$t("message.table_qr_regenerate_confirm"),
                this.$t("message.are_you_sure"),
                "warning",
                {
                    confirmButtonText: this.$t("button.regenerate_qr"),
                    cancelButtonText: this.$t("button.no_cancel"),
                    confirmButtonColor: "#1AB759",
                    cancelButtonColor: "#E93C3C"
                }
            ).then(() => {
                this.loading.isActive = true;
                this.restaurantTableStore.regenerateSelectedQr(this.selectedIds).then(() => {
                    this.list(this.props.search.page);
                    this.loading.isActive = false;
                    alertService.successFlip(1, this.$t('label.table_qr'));
                }).catch((err) => {
                    this.loading.isActive = false;
                    alertService.error(err.response?.data?.message || err.message);
                });
            }).catch(() => {});
        },
        printIds(ids) {
            this.loading.isActive = true;
            this.restaurantTableStore.printData(ids).then((res) => {
                this.printItems = res.data.data.items || [];
                this.printInstruction = res.data.data.instruction || '';
                this.loading.isActive = false;
                this.$nextTick(() => window.print());
            }).catch((err) => {
                this.loading.isActive = false;
                alertService.error(err.response?.data?.message || err.message);
            });
        },
    },
};
</script>
