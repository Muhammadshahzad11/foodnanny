<template>
    <LoadingComponent :props="loading" />
    <div class="db-card db-tab-div active">
        <div class="db-card-header border-none">
            <h3 class="db-card-title">{{ $t("label.address") }}</h3>
            <div class="db-card-filter">
                <TableLimitComponent :method="list" :search="address.search" :page="paginationPage" />
                <RestaurantOwnerAddressCreateComponent :props="address"
                    v-if="permissionChecker('restaurant-owners_create')" />
            </div>
        </div>

        <div class="db-table-responsive">
            <table class="db-table stripe">
                <thead class="db-table-head">
                    <tr class="db-table-head-tr">
                        <th class="db-table-head-th">
                            {{ $t("label.label") }}
                        </th>
                        <th class="db-table-head-th">
                            {{ $t("label.address") }}
                        </th>
                        <th class="db-table-head-th">
                            {{ $t("label.apartment") }}
                        </th>
                        <th v-if="permissionChecker('restaurant-owners_edit') || permissionChecker('restaurant-owners_delete')"
                            class="db-table-head-th">
                            {{ $t("label.action") }}
                        </th>
                    </tr>
                </thead>
                <tbody class="db-table-body" v-if="addresses.length > 0">
                    <tr class="db-table-body-tr" v-for="address in addresses" :key="address">
                        <td class="db-table-body-td">
                            {{ address.label }}
                        </td>
                        <td class="db-table-body-td">
                            {{ address.address }}
                        </td>
                        <td class="db-table-body-td">
                            {{ address.apartment }}
                        </td>
                        <td class="db-table-body-td"
                            v-if="permissionChecker('restaurant-owners_edit') || permissionChecker('restaurant-owners_delete')">
                            <div class="flex justify-start items-center sm:items-start sm:justify-start gap-1.5">
                                <SmIconModalEditComponent @click="edit(address)"
                                    v-if="permissionChecker('restaurant-owners_edit')" />
                                <SmIconDeleteComponent @click="destroy(address.id)"
                                    v-if="permissionChecker('restaurant-owners_delete')" />
                            </div>
                        </td>
                    </tr>
                </tbody>
                <tbody class="db-table-body" v-else>
                    <tr class="db-table-body-tr">
                        <td class="db-table-body-td" colspan="4">
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
            <PaginationSMBox :pagination="pagination" :method="list" />
            <div class="hidden sm:flex sm:flex-1 sm:items-center sm:justify-between">
                <PaginationTextComponent :props="{ page: paginationPage }" />
                <PaginationBox :pagination="pagination" :method="list" />
            </div>
        </div>
    </div>
</template>

<script>
import alertService from "../../../../services/alertService";
import LoadingComponent from "../../../common/LoadingComponent.vue";
import TableLimitComponent from "../../components/TableLimitComponent.vue";
import RestaurantOwnerAddressCreateComponent from "./RestaurantOwnerAddressCreateComponent.vue";
import appService from "../../../../services/appService";
import SmIconDeleteComponent from "../../components/buttons/SmIconDeleteComponent.vue";
import SmIconModalEditComponent from "../../components/buttons/SmIconModalEditComponent.vue";
import labelEnum from "../../../../enums/modules/labelEnum";
import PaginationTextComponent from "../../components/pagination/PaginationTextComponent.vue";
import PaginationBox from "../../components/pagination/PaginationBox.vue";
import PaginationSMBox from "../../components/pagination/PaginationSMBox.vue";
import { useModal } from "../../../../composables/modal";
import { useRestaurantOwnerAddressStore } from "../../../../stores/restaurantOwnerAddress";
import { useFrontendSettingStore } from "../../../../stores/frontendSetting";
import VueSimpleAlert from "vue3-simple-alert";

export default {
    name: "RestaurantOwnerAddressListComponent",
    components: {
        LoadingComponent,
        RestaurantOwnerAddressCreateComponent,
        TableLimitComponent,
        SmIconDeleteComponent,
        SmIconModalEditComponent,
        PaginationSMBox,
        PaginationBox,
        PaginationTextComponent
    },
    props: ["props"],
    setup() {
        const restaurantOwnerAddressStore = useRestaurantOwnerAddressStore();
        const frontendSettingStore = useFrontendSettingStore();
        return {
            restaurantOwnerAddressStore,
            frontendSettingStore
        }
    },
    data() {
        return {
            loading: {
                isActive: false
            },
            address: {
                form: {
                    address: "",
                    apartment: "",
                    latitude: "",
                    longitude: "",
                    label: ""
                },
                search: {
                    paginate: 1,
                    page: 1,
                    per_page: 10,
                    order_column: "id",
                    order_type: "desc"
                },
                status: false,
                switchLabel: "",
                isMap: false
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
        addresses: function () {
            return this.restaurantOwnerAddressStore.lists;
        },
        pagination: function () {
            return this.restaurantOwnerAddressStore.pagination;
        },
        paginationPage: function () {
            return this.restaurantOwnerAddressStore.page;
        }
    },
    methods: {
        permissionChecker(e) {
            return appService.permissionChecker(e);
        },
        list: function (page = 1) {
            this.loading.isActive = true;
            this.address.search.page = page;
            this.restaurantOwnerAddressStore.fetch({ id: this.props, search: this.address.search }).then((res) => {
                this.loading.isActive = false;
            }).catch((err) => {
                this.loading.isActive = false;
            });
        },
        edit: function (address) {
            useModal().openModal('modal');
            this.loading.isActive = true;
            this.restaurantOwnerAddressStore.edit(address.id).then((res) => {
                this.loading.isActive = false;
                this.address.isMap = true;
                this.address.form = {
                    address: address.address,
                    apartment: address.apartment,
                    latitude: address.latitude,
                    longitude: address.longitude,
                    label: address.label
                };
                if (this.address.form.label === this.$t("label.home")) {
                    this.address.status = false;
                    this.address.switchLabel = labelEnum.HOME;
                } else if (this.address.form.label === this.$t("label.work")) {
                    this.address.status = false;
                    this.address.switchLabel = labelEnum.WORK;
                } else {
                    this.address.status = true;
                    this.address.switchLabel = labelEnum.OTHER;
                }
            }).catch((err) => {
                alertService.error(err.response.data.message);
            });
        },
        destroy: function (addressId) {
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
                    this.restaurantOwnerAddressStore.destroy({
                        id: this.props,
                        addressId: addressId,
                        search: this.address.search,
                    }).then((res) => {
                        this.loading.isActive = false;
                        alertService.successFlip(null, this.$t("label.address"));
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
