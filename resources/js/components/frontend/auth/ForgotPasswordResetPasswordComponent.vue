<template>
    <LoadingComponent :props="loading"/>
    <section class="pt-8 pb-16">
        <div class="w-full max-w-[360px] mx-auto mt-8 mb-12 p-5 rounded-2xl bg-white shadow-xs">
            <h3 class="capitalize text-xl mb-6 font-semibold text-center">{{ $t('label.create_new_password') }}</h3>
            <form @submit.prevent="resetPassword">
                <div class="mb-6">
                    <label class="field-title">{{ $t('label.password') }}</label>
                    <input :class="errors.password ? 'invalid' : ''" v-model="form.password" type="password"
                           class="field-control">
                    <small class="db-field-alert" v-if="errors.password">{{ errors.password[0] }}</small>
                </div>
                <div class="mb-6">
                    <label class="field-title">{{ $t('label.confirm_password') }}</label>
                    <input :class="errors.confirm_password ? 'invalid' : ''" v-model="form.confirm_password"
                           type="password" class="field-control">
                    <small class="db-field-alert" v-if="errors.confirm_password">{{
                            errors.confirm_password[0]
                        }}</small>
                </div>
                <button type="submit" class="field-button">
                    {{ $t('button.submit') }}
                </button>
            </form>
        </div>
    </section>
</template>

<script>
import LoadingComponent from "../../common/LoadingComponent.vue";
import {useAuthStore} from "../../../stores/auth.js";
import {useCommonStore} from "../../../stores/common.js";
import alertService from "../../../services/alertService.js";
import appService from "../../../services/appService.js";
import router from "../../../router/index.js";
import {useFrontendCartStore} from "../../../stores/frontendCart.js";
import ENV from "../../../config/env.js";
import {useFrontendSettingStore} from "../../../stores/frontendSetting.js";
import askEnum from "../../../enums/modules/askEnum.js";

export default {
    name: "ForgotPasswordResetPasswordComponent",
    components: {LoadingComponent},
    setup() {
        const authStore            = useAuthStore();
        const commonStore          = useCommonStore();
        const frontendCartStore    = useFrontendCartStore();
        const frontendSettingStore = useFrontendSettingStore();

        return {
            authStore,
            commonStore,
            frontendCartStore,
            frontendSettingStore
        }
    },
    data() {
        return {
            loading: {
                isActive: false,
            },
            form: {
                email: null,
                password: null,
                confirm_password: null,
                token: null
            },
            enums: {
                askEnum: askEnum,
            },
            demo: ENV.DEMO,
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
        this.emailChecking();
    },
    methods: {
        emailChecking: function () {
            if (this.demo.toLowerCase() === "false") {
                if (this.setting.site_email_verification === this.enums.askEnum.YES) {
                    if (!this.authStore.resetInfo.verify) {
                        this.$router.push({name: 'auth.forgotPasswordEmailVerify'});
                    }
                }
            }

            const email = this.authStore.resetInfo.email;
            const token = this.authStore.resetInfo.token;

            if (email && token) {
                this.form.email = email;
                this.form.token = token;
            } else {
                this.form.email = email;
                if (this.setting.site_email_verification === this.enums.askEnum.YES) {
                    this.$router.push({name: 'auth.forgotPassword'});
                }
            }
        },
        resetPassword: function () {
            try {
                this.loading.isActive = true;
                this.authStore.resetPassword(this.form).then(async (res) => {
                    this.errors       = {};
                    const credentials = {
                        email: this.form.email,
                        password: this.form.password
                    };
                    await this.authStore.login(credentials).then(async (loginRes) => {
                        await this.defaultAccessStore.fetch();
                        alertService.success(loginRes.data.message);
                        setTimeout(() => {
                            appService.recursiveRouter(router.options.routes, this.authStore.permission);
                        }, 1000);
                        this.loading.isActive = false;

                        this.form = {
                            email: null,
                            password: null,
                            confirm_password: null,
                            token: null
                        }

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

                    if (err.response.data.errors?.token) {
                        alertService.error(err.response.data.errors?.token[0]);
                    }
                })
            } catch (err) {
                this.loading.isActive = false;
                alertService.error(err.response.data.message);
            }
        }
    }
}
</script>
