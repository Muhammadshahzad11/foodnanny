<template>
    <LoadingComponent :props="loading"/>
    <section class="pt-8 pb-16">
        <div class="w-full max-w-[360px] mx-auto mt-8 mb-12 p-5 rounded-2xl bg-white shadow-xs">
            <h3 class="capitalize text-xl mb-6 font-semibold text-center">
                {{ $t('label.verify_number') }}
            </h3>
            <form @submit.prevent="save">
                <label for="code" class="field-title !normal-case">{{ $t('label.enter_the_code_sent_to') }}
                    <span class="font-medium">
                        {{ props.form.code + '' + props.form.phone }}
                    </span>
                </label>
                <input :class="errors !== '' ? 'invalid' : ''" type="text" id="code" v-model="props.form.token"
                       class="field-control">
                <small class="db-field-alert" v-if="errors">{{ errors }}</small>
                <br>
                <button @click.prevent="resendCode" type="button"
                        class="mb-6 block text-xs font-medium capitalize text-primary">
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
import {useFrontendSignupStore} from "../../../stores/frontendSignup.js";
import {useAuthStore} from "../../../stores/auth.js";
import alertService from "../../../services/alertService.js";
import {useCommonStore} from "../../../stores/common.js";

export default {
    name: "SignupVerifyComponent",
    components: {LoadingComponent},
    setup() {
        const authStore           = useAuthStore();
        const commonStore         = useCommonStore();
        const frontendSignupStore = useFrontendSignupStore();

        return {
            authStore,
            commonStore,
            frontendSignupStore,

        }
    },
    data() {
        return {
            loading: {
                isActive: false,
            },
            props: {
                form: {
                    phone: "",
                    token: "",
                    code: "",
                },
            },
            errors: "",
        };
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
    },
    methods: {
        phoneChecking: function () {
            const code  = this.frontendSignupStore.code;
            const phone = this.frontendSignupStore.phone;
            if (code && phone) {
                this.props.form.code  = code;
                this.props.form.phone = phone;
            } else {
                this.$router.push({name: 'auth.signupPhone'});
            }
        },
        resendCode: function () {
            try {
                this.loading.isActive = true;
                this.frontendSignupStore.callPhone({
                    code: this.frontendSignupStore.code,
                    phone: this.frontendSignupStore.phone
                }).then(async (res) => {
                    this.loading.isActive = false;
                    this.errors           = "";
                    alertService.success(res.data.message);
                    if (res.data?.otp) {
                        this.props.form.token = String(res.data.otp);
                        await alertService.showOtp(res.data.otp);
                    }
                }).catch((err) => {
                    this.loading.isActive = false;
                    this.errors           = err.response.data.message;
                });
            } catch (err) {
                this.loading.isActive = false;
                alertService.error(err);
            }
        },
        save: function () {
            try {
                this.loading.isActive = true;
                this.frontendSignupStore.callVerify(this.props.form).then((res) => {
                    this.loading.isActive = false;
                    alertService.success(res.data.message);
                    this.props.form = {
                        phone: "",
                        token: "",
                        code: ""
                    };
                    this.errors     = '';
                    this.$router.push({
                        name: "auth.signupRegister",
                    });
                }).catch((err) => {
                    this.loading.isActive = false;
                    this.errors           = err.response.data.message;
                });
            } catch (err) {
                this.loading.isActive = false;
                alertService.error(err);
            }
        }
    }
}
</script>
