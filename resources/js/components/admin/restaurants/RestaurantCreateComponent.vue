<template>
    <LoadingComponent :props="loading"/>
    <SmSidebarModalCreateComponent :props="addButton" @click="addReset"/>
    <div id="sidebar" @click="closeBackdrop"
         class="fixed inset-0 z-50 bg-black/50 duration-500 transition-all invisible opacity-0">
        <div
            class="w-full max-w-xl h-dvh overflow-x-hidden thin-scrolling bg-white ms-auto ltr:translate-x-full rtl:-translate-x-full">
            <div class="flex items-center justify-between p-4 border-b border-slate-100">
                <h3 class="drawer-title">{{ $t("menu.restaurants") }}</h3>
                <button class="lab-line-close font-bold text-base" @click="reset"></button>
            </div>
            <div class="drawer-body">
                <form @submit.prevent="save">
                    <div class="form-row">
                        <div class="form-col-12 sm:form-col-6">
                            <label for="name" class="db-field-title required"> {{ $t("label.name") }} </label>
                            <input v-model="props.form.name" v-bind:class="errors.name ? 'invalid' : ''" type="text"
                                   id="name" class="db-field-control"/>
                            <small class="db-field-alert" v-if="errors.name"> {{ errors.name[0] }} </small>
                        </div>
                        <div class="form-col-12 sm:form-col-6">
                            <label class="db-field-title" for="latitude">{{
                                    $t("label.latitude")
                                }}/{{ $t("label.longitude") }} </label>
                            <div class="db-multiple-field">
                                <input v-model="props.form.latitude" v-bind:class="errors.latitude ? 'invalid' : ''"
                                       type="text" id="latitude"/>
                                <input v-model="props.form.longitude" v-bind:class="errors.longitude ? 'invalid' : ''"
                                       type="text" id="longitude"/>
                                <button @click="add" v-on:click="isMap = true" type="button"
                                        class="lab-fill-map-locate !text-xl" data-modal="#restaurantMap"></button>
                            </div>
                            <small class="db-field-alert" v-if="errors.latitude">{{ errors.latitude[0] }}</small>
                            <small class="db-field-alert" v-if="errors.longitude">{{ errors.longitude[0] }}</small>
                        </div>
                        <div class="form-col-12 sm:form-col-6">
                            <label for="email" class="db-field-title"> {{ $t("label.email") }} </label>
                            <input v-model="props.form.email" v-bind:class="errors.email ? 'invalid' : ''" type="email"
                                   id="email" class="db-field-control"/>
                            <small class="db-field-alert" v-if="errors.email"> {{ errors.email[0] }}</small>
                        </div>

                        <div class="form-col-12 sm:form-col-6">
                            <label for="phone" class="db-field-title">{{ $t('label.phone') }}</label>
                            <div class="relative group">
                                <div :class="[isOpen ? 'rounded-t-md' : 'rounded-md', errors.phone ? 'invalid' : '']"
                                     class="flex w-full h-10 border border-[#e5e7eb] group-hover:border-primary/20 transition-all duration-300 group-focus-within:border-primary/20">
                                    <button @click="isOpen = !isOpen" type="button"
                                            :class="errors.phone ? 'invalid' : ''"
                                            class="flex items-center gap-1 h-full pl-3 pr-2 border-r border-[#d9dbe9] group-hover:border-primary/20 group-focus-within:border-primary/20 transition-all duration-300">
                                        <span class="text-sm flex-shrink-0">{{ props.form.flag }}</span>
                                        <span class="text-sm">{{ props.form.country_code }}</span>
                                        <i class="lab-fill-arrow-down"></i>
                                    </button>
                                    <input v-model="props.form.phone" v-on:keypress="phoneNumber($event)" type="text"
                                           class="pl-2 pr-3 w-full h-full text-sm overflow-hidden placeholder:text-sm">
                                </div>
                                <ul :class="isOpen ? 'scale-y-100' : 'scale-y-0'"
                                    class="absolute top-10 left-0 z-10 w-full max-h-60 thin-scrolling rounded-b-lg shadow-paper origin-top border-x border-b group-hover:border-primary/20 group-focus-within:border-primary/20 border-[#d9dbe9] bg-white transition-all duration-300">
                                    <li v-for="countryCode in countryCodes" @click="handleCountry(countryCode)"
                                        class="flex items-center gap-2 px-3 py-1.5 cursor-pointer hover:bg-gray-100 transition-all duration-300">
                                        <div class="text-sm flex-shrink-0">{{ countryCode.flag }}</div>
                                        <span class="text-sm capitalize flex-auto">{{ countryCode.country_name }}</span>
                                        <span class="text-sm">{{ countryCode.calling_code }}</span>
                                    </li>
                                </ul>
                            </div>
                            <small class="db-field-alert" v-if="errors.phone"> {{ errors.phone[0] }} </small>
                        </div>

                        <div class="form-col-12 sm:form-col-6">
                            <label class="db-field-title required" for="active">{{ $t("label.status") }}</label>
                            <div class="db-field-radio-group">
                                <div class="db-field-radio">
                                    <div class="custom-radio">
                                        <input :value="enums.statusEnum.ACTIVE" v-model="props.form.status" id="active"
                                               type="radio" class="custom-radio-field"/>
                                        <span class="custom-radio-span"></span>
                                    </div>
                                    <label for="active" class="db-field-label">{{ $t("label.active") }}</label>
                                </div>
                                <div class="db-field-radio">
                                    <div class="custom-radio">
                                        <input :value="enums.statusEnum.INACTIVE" v-model="props.form.status"
                                               type="radio" id="inactive" class="custom-radio-field"/>
                                        <span class="custom-radio-span"></span>
                                    </div>
                                    <label for="inactive" class="db-field-label">{{ $t("label.inactive") }}</label>
                                </div>
                            </div>
                        </div>

                        <div class="form-col-12 sm:form-col-6">
                            <label for="city" class="db-field-title required"> {{ $t("label.city") }} </label>
                            <input v-model="props.form.city" v-bind:class="errors.city ? 'invalid' : ''" type="text"
                                   id="city" class="db-field-control"/>
                            <small class="db-field-alert" v-if="errors.city"> {{ errors.city[0] }} </small>
                        </div>

                        <div class="form-col-12 sm:form-col-6">
                            <label for="state" class="db-field-title required">{{ $t("label.state") }}</label>
                            <input v-model="props.form.state" v-bind:class="errors.state ? 'invalid' : ''" type="text"
                                   id="state" class="db-field-control"/>
                            <small class="db-field-alert" v-if="errors.state"> {{ errors.state[0] }} </small>
                        </div>
                        <div class="form-col-12 sm:form-col-6">
                            <label for="zip_code" class="db-field-title required">{{ $t("label.zip_code") }}</label>
                            <input v-model="props.form.zip_code" v-bind:class="errors.zip_code ? 'invalid' : ''"
                                   type="text" id="zip_code" class="db-field-control"/>
                            <small class="db-field-alert" v-if="errors.zip_code">{{ errors.zip_code[0] }}</small>
                        </div>

                        <div class="form-col-12 sm:form-col-6">
                            <label for="cuisine_id" class="db-field-title">{{ $t("label.cuisine") }} </label>
                            <vue-select class="db-field-control f-b-custom-select" id="cuisine_id"
                                        v-bind:class="errors.cuisine_id ? 'invalid' : ''"
                                        v-model="props.form.cuisine_id"
                                        :options="cuisines" label-by="name" value-by="id" :closeOnSelect="true"
                                        :searchable="true" :clearOnClose="true" placeholder="--" search-placeholder="--"
                                        :multiple="true"/>
                            <small class="db-field-alert" v-if="errors.cuisine_id"> {{ errors.cuisine_id[0] }} </small>
                        </div>

                        <div class="form-col-12 sm:form-col-6">
                            <label for="online_commission" class="db-field-title">
                                {{ $t("label.online_commission") }} (%)</label>
                            <input v-on:keypress="floatNumber($event)" v-model="props.form.online_commission"
                                   v-bind:class="errors.online_commission ? 'invalid' : ''"
                                   type="text" id="online_commission" class="db-field-control"
                                   autocomplete="off"/>
                            <small class="db-field-alert" v-if="errors.online_commission">
                                {{ errors.online_commission[0] }}
                            </small>
                        </div>
                        <div class="form-col-12 sm:form-col-6">
                            <label for="pos_commission" class="db-field-title">
                                {{ $t("label.pos_commission") }} (%) </label>
                            <input v-on:keypress="floatNumber($event)" v-model="props.form.pos_commission"
                                   v-bind:class="errors.pos_commission ? 'invalid' : ''"
                                   type="text" id="pos_commission" class="db-field-control"
                                   autocomplete="off"/>
                            <small class="db-field-alert" v-if="errors.pos_commission">
                                {{ errors.pos_commission[0] }}
                            </small>
                        </div>
                        <div class="form-col-12">
                            <label for="address" class="db-field-title required">{{ $t("label.address") }}</label>
                            <textarea v-model="props.form.address" v-bind:class="errors.address ? 'invalid' : ''"
                                      id="address" class="db-field-control"></textarea>
                            <small class="db-field-alert" v-if="errors.address">{{ errors.address[0] }}</small>
                        </div>
                        <div class="form-col-12">
                            <div class="flex flex-wrap gap-3 mt-4">
                                <button type="submit" class="db-btn py-2 text-white bg-primary">
                                    <i class="lab lab-fill-save text-base"></i>
                                    <span>{{ $t("label.save") }}</span>
                                </button>
                                <button type="button" class="modal-btn-outline modal-close" @click="reset">
                                    <i class="lab lab-fill-close-circle text-base"></i>
                                    <span>{{ $t("button.close") }}</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div id="restaurantMap" class="modal">
        <div class="modal-dialog">
            <div class="modal-header">
                <h3 class="modal-title">{{ $t("label.address") }}</h3>
                <button class="modal-close lab-line-close font-bold text-base text-slate-400 hover:text-red-500"
                        @click="mapReset"></button>
            </div>
            <div class="modal-body">
                <form @submit.prevent="save">
                    <div class="form-row">
                        <div class="form-col-12 map-height">
                            <MapComponent v-if="isMap"
                                          :location="{ lat: props.form.latitude, lng: props.form.longitude }"
                                          :position="location"/>
                        </div>
                        <div class="form-col-12">
                            <label for="apartment" class="db-field-title font-medium text-sm my-0">
                                {{ address }}
                            </label>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>

<script>
import LoadingComponent from "../../../components/common/LoadingComponent.vue";
import statusEnum from "../../../enums/modules/statusEnum.js";
import roleEnum from "../../../enums/modules/roleEnum.js";
import alertService from "../../../services/alertService.js";
import MapComponent from "../../common/MapComponent.vue";
import {useModal} from "../../../composables/modal.js";
import {useCanvas} from "../../../composables/canvas.js";
import applyByEnum from "../../../enums/modules/applyByEnum.js";
import appService from "../../../services/appService.js";
import {useRestaurantStore} from "../../../stores/restaurant.js";
import {useCuisineStore} from "../../../stores/cuisine.js";
import {useCountryCodeStore} from "../../../stores/countryCode.js";
import {useCompanyStore} from "../../../stores/company.js";
import SmSidebarModalCreateComponent from "../components/buttons/SmSidebarModalCreateComponent.vue";

export default {
    name: "RestaurantCreateComponent",
    components: {SmSidebarModalCreateComponent, LoadingComponent, MapComponent},
    props: ["props"],
    setup() {
        const restaurantStore  = useRestaurantStore();
        const cuisineStore     = useCuisineStore();
        const countryCodeStore = useCountryCodeStore();
        const companyStore     = useCompanyStore();

        return {
            restaurantStore,
            cuisineStore,
            countryCodeStore,
            companyStore
        }
    },
    data() {
        return {
            loading: {
                isActive: false
            },
            addButton: {
                title: this.$t("button.add_restaurant")
            },
            enums: {
                statusEnum: statusEnum,
                roleEnum: roleEnum
            },
            flag: "",
            countryCode: "",
            isOpen: false,
            isMap: false,
            address: "",
            errors: {},
            closeBackdrop: useCanvas().closeBackdrop
        };
    },
    computed: {
        cuisines: function () {
            return this.cuisineStore.lists;
        },
        countryCodes: function () {
            return this.countryCodeStore.lists;
        }
    },
    async mounted() {
        this.loading.isActive = true;
        await this.countryCodeStore.fetch().then(res => {
            this.loading.isActive = false;
        }).catch(err => {
            this.loading.isActive = false;
        });

        await this.companyStore.fetch().then(async companyRes => {
            await this.countryCodeStore.view(companyRes.data.data.company_country_code).then(res => {
                if (this.props.form.country_code === "") {
                    this.props.form.country_code = res.data.data.calling_code;
                    this.props.form.flag         = res.data.data.flag_emoji;
                    this.countryCode             = res.data.data.calling_code;
                    this.flag                    = res.data.data.flag_emoji;
                }
                this.loading.isActive = false;
            }).catch((err) => {
                this.loading.isActive = false;
            });
        }).catch((err) => {
            this.loading.isActive = false;
        });

        try {
            this.cuisineStore.fetch({
                order_column: 'id',
                order_type: 'asc',
                status: statusEnum.ACTIVE
            }).then((res) => {
                this.loading.isActive = false;
            }).catch((err) => {
                this.loading.isActive = false;
            });
        } catch (err) {
            this.loading.isActive = false;
        }
    },
    methods: {
        phoneNumber(e) {
            return appService.phoneNumber(e);
        },
        floatNumber(e) {
            return appService.floatNumber(e);
        },
        add: function () {
            useModal().openModal('restaurantMap');
        },
        handleCountry: async function (val) {
            await this.countryCodeStore.fetchFind({
                country_code: val.calling_code
            }).then(res => {
                this.props.form.country_code = res.data.data.calling_code;
                this.props.form.flag         = res.data.data.flag_emoji;
            }).catch(err => {
                this.loading.isActive = false;
            });
            this.isOpen = false
        },
        location: function (e) {
            this.address              = e.address;
            this.props.form.latitude  = e.location.lat;
            this.props.form.longitude = e.location.lng;
            this.props.form.city      = e.other.city;
            this.props.form.state     = e.other.state;
            this.props.form.zip_code  = e.other.zipCode;
            this.props.form.address   = e.address;
        },
        addReset: function () {
            this.restaurantStore.reset();
            this.errors            = {};
            this.isMap             = false;
            this.$props.props.form = {
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
                country_code: this.countryCode,
                flag: this.flag
            };
        },
        reset: function () {
            useCanvas().closeCanvas('sidebar');
            this.restaurantStore.reset();
            this.errors            = {};
            this.isMap             = false;
            this.$props.props.form = {
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
                country_code: this.countryCode,
                flag: this.flag
            };
        },
        mapReset: function () {
            this.isMap = false;
            useModal().closeModal('restaurantMap');
        },
        save: function () {
            try {
                const tempId          = this.restaurantStore.temp.temp_id;
                this.loading.isActive = true;
                this.restaurantStore.save(this.props).then((res) => {
                    useCanvas().closeCanvas('sidebar');
                    this.loading.isActive = false;
                    alertService.successFlip(tempId === null ? 0 : 1, this.$t("menu.restaurants"));
                    this.props.form = {
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
                        apply: statusEnum.ACTIVE,
                        country_code: this.countryCode,
                        flag: this.flag
                    };
                    this.isMap      = false;
                    this.errors     = {};
                }).catch((err) => {
                    this.loading.isActive = false;
                    this.errors           = err.response.data.errors;
                });
            } catch (err) {
                this.loading.isActive = false;
                alertService.error(err);
            }
        }
    }
}
</script>
