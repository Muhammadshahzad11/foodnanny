<template>
    <LoadingComponent :props="loading"/>

    <div class="db-card db-tab-div active">
        <div class="db-card-header border-none">
            <h3 class="db-card-title">{{ $t("menu.kitchen_settings") }}</h3>
            <div class="db-card-filter">
                <TableLimitComponent :method="list" :search="props.search" :page="paginationPage"/>
                <KitchenStationCreateComponent
                    :props="props"
                    :printers="printerOptions"
                    :categories="categoryOptions"
                />
            </div>
        </div>

        <div class="db-table-responsive">
            <table class="db-table stripe">
                <thead class="db-table-head">
                <tr class="db-table-head-tr">
                    <th class="db-table-head-th">{{ $t("label.kitchen_name") }}</th>
                    <th class="db-table-head-th">{{ $t("label.assigned_printer") }}</th>
                    <th class="db-table-head-th">{{ $t("label.categories") }}</th>
                    <th class="db-table-head-th">{{ $t('label.status') }}</th>
                    <th class="db-table-head-th">{{ $t("label.action") }}</th>
                </tr>
                </thead>
                <tbody class="db-table-body" v-if="stations.length > 0">
                <tr class="db-table-body-tr" v-for="station in stations" :key="station.id">
                    <td class="db-table-body-td">{{ station.name }}</td>
                    <td class="db-table-body-td">{{ station.printer?.name || '—' }}</td>
                    <td class="db-table-body-td">
                        <span v-if="(station.categories || []).length">
                            {{ station.categories.map(c => c.name).join(', ') }}
                        </span>
                        <span v-else>—</span>
                    </td>
                    <td class="db-table-body-td">
                        <span :class="statusClass(station.status)">{{ enums.statusEnumArray[station.status] }}</span>
                    </td>
                    <td class="db-table-body-td">
                        <div class="flex justify-start items-center gap-1.5">
                            <SmModalEditComponent @click="edit(station)"/>
                            <SmDeleteComponent @click="destroy(station.id)"/>
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
            <PaginationSMBox :pagination="pagination" :method="list"/>
            <div class="hidden sm:flex sm:flex-1 sm:items-center sm:justify-between">
                <PaginationTextComponent :props="{ page: paginationPage }"/>
                <PaginationBox :pagination="pagination" :method="list"/>
            </div>
        </div>
    </div>
</template>

<script>
import LoadingComponent from "../../../common/LoadingComponent.vue";
import KitchenStationCreateComponent from "./KitchenStationCreateComponent.vue";
import alertService from "../../../../services/alertService.js";
import PaginationTextComponent from "../../components/pagination/PaginationTextComponent.vue";
import PaginationBox from "../../components/pagination/PaginationBox.vue";
import PaginationSMBox from "../../components/pagination/PaginationSMBox.vue";
import appService from "../../../../services/appService.js";
import TableLimitComponent from "../../components/TableLimitComponent.vue";
import SmDeleteComponent from "../../components/buttons/SmDeleteComponent.vue";
import SmModalEditComponent from "../../components/buttons/SmModalEditComponent.vue";
import statusEnum from "../../../../enums/modules/statusEnum.js";
import {useModal} from "../../../../composables/modal.js";
import {useKitchenStationStore} from "../../../../stores/kitchenStation.js";
import {usePrinterStore} from "../../../../stores/printer.js";
import {useItemCategoryStore} from "../../../../stores/itemCategory.js";
import {useFrontendSettingStore} from "../../../../stores/frontendSetting.js";
import VueSimpleAlert from "vue3-simple-alert";

export default {
    name: "KitchenStationListComponent",
    components: {
        TableLimitComponent,
        PaginationSMBox,
        PaginationBox,
        PaginationTextComponent,
        KitchenStationCreateComponent,
        LoadingComponent,
        SmDeleteComponent,
        SmModalEditComponent,
    },
    setup() {
        return {
            kitchenStationStore: useKitchenStationStore(),
            printerStore: usePrinterStore(),
            itemCategoryStore: useItemCategoryStore(),
            frontendSettingStore: useFrontendSettingStore(),
        };
    },
    data() {
        return {
            loading: {isActive: false},
            enums: {
                statusEnum,
                statusEnumArray: {
                    [statusEnum.ACTIVE]: this.$t("label.active"),
                    [statusEnum.INACTIVE]: this.$t("label.inactive"),
                },
            },
            props: {
                form: {
                    name: "",
                    printer_id: null,
                    category_ids: [],
                    sort_order: 0,
                    status: statusEnum.ACTIVE,
                },
                search: {
                    paginate: 1,
                    page: 1,
                    per_page: 10,
                    order_column: "sort_order",
                    order_type: "asc",
                },
            },
            printerOptions: [],
            categoryOptions: [],
        };
    },
    mounted() {
        this.list();
        this.loadOptions();
    },
    computed: {
        setting() {
            return this.frontendSettingStore.lists;
        },
        stations() {
            return this.kitchenStationStore.lists;
        },
        pagination() {
            return this.kitchenStationStore.pagination;
        },
        paginationPage() {
            return this.kitchenStationStore.page;
        },
    },
    methods: {
        statusClass(status) {
            return appService.statusClass(status);
        },
        loadOptions() {
            this.printerStore.fetch({paginate: 0, vuex: false}).then((res) => {
                this.printerOptions = res.data.data || [];
            }).catch(() => {});
            this.itemCategoryStore.fetch({paginate: 0, vuex: false}).then((res) => {
                this.categoryOptions = res.data.data || [];
            }).catch(() => {});
        },
        list(page = 1) {
            this.loading.isActive = true;
            this.props.search.page = page;
            this.kitchenStationStore.fetch(this.props.search).then(() => {
                this.loading.isActive = false;
            }).catch(() => {
                this.loading.isActive = false;
            });
        },
        edit(station) {
            useModal().openModal('modal');
            this.kitchenStationStore.edit(station.id);
            this.props.form = {
                name: station.name,
                printer_id: station.printer_id,
                category_ids: [...(station.category_ids || [])],
                sort_order: station.sort_order || 0,
                status: station.status,
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
                this.kitchenStationStore.destroy({id, search: this.props.search}).then(() => {
                    this.loading.isActive = false;
                    alertService.successFlip(null, this.$t("menu.kitchen_settings"));
                }).catch((err) => {
                    this.loading.isActive = false;
                    alertService.error(err.response?.data?.message);
                });
            }).catch(() => {});
        },
    },
};
</script>
