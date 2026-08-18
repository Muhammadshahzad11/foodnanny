<template>
    <LoadingComponent :props="loading"/>

    <div class="col-12">
        <div class="row">
            <div class="col-12 sm:col-6 xl:col-3">
                <button type="button" @click="handleTab($event, 'tab-information')"
                        class="tab-active tab-button w-full flex items-center gap-2 px-4 h-10 rounded-lg bg-white hover:text-primary hover:bg-primary/10">
                    <i class="lab lab-line-info-circle text-lg flex-shrink-0"></i>
                    <span
                        class="flex-auto ltr:text-left rtl:text-right text-sm capitalize whitespace-nowrap text-ellipsis overflow-hidden">
                        {{ $t("label.information") }}
                    </span>
                </button>
            </div>

            <div class="col-12 sm:col-6 xl:col-3">
                <button type="button" @click="handleTab($event, 'tab-image')"
                        class="tab-button w-full flex items-center gap-2 px-4 h-10 rounded-lg bg-white hover:text-primary hover:bg-primary/10">
                    <i class="lab lab-line-upload-image text-lg flex-shrink-0"></i>
                    <span
                        class="flex-auto ltr:text-left rtl:text-right text-sm capitalize whitespace-nowrap text-ellipsis overflow-hidden">
                        {{ $t("label.image") }}
                    </span>
                </button>
            </div>

            <div class="col-12 sm:col-6 xl:col-3">
                <button type="button" @click="handleTab($event, 'tab-owner')"
                        class="tab-button w-full flex items-center gap-2 px-4 h-10 rounded-lg bg-white hover:text-primary hover:bg-primary/10">
                    <i class="lab lab-line-restaurant-owners text-lg flex-shrink-0"></i>
                    <span
                        class="flex-auto ltr:text-left rtl:text-right text-sm capitalize whitespace-nowrap text-ellipsis overflow-hidden">
                        {{ $t("label.owner") }}
                    </span>
                </button>
            </div>

            <div class="col-12 sm:col-6 xl:col-3">
                <button type="button" @click="handleTab($event, 'tab-reviews')"
                        class="tab-button w-full flex items-center gap-2 px-4 h-10 rounded-lg bg-white hover:text-primary hover:bg-primary/10">
                    <i class="lab lab-line-review text-lg flex-shrink-0"></i>
                    <span
                        class="flex-auto ltr:text-left rtl:text-right text-sm capitalize whitespace-nowrap text-ellipsis overflow-hidden">
                        {{ $t("label.reviews") }}
                    </span>
                </button>
            </div>
        </div>
    </div>

    <div class="col-12">
        <div class="tab-content db-card tab-active" id="tab-information">
            <div class="db-card">
                <div class="db-card-header">
                    <h3 class="db-card-title">{{ $t("label.basic_info") }}</h3>
                </div>
                <div class="db-card-body">
                    <div class="row py-2">
                        <div class="col-12 sm:col-6 !py-1.5">
                            <div class="db-list-item p-0">
                                <span class="db-list-item-title w-full sm:w-1/2">{{ $t('label.name') }}</span>
                                <span class="db-list-item-text w-full sm:w-1/2">{{ restaurant.name }}</span>
                            </div>
                        </div>

                        <div class="col-12 sm:col-6 !py-1.5">
                            <div class="db-list-item p-0">
                                <span class="db-list-item-title w-full sm:w-1/2">{{ $t('label.user') }}</span>
                                <span class="db-list-item-text w-full sm:w-1/2">{{ restaurant.user_name }}</span>
                            </div>
                        </div>

                        <div class="col-12 sm:col-6 !py-1.5">
                            <div class="db-list-item p-0">
                                <span class="db-list-item-title w-full sm:w-1/2">{{ $t('label.latitude') }}</span>
                                <span class="db-list-item-text w-full sm:w-1/2">{{ restaurant.latitude }}</span>
                            </div>
                        </div>

                        <div class="col-12 sm:col-6 !py-1.5">
                            <div class="db-list-item p-0">
                                <span class="db-list-item-title w-full sm:w-1/2">{{ $t('label.longitude') }}</span>
                                <span class="db-list-item-text w-full sm:w-1/2">{{ restaurant.longitude }}</span>
                            </div>
                        </div>

                        <div class="col-12 sm:col-6 !py-1.5">
                            <div class="db-list-item p-0">
                                <span class="db-list-item-title w-full sm:w-1/2">{{ $t('label.email') }}</span>
                                <span class="db-list-item-text w-full sm:w-1/2">{{ restaurant.email }}</span>
                            </div>
                        </div>

                        <div class="col-12 sm:col-6 !py-1.5">
                            <div class="db-list-item p-0">
                                <span class="db-list-item-title w-full sm:w-1/2">{{ $t('label.phone') }}</span>
                                <span class="db-list-item-text w-full sm:w-1/2">
                                    <span v-if="restaurant.phone" class="db-list-item-text w-full sm:w-1/2">{{
                                            restaurant.country_code + "" + restaurant.phone
                                        }}</span>
                                </span>
                            </div>
                        </div>

                        <div class="col-12 sm:col-6 !py-1.5">
                            <div class="db-list-item p-0">
                                <span class="db-list-item-title w-full sm:w-1/2">{{ $t('label.city') }}</span>
                                <span class="db-list-item-text w-full sm:w-1/2">{{ restaurant.city }}</span>
                            </div>
                        </div>

                        <div class="col-12 sm:col-6 !py-1.5">
                            <div class="db-list-item p-0">
                                <span class="db-list-item-title w-full sm:w-1/2">{{ $t('label.state') }}</span>
                                <span class="db-list-item-text w-full sm:w-1/2">{{ restaurant.state }}</span>
                            </div>
                        </div>

                        <div class="col-12 sm:col-6 !py-1.5">
                            <div class="db-list-item p-0">
                                <span class="db-list-item-title w-full sm:w-1/2">{{ $t('label.zip_code') }}</span>
                                <span class="db-list-item-text w-full sm:w-1/2">{{
                                        restaurant.zip_code
                                    }}</span>
                            </div>
                        </div>

                        <div class="col-12 sm:col-6 !py-1.5">
                            <div class="db-list-item p-0">
                                <span class="db-list-item-title w-full sm:w-1/2">{{ $t('label.cuisine') }}</span>
                                <span class="db-list-item-text w-full sm:w-1/2">{{
                                        restaurant.cuisine
                                    }}</span>
                            </div>
                        </div>

                        <div class="col-12 sm:col-6 !py-1.5">
                            <div class="db-list-item p-0">
                                <span class="db-list-item-title w-full sm:w-1/2">{{
                                        $t('label.online_commission')
                                    }}</span>
                                <span class="db-list-item-text w-full sm:w-1/2">
                                    {{ restaurant.flat_online_commission }} (%)
                                </span>
                            </div>
                        </div>
                        <div class="col-12 sm:col-6 !py-1.5">
                            <div class="db-list-item p-0">
                                <span class="db-list-item-title w-full sm:w-1/2">{{ $t('label.pos_commission') }}</span>
                                <span class="db-list-item-text w-full sm:w-1/2">
                                    {{ restaurant.flat_pos_commission }} (%)
                                </span>
                            </div>
                        </div>
                        <div class="col-12 sm:col-6 !py-1.5">
                            <div class="db-list-item p-0">
                                <span class="db-list-item-title w-full sm:w-1/2">{{ $t('label.status') }}</span>
                                <span class="db-list-item-text w-full sm:w-1/2">
                                    <span v-if="isPendingRestaurant">{{ $t('label.pending') }}</span>
                                    <span v-else>{{ enums.statusEnumArray[restaurant.status] }}</span>
                                </span>
                            </div>
                        </div>

                        <div class="col-12 sm:col-6 !py-1.5">
                            <div class="db-list-item p-0">
                                <span class="db-list-item-title w-full sm:w-1/2">{{ $t('label.current_status') }}</span>
                                <span class="db-list-item-text w-full sm:w-1/2">
                                    <span>{{ enums.statusEnumArray[restaurant.current_status] }}</span>
                                </span>
                            </div>
                        </div>

                        <div class="col-12 sm:col-6 !py-1.5">
                            <div class="db-list-item p-0">
                                <span class="db-list-item-title w-full sm:w-1/2">{{ $t('label.apply_by') }}</span>
                                <span class="db-list-item-text w-full sm:w-1/2">
                                    {{ enums.applyByEnumArray[restaurant.apply] }}
                                </span>
                            </div>
                        </div>

                        <div class="col-12 !py-1.5" v-if="isPendingRestaurant && permissionChecker('restaurants_edit')">
                            <button type="button" class="db-btn py-2 text-white bg-primary" @click="approve">
                                <i class="lab lab-line-shield lab-font-size-16"></i>
                                <span>{{ $t('button.approve') }}</span>
                            </button>
                        </div>

                        <div class="col-12 !py-1.5">
                            <div class="db-list-item p-0">
                                <span class="db-list-item-text w-full">
                                    <span class="db-list-item-title">{{ $t('label.address') }}</span><br>
                                    <span class="mt-2">{{ restaurant.address }}</span>
                                </span>
                            </div>
                        </div>
                        <div v-if="restaurant.description" class="col-12 !py-1.5">
                            <div class="db-list-item p-0">
                                <span class="db-list-item-text w-full">
                                    <span class="db-list-item-title">{{ $t('label.description') }}</span><br>
                                    <span class="mt-2">{{ restaurant.description }}</span>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="tab-content db-card" id="tab-image">
            <div class="db-card">
                <div class="db-card-body">
                    <div class="row">
                        <div class="form-col-12 xl:form-col-6">
                            <fieldset class="p-4 mb-6 border border-[#DBDEE0]">
                                <legend
                                    class="py-1.5 px-4 text-base font-semibold capitalize border border-[#DBDEE0] text-primary">
                                    {{ $t('label.logo') }}
                                </legend>
                                <div class="row py-2">
                                    <form @submit.prevent="saveLogo" class="w-auto">
                                        <p class="mt-2 px-4">{{ $t('label.size') }}: (1:1)</p>
                                        <div class="flex gap-3 md:gap-4 p-4">
                                            <label for="logo"
                                                   class="db-btn relative cursor-pointer h-[38px] shadow-[0px_6px_10px_rgb(var(--primary)/0.24)] bg-primary text-white">
                                                <i class="lab lab-line-upload-image"></i>
                                                <span
                                                    class="hidden sm:inline-block">{{
                                                        $t("button.upload_new_logo")
                                                    }}</span>
                                                <input v-if="uploadLogoButton" @change="changePreviewLogo"
                                                       ref="logoProperty" accept="image/png, image/jpeg, image/jpg"
                                                       type="file" id="logo"
                                                       class="absolute top-0 left-0 w-full h-full -z-10 opacity-0"/>
                                            </label>
                                            <button v-if="saveLogoButton" type="submit"
                                                    class="db-btn h-[38px] shadow-[0px_6px_10px_rgba(26,_183,_89,_0.24)] text-white bg-[#1AB759]">
                                                <i class="lab lab-line-circle-check"></i>
                                                <span class="hidden sm:inline-block">{{ $t("button.save") }}</span>
                                            </button>
                                            <button v-if="resetLogoButton" @click="resetPreviewLogo" type="button"
                                                    class="db-btn-outline h-[38px] shadow-[0px_6px_10px_rgba(251,_78,_78,_0.24)] !text-[#FB4E4E] !bg-white !border-[#FB4E4E]">
                                                <i class="lab lab-line-reset"></i>
                                                <span class="hidden sm:inline-block">{{ $t("button.reset") }}</span>
                                            </button>
                                        </div>
                                    </form>
                                </div>
                                <div class="col-12 sm:col-5">
                                    <img class="w-[120px] h-[120px] object-fill db-image" alt="restaurant-logo"
                                         :src="previewLogo"/>
                                </div>
                            </fieldset>
                        </div>

                        <div class="form-col-12 xl:form-col-6">
                            <fieldset class="p-4 mb-6 border border-[#DBDEE0]">
                                <legend
                                    class="py-1.5 px-4 text-base font-semibold capitalize border border-[#DBDEE0] text-primary">
                                    {{ $t('label.cover') }}
                                </legend>
                                <div class="row py-2">
                                    <form @submit.prevent="saveImage" class="w-auto">
                                        <p class="mt-2 px-4">{{ $t('label.size') }}: (1126px,636px)</p>
                                        <div class="flex gap-3 md:gap-4 p-4">
                                            <label for="photo"
                                                   class="db-btn relative cursor-pointer h-[38px] shadow-[0px_6px_10px_rgb(var(--primary)/0.24)] bg-primary text-white">
                                                <i class="lab lab-line-upload-image"></i>
                                                <span class="hidden sm:inline-block">{{
                                                        $t("button.upload_new_cover")
                                                    }}</span>
                                                <input v-if="uploadButton" @change="changePreviewImage"
                                                       ref="imageProperty" accept="image/png, image/jpeg, image/jpg"
                                                       type="file" id="photo"
                                                       class="absolute top-0 left-0 w-full h-full -z-10 opacity-0"/>
                                            </label>
                                            <button v-if="saveButton" type="submit"
                                                    class="db-btn h-[38px] shadow-[0px_6px_10px_rgba(26,_183,_89,_0.24)] text-white bg-[#1AB759]">
                                                <i class="lab lab-line-circle-check"></i>
                                                <span class="hidden sm:inline-block">{{ $t("button.save") }}</span>
                                            </button>
                                            <button v-if="resetButton" @click="resetPreviewImage" type="button"
                                                    class="db-btn-outline h-[38px] shadow-[0px_6px_10px_rgba(251,_78,_78,_0.24)] !text-[#FB4E4E] !bg-white !border-[#FB4E4E]">
                                                <i class="lab lab-line-reset"></i>
                                                <span class="hidden sm:inline-block">{{ $t("button.reset") }}</span>
                                            </button>
                                        </div>
                                    </form>
                                </div>
                                <div class="col-12 sm:col-5">
                                    <img class="db-image" alt="restaurant" :src="previewImage"/>
                                </div>
                            </fieldset>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="tab-content db-card" id="tab-owner">
            <div class="db-card-header">
                <h3 class="db-card-title">{{ $t('label.owner_info') }}</h3>
            </div>
            <div class="db-card-body">
                <form @submit.prevent="userSave" class="d-block w-full">
                    <div class="form-row">
                        <div class="form-col-12 sm:form-col-6">
                            <label for="restaurant_owner_name" class="db-field-title required">{{
                                    $t('label.name')
                                }}</label>
                            <input type="text" id="restaurant_owner_name" class="db-field-control" v-model="form.name"
                                   :class="errors.name ? 'invalid' : ''">
                            <small class="db-field-alert" v-if="errors.name">
                                {{ errors.name[0] }}
                            </small>
                        </div>

                        <div class="form-col-12 sm:form-col-6">
                            <label for="restaurant_owner_email"
                                   class="db-field-title required">{{ $t('label.email') }}</label>
                            <input type="text" id="restaurant_owner_email" class="db-field-control" v-model="form.email"
                                   :class="errors.email ? 'invalid' : ''">
                            <small class="db-field-alert" v-if="errors.email">
                                {{ errors.email[0] }}
                            </small>
                        </div>

                        <div class="form-col-12 sm:form-col-6">
                            <label for="restaurant_owner_phone" class="db-field-title">{{ $t('label.phone') }}</label>
                            <div class="relative group">
                                <div :class="[isOpen ? 'rounded-t-md' : 'rounded-md', errors.phone ? 'invalid' : '']"
                                     class="flex w-full h-10 border border-[#e5e7eb] group-hover:border-primary/20 transition-all duration-300 group-focus-within:border-primary/20">
                                    <button @click="isOpen = !isOpen" type="button"
                                            :class="errors.phone ? 'invalid' : ''"
                                            class="flex items-center gap-1 h-full pl-3 pr-2 border-r border-[#d9dbe9] group-hover:border-primary/20 group-focus-within:border-primary/20 transition-all duration-300">
                                        <span class="text-sm flex-shrink-0">{{ form.flag }}</span>
                                        <span class="text-sm">{{ form.country_code }}</span>
                                        <i class="lab-fill-arrow-down"></i>
                                    </button>
                                    <input v-model="form.phone" v-on:keypress="phoneNumber($event)"
                                           id="restaurant_owner_phone" type="text"
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
                            <label for="restaurant_owner_password" class="db-field-title required">
                                {{ $t("label.new_password") }}
                            </label>
                            <input v-model="form.password" v-bind:class="errors.password ? 'invalid' : ''"
                                   type="password" id="restaurant_owner_password" class="db-field-control"
                                   autocomplete="off"/>
                            <small class="db-field-alert" v-if="errors.password">
                                {{ errors.password[0] }}
                            </small>
                        </div>

                        <div class="form-col-12 sm:form-col-6">
                            <label for="restaurant_owner_confirm_password" class="db-field-title required">
                                {{ $t("label.confirm_new_password") }}
                            </label>
                            <input v-model="form.confirm_password"
                                   v-bind:class="errors.confirm_password ? 'invalid' : ''" type="password"
                                   id="restaurant_owner_confirm_password" class="db-field-control" autocomplete="off"/>
                            <small class="db-field-alert" v-if="errors.confirm_password">
                                {{ errors.confirm_password[0] }}
                            </small>
                        </div>

                        <div class="form-col-12">
                            <button type="submit" class="db-btn text-white bg-primary">
                                <i class="lab lab-fill-save text-base"></i>
                                <span>{{ $t("button.save") }}</span>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <div class="tab-content db-card" id="tab-reviews">
            <ReviewListComponent/>
        </div>
    </div>
</template>

<script>
import LoadingComponent from "../../../components/common/LoadingComponent.vue";
import statusEnum from "../../../enums/modules/statusEnum";
import applyByEnum from "../../../enums/modules/applyByEnum";
import appService from "../../../services/appService";
import alertService from "../../../services/alertService";
import {useTab} from "../../../composables/tab";
import ReviewListComponent from "./review/ReviewListComponent.vue";
import {useRestaurantStore} from "../../../stores/restaurant";
import {useCuisineStore} from "../../../stores/cuisine";
import {useFrontendSettingStore} from "../../../stores/frontendSetting";
import {useCountryCodeStore} from "../../../stores/countryCode";
import {useCompanyStore} from "../../../stores/company.js";
import VueSimpleAlert from "vue3-simple-alert";

export default {
    name: "RestaurantShowComponent",
    components: {
        LoadingComponent,
        ReviewListComponent
    },
    setup() {
        const companyStore         = useCompanyStore();
        const restaurantStore      = useRestaurantStore();
        const cuisineStore         = useCuisineStore();
        const frontendSettingStore = useFrontendSettingStore();
        const countryCodeStore     = useCountryCodeStore();
        return {
            companyStore,
            restaurantStore,
            cuisineStore,
            frontendSettingStore,
            countryCodeStore
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
                },
                applyByEnumArray: {
                    [applyByEnum.ADMIN]: this.$t("label.admin"),
                    [applyByEnum.RESTAURANT_OWNER]: this.$t("label.restaurant_owner")
                }
            },
            form: {
                name: "",
                email: "",
                phone: "",
                country_code: "",
                password: "",
                confirm_password: ""
            },
            flag: "",
            countryCode: "",
            isOpen: false,
            errors: {},
            defaultImage: null,
            previewImage: null,
            uploadButton: true,
            resetButton: false,
            saveButton: false,

            defaultLogo: null,
            previewLogo: null,
            uploadLogoButton: true,
            resetLogoButton: false,
            saveLogoButton: false,
            handleTab: useTab().handleTab
        }
    },
    computed: {
        restaurant: function () {
            return this.restaurantStore.show;
        },
        countryCodes: function () {
            return this.countryCodeStore.lists;
        },
        isPendingRestaurant: function () {
            return this.restaurant
                && this.restaurant.status == statusEnum.INACTIVE
                && this.restaurant.apply == applyByEnum.RESTAURANT_OWNER;
        }
    },
    async mounted() {
        this.loading.isActive = true;
        this.restaurantStore.view(this.$route.params.id).then(res => {
            this.defaultImage      = res.data.data.image;
            this.previewImage      = res.data.data.image;
            this.defaultLogo       = res.data.data.logo;
            this.previewLogo       = res.data.data.logo;
            this.form.name         = res.data.data.user_name;
            this.form.email        = res.data.data.user_email;
            this.form.phone        = res.data.data.user_phone;
            this.form.country_code = res.data.data.user_country_code;

            this.countryCodeStore.fetchFind({country_code: res.data.data.user_country_code}).then(res => {
                this.form.flag = res.data.data.flag_emoji;
            }).catch()
            this.loading.isActive = false;
        }).catch((error) => {
            this.loading.isActive = false;
        });

        await this.countryCodeStore.fetch().then(res => {
            this.loading.isActive = false;
        }).catch(err => {
            this.loading.isActive = false;
        });
    },
    methods: {
        phoneNumber(e) {
            return appService.phoneNumber(e);
        },
        permissionChecker(e) {
            return appService.permissionChecker(e);
        },
        approve: function () {
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
                this.loading.isActive = true;
                this.restaurantStore.approve({
                    id: this.$route.params.id,
                    search: {paginate: 1, page: 1, per_page: 10}
                }).then((res) => {
                    this.restaurantStore.view(this.$route.params.id);
                    this.loading.isActive = false;
                    alertService.success(res.data?.message || this.$t("message.restaurant_approved_successfully"));
                }).catch((err) => {
                    this.loading.isActive = false;
                    alertService.error(err.response?.data?.message || err.message);
                });
            }).catch(() => {
            });
        },
        changePreviewLogo: function (e) {
            if (e.target.files[0]) {
                this.previewLogo     = URL.createObjectURL(e.target.files[0]);
                this.saveLogoButton  = true;
                this.resetLogoButton = true;
            }
        },
        resetPreviewLogo: function () {
            this.$refs.logoProperty.value = null;
            this.previewLogo              = this.defaultLogo;
            this.saveLogoButton           = false;
            this.resetLogoButton          = false;
        },
        saveLogo: function () {
            if (this.$refs.logoProperty.files[0]) {
                try {
                    this.loading.isActive = true;
                    const formData        = new FormData();
                    formData.append("image", this.$refs.logoProperty.files[0]);
                    this.restaurantStore.changeLogo({id: this.$route.params.id, form: formData}).then((res) => {
                        alertService.success(this.$t("message.logo_update"));
                        this.defaultLogo              = res.data.data.logo;
                        this.previewLogo              = res.data.data.logo;
                        this.$refs.logoProperty.value = null;
                        this.saveLogoButton           = false;
                        this.resetLogoButton          = false;
                        this.loading.isActive         = false;
                    }).catch((err) => {
                        this.loading.isActive = false;
                        if (typeof err.response.data.status !== "undefined" && err.response.data.status === false) {
                            alertService.error(err.response.data.message);
                        } else if (err.response.data.errors && err.response.data.errors.image) {
                            err.response.data.errors.image.forEach((error) => {
                                alertService.error(error);
                            });
                        }
                    });
                } catch (err) {
                    this.loading.isActive = false;
                    alertService.error(err.response.data.message);
                }
            }
        },
        changePreviewImage: function (e) {
            if (e.target.files[0]) {
                this.previewImage = URL.createObjectURL(e.target.files[0]);
                this.saveButton   = true;
                this.resetButton  = true;
            }
        },
        resetPreviewImage: function () {
            this.$refs.imageProperty.value = null;
            this.previewImage              = this.defaultImage;
            this.saveButton                = false;
            this.resetButton               = false;
        },
        saveImage: function () {
            if (this.$refs.imageProperty.files[0]) {
                try {
                    this.loading.isActive = true;
                    const formData        = new FormData();
                    formData.append("image", this.$refs.imageProperty.files[0]);
                    this.restaurantStore.changeImage({id: this.$route.params.id, form: formData}).then((res) => {
                        alertService.success(this.$t("message.image_update"));
                        this.defaultImage              = res.data.data.image;
                        this.previewImage              = res.data.data.image;
                        this.$refs.imageProperty.value = null;
                        this.saveButton                = false;
                        this.resetButton               = false;
                        this.loading.isActive          = false;
                    }).catch((err) => {
                        this.loading.isActive = false;
                        if (typeof err.response.data.status !== "undefined" && err.response.data.status === false) {
                            alertService.error(err.response.data.message);
                        } else if (err.response.data.errors && err.response.data.errors.image) {
                            err.response.data.errors.image.forEach((error) => {
                                alertService.error(error);
                            });
                        }
                    });
                } catch (err) {
                    this.loading.isActive = false;
                    alertService.error(err.response.data.message);
                }
            }
        },
        handleCountry: async function (val) {
            await this.countryCodeStore.fetchFind({
                country_code: val.calling_code
            }).then(res => {
                this.form.country_code = res.data.data.calling_code;
                this.form.flag         = res.data.data.flag_emoji;
            }).catch(err => {
                this.loading.isActive = false;
            });
            this.isOpen = false
        },
        userSave: function () {
            try {
                this.loading.isActive = true;
                this.restaurantStore.userSave({id: this.$route.params.id, form: this.form}).then((res) => {
                    this.loading.isActive = false;
                    alertService.success(this.$t("message.restaurant_owner_update_successfully"))
                    this.errors = {};
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
