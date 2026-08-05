<template>
    <div class="db-card">
        <div class="db-card-header border-none">
            <h3 class="db-card-title">{{ $t('label.addon') }}</h3>
            <div class="db-card-filter">
                <TableLimitComponent @click.prevent="handlePaper" :method="list" :search="addonProps.search"
                    :page="paginationPage" />
                <ItemAddonCreateComponent :props="addonProps" v-if="permissionChecker('items_create')" />
            </div>
        </div>
        <div class="db-table-responsive">
            <table class="db-table stripe">
                <thead class="db-table-head">
                    <tr class="db-table-head-tr">
                        <th class="db-table-head-th">{{ $t("label.name") }}</th>
                        <th class="db-table-head-th">{{ $t("label.price") }}</th>
                        <th class="db-table-head-th">{{ $t("label.status") }}</th>
                        <th v-if="permissionChecker('items_delete')" class="db-table-head-th">{{ $t("label.action") }}</th>
                    </tr>
                </thead>
                <tbody class="db-table-body" v-if="addons.length > 0">
                    <tr class="db-table-body-tr" v-for="addon in addons" :key="addon">
                        <td class="db-table-body-td">
                            {{ addon.addon_item_name }}<br>
                            <span v-if="addon.variation_names.length > 0"
                                v-for="(variationName, index) in addon.variation_names">
                                <span>{{ variationName.attribute_name }} : {{ variationName.name }}
                                    <span v-if="index + 1 < addon.variation_names.length">, </span>
                                </span>
                            </span>
                        </td>
                        <td class="db-table-body-td">
                            {{ addon.total_flat_price }}
                        </td>
                        <td class="db-table-body-td">
                            <span :class="statusClass(addon.addon_item_status)">
                                {{ enums.statusEnumArray[addon.addon_item_status] }}
                            </span>
                        </td>
                        <td class="db-table-body-td" v-if="permissionChecker('items_delete')">
                            <SmIconDeleteComponent @click="destroy(addon.id)" />
                        </td>
                    </tr>
                </tbody>
                <tbody class="db-table-body" v-else>
                    <tr class="db-table-body-tr">
                        <td class="db-table-body-td" colspan="4">
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
import alertService from "../../../../services/alertService.js";
import statusEnum from "../../../../enums/modules/statusEnum.js";
import appService from "../../../../services/appService.js";
import SmIconDeleteComponent from "../../components/buttons/SmIconDeleteComponent.vue";
import ItemAddonCreateComponent from "./ItemAddonCreateComponent.vue";
import PaginationTextComponent from "../../components/pagination/PaginationTextComponent.vue";
import PaginationBox from "../../components/pagination/PaginationBox.vue";
import PaginationSMBox from "../../components/pagination/PaginationSMBox.vue";
import TableLimitComponent from "../../components/TableLimitComponent.vue";
import { usePaper } from "../../../../composables/paper.js";
import { useFrontendSettingStore } from "../../../../stores/frontendSetting.js";
import { useItemAddonStore } from "../../../../stores/itemAddon.js";
import VueSimpleAlert from "vue3-simple-alert";

export default {
    name: "ItemAddonListComponent",
    components: {
        ItemAddonCreateComponent, 
        SmIconDeleteComponent,
        PaginationSMBox,
        PaginationBox,
        PaginationTextComponent,
        TableLimitComponent
    },
    setup() {
        const frontendSettingStore = useFrontendSettingStore();
        const itemAddonStore = useItemAddonStore();

        return {
            frontendSettingStore,
            itemAddonStore
        }
    },
    props: {
        item: { type: Number }
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
                },
            },
            addonProps: {
                id: this.item,
                form: {
                    addon_item_id: null,
                    addon_item_variation: {}
                },
                search: {
                    id: this.item,
                    paginate: 1,
                    page: 1,
                    per_page: 10,
                    order_column: 'id',
                    order_type: 'desc'
                }
            },
            variations: [],
            handlePaper: usePaper().handlePaper
        }
    },
    mounted() {
        this.list();
    },
    computed: {
        setting: function () {
            return this.frontendSettingStore.lists;
        },
        addons: function () {
            return this.itemAddonStore.lists;
        },
        pagination: function () {
            return this.itemAddonStore.pagination;
        },
        paginationPage: function () {
            return this.itemAddonStore.page;
        },
    },
    methods: {
        permissionChecker(e) {
            return appService.permissionChecker(e);
        },
        statusClass: function (status) {
            return appService.statusClass(status);
        },
        list: function (page = 1) {
            this.loading.isActive = true;
            this.addonProps.search.page = page;
            this.itemAddonStore.fetch(this.addonProps.search).then((res) => {
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
                    this.itemAddonStore.destroy({ item: this.item, id: id, search: this.addonProps.search }).then((res) => {
                        this.loading.isActive = false;
                        alertService.successFlip(null, this.$t('label.addon'));
                    }).catch((err) => {
                        this.loading.isActive = false;
                        alertService.error(err.response.data.message);
                    })
                } catch (err) {
                    this.loading.isActive = false;
                    alertService.error(err.response.data.message);
                }
            }).catch((err) => {
                this.loading.isActive = false;
            });
        }
    }
}
</script>
