<template>
    <LoadingComponent :props="loading" />

    <div class="db-card db-tab-div active">
        <div class="db-card-header border-none">
            <h3 class="db-card-title">{{ $t('menu.push_notifications') }}</h3>
            <nav class="flex flex-wrap gap-2 mobile:justify-center">
                <TableLimitComponent :method="list" :search="props.search" :page="paginationPage" />
                <FilterComponent @click.prevent="handleSlide('pushnotification-filter')" />
                <div class="paper-group">
                    <ExportComponent @click.prevent="handlePaper" />
                    <nav class="paper-content absolute top-9 right-1/2 translate-x-1/2 z-30 min-w-[80px] w-fit rounded-md shadow-paper bg-white">
                        <PrintComponent :props="printObj" />
                        <ExcelComponent :method="xls" />
                    </nav>
                </div>
                <PushNotificationCreateComponent :props="props" v-if="permissionChecker('push-notifications_create')" />
            </nav>
        </div>

        <div class="table-filter-div" id="pushnotification-filter">
            <form class="p-4 sm:p-5 mb-5" @submit.prevent="search">
                <div class="row">
                    <div class="col-12 sm:col-6 md:col-4 xl:col-3">
                        <label for="searchTitle" class="db-field-title after:hidden">{{ $t('label.title') }}</label>
                        <input id="searchTitle" v-model="props.search.title" type="text" class="db-field-control">
                    </div>
                    <div class="col-12 sm:col-6 md:col-4 xl:col-3">
                        <label for="searchRole" class="db-field-title after:hidden">{{ $t('label.role') }}</label>
                        <vue-select @search:change="selectUser($event)" class="db-field-control f-b-custom-select"
                            id="role_id" v-model="props.search.role_id" :options="roles" label-by="name" value-by="id"
                            :closeOnSelect="true" :searchable="true" :clearOnClose="true" placeholder="--"
                            search-placeholder="--" />
                    </div>
                    <div class="col-12 sm:col-6 md:col-4 xl:col-3">
                        <label for="searchUser" class="db-field-title after:hidden">{{ $t('label.user') }}</label>
                        <vue-select class="db-field-control f-b-custom-select" id="user_id" v-model="props.search.user_id"
                            :options="users" label-by="name" value-by="id" :closeOnSelect="true" :searchable="true"
                            :clearOnClose="true" placeholder="--" search-placeholder="--" />
                    </div>
                    <div class="col-12">
                        <div class="flex flex-wrap gap-3 mt-4">
                            <button class="db-btn py-2 text-white bg-primary">
                                <i class="lab lab-line-search lab-font-size-16"></i>
                                <span>{{ $t('button.search') }}</span>
                            </button>
                            <button class="db-btn py-2 text-white bg-gray-600" @click="clear">
                                <i class="lab lab-line-cross lab-font-size-22"></i>
                                <span>{{ $t('button.clear') }}</span>
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <div class="db-table-responsive">
            <table class="db-table stripe" id="print">
                <thead class="db-table-head">
                    <tr class="db-table-head-tr">
                        <th class="db-table-head-th">{{ $t('label.title') }}</th>
                        <th class="db-table-head-th">{{ $t('label.role') }}</th>
                        <th class="db-table-head-th">{{ $t('label.user') }}</th>
                        <th class="db-table-head-th hidden-print" v-if="permissionChecker('push-notifications_show') || permissionChecker('push-notifications_delete')">
                            {{ $t('label.action') }}</th>
                    </tr>
                </thead>
                <tbody class="db-table-body" v-if="pushNotifications.length > 0">
                    <tr class="db-table-body-tr" v-for="pushNotification in pushNotifications" :key="pushNotification">
                        <td class="db-table-body-td">
                            <div>{{ textShortener(pushNotification.title) }}</div>
                        </td>
                        <td class="db-table-body-td">
                            <div>{{ pushNotification.role }}</div>
                        </td>
                        <td class="db-table-body-td">
                            <div>{{ pushNotification.customer }}</div>
                        </td>
                        <td class="db-table-body-td hidden-print"
                            v-if="permissionChecker('push-notifications_show') || permissionChecker('push-notifications_delete')">
                            <div class="flex justify-start items-center sm:items-start sm:justify-start gap-1.5">
                                <SmIconViewComponent :link="'admin.pushNotification.show'" :id="pushNotification.id"
                                    v-if="permissionChecker('push-notifications_show')" />
                                <SmIconDeleteComponent @click="destroy(pushNotification.id)"
                                    v-if="permissionChecker('push-notifications_delete')" />
                            </div>
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
import LoadingComponent from "../../common/LoadingComponent.vue";
import PushNotificationCreateComponent from "./PushNotificationCreateComponent.vue";
import alertService from "../../../services/alertService.js";
import PaginationTextComponent from "../components/pagination/PaginationTextComponent.vue";
import PaginationBox from "../components/pagination/PaginationBox.vue";
import PaginationSMBox from "../components/pagination/PaginationSMBox.vue";
import appService from "../../../services/appService.js";
import statusEnum from "../../../enums/modules/statusEnum.js";
import TableLimitComponent from "../components/TableLimitComponent.vue";
import SmIconDeleteComponent from "../components/buttons/SmIconDeleteComponent.vue";
import SmIconViewComponent from "../components/buttons/SmIconViewComponent.vue";
import FilterComponent from "../components/buttons/collapse/FilterComponent.vue";
import ExportComponent from "../components/buttons/export/ExportComponent.vue";
import PrintComponent from "../components/buttons/export/PrintComponent.vue";
import ExcelComponent from "../components/buttons/export/ExcelComponent.vue";
import { usePaper } from "../../../composables/paper.js";
import { useSlide } from "../../../composables/slide.js";
import { useRoleStore } from "../../../stores/role.js";
import { useUserStore } from "../../../stores/user.js";
import { useFrontendSettingStore } from "../../../stores/frontendSetting.js";
import { usePushNotificationStore } from "../../../stores/pushNotification.js";
import VueSimpleAlert from "vue3-simple-alert";

export default {
    name: "PushNotificationListComponent",
    components: {
        TableLimitComponent,
        PaginationSMBox,
        PaginationBox,
        PaginationTextComponent,
        PushNotificationCreateComponent,
        LoadingComponent,
        SmIconDeleteComponent,
        SmIconViewComponent,
        FilterComponent,
        ExportComponent,
        PrintComponent,
        ExcelComponent
    },
    setup() {
        const roleStore             = useRoleStore();
        const userStore             = useUserStore();
        const frontendSettingStore  = useFrontendSettingStore();
        const pushNotificationStore = usePushNotificationStore();
        return {
            roleStore,
            userStore,
            frontendSettingStore,
            pushNotificationStore
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
            printLoading: true,
            printObj: {
                id: "print",
                popTitle: this.$t('menu.push_notifications')
            },
            props: {
                form: {
                    title      : "",
                    description: "",
                    role_id    : null,
                    user_id    : null
                },
                search: {
                    paginate: 1,
                    page: 1,
                    per_page: 10,
                    order_column: 'id',
                    order_type: 'desc',
                    title: "",
                    role_id: null,
                    user_id: null
                }
            },
            handlePaper: usePaper().handlePaper,
            handleSlide: useSlide().handleSlide
        }
    },
    mounted() {
        this.list();
        this.roleStore.fetch({order_column: 'id', order_type: 'asc'});
        this.userStore.fetch({order_column: 'id',  order_type: 'asc', status: statusEnum.ACTIVE});
    },
    computed: {
        setting: function () {
            return this.frontendSettingStore.lists;
        },
        pushNotifications: function () {
            return this.pushNotificationStore.lists;
        },
        pagination: function () {
            return this.pushNotificationStore.pagination;
        },
        paginationPage: function () {
            return this.pushNotificationStore.page;
        },
        roles: function () {
            return this.roleStore.lists;
        },
        users: function () {
            return this.userStore.lists;
        }
    },
    methods: {
        permissionChecker(e) {
            return appService.permissionChecker(e);
        },
        textShortener: function (text, number = 30) {
            return appService.textShortener(text, number);
        },
        search: function () {
            this.list();
        },
        selectUser: function (e) {
            this.props.search.user_id = null;
            this.userStore.fetch({
                order_column: 'id',
                order_type: 'asc',
                status: statusEnum.ACTIVE,
                role_id: this.props.search.role_id
            });
        },
        clear: function () {
            this.props.search.paginate     = 1;
            this.props.search.page         = 1;
            this.props.search.per_page     = 10;
            this.props.search.order_column = 'id';
            this.props.search.order_type   = 'desc';
            this.props.search.title        = "";
            this.props.search.role_id      = null;
            this.props.search.user_id      = null;
            this.list();
        },
        list: function (page = 1) {
            this.loading.isActive  = true;
            this.props.search.page = page;
            this.pushNotificationStore.fetch(this.props.search).then(res => {
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
                    this.pushNotificationStore.destroy({id: id, search: this.props.search}).then((res) => {
                        this.loading.isActive = false;
                        alertService.successFlip(null, this.$t('label.push_notification'));
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
        },
        xls: function () {
            this.loading.isActive = true;
            this.pushNotificationStore.export(this.props.search).then(res => {
                this.loading.isActive = false;
                const blob = new Blob([res.data], { type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' });
                const link = document.createElement('a');
                link.href = URL.createObjectURL(blob);
                link.download = this.$t("menu.push_notifications");
                link.click();
                URL.revokeObjectURL(link.href);
            }).catch((err) => {
                this.loading.isActive = false;
                alertService.error(err.response.data.message);
            });
        }
    }
}
</script>
