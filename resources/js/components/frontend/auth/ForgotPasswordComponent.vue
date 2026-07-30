<template>
    <LoadingComponent :props="loading"/>
    <section class="pt-8 pb-16">
        <div class="w-full max-w-[360px] mx-auto mt-8 mb-12 p-5 rounded-2xl bg-white shadow-xs">
            <h3 class="capitalize text-xl mb-6 font-semibold text-center">{{
                    $t('label.forgot_password')
                }}</h3>
            <form @submit.prevent="forgotPassword">
                <div class="mb-6">
                    <label for="email" class="field-title required">{{ $t('label.email') }}</label>
                    <input id="email" :class="errors.email ? 'invalid' : ''" v-model="form.email" type="email"
                           class="field-control">
                    <small class="db-field-alert" v-if="errors.email">{{ errors.email[0] }}</small>
                </div>
                <button type="submit"
                        class="w-full h-12 text-center capitalize font-medium rounded-3xl mb-6 text-white bg-primary">
                    {{ $t('label.next') }}
                </button>
                <div class="flex items-center justify-center gap-1.5">
                    <span class="text-xs text-paragraph">{{ $t('label.already_have_an_account') }}</span>
                    <router-link class="text-xs font-medium capitalize text-primary" :to="{ name: 'auth.login' }">
                        {{ $t('button.login') }}
                    </router-link>
                </div>
            </form>
        </div>
    </section>
</template>
<script>
import LoadingComponent from "../../common/LoadingComponent.vue";
import alertService from "../../../services/alertService.js";
import {useAuthStore} from "../../../stores/auth.js";
import {useCommonStore} from "../../../stores/common.js";
import ENV from "../../../config/env.js";
import {useFrontendSettingStore} from "../../../stores/frontendSetting.js";
import askEnum from "../../../enums/modules/askEnum.js";

export default {
    name: "ForgetPasswordComponent",
    components: {LoadingComponent},
    setup() {
        const authStore   = useAuthStore();
        const commonStore = useCommonStore();
        const frontendSettingStore = useFrontendSettingStore();

        return {
            authStore,
            commonStore,
            frontendSettingStore
        }
    },
    data() {
        return {
            loading: {
                isActive: false,
            },
            form: {
                email: ""
            },
            enums: {
                askEnum: askEnum,
            },
            demo: ENV.DEMO,
            errors: {}
        }
    },
    computed: {
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
    },
    methods: {
        forgotPassword: function () {
            try {
                this.loading.isActive = true;
                this.authStore.forgotPassword(this.form).then((res) => {
                    this.loading.isActive = false;
                    if (this.demo.toLowerCase() === "true") {
                        this.$router.push({name: "auth.forgotPasswordResetPassword"});
                    } else {
                        if (this.setting.site_email_verification === this.enums.askEnum.NO) {
                            this.$router.push({name: "auth.forgotPasswordResetPassword"});
                        } else {
                            alertService.success(res.data.message);
                            this.$router.push({name: 'auth.forgotPasswordEmailVerify'});
                        }
                    }
                }).catch((err) => {
                    this.loading.isActive = false;
                    if (err?.response?.data?.errors) {
                        this.errors = err.response.data.errors;
                    } else if (err?.response?.data?.message) {
                        alertService.error(err.response.data.message);
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
