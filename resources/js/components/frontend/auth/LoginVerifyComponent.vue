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
                       class="field-control" autocomplete="one-time-code" inputmode="numeric">
                <small class="db-field-alert" v-if="errors">{{ errors }}</small>
                <br>
                <button @click.prevent="resendCode" type="button"
                        class="mb-6 block text-xs font-medium capitalize text-primary">
                    {{ $t('button.resend_code') }}
                </button>
                <button type="submit" class="field-button">
                    {{ $t('button.login') }}
                </button>
            </form>
        </div>
    </section>
</template>

<script>
import LoadingComponent from "../../common/LoadingComponent.vue";
import {useAuthStore} from "../../../stores/auth.js";
import alertService from "../../../services/alertService.js";
import {useCommonStore} from "../../../stores/common.js";
import {useFrontendCartStore} from "../../../stores/frontendCart.js";
import appService from "../../../services/appService.js";
import router from "../../../router/index.js";
import {useDefaultAccessStore} from "../../../stores/defaultAccess.js";
import {useDineInContextStore} from "../../../stores/dineInContext.js";
import {useMyRestaurantStore} from "../../../stores/myRestaurant.js";

export default {
    name: "LoginVerifyComponent",
    components: {LoadingComponent},
    setup() {
        const authStore          = useAuthStore();
        const commonStore        = useCommonStore();
        const frontendCartStore  = useFrontendCartStore();
        const defaultAccessStore = useDefaultAccessStore();
        const dineInContextStore = useDineInContextStore();
        const myRestaurantStore  = useMyRestaurantStore();

        return {
            authStore,
            commonStore,
            frontendCartStore,
            defaultAccessStore,
            dineInContextStore,
            myRestaurantStore,
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
    computed: {
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
            return;
        }
        this.phoneChecking();
    },
    methods: {
        phoneChecking: function () {
            const code  = this.authStore.phoneLoginInfo?.code;
            const phone = this.authStore.phoneLoginInfo?.phone;
            if (code && phone) {
                this.props.form.code  = code;
                this.props.form.phone = phone;
            } else {
                this.$router.push({name: 'auth.login'});
            }
        },
        resendCode: function () {
            try {
                this.loading.isActive = true;
                this.authStore.sendPhoneLoginOtp({
                    code: this.props.form.code,
                    phone: this.props.form.phone,
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
                    this.errors           = err?.response?.data?.message || '';
                });
            } catch (err) {
                this.loading.isActive = false;
                alertService.error(err);
            }
        },
        save: async function () {
            try {
                this.loading.isActive = true;
                await this.authStore.phoneLogin(this.props.form).then(async (loginRes) => {
                    this.errors = "";
                    await this.defaultAccessStore.fetch();
                    await this.myRestaurantStore.resetDefaultRestaurant();
                    alertService.success(loginRes.data.message);
                    setTimeout(async () => {
                        await appService.recursiveRouter(router.options.routes, this.authStore.permission);
                    }, 1000);
                    this.loading.isActive = false;
                    this.props.form       = {
                        code: "",
                        phone: "",
                        token: ""
                    };

                    await appService.redirectAfterAuth(this.$router, {
                        carts: this.carts,
                        location: this.location,
                        dineInContext: this.dineInContextStore,
                    });
                }).catch((err) => {
                    this.loading.isActive = false;
                    this.errors           = err?.response?.data?.message
                        || err?.response?.data?.errors?.validation
                        || '';
                })
            } catch (err) {
                this.loading.isActive = false;
                alertService.error(err);
            }
        }
    }
}
</script>
