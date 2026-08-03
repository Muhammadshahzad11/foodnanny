<template>
    <LoadingComponent :props="loading"/>
    <TableQrModalComponent
        :visible="qrModalVisible"
        :qr="activeQr"
        @close="qrModalVisible = false"
        @generate="generateQr"
        @regenerate="regenerateQr"
        @download="downloadQr"
        @print="printQr"
    />
    <TableQrPrintSheetComponent :items="printItems" :instruction="printInstruction"/>

    <div class="col-12" v-if="table">
        <div class="db-card mb-4">
            <div class="db-card-header">
                <h3 class="db-card-title">{{ table.name }} ({{ table.table_number }})</h3>
                <div class="flex flex-wrap gap-2">
                    <button
                        v-if="permissionChecker('table_qr_view') || permissionChecker('table_qr_generate')"
                        type="button"
                        class="db-btn py-2 text-white bg-indigo-600"
                        @click="openQr"
                    >
                        {{ $t('label.table_qr') }}
                    </button>
                    <button
                        v-if="permissionChecker('tables_edit') && table.status !== enums.tableStatusEnum.AVAILABLE"
                        type="button"
                        class="db-btn py-2 text-white bg-primary"
                        @click="setStatus(enums.tableStatusEnum.AVAILABLE)"
                    >
                        {{ $t("label.available") }}
                    </button>
                    <button
                        v-if="permissionChecker('tables_edit') && table.status !== enums.tableStatusEnum.INACTIVE"
                        type="button"
                        class="db-btn py-2 text-white bg-gray-600"
                        @click="setStatus(enums.tableStatusEnum.INACTIVE)"
                    >
                        {{ $t("label.inactive") }}
                    </button>
                    <router-link :to="{ name: 'admin.tables.list' }" class="db-btn py-2 text-white bg-slate-500">
                        {{ $t("button.close") }}
                    </router-link>
                </div>
            </div>
            <div class="db-card-body">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm text-slate-500">{{ $t("label.table_number") }}</p>
                        <p class="font-medium">{{ table.table_number }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-slate-500">{{ $t("label.table_name") }}</p>
                        <p class="font-medium">{{ table.name }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-slate-500">{{ $t("label.capacity") }}</p>
                        <p class="font-medium">{{ table.capacity }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-slate-500">{{ $t("label.zone") }}</p>
                        <p class="font-medium">{{ table.zone || '-' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-slate-500">{{ $t("label.restaurant") }}</p>
                        <p class="font-medium">{{ table.restaurant?.name || '-' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-slate-500">{{ $t("label.status") }}</p>
                        <p class="font-medium">{{ statusLabel(table.status) }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-slate-500">{{ $t("label.has_qr") }}</p>
                        <p class="font-medium">{{ table.has_qr ? $t('label.active') : '-' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-slate-500">UUID</p>
                        <p class="font-medium break-all text-sm">{{ table.uuid || '-' }}</p>
                    </div>
                    <div class="md:col-span-2" v-if="table.qr_url">
                        <p class="text-sm text-slate-500">{{ $t('label.table_qr') }} URL</p>
                        <p class="font-medium break-all text-sm">{{ table.qr_url }}</p>
                    </div>
                    <div class="md:col-span-2">
                        <p class="text-sm text-slate-500">{{ $t("label.notes") }}</p>
                        <p class="font-medium">{{ table.notes || '-' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-slate-500">{{ $t("label.created_by") }}</p>
                        <p class="font-medium">{{ table.created_by?.name || '-' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-slate-500">{{ $t("label.updated_by") }}</p>
                        <p class="font-medium">{{ table.updated_by?.name || '-' }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import LoadingComponent from "../../common/LoadingComponent.vue";
import TableQrModalComponent from "./TableQrModalComponent.vue";
import TableQrPrintSheetComponent from "./TableQrPrintSheetComponent.vue";
import tableStatusEnum from "../../../enums/modules/tableStatusEnum.js";
import alertService from "../../../services/alertService.js";
import appService from "../../../services/appService.js";
import {useRestaurantTableStore} from "../../../stores/restaurantTable.js";
import VueSimpleAlert from "vue3-simple-alert";

export default {
    name: "TableShowComponent",
    components: {LoadingComponent, TableQrModalComponent, TableQrPrintSheetComponent},
    setup() {
        const restaurantTableStore = useRestaurantTableStore();
        return {restaurantTableStore};
    },
    data() {
        return {
            loading: {isActive: false},
            enums: {tableStatusEnum},
            qrModalVisible: false,
            activeQr: null,
            printItems: [],
            printInstruction: "",
        };
    },
    mounted() {
        this.fetch();
    },
    computed: {
        table() {
            return this.restaurantTableStore.show;
        },
    },
    methods: {
        permissionChecker(permission) {
            return appService.permissionChecker(permission);
        },
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
        fetch() {
            this.loading.isActive = true;
            this.restaurantTableStore.show(this.$route.params.id).then(() => {
                this.loading.isActive = false;
            }).catch((err) => {
                this.loading.isActive = false;
                alertService.error(err.response?.data?.message || err.message);
            });
        },
        setStatus(status) {
            this.loading.isActive = true;
            this.restaurantTableStore.changeStatus({
                id: this.table.id,
                status,
                search: {paginate: 1, page: 1, per_page: 10}
            }).then(() => this.restaurantTableStore.show(this.table.id)).then(() => {
                this.loading.isActive = false;
                alertService.successFlip(1, this.$t("menu.tables"));
            }).catch((err) => {
                this.loading.isActive = false;
                alertService.error(err.response?.data?.message || err.message);
            });
        },
        openQr() {
            this.loading.isActive = true;
            this.restaurantTableStore.fetchQrPreview(this.table.id).then((res) => {
                this.activeQr = res.data.data?.has_qr ? res.data.data : null;
                this.qrModalVisible = true;
                this.loading.isActive = false;
            }).catch((err) => {
                this.loading.isActive = false;
                alertService.error(err.response?.data?.message || err.message);
            });
        },
        generateQr() {
            this.loading.isActive = true;
            this.restaurantTableStore.generateQr(this.table.id).then((res) => {
                this.activeQr = res.data.data;
                return this.restaurantTableStore.show(this.table.id);
            }).then(() => {
                this.loading.isActive = false;
                alertService.successFlip(0, this.$t('label.table_qr'));
            }).catch((err) => {
                this.loading.isActive = false;
                alertService.error(err.response?.data?.message || err.message);
            });
        },
        regenerateQr() {
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
                this.restaurantTableStore.regenerateQr(this.table.id).then((res) => {
                    this.activeQr = res.data.data;
                    return this.restaurantTableStore.show(this.table.id);
                }).then(() => {
                    this.loading.isActive = false;
                    alertService.successFlip(1, this.$t('label.table_qr'));
                }).catch((err) => {
                    this.loading.isActive = false;
                    alertService.error(err.response?.data?.message || err.message);
                });
            }).catch(() => {});
        },
        downloadQr(format = 'png') {
            this.loading.isActive = true;
            this.restaurantTableStore.downloadQr(this.table.id, format).then((res) => {
                const url = window.URL.createObjectURL(res.data);
                const link = document.createElement('a');
                link.href = url;
                link.download = `table-qr.${format}`;
                link.click();
                window.URL.revokeObjectURL(url);
                this.loading.isActive = false;
            }).catch((err) => {
                this.loading.isActive = false;
                alertService.error(err.response?.data?.message || err.message);
            });
        },
        printQr() {
            this.loading.isActive = true;
            this.restaurantTableStore.printData([this.table.id]).then((res) => {
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
