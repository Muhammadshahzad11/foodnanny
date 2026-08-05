<template>
    <LoadingComponent :props="loading"/>
    <section class="pb-16">
        <div class="w-full max-w-[550px] mx-auto mt-8 mb-12 p-5 rounded-2xl bg-white shadow-xs">
            <h2 class="capitalize mb-6 text-center text-[22px] font-semibold leading-[34px] text-heading">
                {{ $t('label.delivery_information') }}
            </h2>
            <form @submit.prevent="save">
                <div class="row">
                    <div class="col-12 sm:col-6 !p-2">
                        <label for="name" class="field-title required">{{ $t('label.name') }}</label>
                        <input type="text" id="name" v-model="props.form.name" class="field-control"
                               v-bind:class="errors.name ? 'invalid' : ''">
                        <small class="db-field-alert" v-if="errors.name">
                            {{ errors.name[0] }}
                        </small>
                    </div>
                    <div class="col-12 sm:col-6 !p-2">
                        <label for="email" class="field-title required">{{ $t('label.email') }}</label>
                        <input type="email" id="email" v-model="props.form.email" class="field-control"
                               v-bind:class="errors.email ? 'invalid' : ''">
                        <small class="db-field-alert" v-if="errors.email">
                            {{ errors.email[0] }}
                        </small>
                    </div>
                    <div class="col-12 sm:col-6 !p-2">
                        <label for="password" class="field-title required">{{ $t('label.password') }}</label>
                        <input type="password" id="password" v-model="props.form.password" class="field-control"
                               autocomplete="on" v-bind:class="errors.password ? 'invalid' : ''">
                        <small class="db-field-alert" v-if="errors.password">
                            {{ errors.password[0] }}
                        </small>
                    </div>
                    <div class="col-12 sm:col-6 !p-2">
                        <label for="confirm_password" class="field-title required">
                            {{ $t('label.confirm_password') }}
                        </label>
                        <input type="password" id="confirm_password" v-model="props.form.confirm_password"
                               class="field-control" autocomplete="off"
                               v-bind:class="errors.confirm_password ? 'invalid' : ''">
                        <small class="db-field-alert" v-if="errors.confirm_password">
                            {{ errors.confirm_password[0] }}
                        </small>
                    </div>
                </div>
                <div class="flex items-start gap-2 mt-4" v-if="slug !== 'not-found'">
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
                <button type="submit" class="field-button mt-6">
                    {{ $t('button.signup') }}
                </button>
            </form>
        </div>
    </section>
</template>

<script>
import LoadingComponent from "../../common/LoadingComponent.vue";
import askEnum from "../../../enums/modules/askEnum.js";
import {useFrontendDeliveryBoySignupStore} from "../../../stores/frontendDeliveryBoySignup.js";
import {useFrontendSettingStore} from "../../../stores/frontendSetting.js";
import {useFrontendPageStore} from "../../../stores/frontendPage.js";
import alertService from "../../../services/alertService.js";
import appService from "../../../services/appService.js";
import router from "../../../router/index.js";
import {useAuthStore} from "../../../stores/auth.js";
import {useCommonStore} from "../../../stores/common.js";
import {useFrontendCartStore} from "../../../stores/frontendCart.js";
import {useDefaultAccessStore} from "../../../stores/defaultAccess.js";
import ENV from "../../../config/env.js";

export default {
    name: "DeliveryBoyRegisterComponent",
    components: {LoadingComponent},
    setup() {
        const authStore                      = useAuthStore();
        const commonStore                    = useCommonStore();
        const frontendCartStore              = useFrontendCartStore();
        const frontendPageStore              = useFrontendPageStore();
        const defaultAccessStore             = useDefaultAccessStore();
        const frontendSettingStore           = useFrontendSettingStore();
        const frontendDeliveryBoySignupStore = useFrontendDeliveryBoySignupStore();

        return {
            authStore,
            commonStore,
            frontendCartStore,
            frontendPageStore,
            defaultAccessStore,
            frontendSettingStore,
            frontendDeliveryBoySignupStore
        }
    },
    data() {
        return {
            loading: {
                isActive: false,
            },
            enums: {
                askEnum: askEnum
            },
            props: {
                form: {
                    name: "",
                    email: "",
                    password: "",
                    confirm_password: "",
                    phone: "",
                    country_code: "",
                    terms_and_conditions: "",
                    page_id: null,
                    token: ""
                }
            },
            demo: ENV.DEMO,
            slug: "not-found",
            errors: {}
        }
    },
    computed: {
        carts: function () {
            return this.frontendCartStore.lists;
        },
        location: function () {
            return this.commonStore.location;
        },
        setting: function () {
            return this.frontendSettingStore.lists;
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
        await this.phoneChecking();
        await this.termsAndConditionsChecking();
    },
    methods: {
        phoneChecking: function () {
            if (this.demo.toLowerCase() === "false") {
                if (this.setting.site_phone_verification === this.enums.askEnum.YES) {
                    if (!this.frontendDeliveryBoySignupStore.verify) {
                        this.$router.push({name: 'auth.signupDeliveryBoy'});
                    }
                }
            }

            const code  = this.frontendDeliveryBoySignupStore.code;
            const phone = this.frontendDeliveryBoySignupStore.phone;
            const token = this.frontendDeliveryBoySignupStore.token;
            if (code && phone) {
                this.props.form.country_code = code;
                this.props.form.phone        = phone;
                this.props.form.token        = token;
            }
        },
        termsAndConditionsChecking: async function () {
            await this.frontendSettingStore.fetch().then(async res => {
                if (res.data.data.terms_and_conditions_delivery_boy_page_id > 0) {
                    this.props.form.page_id = res.data.data.terms_and_conditions_delivery_boy_page_id;
                    await this.frontendPageStore.info(res.data.data.terms_and_conditions_delivery_boy_page_id).then(res => {
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
            try {
                this.loading.isActive = true;
                await this.frontendDeliveryBoySignupStore.callRegister(this.props.form).then(async res => {
                    this.errors = {};
                    await this.authStore.login({
                        email: this.props.form.email,
                        password: this.props.form.password
                    }).then(async (loginRes) => {
                        await this.defaultAccessStore.fetch();
                        alertService.success(loginRes.data.message);
                        setTimeout(() => {
                            appService.recursiveRouter(router.options.routes, this.authStore.permission);
                        }, 1000);
                        this.loading.isActive = false;
                        this.props.form       = {
                            name: "",
                            email: "",
                            password: "",
                            confirm_password: "",
                            phone: "",
                            country_code: "",
                            terms_and_conditions: "",
                            page_id: null,
                            token: ""
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
                }).catch(err => {
                    this.loading.isActive = false;
                    if (err?.response?.data?.errors) {
                        this.errors = err.response.data.errors;
                    } else if (err?.response?.data?.message) {
                        alertService.error(err.response.data.message);
                    }

                    if (err?.response?.data?.errors?.terms_and_conditions) {
                        alertService.error(err.response.data.errors.terms_and_conditions[0]);
                    }

                    if (err?.response?.data?.errors?.token) {
                        alertService.error(err.response.data.errors.token[0]);
                    }
                })
            } catch (err) {
                this.loading.isActive = false;
                alertService.error(err);
            }
        }
    }
}
</script>
