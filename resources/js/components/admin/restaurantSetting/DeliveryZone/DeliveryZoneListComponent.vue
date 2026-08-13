<template>
    <LoadingComponent :props="loading"/>

    <!-- Create zone form (full page, not modal) -->
    <div class="db-card mb-4">
        <div class="db-card-header border-none flex flex-wrap items-center justify-between gap-3">
            <div>
                <h3 class="db-card-title">{{ $t('label.add_new_delivery_zone') }}</h3>
                <p class="text-sm text-slate-500 mt-1 mb-0">{{ $t('label.zone_create_instruction') }}</p>
            </div>
            <router-link
                :to="{ name: 'admin.deliveryZones.create' }"
                class="h-9 px-3 py-4 flex items-center gap-1 text-sm tracking-wide capitalize rounded-md shadow text-white bg-primary"
            >
                <i class="lab lab-line-add-circle"></i>
                <span>{{ $t('button.add_delivery_zone') }}</span>
            </router-link>
        </div>
        <div class="px-4 pb-4 grid grid-cols-1 sm:grid-cols-2 gap-3 text-sm text-slate-600">
            <div class="flex gap-3 items-start">
                <span class="w-9 h-9 rounded bg-slate-100 flex items-center justify-center text-primary shrink-0">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 11V6a2 2 0 0 0-4 0"/><path d="M14 10V4a2 2 0 0 0-4 0v2"/><path d="M10 10.5V6a2 2 0 0 0-4 0v8"/><path d="M18 8a2 2 0 1 1 4 0v6a8 8 0 0 1-8 8h-2c-2.8 0-4.5-.86-5.99-2.34l-3.6-3.6a2 2 0 0 1 2.83-2.82L7 15"/></svg>
                </span>
                <p class="m-0">{{ $t('label.hand_tool_help') }}</p>
            </div>
            <div class="flex gap-3 items-start">
                <span class="w-9 h-9 rounded bg-slate-100 flex items-center justify-center text-primary shrink-0">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2l8 4.5v9L12 20l-8-4.5v-9L12 2z"/></svg>
                </span>
                <p class="m-0">{{ $t('label.shape_tool_help') }}</p>
            </div>
        </div>
    </div>

    <div class="db-card db-tab-div active">
        <div class="db-card-header border-none">
            <h3 class="db-card-title">{{ $t("menu.delivery_zones") }}</h3>
            <div class="db-card-filter flex flex-wrap gap-2 items-center">
                <input
                    v-model="props.search.search"
                    type="text"
                    class="db-field-control w-40 sm:w-56"
                    :placeholder="$t('label.search_zones')"
                    @keyup.enter="list(1)"
                />
                <select v-model="props.search.status" class="db-field-control w-32" @change="list(1)">
                    <option value="">{{ $t('label.status') }}</option>
                    <option :value="enums.statusEnum.ACTIVE">{{ $t('label.active') }}</option>
                    <option :value="enums.statusEnum.INACTIVE">{{ $t('label.inactive') }}</option>
                </select>
                <TableLimitComponent :method="list" :search="props.search" :page="paginationPage"/>
            </div>
        </div>

        <div class="db-table-responsive">
            <table class="db-table stripe">
                <thead class="db-table-head">
                <tr class="db-table-head-tr">
                    <th class="db-table-head-th">#</th>
                    <th class="db-table-head-th">{{ $t("label.zone_name") }}</th>
                    <th class="db-table-head-th">{{ $t("label.display_name") }}</th>
                    <th class="db-table-head-th">{{ $t("label.zone_admin") }}</th>
                    <th class="db-table-head-th">{{ $t("label.restaurants") }}</th>
                    <th class="db-table-head-th">{{ $t("label.base_delivery_fee") }}</th>
                    <th class="db-table-head-th">{{ $t('label.status') }}</th>
                    <th class="db-table-head-th">{{ $t("label.action") }}</th>
                </tr>
                </thead>
                <tbody class="db-table-body" v-if="zones.length > 0">
                <tr class="db-table-body-tr" v-for="(zone, index) in zones" :key="zone.id">
                    <td class="db-table-body-td">{{ (paginationPage.from || 1) + index }}</td>
                    <td class="db-table-body-td">{{ zone.name }}</td>
                    <td class="db-table-body-td">{{ zone.display_name }}</td>
                    <td class="db-table-body-td">{{ zone.admin_name || '—' }}</td>
                    <td class="db-table-body-td">{{ zone.restaurants_count || 0 }}</td>
                    <td class="db-table-body-td">{{ zone.base_delivery_fee_flat }}</td>
                    <td class="db-table-body-td">
                        <span :class="statusClass(zone.status)">
                            {{ enums.statusEnumArray[zone.status] }}
                        </span>
                    </td>
                    <td class="db-table-body-td">
                        <div class="flex justify-start items-center gap-1.5">
                            <router-link
                                :to="{ name: 'admin.deliveryZones.edit', params: { id: zone.id } }"
                                class="h-7 w-7 leading-7 text-center rounded text-blue-600 bg-blue-100"
                                :title="$t('button.edit') || 'Edit'"
                            >
                                <i class="lab lab-line-edit"></i>
                            </router-link>
                            <router-link
                                :to="{ name: 'admin.deliveryZones.settings', params: { id: zone.id } }"
                                class="h-7 w-7 leading-7 text-center rounded text-slate-700 bg-slate-100"
                                :title="$t('label.zone_settings')"
                            >
                                <i class="lab lab-line-system-settings"></i>
                            </router-link>
                            <button
                                v-if="zone.status === enums.statusEnum.ACTIVE"
                                type="button"
                                class="h-7 w-7 leading-7 text-center rounded text-orange-600 bg-orange-100"
                                :title="$t('button.deactivate')"
                                @click="deactivate(zone.id)"
                            >
                                <i class="lab lab-line-close-circle"></i>
                            </button>
                            <SmDeleteComponent @click="destroy(zone.id)"/>
                        </div>
                    </td>
                </tr>
                </tbody>
                <tbody class="db-table-body" v-else>
                <tr class="db-table-body-tr">
                    <td class="db-table-body-td" colspan="8">
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
import alertService from "../../../../services/alertService.js";
import PaginationTextComponent from "../../components/pagination/PaginationTextComponent.vue";
import PaginationBox from "../../components/pagination/PaginationBox.vue";
import PaginationSMBox from "../../components/pagination/PaginationSMBox.vue";
import appService from "../../../../services/appService.js";
import TableLimitComponent from "../../components/TableLimitComponent.vue";
import SmDeleteComponent from "../../components/buttons/SmDeleteComponent.vue";
import statusEnum from "../../../../enums/modules/statusEnum.js";
import deliveryChargeTypeEnum from "../../../../enums/modules/deliveryChargeTypeEnum.js";
import {useRestaurantDeliveryZoneStore} from "../../../../stores/restaurantDeliveryZone.js";
import {useFrontendSettingStore} from "../../../../stores/frontendSetting.js";
import VueSimpleAlert from "vue3-simple-alert";

export default {
    name: "DeliveryZoneListComponent",
    components: {
        TableLimitComponent,
        PaginationSMBox,
        PaginationBox,
        PaginationTextComponent,
        LoadingComponent,
        SmDeleteComponent,
    },
    setup() {
        const zoneStore = useRestaurantDeliveryZoneStore();
        const frontendSettingStore = useFrontendSettingStore();
        return {zoneStore, frontendSettingStore};
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
                chargeType: deliveryChargeTypeEnum,
                chargeTypeLabel: {
                    [deliveryChargeTypeEnum.FIXED]: this.$t("label.fixed"),
                    [deliveryChargeTypeEnum.PER_KM]: this.$t("label.per_km"),
                    [deliveryChargeTypeEnum.RANGE]: this.$t("label.range"),
                },
            },
            props: {
                search: {
                    paginate: 1,
                    page: 1,
                    per_page: 10,
                    order_column: "id",
                    order_type: "desc",
                    search: "",
                    status: "",
                },
            },
        };
    },
    mounted() {
        this.list();
    },
    computed: {
        setting() {
            return this.frontendSettingStore.lists;
        },
        zones() {
            return this.zoneStore.lists;
        },
        pagination() {
            return this.zoneStore.pagination;
        },
        paginationPage() {
            return this.zoneStore.page;
        },
    },
    methods: {
        statusClass(status) {
            return appService.statusClass(status);
        },
        list(page = 1) {
            this.loading.isActive = true;
            this.props.search.page = page;
            this.zoneStore.fetch(this.props.search).then(() => {
                this.loading.isActive = false;
            }).catch(() => {
                this.loading.isActive = false;
            });
        },
        deactivate(id) {
            this.loading.isActive = true;
            this.zoneStore.deactivate({id, search: this.props.search}).then(() => {
                this.loading.isActive = false;
            }).catch((err) => {
                this.loading.isActive = false;
                alertService.error(err.response?.data?.message || err);
            });
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
                    cancelButtonColor: "#E93C3C",
                }
            ).then(() => {
                this.loading.isActive = true;
                this.zoneStore.destroy({id, search: this.props.search}).then(() => {
                    this.loading.isActive = false;
                }).catch((err) => {
                    this.loading.isActive = false;
                    alertService.error(err.response?.data?.message || err);
                });
            }).catch(() => {});
        },
    },
};
</script>
