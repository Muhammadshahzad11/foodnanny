<template>
    <LoadingComponent :props="loading"/>

    <div class="db-card db-tab-div active">
        <div class="db-card-header border-none">
            <h3 class="db-card-title">{{ $t('menu.analytic_section') }} <span class="text-primary normal-case">({{
                    analytic.name
                }})</span></h3>
            <div class="db-card-filter">
                <TableLimitComponent :method="list" :search="props.search" :page="paginationPage"/>
                <AnalyticSectionCreateComponent :props="props"/>
            </div>
        </div>

        <div class="db-table-responsive">
            <table class="db-table stripe">
                <thead class="db-table-head">
                <tr class="db-table-head-tr">
                    <th class="db-table-head-th">{{ $t('label.name') }}</th>
                    <th class="db-table-head-th">{{ $t('label.section') }}</th>
                    <th class="db-table-head-th">{{ $t('label.action') }}</th>
                </tr>
                </thead>
                <tbody class="db-table-body" v-if="analyticSections.length > 0">
                <tr class="db-table-body-tr" v-for="analyticSection in analyticSections" :key="analyticSection">
                    <td class="db-table-body-td">{{ analyticSection.name }}</td>
                    <td class="db-table-body-td">
                        {{ enums.analyticSectionEnumArray[analyticSection.section] }}
                    </td>
                    <td class="db-table-body-td">
                        <div class="flex justify-start items-center sm:items-start sm:justify-start gap-1.5">
                            <SmModalEditComponent @click="edit(analyticSection)"/>
                            <SmDeleteComponent @click="destroy(analyticSection.id)"/>
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
import AnalyticSectionCreateComponent from "./AnalyticSection/AnalyticSectionCreateComponent.vue";
import alertService from "../../../../services/alertService.js";
import PaginationTextComponent from "../../components/pagination/PaginationTextComponent.vue";
import PaginationBox from "../../components/pagination/PaginationBox.vue";
import PaginationSMBox from "../../components/pagination/PaginationSMBox.vue";
import appService from "../../../../services/appService.js";
import analyticSectionEnum from "../../../../enums/modules/analyticSectionEnum.js";
import TableLimitComponent from "../../components/TableLimitComponent.vue";
import SmDeleteComponent from "../../components/buttons/SmDeleteComponent.vue";
import SmModalEditComponent from "../../components/buttons/SmModalEditComponent.vue";
import {useModal} from "../../../../composables/modal.js";
import {useAnalyticStore} from "../../../../stores/analytic.js";
import {useFrontendSettingStore} from "../../../../stores/frontendSetting.js";
import {useAnalyticSectionStore} from "../../../../stores/analyticSection.js";
import VueSimpleAlert from "vue3-simple-alert";

export default {
    name: "AnalyticShowComponent",
    components: {
        TableLimitComponent,
        PaginationSMBox,
        PaginationBox,
        PaginationTextComponent,
        AnalyticSectionCreateComponent,
        LoadingComponent,
        SmDeleteComponent,
        SmModalEditComponent
    },
    setup() {
        const analyticStore        = useAnalyticStore();
        const frontendSettingStore = useFrontendSettingStore();
        const analyticSectionStore = useAnalyticSectionStore();

        return {analyticStore, frontendSettingStore, analyticSectionStore}
    },
    data() {
        return {
            loading: {
                isActive: false
            },
            enums: {
                analyticSectionEnum: analyticSectionEnum,
                analyticSectionEnumArray: {
                    [analyticSectionEnum.HEADER]: this.$t("label.header"),
                    [analyticSectionEnum.BODY]: this.$t("label.body"),
                    [analyticSectionEnum.FOOTER]: this.$t("label.footer")
                }
            },
            props: {
                form: {
                    name: "",
                    data: '',
                    section: analyticSectionEnum.HEADER
                },
                search: {
                    paginate: 1,
                    page: 1,
                    per_page: 10,
                    order_column: 'id',
                    order_type: 'desc'
                },
                analyticId: 0
            }
        }
    },
    computed: {
        setting: function () {
            return this.frontendSettingStore.lists;
        },
        analytic: function () {
            return this.analyticStore.show;
        },
        analyticSections: function () {
            return this.analyticSectionStore.lists;
        },
        pagination: function () {
            return this.analyticSectionStore.pagination;
        },
        paginationPage: function () {
            return this.analyticSectionStore.page;
        }
    },
    mounted() {
        this.props.analyticId = this.$route.params.id;
        this.list();
        this.show();
    },
    methods: {
        show: function () {
            this.loading.isActive = true;
            this.analyticStore.view(this.props.analyticId).then(res => {
                this.loading.isActive = false;
            }).catch((err) => {
                this.loading.isActive = false;
            });
        },
        list: function (page = 1) {
            this.loading.isActive  = true;
            this.props.search.page = page;
            this.analyticSectionStore.fetch(this.props).then(res => {
                this.loading.isActive = false;
            }).catch((err) => {
                this.loading.isActive = false;
            });
        },
        edit: function (analyticSection) {
            useModal().openModal('modal');
            this.loading.isActive = true;
            this.analyticSectionStore.edit(analyticSection.id);
            this.props.form       = {
                name: analyticSection.name,
                data: analyticSection.data,
                section: analyticSection.section
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
                    this.analyticSectionStore.destroy({
                        analyticId: this.props.analyticId,
                        id: id,
                        search: this.props.search
                    }).then((res) => {
                        this.loading.isActive = false;
                        alertService.successFlip(null, this.$t('menu.analytic_section'));
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
            })
        }
    }
}
</script>
