<template>
    <LoadingComponent :props="loading"/>
    <section class="pb-16">
        <div class="w-full max-w-[550px] mx-auto mt-8 mb-12 p-5 rounded-2xl bg-white shadow-xs">
            <h2 class="capitalize mb-6 text-center text-[22px] font-semibold leading-[34px] text-heading">
                {{ $t('label.restaurant_information') }}
            </h2>
            <form @submit.prevent="save">
                <div class="row">
                    <div class="col-12 sm:col-6 !p-2">
                        <label for="name" class="field-title required">{{ $t('label.restaurant_name') }}</label>
                        <input id="name" type="text" :class="errors.restaurant_name ? 'invalid' : ''"
                               v-model="props.form.restaurant_name"
                               class="field-control">
                        <small class="db-field-alert" v-if="errors.restaurant_name">
                            {{ errors.restaurant_name[0] }}
                        </small>
                    </div>
                    <div class="col-12 sm:col-6 !p-2">
                        <label for="address" class="field-title required">{{ $t('label.restaurant_address') }}</label>
                        <input id="address" type="text" :class="errors.restaurant_address ? 'invalid' : ''"
                               v-model="props.form.restaurant_address" class="field-control">
                        <small class="db-field-alert" v-if="errors.restaurant_address">
                            {{ errors.restaurant_address[0] }}
                        </small>
                    </div>
                    <div class="col-12 sm:col-6 !p-2">
                        <label for="email" class="field-title required">{{ $t('label.restaurant_email') }}</label>
                        <input id="email" type="email" :class="errors.restaurant_email ? 'invalid' : ''"
                               v-model="props.form.restaurant_email" class="field-control">
                        <small class="db-field-alert" v-if="errors.restaurant_email">
                            {{ errors.restaurant_email[0] }}
                        </small>
                    </div>
                    <div class="col-12 sm:col-6 !p-2">
                        <label for="phone" class="field-title required">{{ $t('label.restaurant_phone') }}</label>
                        <div class="relative group">
                            <div
                                :class="[isOpen ? 'rounded-t-lg' : 'rounded-lg', errors.restaurant_phone ? 'invalid' : '']"
                                class="flex w-full h-12 border border-[#d9dbe9] group-hover:border-primary/20 transition-all duration-300 group-focus-within:border-primary/20">
                                <button @click="isOpen = !isOpen" type="button"
                                        :class="errors.restaurant_phone ? 'invalid' : ''"
                                        class="flex items-center gap-1 h-full pl-3 pr-2 border-r border-[#d9dbe9] group-hover:border-primary/20 group-focus-within:border-primary/20 transition-all duration-300">
                                    <span class="text-xl flex-shrink-0">{{ flag }}</span>
                                    <span class="text-sm">{{ countryCode }}</span>
                                    <i class="lab-fill-arrow-down"></i>
                                </button>
                                <input id="phone" v-model="props.form.restaurant_phone"
                                       v-on:keypress="phoneNumber($event)"
                                       type="text" class="pl-2 pr-3 w-full overflow-hidden h-full placeholder:text-sm">
                            </div>
                            <ul :class="isOpen ? 'scale-y-100' : 'scale-y-0'"
                                class="absolute top-12 left-0 z-10 w-full max-h-60 thin-scrolling rounded-b-lg shadow-paper origin-top border-x border-b group-hover:border-primary/20 group-focus-within:border-primary/20 border-[#d9dbe9] bg-white transition-all duration-300">
                                <li v-for="countryCode in countryCodes" @click="handleCountry(countryCode)"
                                    class="flex items-center gap-2 px-3 py-1.5 cursor-pointer hover:bg-gray-100 transition-all duration-300">
                                    <div class="text-xl flex-shrink-0">{{ countryCode.flag }}</div>
                                    <span class="text-sm capitalize flex-auto">{{ countryCode.country_name }}</span>
                                    <span class="text-sm">{{ countryCode.calling_code }}</span>
                                </li>
                            </ul>
                        </div>

                        <small class="db-field-alert" v-if="errors.restaurant_phone">
                            {{ errors.restaurant_phone[0] }}
                        </small>
                    </div>
                    <div class="flex items-start gap-2 mt-4 !pl-2" v-if="slug !== 'not-found'">
                        <input @click="checkTermsAndCondition($event)" type="checkbox" id="terms_and_conditions"
                               :value="enums.askEnum.YES" class="cs-custom-checkbox"/>
                        <label for="terms_and_conditions" class="text-xs -mt-[1px] cursor-pointer">
                            {{ $t('message.singing_up_agree') }}
                            <router-link :to="{ name: 'frontend.page', params: { slug: slug } }" target="_blank"
                                         class="text-primary">
                                {{ $t('menu.terms_and_conditions') }}
                            </router-link>
                        </label>
                    </div>
                    <div class="col-12 sm:col-6 !p-2">
                        <router-link :to="{ name: 'auth.signupRestaurantOwner' }"
                                     class="field-button mt-2 border border-primary text-primary bg-white">
                            {{ $t('label.previous') }}
                        </router-link>
                    </div>
                    <div class="col-12 sm:col-6 !p-2">
                        <button type="submit" class="field-button mt-2">{{ $t('button.sign_up') }}</button>
                    </div>
                </div>
            </form>
        </div>
    </section>
</template>

<script>
import LoadingComponent from "../../common/LoadingComponent.vue";
import {useAuthStore} from "../../../stores/auth.js";
import {useCommonStore} from "../../../stores/common.js";
import {useFrontendRestaurantSignupStore} from "../../../stores/frontendRestaurantSignup.js";
import askEnum from "../../../enums/modules/askEnum.js";
import {useFrontendSettingStore} from "../../../stores/frontendSetting.js";
import {useFrontendPageStore} from "../../../stores/frontendPage.js";
import appService from "../../../services/appService.js";
import {useFrontendCountryCodeStore} from "../../../stores/frontendCountryCode.js";
import alertService from "../../../services/alertService.js";
import {useFrontendCartStore} from "../../../stores/frontendCart.js";
import router from "../../../router/index.js";
import {useDefaultAccessStore} from "../../../stores/defaultAccess.js";

export default {
    name: "RestaurantInfoComponent",
    components: {LoadingComponent},
    setup() {

        const authStore                     = useAuthStore();
        const commonStore                   = useCommonStore();
        const frontendPageStore             = useFrontendPageStore();
        const frontendCartStore             = useFrontendCartStore();
        const defaultAccessStore            = useDefaultAccessStore();
        const frontendSettingStore          = useFrontendSettingStore();
        const frontendCountryCodeStore      = useFrontendCountryCodeStore();
        const frontendRestaurantSignupStore = useFrontendRestaurantSignupStore();


        return {
            authStore,
            commonStore,
            frontendPageStore,
            frontendCartStore,
            defaultAccessStore,
            frontendSettingStore,
            frontendCountryCodeStore,
            frontendRestaurantSignupStore
        }
    },
    data() {
        return {
            loading: {
                isActive: false,
            },
            enums: {
                askEnum: askEnum,
            },
            props: {
                form: {
                    restaurant_name: "",
                    restaurant_email: "",
                    restaurant_address: "",
                    restaurant_country_code: "",
                    restaurant_phone: "",
                    terms_and_conditions: "",
                    page_id: null,
                }
            },
            defaultCountryCode: "",
            countryCode: "",
            flag: "",
            isOpen: false,
            slug: "not-found",
            errors: {}
        }
    },
    computed: {
        countryCodes: function () {
            return this.frontendCountryCodeStore.lists;
        },
        carts: function () {
            return this.frontendCartStore.lists;
        },
        location: function () {
            return this.commonStore.location;
        }
    },
    async mounted() {
        if (this.authStore.status) {
            if (this.commonStore.location) {
                await this.$router.push({name: "frontend.restaurant"});
            } else {
                await this.$router.push({name: "frontend.home"});
            }
        }
        await this.ownerChecking();
        await this.termsAndConditionsChecking();

        this.loading.isActive = true;
        this.frontendCountryCodeStore.fetch().then(res => {
            this.loading.isActive = false;
        }).catch(err => {
            this.loading.isActive = false;
        })

        this.loading.isActive = true;
        this.frontendSettingStore.fetch().then(res => {
            this.defaultCountryCode = res.data.data.company_country_code;
            this.frontendCountryCodeStore.view(this.defaultCountryCode).then(res => {
                this.props.form.restaurant_country_code = res.data.data.calling_code;
                this.countryCode                        = res.data.data.calling_code;
                this.flag                               = res.data.data.flag_emoji;
                this.loading.isActive                   = false;
            }).catch((err) => {
                this.loading.isActive = false;
            });
            this.loading.isActive = false;
        }).catch((err) => {
            this.loading.isActive = false;
        })
    },
    methods: {
        phoneNumber(e) {
            return appService.phoneNumber(e);
        },
        handleCountry: function (val) {
            this.frontendCountryCodeStore.fetchFind({
                country_code: val.calling_code
            }).then(res => {
                this.flag                               = res.data.data.flag_emoji;
                this.countryCode                        = res.data.data.calling_code;
                this.props.form.restaurant_country_code = res.data.data.calling_code;
            }).catch(err => {
                this.loading.isActive = false;
            });
            this.isOpen = false
        },
        ownerChecking: function () {
            if (!this.frontendRestaurantSignupStore.ownerVerify) {
                this.$router.push({name: 'auth.signupRestaurantOwner'});
            }
        },
        termsAndConditionsChecking: async function () {
            await this.frontendSettingStore.fetch().then(async res => {
                if (res.data.data.terms_and_conditions_restaurant_page_id > 0) {
                    this.props.form.page_id = res.data.data.terms_and_conditions_restaurant_page_id;
                    await this.frontendPageStore.info(res.data.data.terms_and_conditions_restaurant_page_id).then(res => {
                        this.slug = res.data.data.slug;
                    }).catch()
                }
            }).catch(err => {
                this.loading.isActive = false;
                this.errors           = err.response.data.errors;
            })
        },
        checkTermsAndCondition: function (e) {
            this.props.form.terms_and_conditions = e.target.checked === true ? askEnum.YES : "";
        },
        save: async function () {
            const formData = {
                owner_name: this.frontendRestaurantSignupStore.ownerName,
                owner_email: this.frontendRestaurantSignupStore.ownerEmail,
                owner_phone: this.frontendRestaurantSignupStore.phone,
                owner_country_code: this.frontendRestaurantSignupStore.code,
                owner_password: this.frontendRestaurantSignupStore.ownerPassword,
                restaurant_name: this.props.form.restaurant_name,
                restaurant_email: this.props.form.restaurant_email,
                restaurant_address: this.props.form.restaurant_address,
                restaurant_country_code: this.props.form.restaurant_country_code,
                restaurant_phone: this.props.form.restaurant_phone,
                terms_and_conditions: this.props.form.terms_and_conditions,
                page_id: this.props.form.page_id,
                token: this.frontendRestaurantSignupStore.token
            }
            try {
                this.loading.isActive = true;
                await this.frontendRestaurantSignupStore.callRegister(formData).then(async (res) => {
                    this.errors       = {};
                    const credentials = {
                        email: formData.owner_email,
                        password: formData.owner_password
                    };
                    await this.authStore.login(credentials).then(async (loginRes) => {
                        await this.defaultAccessStore.fetch();
                        alertService.success(loginRes.data.message);
                        setTimeout(() => {
                            appService.recursiveRouter(router.options.routes, this.authStore.permission);
                        }, 1000);
                        this.loading.isActive = false;
                        this.props.form       = {
                            restaurant_name: "",
                            restaurant_email: "",
                            restaurant_address: "",
                            restaurant_country_code: "",
                            restaurant_phone: "",
                            terms_and_conditions: "",
                            page_id: null
                        };

                        if (this.carts.length > 0 && this.location) {
                            await this.$router.push({name: "frontend.checkout"});
                        } else if (this.location) {
                            await this.$router.push({name: "frontend.restaurant"});
                        } else {
                            await this.$router.push({name: "frontend.home"});
                        }
                    }).catch(async (err) => {
                        this.loading.isActive = false;
                        await this.$router.push({name: "auth.login"});
                    })
                }).catch((err) => {
                    this.loading.isActive = false;
                    if (err?.response?.data?.errors) {
                        this.errors = err.response.data.errors;
                    } else if (err?.response?.data?.message) {
                        alertService.error(err.response.data.message);
                    }

                    if (err.response.data.errors?.terms_and_conditions) {
                        alertService.error(err.response.data.errors.terms_and_conditions[0]);
                    }

                    if (err.response.data.errors?.token) {
                        alertService.error(err.response.data.errors.token[0]);
                    }
                });
            } catch (err) {
                this.loading.isActive = false;
                alertService.error(err);
            }
        }
    }
}
</script>
