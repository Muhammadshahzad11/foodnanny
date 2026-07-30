<template>
    <LoadingComponent :props="loading" />
    <div id="role" class="db-card db-tab-div active">
        <div class="db-card-header">
            <h3 class="db-card-title"> {{ $t('menu.role') }} &amp; {{ $t('label.permissions') }}</h3>
            <div class="db-card-filter">
                <TableLimitComponent :method="list" :search="props.search" :page="paginationPage" />
                <RoleCreateComponent :props="props" />
            </div>
        </div>
        <ul v-if="roles.length > 0">
            <li v-for="role in roles" :key="role.role"
                class="flex flex-col items-center justify-between gap-4 sm:flex-row sm:justify-between py-3 px-4 border-b last:border-none border-solid border-slate-200">
                <span class="font-medium capitalize text-center sm:text-left text-sm text-slate-500">
                    {{ role.name }}
                    <span class="block font-normal whitespace-nowrap">({{ role.users_count }}) {{ $t('label.members') }}</span>
                </span>
                <div class="flex flex-wrap justify-center items-center sm:items-start sm:justify-end gap-1.5">
                    <router-link class="db-btn-outline sm primary modal-btn m-0.5" v-if="role.id !==1" :to="{ name: 'admin.settings.role.show', params: { id: role.id } }">
                        <i class="lab lab-line-key"></i>
                        <span>{{ $t("button.permissions") }}</span>
                    </router-link>
                    <SmModalEditComponent @click="edit(role)" />
                    <SmDeleteComponent @click="destroy(role.id)" v-if="!enums.roleEnumArray.includes(role.id)" />
                </div>
            </li>
        </ul>
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
import alertService from "../../../../services/alertService.js";
import RoleCreateComponent from "./RoleCreateComponent.vue";
import PaginationTextComponent from "../../components/pagination/PaginationTextComponent.vue";
import PaginationBox from "../../components/pagination/PaginationBox.vue";
import PaginationSMBox from "../../components/pagination/PaginationSMBox.vue";
import appService from "../../../../services/appService.js";
import TableLimitComponent from "../../components/TableLimitComponent.vue";
import SmDeleteComponent from "../../components/buttons/SmDeleteComponent.vue";
import SmModalEditComponent from "../../components/buttons/SmModalEditComponent.vue";
import roleEnum from "../../../../enums/modules/roleEnum.js";
import { useModal } from "../../../../composables/modal.js";
import {useRoleStore} from "../../../../stores/role.js";
import VueSimpleAlert from "vue3-simple-alert";

export default {
    name: "RoleListComponent",
    components: {
        TableLimitComponent,
        PaginationSMBox,
        RoleCreateComponent,
        PaginationBox,
        PaginationTextComponent,
        LoadingComponent,
        SmDeleteComponent,
        SmModalEditComponent
    },
    setup() {
        const roleStore = useRoleStore();
        return {roleStore}
    },
    data() {
        return {
            loading: {
                isActive: false
            },
            props: {
                form: {
                    name: ""
                },
                search: {
                    paginate    : 1,
                    page        : 1,
                    per_page    : 10,
                    order_column: 'id',
                    order_type  : 'asc'
                }
            },
            enums: {
                roleEnumArray: [
                    roleEnum.ADMIN,
                    roleEnum.RESTAURANT_OWNER,
                    roleEnum.CUSTOMER,
                    roleEnum.DELIVERY_BOY
                ]
            }
        }
    },
    computed: {
        roles: function () {
            return this.roleStore.lists;
        },
        pagination: function () {
            return this.roleStore.pagination;
        },
        paginationPage: function () {
            return this.roleStore.page;
        }
    },
    mounted() {
        this.list();
    },
    methods: {
        list: function (page = 1) {
            this.loading.isActive  = true;
            this.props.search.page = page;
            this.roleStore.fetch(this.props.search).then(res => {
                this.loading.isActive = false;
            }).catch((err) => {
                this.loading.isActive = false;
            });
        },
        edit: function (role) {
            useModal().openModal('modal');
            this.loading.isActive = true;
            this.props.form = {
                name: role.name
            };
            this.roleStore.edit(role.id);
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
                    this.roleStore.destroy({id: id, search: this.props.search}).then((res) => {
                        this.loading.isActive = false;
                        alertService.successFlip(null, this.$t('menu.role'));
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
