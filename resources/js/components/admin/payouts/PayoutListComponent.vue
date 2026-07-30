<template>
    <LoadingComponent :props="loading"/>
    <div class="col-12">
        <div class="db-card">
            <div class="db-card-header border-none">
                <h3 class="db-card-title">{{ $t('menu.payouts') }}</h3>
                <nav class="flex flex-wrap gap-2 mobile:justify-center">
                    <TableLimitComponent :method="list" :search="props.search" :page="paginationPage"/>
                    <FilterComponent @click.prevent="handleSlide('payout')"/>
                    <div class="paper-group">
                        <ExportComponent @click.prevent="handlePaper"/>
                        <nav
                            class="paper-content absolute top-9 right-1/2 translate-x-1/2 z-30 min-w-[80px] w-fit rounded-md shadow-paper bg-white">
                            <PrintComponent :props="printObj"/>
                            <ExcelComponent :method="xls"/>
                        </nav>
                    </div>
                    <router-link :to="{ name: 'admin.payouts.create' }" v-if="permissionChecker('payouts_create')" class="h-9 px-3 py-4 font-medium border border-primary flex items-center gap-1 text-sm tracking-wide capitalize rounded-md shadow text-white bg-primary">
                        <i class="lab lab-line-add-circle"></i>
                        <span>{{ $t("button.add_payout") }}</span>
                    </router-link>
                </nav>
            </div>

            <div class="table-filter-div" id="payout">
                <form class="p-4 sm:p-5 mb-5" @submit.prevent="search">
                    <div class="row">
                        <div class="col-12 sm:col-6 md:col-4 xl:col-3">
                            <label for="searchAmount" class="db-field-title after:hidden">
                                {{ $t('label.amount') }}
                            </label>
                            <input v-on:keypress="floatNumber($event)" id="searchAmount" v-model="props.search.amount"
                                   type="text" class="db-field-control">
                        </div>

                        <div class="col-12 sm:col-6 md:col-4 xl:col-3">
                            <label for="searchStartDate" class="db-field-title after:hidden">
                                {{ $t('label.date') }}
                            </label>
                            <DatePickerComponent @update:modelValue="handleDate" inputStyle="filter"
                                                 :hideInputIcon="true" :range="true" v-model="modelValue"/>
                        </div>
                        <div v-if="roleId === this.enums.roleEnum.ADMIN && defaultRestaurant === 0"
                             class="col-12 sm:col-6 md:col-4 xl:col-3">
                            <label for="searchType" class="db-field-title after:hidden">
                                {{ $t("label.type") }}
                            </label>
                            <vue-select class="db-field-control f-b-custom-select" id="searchType"
                                        v-model="props.search.type"
                                        :options="[{ id: enums.modelTypeEnum.RESTAURANT, name: $t('label.restaurant') }, { id: enums.modelTypeEnum.DELIVERY_BOY, name: $t('label.delivery_boy') }]"
                                        label-by="name" value-by="id" :closeOnSelect="true" :searchable="true"
                                        :clearOnClose="true" placeholder="--" search-placeholder="--"/>
                        </div>

                        <div class="col-12 sm:col-6 md:col-4 xl:col-3"
                             v-if="roleId === this.enums.roleEnum.ADMIN && props.search.type === enums.modelTypeEnum.RESTAURANT">
                            <label for="searchRestaurantId" class="db-field-title">
                                {{ $t("label.restaurant") }}
                            </label>
                            <vue-select class="db-field-control f-b-custom-select" id="searchRestaurantId"
                                        v-model="props.search.restaurant_id" :options="restaurants"
                                        label-by="name_email" value-by="id" :closeOnSelect="true" :searchable="true"
                                        :clearOnClose="true" placeholder="--" search-placeholder="--"/>
                        </div>

                        <div class="col-12 sm:col-6 md:col-4 xl:col-3"
                             v-if="roleId === this.enums.roleEnum.ADMIN && props.search.type === enums.modelTypeEnum.DELIVERY_BOY">
                            <label for="delivery_boy_id" class="db-field-title after:hidden">
                                {{ $t('label.delivery_boy') }}
                            </label>
                            <vue-select class="db-field-control f-b-custom-select" id="delivery_boy_id"
                                        v-model="props.search.delivery_boy_id" :options="deliveryBoys"
                                        label-by="name_email" value-by="id" :closeOnSelect="true" :searchable="true"
                                        :clearOnClose="true" placeholder="--" search-placeholder="--"/>
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
                        <th class="db-table-head-th">{{ $t('label.name') }}</th>
                        <th class="db-table-head-th">{{ $t('label.email') }}</th>
                        <th class="db-table-head-th">{{ $t('label.phone') }}</th>
                        <th class="db-table-head-th">{{ $t('label.date') }}</th>
                        <th class="db-table-head-th">{{ $t('label.amount') }}</th>
                        <th class="db-table-head-th hidden-print"
                            v-if="permissionChecker('payouts_show') || permissionChecker('payouts_delete')">
                            {{ $t("label.action") }}
                        </th>
                    </tr>
                    </thead>
                    <tbody class="db-table-body" v-if="payouts.length > 0">
                    <tr class="db-table-body-tr" v-for="payout in payouts" :key="payout">
                        <td class="db-table-body-td">
                            {{ payout.name }}
                        </td>
                        <td class="db-table-body-td">
                            {{ payout.email }}
                        </td>
                        <td class="db-table-body-td">
                            <span v-if="payout.phone"> {{
                                    payout.country_code + '' +
                                    payout.phone
                                }} </span>
                        </td>
                        <td class="db-table-body-td">
                            {{ payout.date }}
                        </td>
                        <td class="db-table-body-td">
                            {{ payout.amount }}
                        </td>
                        <td class="db-table-body-td hidden-print"
                            v-if="permissionChecker('payouts_show') || permissionChecker('payouts_delete')">
                            <div class="flex justify-start items-center sm:items-start sm:justify-start gap-1.5">
                                <SmIconViewComponent :link="'admin.payouts.show'" :id="payout.id"
                                                     v-if="permissionChecker('payouts_show')"/>
                                <SmIconDeleteComponent @click="destroy(payout.id)"
                                                       v-if="permissionChecker('payouts_delete')"/>
                            </div>
                        </td>
                    </tr>
                    </tbody>
                    <tbody class="db-table-body" v-else>
                    <tr class="db-table-body-tr">
                        <td class="db-table-body-td" colspan="6">
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
    </div>
</template>

<script>
import LoadingComponent from "../../common/LoadingComponent.vue";
import appService from "../../../services/appService.js";
import FilterComponent from "../components/buttons/collapse/FilterComponent.vue";
import {useSlide} from "../../../composables/slide.js";
import ExportComponent from "../components/buttons/export/ExportComponent.vue";
import {usePaper} from "../../../composables/paper.js";
import PrintComponent from "../components/buttons/export/PrintComponent.vue";
import ExcelComponent from "../components/buttons/export/ExcelComponent.vue";
import TableLimitComponent from "../components/TableLimitComponent.vue";
import {usePayoutStore} from "../../../stores/payout.js";
import DatePickerComponent from "../components/DatePickerComponent.vue";
import modelTypeEnum from "../../../enums/modules/modelTypeEnum.js";
import {useRestaurantStore} from "../../../stores/restaurant.js";
import {useDeliveryBoyStore} from "../../../stores/deliveryBoy.js";
import PaginationSMBox from "../components/pagination/PaginationSMBox.vue";
import PaginationTextComponent from "../components/pagination/PaginationTextComponent.vue";
import PaginationBox from "../components/pagination/PaginationBox.vue";
import {useFrontendSettingStore} from "../../../stores/frontendSetting.js";
import SmIconDeleteComponent from "../components/buttons/SmIconDeleteComponent.vue";
import SmIconViewComponent from "../components/buttons/SmIconViewComponent.vue";
import alertService from "../../../services/alertService.js";
import {useAuthStore} from "../../../stores/auth.js";
import roleEnum from "../../../enums/modules/roleEnum.js";
import VueSimpleAlert from "vue3-simple-alert";
import {useDefaultAccessStore} from "../../../stores/defaultAccess.js";

export default {
    name: "PayoutListComponent",
    components: {
        TableLimitComponent,
        PaginationSMBox,
        PaginationBox,
        PaginationTextComponent,
        LoadingComponent,
        FilterComponent,
        ExportComponent,
        PrintComponent,
        ExcelComponent,
        DatePickerComponent,
        SmIconDeleteComponent,
        SmIconViewComponent
    },
    setup() {
        const authStore            = useAuthStore();
        const payoutStore          = usePayoutStore();
        const restaurantStore      = useRestaurantStore();
        const deliveryBoyStore     = useDeliveryBoyStore();
        const defaultAccessStore   = useDefaultAccessStore();
        const frontendSettingStore = useFrontendSettingStore();

        return {
            authStore,
            payoutStore,
            restaurantStore,
            deliveryBoyStore,
            defaultAccessStore,
            frontendSettingStore
    }
    },
    data() {
        return {
            loading: {
                isActive: false
            },
            printObj: {
                id: "print",
                popTitle: this.$t("menu.payouts")
            },
            enums: {
                modelTypeEnum: modelTypeEnum,
                roleEnum: roleEnum
            },
            props: {
                search: {
                    paginate: 1,
                    page: 1,
                    per_page: 10,
                    order_column: 'id',
                    order_type: "desc",
                    amount: "",
                    from_date: "",
                    to_date: "",
                    type: null,
                    restaurant_id: null,
                    delivery_boy_id: null
                }
            },
            modelValue: null,
            handlePaper: usePaper().handlePaper,
            handleSlide: useSlide().handleSlide
        }
    },
    mounted() {
        this.list();
        if (this.authStore.info?.role_id === this.enums.roleEnum.ADMIN) {
            this.restaurantStore.fetchAllRestaurant();
            this.deliveryBoyStore.fetchAllDeliveryBoy();
        }
    },
    computed: {
        roleId: function () {
            return this.authStore.info?.role_id;
        },
        defaultRestaurant: function () {
            return this.defaultAccessStore.lists?.restaurant_id;
        },
        setting: function () {
            return this.frontendSettingStore.lists;
        },
        payouts: function () {
            return this.payoutStore.lists;
        },
        pagination: function () {
            return this.payoutStore.pagination;
        },
        paginationPage: function () {
            return this.payoutStore.page;
        },
        restaurants: function () {
            return this.restaurantStore.allRestaurants;
        },
        deliveryBoys: function () {
            return this.deliveryBoyStore.allDeliveryBoys;
        },
    },
    methods: {
        permissionChecker(e) {
            return appService.permissionChecker(e);
        },
        floatNumber(e) {
            return appService.floatNumber(e);
        },
        search: function () {
            this.list();
        },
        clear: function () {
            this.props.search.paginate        = 1;
            this.props.search.page            = 1;
            this.props.search.order_column    = 'id';
            this.props.search.order_type      = "desc";
            this.props.search.amount          = "";
            this.props.search.from_date       = "";
            this.props.search.to_date         = "";
            this.props.search.type            = null;
            this.props.search.restaurant_id   = null;
            this.props.search.delivery_boy_id = null;
            this.modelValue                   = null;
        },
        handleDate: function (e) {
            if (e) {
                this.props.search.from_date = e[0];
                this.props.search.to_date   = e[1];
            } else {
                this.props.search.from_date = null;
                this.props.search.to_date   = null;
            }
        },
        list: function (page = 1) {
            this.loading.isActive  = true;
            this.props.search.page = page;
            this.payoutStore.fetch(this.props.search).then(res => {
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
                    this.payoutStore.destroy({id: id, search: this.props.search}).then((res) => {
                        this.loading.isActive = false;
                        alertService.successFlip(null, this.$t("menu.payouts"));
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
        xls: function () {
            this.loading.isActive = true;
            this.payoutStore.export(this.props.search).then((res) => {
                this.loading.isActive = false;
                const blob            = new Blob([res.data], {
                    type: "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet",
                });
                const link            = document.createElement("a");
                link.href             = URL.createObjectURL(blob);
                link.download         = this.$t("menu.payouts");
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
