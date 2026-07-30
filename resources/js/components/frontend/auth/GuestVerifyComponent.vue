<template>
    <LoadingComponent :props="loading"/>
    <section class="pt-8 pb-16">
        <div class="w-full max-w-[360px] mx-auto mt-8 mb-12 p-5 rounded-2xl bg-white shadow-xs">
            <h2 class="capitalize text-xl mb-6 font-semibold text-center">
                {{ $t('label.verify_number') }}
            </h2>
            <form @submit.prevent="save">
                <label for="code" class="field-title !normal-case">{{ $t('label.enter_the_code_sent_to') }}
                    <span class="font-medium">
                        {{ props.form.code + '' + props.form.phone }}
                    </span>
                </label>
                <input id="code" :class="errors !== '' ? 'invalid' : ''" type="text" v-model="props.form.token"
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
import {useFrontendGuestSignupStore} from "../../../stores/frontendGuestSignup.js";
import alertService from "../../../services/alertService.js";
import {useAuthStore} from "../../../stores/auth.js";
import {useCommonStore} from "../../../stores/common.js";
import {useFrontendCartStore} from "../../../stores/frontendCart.js";
import appService from "../../../services/appService.js";
import router from "../../../router/index.js";
import {useDefaultAccessStore} from "../../../stores/defaultAccess.js";

export default {
    name: "GuestVerifyComponent",
    components: {LoadingComponent},
    setup() {
        const authStore                = useAuthStore();
        const commonStore              = useCommonStore();
        const frontendCartStore        = useFrontendCartStore();
        const defaultAccessStore       = useDefaultAccessStore();
        const frontendGuestSignupStore = useFrontendGuestSignupStore();

        return {
            authStore,
            commonStore,
            frontendCartStore,
            defaultAccessStore,
            frontendGuestSignupStore
        }
    },
    data() {
        return {
            loading: {
                isActive: false,
            },
            props: {
                form: {
                    code: "",
                    phone: "",
                    token: ""
                }
            },
            errors: ''
        }
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
        }
        await this.phoneChecking();
    },
    methods: {
        phoneChecking: function () {
            const code  = this.frontendGuestSignupStore.code;
            const phone = this.frontendGuestSignupStore.phone;
            if (code && phone) {
                this.props.form.code  = code;
                this.props.form.phone = phone;
            } else {
                this.$router.push({name: 'auth.guestLogin'});
            }
        },
        resendCode: function () {
            try {
                this.loading.isActive = true;
                this.frontendGuestSignupStore.callPhone({
                    code: this.frontendGuestSignupStore.code,
                    phone: this.frontendGuestSignupStore.phone
                }).then(res => {
                    this.loading.isActive = false;
                    this.errors           = "";
                    alertService.success(res.data.message);
                }).catch((err) => {
                    this.loading.isActive = false;
                    this.errors           = err.response.data.message;
                });
            } catch (err) {
                this.loading.isActive = false;
                alertService.error(err);
            }
        },
        save: async function () {
            try {
                this.loading.isActive = true;
                await this.authStore.guestLoginVerify(this.props.form).then(async loginRes => {
                    this.errors = {};
                    await this.frontendGuestSignupStore.callReset();
                    await this.defaultAccessStore.fetch();
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

                    if (this.carts.length > 0 && this.location) {
                        await this.$router.push({name: "frontend.checkout"});
                    } else if (this.location) {
                        await this.$router.push({name: "frontend.restaurant"});
                    } else {
                        await this.$router.push({name: "frontend.home"});
                    }
                }).catch((err) => {
                    this.loading.isActive = false;
                    this.errors           = err.response.data.message;
                })
            } catch (err) {
                this.loading.isActive = false;
                alertService.error(err);
            }
        }
    }
}
</script>
