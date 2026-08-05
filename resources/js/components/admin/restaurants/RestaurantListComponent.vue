<template>
    <LoadingComponent :props="loading"/>
    <div class="col-12">
        <div class="db-card">
            <div class="db-card-header border-none">
                <h3 class="db-card-title">{{ $t('menu.restaurants') }}</h3>
                <nav class="flex flex-wrap gap-2 mobile:justify-center">
                    <TableLimitComponent @click.prevent="handlePaper" :method="list" :search="props.search"
                                         :page="paginationPage"/>
                    <FilterComponent @click.prevent="handleSlide('restaurant-filter')"/>
                    <div class="paper-group">
                        <ExportComponent @click.prevent="handlePaper"/>
                        <nav
                            class="paper-content absolute top-9 right-1/2 translate-x-1/2 z-30 min-w-[80px] w-fit rounded-md shadow-paper bg-white">
                            <PrintComponent :props="printObj"/>
                            <ExcelComponent :method="xls"/>
                        </nav>
                    </div>
                    <RestaurantCreateComponent :props="props" v-if="permissionChecker('restaurants_create')"/>
                </nav>
            </div>

            <div class="table-filter-div" id="restaurant-filter">
                <form class="p-4 sm:p-5 mb-5" @submit.prevent="search">
                    <div class="row">
                        <div class="col-12 sm:col-6 md:col-4 xl:col-3">
                            <label for="searchName" class="db-field-title after:hidden">{{ $t("label.name") }}</label>
                            <input id="searchName" v-model="props.search.name" type="text" class="db-field-control"/>
                        </div>
                        <div class="col-12 sm:col-6 md:col-4 xl:col-3">
                            <label for="searchEmail" class="db-field-title after:hidden">{{ $t("label.email") }}</label>
                            <input id="searchEmail" v-model="props.search.email" type="email" class="db-field-control"/>
                        </div>
                        <div class="col-12 sm:col-6 md:col-4 xl:col-3">
                            <label for="searchPhone" class="db-field-title after:hidden">{{ $t("label.phone") }}</label>
                            <input v-on:keypress="phoneNumber($event)" id="searchPhone" v-model="props.search.phone"
                                   type="text" class="db-field-control"/>
                        </div>
                        <div class="col-12 sm:col-6 md:col-4 xl:col-3">
                            <label class="db-field-title" for="searchLatitude">{{
                                    $t("label.latitude")
                                }}/{{ $t("label.longitude") }}</label>
                            <div class="db-multiple-field">
                                <input v-model="props.search.latitude" type="text" id="searchLatitude"/>
                                <input v-model="props.search.longitude" type="text" id="longitude"/>
                            </div>
                        </div>
                        <div class="col-12 sm:col-6 md:col-4 xl:col-3">
                            <label for="searchCity" class="db-field-title after:hidden">{{ $t("label.city") }}</label>
                            <input id="searchCity" v-model="props.search.city" type="text" class="db-field-control"/>
                        </div>
                        <div class="col-12 sm:col-6 md:col-4 xl:col-3">
                            <label for="searchState" class="db-field-title after:hidden">{{ $t("label.state") }}</label>
                            <input id="searchState" v-model="props.search.state" type="text" class="db-field-control"/>
                        </div>
                        <div class="col-12 sm:col-6 md:col-4 xl:col-3">
                            <label for="searchZipCode"
                                   class="db-field-title after:hidden">{{ $t("label.zip_code") }}</label>
                            <input id="searchZipCode" v-model="props.search.zip_code" type="text"
                                   class="db-field-control"/>
                        </div>
                        <div class="col-12 sm:col-6 md:col-4 xl:col-3">
                            <label for="searchCuisineId" class="db-field-title">{{ $t("label.cuisine") }}</label>
                            <vue-select class="db-field-control f-b-custom-select" id="searchCuisineId"
                                        v-model="props.search.cuisine_id" :options="cuisines" label-by="name"
                                        value-by="id"
                                        :closeOnSelect="true" :searchable="true" :clearOnClose="true" placeholder="--"
                                        search-placeholder="--"/>
                        </div>
                        <div class="col-12 sm:col-6 md:col-4 xl:col-3">
                            <label for="searchStatus"
                                   class="db-field-title after:hidden">{{ $t("label.status") }}</label>
                            <vue-select class="db-field-control f-b-custom-select" id="searchStatus"
                                        v-model="props.search.status" :options="[
                                    { id: enums.statusEnum.ACTIVE, name: $t('label.active') },
                                    { id: enums.statusEnum.INACTIVE, name: $t('label.inactive') },
                                ]" label-by="name" value-by="id" :closeOnSelect="true" :searchable="true"
                                        :clearOnClose="true" placeholder="--"
                                        search-placeholder="--"/>
                        </div>
                        <div class="col-12 sm:col-6 md:col-4 xl:col-3">
                            <label for="searchApply"
                                   class="db-field-title after:hidden">{{ $t("label.apply_by") }}</label>
                            <vue-select class="db-field-control f-b-custom-select" id="searchApply"
                                        v-model="props.search.apply" :options="[
                                    { id: enums.applyByEnum.ADMIN, name: $t('label.admin') },
                                    { id: enums.applyByEnum.RESTAURANT_OWNER, name: $t('label.restaurant_owner') },
                                ]" label-by="name" value-by="id" :closeOnSelect="true" :searchable="true"
                                        :clearOnClose="true" placeholder="--"
                                        search-placeholder="--"/>
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
                        <th class="db-table-head-th">
                            {{ $t('label.name') }}
                        </th>
                        <th class="db-table-head-th">
                            {{ $t('label.email') }}
                        </th>
                        <th class="db-table-head-th">
                            {{ $t('label.phone') }}
                        </th>
                        <th class="db-table-head-th">
                            {{ $t('label.status') }}
                        </th>
                        <th class="db-table-head-th hidden-print"
                            v-if="permissionChecker('restaurants_show') || permissionChecker('restaurants_edit') || permissionChecker('restaurants_delete')">
                            {{ $t('label.action') }}
                        </th>
                    </tr>
                    </thead>
                    <tbody class="db-table-body" v-if="restaurants.length > 0">
                    <tr class="db-table-body-tr" v-for="restaurant in restaurants" :key="restaurant">
                        <td class="db-table-body-td">
                            {{ textShortener(restaurant.name, 40) }}
                        </td>
                        <td class="db-table-body-td">
                            {{ restaurant.email }}
                        </td>
                        <td class="db-table-body-td">
                            <span v-if="restaurant.phone"> {{ restaurant.country_code + '' + restaurant.phone }} </span>
                        </td>
                        <td class="db-table-body-td">
                                <span :class="restaurantStatusClass(restaurant)">
                                    {{ restaurantStatusLabel(restaurant) }}
                                </span>
                        </td>
                        <td class="db-table-body-td hidden-print"
                            v-if="permissionChecker('restaurants_show') || permissionChecker('restaurants_edit') || permissionChecker('restaurants_delete')">
                            <div class="flex justify-start items-center sm:items-start sm:justify-start gap-1.5">
                                <SmIconViewComponent :link="'admin.restaurant.show'" :id="restaurant.id"
                                                     v-if="permissionChecker('restaurants_show')"/>
                                <SmIconSidebarModalEditComponent @click="edit(restaurant)"
                                                                 v-if="permissionChecker('restaurants_edit')"/>
                                <SmIconModalVerifyComponent @click="approve(restaurant)"
                                                            :label="$t('button.approve')"
                                                            v-if="permissionChecker('restaurants_edit') && isPendingRestaurant(restaurant)"/>
                                <SmIconDeleteComponent @click="destroy(restaurant.id)"
                                                       v-if="permissionChecker('restaurants_delete')"/>
                            </div>
                        </td>
                    </tr>
                    </tbody>
                    <tbody class="db-table-body" v-else>
                    <tr class="db-table-body-tr">
                        <td class="db-table-body-td" colspan="5">
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
import LoadingComponent from "../../../components/common/LoadingComponent.vue";
import RestaurantCreateComponent from "./RestaurantCreateComponent.vue";
import alertService from "../../../services/alertService.js";
import statusEnum from "../../../enums/modules/statusEnum.js";
import applyByEnum from "../../../enums/modules/applyByEnum.js";
import PaginationTextComponent from "../components/pagination/PaginationTextComponent.vue";
import PaginationBox from "../components/pagination/PaginationBox.vue";
import PaginationSMBox from "../components/pagination/PaginationSMBox.vue";
import appService from "../../../services/appService.js";
import TableLimitComponent from "../components/TableLimitComponent.vue";
import SmIconSidebarModalEditComponent from "../components/buttons/SmIconSidebarModalEditComponent.vue";
import SmIconDeleteComponent from "../components/buttons/SmIconDeleteComponent.vue";
import SmIconViewComponent from "../components/buttons/SmIconViewComponent.vue";
import SmIconModalVerifyComponent from "../components/buttons/SmIconModalVerifyComponent.vue";
import FilterComponent from "../components/buttons/collapse/FilterComponent.vue";
import ExportComponent from "../components/buttons/export/ExportComponent.vue";
import PrintComponent from "../components/buttons/export/PrintComponent.vue";
import ExcelComponent from "../components/buttons/export/ExcelComponent.vue";
import {usePaper} from "../../../composables/paper.js";
import {useSlide} from "../../../composables/slide.js";
import {useRestaurantStore} from "../../../stores/restaurant.js";
import {useCuisineStore} from "../../../stores/cuisine.js";
import {useFrontendSettingStore} from "../../../stores/frontendSetting.js";
import {useCountryCodeStore} from "../../../stores/countryCode.js";
import {useCompanyStore} from "../../../stores/company.js";
import VueSimpleAlert from "vue3-simple-alert";

export default {
    name: "RestaurantListComponent",
    components: {
        TableLimitComponent,
        PaginationSMBox,
        PaginationBox,
        PaginationTextComponent,
        RestaurantCreateComponent,
        LoadingComponent,
        SmIconSidebarModalEditComponent,
        SmIconDeleteComponent,
        SmIconViewComponent,
        SmIconModalVerifyComponent,
        FilterComponent,
        ExportComponent,
        PrintComponent,
        ExcelComponent
    },
    setup() {
        const restaurantStore      = useRestaurantStore();
        const cuisineStore         = useCuisineStore();
        const frontendSettingStore = useFrontendSettingStore();
        const countryCodeStore     = useCountryCodeStore();
        const companyStore         = useCompanyStore();

        return {
            restaurantStore,
            cuisineStore,
            frontendSettingStore,
            countryCodeStore,
            companyStore
        }
    },
    data() {
        return {
            loading: {
                isActive: false
            },
            enums: {
                statusEnum: statusEnum,
                applyByEnum: applyByEnum,
                statusEnumArray: {
                    [statusEnum.ACTIVE]: this.$t("label.active"),
                    [statusEnum.INACTIVE]: this.$t("label.inactive")
                }
            },
            printLoading: true,
            printObj: {
                id: "print",
                popTitle: this.$t("menu.restaurants")
            },
            props: {
                form: {
                    name: "",
                    email: "",
                    phone: "",
                    user_id: null,
                    cuisine_id: [],
                    latitude: "",
                    longitude: "",
                    city: "",
                    state: "",
                    zip_code: "",
                    address: "",
                    online_commission: "",
                    pos_commission: "",
                    status: statusEnum.ACTIVE,
                    apply: applyByEnum.ADMIN,
                    country_code: "",
                    flag: ""
                },
                search: {
                    paginate: 1,
                    page: 1,
                    per_page: 10,
                    order_column: 'id',
                    order_type: 'desc',
                    name: "",
                    email: "",
                    phone: "",
                    latitude: "",
                    longitude: "",
                    city: "",
                    state: "",
                    zip_code: "",
                    status: null,
                    apply: null,
                    cuisine_id: null
                }
            },
            searchProps: {
                paginate: 0,
                order_column: 'id',
                order_type: 'asc'
            },
            handlePaper: usePaper().handlePaper,
            handleSlide: useSlide().handleSlide
        }
    },
    async mounted() {
        await this.list();
        this.cuisineStore.fetch(this.searchProps);
    },
    computed: {
        setting: function () {
            return this.frontendSettingStore.lists
        },
        restaurants: function () {
            return this.restaurantStore.lists;
        },
        pagination: function () {
            return this.restaurantStore.pagination;
        },
        paginationPage: function () {
            return this.restaurantStore.page;
        },
        cuisines: function () {
            return this.cuisineStore.lists;
        }
    },
    methods: {
        phoneNumber(e) {
            return appService.phoneNumber(e);
        },
        permissionChecker(e) {
            return appService.permissionChecker(e);
        },
        statusClass: function (status) {
            return appService.statusClass(status);
        },
        isPendingRestaurant: function (restaurant) {
            return restaurant.status == statusEnum.INACTIVE
                && restaurant.apply == applyByEnum.RESTAURANT_OWNER;
        },
        restaurantStatusLabel: function (restaurant) {
            if (this.isPendingRestaurant(restaurant)) {
                return this.$t("label.pending");
            }
            return this.enums.statusEnumArray[restaurant.status];
        },
        restaurantStatusClass: function (restaurant) {
            if (this.isPendingRestaurant(restaurant)) {
                return "db-table-badge text-amber-700 bg-amber-100";
            }
            return this.statusClass(restaurant.status);
        },
        textShortener: function (text, number = 30) {
            return appService.textShortener(text, number);
        },
        list: function (page = 1) {
            this.loading.isActive  = true;
            this.props.search.page = page;
            this.restaurantStore.fetch(this.props.search).then((res) => {
                this.loading.isActive = false;
            }).catch((err) => {
                this.loading.isActive = false;
            });
        },
        search: function () {
            this.list();
        },
        clear: function () {
            this.props.search.paginate            = 1;
            this.props.search.page                = 1;
            this.props.search.name                = "";
            this.props.search.email               = "";
            this.props.search.phone               = "";
            this.props.search.latitude            = "";
            this.props.search.longitude           = "";
            this.props.search.city                = "";
            this.props.search.state               = "";
            this.props.search.zip_code            = "";
            this.props.search.status              = null;
            this.props.search.apply               = null;
            this.props.search.cuisine_id          = null;
            this.props.search.online_commission = "";
            this.list();
        },
        edit: function (restaurant) {
            this.loading.isActive = true;
            this.restaurantStore.edit(restaurant.id);
            this.props.form = {
                name: restaurant.name,
                email: restaurant.email,
                phone: restaurant.phone,
                user_id: restaurant.user_id,
                latitude: restaurant.latitude,
                longitude: restaurant.longitude,
                city: restaurant.city,
                state: restaurant.state,
                zip_code: restaurant.zip_code,
                address: restaurant.address,
                status: restaurant.status,
                apply: restaurant.apply,
                country_code: restaurant.country_code,
                online_commission: restaurant.flat_online_commission,
                pos_commission: restaurant.flat_pos_commission,
                cuisine_id: this.cuisineUpdate(restaurant.cuisine_id)
            };
            this.countryCodeStore.fetchFind({country_code: restaurant.country_code}).then(res => {
                this.props.form.flag = res.data.data.flag_emoji;
            }).catch()
            this.loading.isActive = false;
        },
        cuisineUpdate: function (objects) {
            return objects.map(object => object.cuisine_id);
        },
        approve: function (restaurant) {
            return new VueSimpleAlert.confirm(
                this.$t("message.restaurant_approve_confirm"),
                this.$t("message.are_you_sure"),
                "warning",
                {
                    confirmButtonText: this.$t("button.approve"),
                    cancelButtonText: this.$t("button.no_cancel"),
                    confirmButtonColor: "#1AB759",
                    cancelButtonColor: "#E93C3C"
                }
            ).then(() => {
                try {
                    this.loading.isActive = true;
                    this.restaurantStore.approve({id: restaurant.id, search: this.props.search}).then((res) => {
                        this.loading.isActive = false;
                        alertService.success(res.data?.message || this.$t("message.restaurant_approved_successfully"));
                    }).catch((err) => {
                        this.loading.isActive = false;
                        alertService.error(err.response?.data?.message || err.message);
                    });
                } catch (err) {
                    this.loading.isActive = false;
                    alertService.error(err.message);
                }
            }).catch(() => {
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
                    this.restaurantStore.destroy({id: id, search: this.props.search}).then((res) => {
                        this.loading.isActive = false;
                        alertService.successFlip(null, this.$t('menu.restaurants'));
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
            this.restaurantStore.export(this.props.search).then((res) => {
                this.loading.isActive = false;
                const blob            = new Blob([res.data], {
                    type: "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet",
                });
                const link            = document.createElement("a");
                link.href             = URL.createObjectURL(blob);
                link.download         = this.$t("menu.restaurants");
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
