<template>
    <LoadingComponent :props="loading"/>
    <div class="db-card db-tab-div active">
        <div class="db-card-header border-none">
            <h3 class="db-card-title">{{ $t("menu.cuisines") }}</h3>
            <div class="db-card-filter">
                <TableLimitComponent :method="list" :search="props.search" :page="paginationPage"/>
                <CuisineCreateComponent :props="props"/>
            </div>
        </div>
        <div class="db-table-responsive">
            <table class="db-table stripe">
                <thead class="db-table-head">
                    <tr class="db-table-head-tr">
                        <th class="db-table-head-th"><i class="lab lab-fill-list text-lg"></i></th>
                        <th class="db-table-head-th">
                            {{ $t("label.name") }}
                        </th>
                        <th class="db-table-head-th">
                            {{ $t("label.status") }}
                        </th>
                        <th class="db-table-head-th">
                            {{ $t("label.action") }}
                        </th>
                    </tr>
                </thead>
                <draggable tag="tbody" class="db-table-body" v-if="cuisines.length > 0" v-model="itemCuisines" @end="sort" :handle="'.drag-handle'">
                    <tr class="db-table-body-tr" v-for="cuisine in cuisines" :key="cuisine">
                        <td class="db-table-body-td">
                            <i class="lab lab-fill-move cursor-move drag-handle text-lg"></i>
                        </td>
                        <td class="db-table-body-td">
                            {{ textShortener(cuisine.name) }}
                        </td>
                        <td class="db-table-body-td">
                            <span :class="statusClass(cuisine.status)">
                                {{ enums.statusEnumArray[cuisine.status] }}
                            </span>
                        </td>
                        <td class="db-table-body-td">
                            <div class="flex justify-start items-center sm:items-start sm:justify-start gap-1.5">
                                <SmViewComponent :link="'admin.cuisines.show'" :id="cuisine.id"/>
                                <SmModalEditComponent @click="edit(cuisine)"/>
                                <SmDeleteComponent @click="destroy(cuisine.id)"/>
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
import {useCuisineStore} from "../../../../stores/cuisine.js";
import statusEnum from "../../../../enums/modules/statusEnum.js";
import {useFrontendSettingStore} from "../../../../stores/frontendSetting.js";
import appService from "../../../../services/appService.js";
import alertService from "../../../../services/alertService.js";
import {VueDraggableNext} from 'vue-draggable-next'
import SmViewComponent from "../../components/buttons/SmViewComponent.vue";
import SmModalEditComponent from "../../components/buttons/SmModalEditComponent.vue";
import {useModal} from "../../../../composables/modal.js";
import SmDeleteComponent from "../../components/buttons/SmDeleteComponent.vue";
import PaginationSMBox from "../../components/pagination/PaginationSMBox.vue";
import PaginationTextComponent from "../../components/pagination/PaginationTextComponent.vue";
import PaginationBox from "../../components/pagination/PaginationBox.vue";
import TableLimitComponent from "../../components/TableLimitComponent.vue";
import CuisineCreateComponent from "./CuisineCreateComponent.vue";
import VueSimpleAlert from "vue3-simple-alert";

export default {
    name: "CuisineListComponent",
    components: {
        CuisineCreateComponent,
        TableLimitComponent,
        PaginationBox,
        PaginationTextComponent,
        PaginationSMBox,
        SmDeleteComponent,
        SmModalEditComponent,
        SmViewComponent,
        LoadingComponent,
        draggable: VueDraggableNext,
    },
    setup() {
        const cuisineStore         = useCuisineStore();
        const frontendSettingStore = useFrontendSettingStore();

        return {cuisineStore, frontendSettingStore}
    },
    data() {
        return {
            loading: {
                isActive: false,
            },
            enums: {
                statusEnum     : statusEnum,
                statusEnumArray: {
                    [statusEnum.ACTIVE]  : this.$t("label.active"),
                    [statusEnum.INACTIVE]: this.$t("label.inactive"),
                }
            },
            props: {
                form: {
                    name       : "",
                    description: "",
                    status     : statusEnum.ACTIVE
                },
                search: {
                    paginate    : 1,
                    page        : 1,
                    per_page    : 10,
                    order_column: 'sort',
                    order_type  : 'asc'
                },
            },
            itemCuisines: []
        }
    },
    mounted() {
        this.list();
    },
    computed: {
        setting: function () {
            return this.frontendSettingStore.lists;
        },
        cuisines: function () {
            this.itemCuisines = this.cuisineStore.lists;
            return this.cuisineStore.lists;
        },
        pagination: function () {
            return this.cuisineStore.pagination;
        },
        paginationPage: function () {
            return this.cuisineStore.page;
        }
    },
    methods: {
        statusClass: function (status) {
            return appService.statusClass(status);
        },
        textShortener: function (text, number = 30) {
            return appService.textShortener(text, number);
        },
        list: function (page = 1) {
            this.loading.isActive  = true;
            this.props.search.page = page;
            this.cuisineStore.fetch(this.props.search).then((res) => {
                this.itemCuisines     = res.data.data;
                this.loading.isActive = false;
            }).catch((err) => {
                this.loading.isActive = false;
            });
        },
        edit: function (cuisine) {
            useModal().openModal('modal');
            this.loading.isActive = true;
            this.cuisineStore.edit(cuisine.id);
            this.props.form       = {
                name       : cuisine.name,
                description: cuisine.description,
                status     : cuisine.status,
            };
            this.loading.isActive = false;
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
                    this.cuisineStore.destroy({
                        id: id,
                        search: this.props.search
                    }).then((res) => {
                        this.loading.isActive = false;
                        alertService.successFlip(null, this.$t("menu.cuisines"));
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
        sort: function () {
            const sortedIds = this.itemCuisines.map(cuisine => cuisine.id);
            this.cuisineStore.sort({
                form  : {cuisine_id: sortedIds},
                search: this.props.search
            }).then((res) => {
                this.list();
            }).catch((err) => {
                alertService.error(err.response.data.message);
            })
        }
    }
}
</script>
