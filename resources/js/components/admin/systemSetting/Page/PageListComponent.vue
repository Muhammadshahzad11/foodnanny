<template>
    <LoadingComponent :props="loading" />

    <div class="db-card db-tab-div active">
        <div class="db-card-header border-none">
            <h3 class="db-card-title">{{ $t("menu.pages") }}</h3>
            <div class="db-card-filter">
                <TableLimitComponent :method="list" :search="props.search" :page="paginationPage"/>
                <PageCreateComponent :props="props" />
            </div>
        </div>

        <div class="db-table-responsive">
            <table class="db-table stripe">
                <thead class="db-table-head">
                    <tr class="db-table-head-tr">
                        <th class="db-table-head-th">
                            {{ $t("label.title") }}
                        </th>
                        <th class="db-table-head-th">
                            {{ $t("label.status") }}
                        </th>
                        <th class="db-table-head-th">
                            {{ $t("label.action") }}
                        </th>
                    </tr>
                </thead>
                <tbody class="db-table-body" v-if="pages.length > 0">
                    <tr class="db-table-body-tr" v-for="page in pages" :key="page">
                        <td class="db-table-body-td">
                            {{ textShortener(page.title) }}
                        </td>
                        <td class="db-table-body-td">
                            <span :class="statusClass(page.status)">
                                {{ enums.statusEnumArray[page.status] }}
                            </span>
                        </td>
                        <td class="db-table-body-td">
                            <div class="flex justify-start items-center sm:items-start sm:justify-start gap-1.5">
                                <SmViewComponent :link="'admin.systemSettings.page.show'" :id="page.id" />
                                <SmModalEditComponent @click="edit(page)" />
                                <SmDeleteComponent @click="destroy(page.id)" />
                            </div>
                        </td>
                    </tr>
                </tbody>
                <tbody class="db-table-body" v-else>
                    <tr class="db-table-body-tr">
                        <td class="db-table-body-td" colspan="3">
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
</template>

<script>
import LoadingComponent from "../../../common/LoadingComponent.vue";
import PageCreateComponent from "./PageCreateComponent.vue";
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
import {usePageStore} from "../../../../stores/page.js";
import {useFrontendSettingStore} from "../../../../stores/frontendSetting.js";
import VueSimpleAlert from "vue3-simple-alert";

export default {
    name: "PageListComponent",
    components: {
        TableLimitComponent,
        PaginationSMBox,
        PaginationBox,
        PaginationTextComponent,
        PageCreateComponent,
        LoadingComponent,
        SmDeleteComponent,
        SmModalEditComponent,
        SmViewComponent
    },
    setup() {
        const pageStore            = usePageStore();
        const frontendSettingStore = useFrontendSettingStore();
        return {pageStore, frontendSettingStore}
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
                    title          : "",
                    description    : "",
                    menu_section_id: null,
                    template_id    : null,
                    status         : statusEnum.ACTIVE
                },
                search: {
                    paginate    : 1,
                    page        : 1,
                    per_page    : 10,
                    order_column: "id",
                    order_type  : "desc"
                }
            }
        };
    },
    mounted() {
        this.list();
    },
    computed: {
        setting: function () {
            return this.frontendSettingStore.lists;
        },
        pages: function () {
            return this.pageStore.lists;
        },
        pagination: function () {
            return this.pageStore.pagination;
        },
        paginationPage: function () {
            return this.pageStore.page;
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
            this.loading.isActive = true;
            this.props.search.page = page;
            this.pageStore.fetch(this.props.search).then((res) => {
                this.loading.isActive = false;
            }).catch((err) => {
                this.loading.isActive = false;
            });
        },
        edit: function (page) {
            useModal().openModal('modal');
            this.loading.isActive = true;
            this.pageStore.edit(page.id);
            this.props.form = {
                title          : page.title,
                status         : page.status,
                description    : page.description,
                menu_section_id: page.menu_section_id,
                template_id    : page.template_id === 0 ? null: page.template_id
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
                    this.pageStore.destroy({id: id, search: this.props.search}).then((res) => {
                        this.loading.isActive = false;
                        alertService.successFlip(null, this.$t("menu.pages"));
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
        }
    }
};
</script>
