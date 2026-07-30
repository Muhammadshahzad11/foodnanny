<template>
    <div class="db-card">
        <div class="db-card-header border-none">
            <h3 class="db-card-title">{{ $t('label.extra') }}</h3>
            <div class="db-card-filter">
                <TableLimitComponent @click.prevent="handlePaper" :method="list" :search="extraProps.search"
                    :page="paginationPage" />
                <ItemExtraCreateComponent :props="extraProps" v-if="permissionChecker('items_create')" />
            </div>
        </div>
        <div class="db-table-responsive">
            <table class="db-table stripe">
                <thead class="db-table-head">
                    <tr class="db-table-head-tr">
                        <th class="db-table-head-th">{{ $t("label.name") }}</th>
                        <th class="db-table-head-th">{{ $t("label.price") }}</th>
                        <th class="db-table-head-th">{{ $t("label.status") }}</th>
                        <th v-if="permissionChecker('items_edit') || permissionChecker('items_delete')"
                            class="db-table-head-th">{{ $t("label.action") }}</th>
                    </tr>
                </thead>
                <tbody class="db-table-body" v-if="extras.length > 0">
                    <tr class="db-table-body-tr" v-for="extra in extras" :key="extra">
                        <td class="db-table-body-td">
                            {{ extra.name }}
                        </td>
                        <td class="db-table-body-td">
                            {{ extra.flat_price }}
                        </td>
                        <td class="db-table-body-td">
                            <span :class="statusClass(extra.status)">
                                {{ enums.statusEnumArray[extra.status] }}
                            </span>
                        </td>
                        <td class="db-table-body-td"
                            v-if="permissionChecker('items_edit') || permissionChecker('items_delete')">
                            <SmIconModalEditComponent @click="edit(extra)" v-if="permissionChecker('items_edit')" />
                            <SmIconDeleteComponent @click="destroy(extra.id)"
                                v-if="permissionChecker('items_delete')" />
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
import SmIconModalEditComponent from "../../components/buttons/SmIconModalEditComponent.vue";
import ItemExtraCreateComponent from "./ItemExtraCreateComponent.vue";
import { useModal } from "../../../../composables/modal.js";
import PaginationTextComponent from "../../components/pagination/PaginationTextComponent.vue";
import PaginationBox from "../../components/pagination/PaginationBox.vue";
import PaginationSMBox from "../../components/pagination/PaginationSMBox.vue";
import TableLimitComponent from "../../components/TableLimitComponent.vue";
import { usePaper } from "../../../../composables/paper.js";
import { useFrontendSettingStore } from "../../../../stores/frontendSetting.js";
import { useItemExtraStore } from "../../../../stores/itemExtra.js";
import VueSimpleAlert from "vue3-simple-alert";

export default {
    name: "ItemVariationListComponent",
    components: {
        ItemExtraCreateComponent, 
        SmIconModalEditComponent,
        SmIconDeleteComponent,
        PaginationSMBox,
        PaginationBox,
        PaginationTextComponent,
        TableLimitComponent
    },
    props: {
        item: { type: Number }
    },
    setup() {
        const frontendSettingStore = useFrontendSettingStore();
        const itemExtraStore = useItemExtraStore();
        return {
            frontendSettingStore,
            itemExtraStore
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
            extraProps: {
                id: 0,
                form: {
                    name: "",
                    price: null,
                    status: statusEnum.ACTIVE
                },
                search: {
                    paginate: 1,
                    page: 1,
                    per_page: 10,
                    order_column: 'id',
                    order_type: 'desc'
                }
            },
            handlePaper: usePaper().handlePaper
        }
    },
    mounted() {
        this.extraProps.id = this.item;
        this.extraProps.search.id = this.item;
        this.list();
    },
    computed: {
        setting: function () {
            return this.frontendSettingStore.lists;
        },
        extras: function () {
            return this.itemExtraStore.lists;
        },
        pagination: function () {
            return this.itemExtraStore.pagination;
        },
        paginationPage: function () {
            return this.itemExtraStore.page;
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
            this.extraProps.search.page = page;
            this.itemExtraStore.fetch(this.extraProps.search).then((res) => {
                this.loading.isActive = false;
            }).catch((err) => {
                this.loading.isActive = false;
            });
        },
        edit: function (itemExtra) {
            useModal().openModal('extraModal');
            this.loading.isActive = true;
            this.itemExtraStore.edit(itemExtra.id);
            this.loading.isActive = false;
            this.extraProps.form = {
                name: itemExtra.name,
                price: itemExtra.flat_price,
                status: itemExtra.status
            };
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
                    this.itemExtraStore.destroy({ item: this.item, id: id, search: this.extraProps.search }).then((res) => {
                        this.loading.isActive = false;
                        alertService.successFlip(null, this.$t('label.extra'));
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
