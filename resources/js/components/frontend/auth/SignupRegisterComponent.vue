<template>
    <LoadingComponent :props="loading"/>
    <section class="pt-8 pb-16">
        <div class="w-full max-w-[550px] mx-auto mt-8 mb-12 p-5 rounded-2xl bg-white shadow-xs">
            <h2 class="capitalize text-xl mb-6 font-semibold text-center">
                {{ $t('label.create_account') }}
            </h2>
            <form @submit.prevent="save">
                <div class="row !-m-2">
                    <div class="col-12 sm:col-6 !p-2">
                        <label for="first_name" class="field-title required">{{ $t('label.first_name') }}</label>
                        <input id="first_name" type="text" v-model="props.form.first_name" v-bind:class="errors.first_name ? 'invalid' : ''" class="field-control">
                        <small class="db-field-alert" v-if="errors.first_name">
                            {{ errors.first_name[0] }}
                        </small>
                    </div>
                    <div class="col-12 sm:col-6 !p-2">
                        <label for="last_name" class="field-title required">{{ $t('label.last_name') }}</label>
                        <input id="last_name" type="text" v-model="props.form.last_name" v-bind:class="errors.last_name ? 'invalid' : ''" class="field-control">
                        <small class="db-field-alert" v-if="errors.last_name">
                            {{ errors.last_name[0] }}
                        </small>
                    </div>
                    <div class="col-12 sm:col-6 !p-2">
                        <label for="email" class="field-title">{{ $t('label.email') }}</label>
                        <input id="email" type="email" v-model="props.form.email" v-bind:class="errors.email ? 'invalid' : ''" class="field-control">
                        <small class="db-field-alert" v-if="errors.email">
                            {{ errors.email[0] }}
                        </small>
                    </div>
                    <div class="col-12 sm:col-6 !p-2">
                        <label for="password" class="field-title required">{{ $t('label.password') }}</label>
                        <input id="password" type="password" v-model="props.form.password" v-bind:class="errors.password ? 'invalid' : ''" class="field-control"
                               autocomplete="off">
                        <small class="db-field-alert" v-if="errors.password">
                            {{ errors.password[0] }}
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
                    {{ $t('button.sign_up') }}
                </button>
            </form>
        </div>
    </section>
</template>

<script>

import LoadingComponent from "../../common/LoadingComponent.vue";
import askEnum from "../../../enums/modules/askEnum.js";
import {useFrontendSignupStore} from "../../../stores/frontendSignup.js";
import {useFrontendSettingStore} from "../../../stores/frontendSetting.js";
import {useFrontendPageStore} from "../../../stores/frontendPage.js";
import alertService from "../../../services/alertService.js";
import appService from "../../../services/appService.js";
import router from "../../../router/index.js";
import {useDefaultAccessStore} from "../../../stores/defaultAccess.js";
import {useFrontendCartStore} from "../../../stores/frontendCart.js";
import {useCommonStore} from "../../../stores/common.js";
import {useAuthStore} from "../../../stores/auth.js";
import ENV from "../../../config/env.js";

export default {
    name: "SignupRegisterComponent",
    components: {LoadingComponent},
    setup() {
        const authStore            = useAuthStore();
        const commonStore          = useCommonStore();
        const frontendCartStore    = useFrontendCartStore();
        const frontendPageStore    = useFrontendPageStore();
        const defaultAccessStore   = useDefaultAccessStore();
        const frontendSignupStore  = useFrontendSignupStore();
        const frontendSettingStore = useFrontendSettingStore();

        return {
            authStore,
            commonStore,
            frontendCartStore,
            frontendPageStore,
            defaultAccessStore,
            frontendSignupStore,
            frontendSettingStore
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
                    first_name: "",
                    last_name: "",
                    email: "",
                    password: "",
                    phone: "",
                    country_code: "",
                    token: "",
                    terms_and_conditions: "",
                    page_id: null
                }
            },
            demo: ENV.DEMO,
            slug: "not-found",
            errors: {},
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

        this.phoneChecking();
        this.termsAndConditionsChecking();
    },
    methods: {
        phoneChecking: function () {
            if (this.demo.toLowerCase() === "false") {
                if (this.setting.site_phone_verification === this.enums.askEnum.YES) {
                    if (!this.frontendSignupStore.verify) {
                        this.$router.push({name: 'auth.signupPhone'});
                    }
                }
            }

            const code  = this.frontendSignupStore.code;
            const phone = this.frontendSignupStore.phone;
            const token = this.frontendSignupStore.token;

            if (code && phone) {
                this.props.form.country_code = code;
                this.props.form.phone        = phone;
                this.props.form.token        = token;
            }
        },
        termsAndConditionsChecking: function () {
            this.frontendSettingStore.fetch().then((res) => {
                if (res.data.data.terms_and_conditions_customer_page_id > 0) {
                    this.props.form.page_id = res.data.data.terms_and_conditions_customer_page_id;
                    this.frontendPageStore.info(res.data.data.terms_and_conditions_customer_page_id).then(res => {
                        this.slug = res.data.data.slug;
                    }).catch();
                }
            }).catch((err) => {
                this.loading.isActive = false;
                this.errors           = err.response.data.errors;
            });
        },
        checkTermsAndCondition: function (e) {
            this.props.form.terms_and_conditions = e.target.checked === true ? askEnum.YES : "";
        },
        save: function () {
            try {
                this.loading.isActive = true;
                this.frontendSignupStore.callRegister(this.props.form).then(async res => {
                    this.errors       = {};
                    const credentials = {
                        country_code: this.props.form.country_code,
                        phone: this.props.form.phone,
                        password: this.props.form.password
                    };
                    await this.authStore.phoneLogin(credentials).then(async (loginRes) => {
                        await this.defaultAccessStore.fetch();
                        alertService.success(loginRes.data.message);
                        setTimeout(() => {
                            appService.recursiveRouter(router.options.routes, this.authStore.permission);
                        }, 1000);
                        this.loading.isActive = false;
                        this.props.form       = {
                            first_name: "",
                            last_name: "",
                            email: "",
                            password: "",
                            phone: "",
                            token: "",
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
                        alertService.error(err.response.data.errors?.terms_and_conditions[0]);
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
