<template>
    <LoadingComponent :props="loading"/>
    <section class="pt-8 pb-16">
        <div class="w-full max-w-[360px] mx-auto mt-8 mb-12 p-5 rounded-2xl bg-white shadow-xs">
            <h3 class="capitalize text-xl mb-6 font-semibold text-center">{{ $t('label.verify_email') }}</h3>
            <form @submit.prevent="verifyCode">
                <div class="mb-3">
                    <label for="code" class="field-title !normal-case">{{ $t('label.enter_the_code_sent_to') }}
                        <span class="font-medium lowercase">{{ form.email }}</span>
                    </label>
                    <input id="code" :class="errors.code ? 'invalid' : ''" v-model="form.code" type="number" class="field-control">
                    <small class="db-field-alert" v-if="errors.code">{{ errors.code[0] }}</small>
                </div>
                <button @click.prevent="resendCode" type="button" class="mb-6 block text-xs font-medium capitalize text-primary">
                    {{ $t('button.resend_code') }}
                </button>
                <button type="submit" class="field-button">
                    {{ $t('label.continue') }}
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

export default {
    name: "ForgotPasswordEmailVerifyComponent",
    components: {LoadingComponent},
    setup() {
        const authStore   = useAuthStore();
        const commonStore = useCommonStore();

        return {
            authStore,
            commonStore
        }
    },
    data() {
        return {
            loading: {
                isActive: false,
            },
            form: {
                email: null,
                code: null
            },
            errors: {}
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
            const email = this.authStore.resetInfo.email;
            if (email) {
                this.form.email = email;
            } else {
                this.$router.push({name: 'auth.forgotPassword'});
            }
        },
        resendCode: function () {
            try {
                this.loading.isActive = true;
                this.authStore.forgotPassword(this.form).then((res) => {
                    this.loading.isActive = false;
                    alertService.success(res.data.message);
                }).catch((err) => {
                    this.loading.isActive = false;
                    this.errors           = err.response.data.errors;
                })
            } catch (err) {
                this.loading.isActive = false;
                alertService.error(err.response.data.message);
            }
        },
        verifyCode: function () {
            try {
                this.loading.isActive = true;
                this.authStore.verifyCode(this.form).then((res) => {
                    this.loading.isActive = false;
                    alertService.success(res.data.message);
                    this.$router.push({name: 'auth.forgotPasswordResetPassword'});
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
                alertService.error(err.response.data.message);
            }
        }
    }
}
</script>
