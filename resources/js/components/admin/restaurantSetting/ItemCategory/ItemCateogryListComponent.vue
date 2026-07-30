<template>
    <LoadingComponent :props="loading" />

    <div class="db-card db-tab-div active">
        <div class="db-card-header border-none">
            <h3 class="db-card-title">{{ $t('menu.item_categories') }}</h3>
            <div class="db-card-filter">
                <TableLimitComponent :method="list" :search="props.search" :page="paginationPage" />
                <ItemCategoryCreateComponent :props="props" />
            </div>
        </div>
        <div class="db-table-responsive">
            <table class="db-table stripe">
                <thead class="db-table-head">
                    <tr class="db-table-head-tr">
                        <th class="db-table-head-th"><i class="lab lab-fill-list text-lg"></i></th>
                        <th class="db-table-head-th">{{ $t('label.name') }}</th>
                        <th class="db-table-head-th">{{ $t('label.status') }}</th>
                        <th class="db-table-head-th">{{ $t('label.action') }}</th>
                    </tr>
                </thead>
                <draggable tag="tbody" class="db-table-body" v-if="itemCategories.length > 0" v-model="modalSortItemCategories" @end="sortItemCategory" :handle="'.drag-handle'">
                    <tr class="db-table-body-tr" v-for="itemCategory in itemCategories" :key="itemCategory">
                        <td class="db-table-body-td"><i class="lab lab-fill-move cursor-move drag-handle text-lg"></i></td>
                        <td class="db-table-body-td">{{ itemCategory.name }}</td>
                        <td class="db-table-body-td">
                            <span :class="statusClass(itemCategory.status)">
                                {{ enums.statusEnumArray[itemCategory.status] }}
                            </span>
                        </td>
                        <td class="db-table-body-td">
                            <div class="flex justify-start items-center sm:items-start sm:justify-start gap-1.5">
                                <SmViewComponent :link="'admin.restaurantSettings.itemCategory.show'" :id="itemCategory.id" />
                                <SmModalEditComponent @click="edit(itemCategory)" />
                                <SmDeleteComponent @click="destroy(itemCategory.id)" />
                            </div>
                        </td>
                    </tr>
                </draggable>
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
import LoadingComponent from "../../../common/LoadingComponent.vue";
import ItemCategoryCreateComponent from "./ItemCategoryCreateComponent.vue";
import alertService from "../../../../services/alertService.js";
import PaginationTextComponent from "../../components/pagination/PaginationTextComponent.vue";
import PaginationBox from "../../components/pagination/PaginationBox.vue";
import PaginationSMBox from "../../components/pagination/PaginationSMBox.vue";
import appService from "../../../../services/appService.js";
import statusEnum from "../../../../enums/modules/statusEnum.js";
import TableLimitComponent from "../../components/TableLimitComponent.vue";
import SmDeleteComponent from "../../components/buttons/SmDeleteComponent.vue";
import SmModalEditComponent from "../../components/buttons/SmModalEditComponent.vue";
import SmViewComponent from "../../components/buttons/SmViewComponent.vue";
import { useModal } from "../../../../composables/modal.js";
import { VueDraggableNext } from 'vue-draggable-next'
import {useItemCategoryStore} from "../../../../stores/itemCategory.js";
import {useFrontendSettingStore} from "../../../../stores/frontendSetting.js";
import VueSimpleAlert from "vue3-simple-alert";

export default {
    name: "ItemCategoryListComponent",
    components: {
        TableLimitComponent,
        PaginationSMBox,
        PaginationBox,
        PaginationTextComponent,
        ItemCategoryCreateComponent,
        LoadingComponent,
        SmDeleteComponent,
        SmModalEditComponent,
        SmViewComponent,
        draggable: VueDraggableNext
    },
    setup() {
        const itemCategoryStore = useItemCategoryStore();
        const frontendSettingStore = useFrontendSettingStore();
        return {
            itemCategoryStore,
            frontendSettingStore
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
            props: {
                form: {
                    name: "",
                    status: statusEnum.ACTIVE,
                    description: ""
                },
                search: {
                    paginate: 1,
                    page: 1,
                    per_page: 10,
                    order_column: 'sort',
                    order_type: 'asc'
                }
            },
            modalSortItemCategories: []
        }
    },
    computed: {
        setting: function () {
            return this.frontendSettingStore.lists;
        },
        itemCategories: function () {
            this.modalSortItemCategories = this.itemCategoryStore.lists;
            return this.itemCategoryStore.lists;
        },
        pagination: function () {
            return this.itemCategoryStore.pagination;
        },
        paginationPage: function () {
            return this.itemCategoryStore.page;
        }
    },
    mounted() {
        this.list();
    },
    methods: {
        statusClass: function (status) {
            return appService.statusClass(status);
        },
        list: function (page = 1) {
            this.loading.isActive = true;
            this.props.search.page = page;
            this.itemCategoryStore.fetch(this.props.search).then(res => {
                this.modalSortItemCategories = res.data.data;
                this.loading.isActive = false;
            }).catch((err) => {
                this.loading.isActive = false;
            });
        },
        edit: function (itemCategory) {
            useModal().openModal('modal');
            this.loading.isActive = true;
            this.itemCategoryStore.edit(itemCategory.id);
            this.props.form = {
                name: itemCategory.name,
                status: itemCategory.status,
                description: itemCategory.description
            };
            this.loading.isActive = false;
        },
        sortItemCategory: function () {
            const sortedIds = this.modalSortItemCategories.map(category => category.id);
            this.itemCategoryStore.sort({form: {category_id: sortedIds}, search: this.props.search}).then((res) => {
                this.list();
            }).catch((err) => {
                alertService.error(err.response.data.message);
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
                    this.itemCategoryStore.destroy({ id: id, search: this.props.search }).then((res) => {
                        this.loading.isActive = false;
                        alertService.successFlip(null, this.$t('menu.item_categories'));
                    }).catch((err) => {
                        this.loading.isActive = false;
                       if (err.response.data.status !== "undefined" && err.response.data.status === false) {
                            alertService.error(err.response.data.message)
                        } else {
                            this.errors = err.response.data.errors;
                        }
                    })
                } catch (err) {
                    this.loading.isActive = false;
                    alertService.error(err.response.data.message);
                }
            }).catch((err) => {
                this.loading.isActive = false;
            })
        }
    }
}
</script>
