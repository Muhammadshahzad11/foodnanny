<template>
    <LoadingComponent :props="loading"/>
    <div class="col-12" v-if="table">
        <div class="db-card mb-4">
            <div class="db-card-header">
                <h3 class="db-card-title">{{ table.name }} ({{ table.table_number }})</h3>
                <div class="flex gap-2">
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
import tableStatusEnum from "../../../enums/modules/tableStatusEnum.js";
import alertService from "../../../services/alertService.js";
import appService from "../../../services/appService.js";
import {useRestaurantTableStore} from "../../../stores/restaurantTable.js";

export default {
    name: "TableShowComponent",
    components: {LoadingComponent},
    setup() {
        const restaurantTableStore = useRestaurantTableStore();
        return {restaurantTableStore};
    },
    data() {
        return {
            loading: {isActive: false},
            enums: {tableStatusEnum},
        };
    },
    mounted() {
        this.fetch();
    },
    computed: {
        table: function () {
            return this.restaurantTableStore.show;
        },
    },
    methods: {
        permissionChecker: function (permission) {
            return appService.permissionChecker(permission);
        },
        statusLabel: function (status) {
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
        fetch: function () {
            this.loading.isActive = true;
            this.restaurantTableStore.show(this.$route.params.id).then(() => {
                this.loading.isActive = false;
            }).catch((err) => {
                this.loading.isActive = false;
                alertService.error(err.response?.data?.message || err.message);
            });
        },
        setStatus: function (status) {
            this.loading.isActive = true;
            this.restaurantTableStore.changeStatus({
                id: this.table.id,
                status: status,
                search: {paginate: 1, page: 1, per_page: 10}
            }).then(() => {
                return this.restaurantTableStore.show(this.table.id);
            }).then(() => {
                this.loading.isActive = false;
                alertService.successFlip(1, this.$t("menu.tables"));
            }).catch((err) => {
                this.loading.isActive = false;
                alertService.error(err.response?.data?.message || err.message);
            });
        },
    },
};
</script>
