<template>
    <LoadingComponent :props="loading"/>
    <section class="pb-16">
        <div class="w-full max-w-[360px] mx-auto mt-8 mb-12 p-5 rounded-2xl bg-white shadow-xs">
            <h3 class="capitalize text-xl mb-6 font-semibold text-center">
                {{ $t('label.verify_number') }}
            </h3>
            <form @submit.prevent="save">
                <label for="code" class="field-title normal-case">{{ $t('label.enter_the_code_sent_to') }}
                    <span class="font-medium">
                        {{ props.form.code + '' + props.form.phone }}
                    </span>
                </label>
                <input :class="errors !== '' ? 'invalid' : ''" type="text" v-model="props.form.token" id="code" class="field-control">
                <small class="db-field-alert" v-if="errors">{{ errors }}</small>
                <br>
                <button @click.prevent="resendCode" type="button" class="mb-6 block text-xs font-medium capitalize text-primary">
                    {{ $t('button.resend_code') }}
                </button>
                <button type="submit" class="field-button">
                    {{ $t('label.next') }}
                </button>
            </form>
        </div>
    </section>
</template>

<script>
import LoadingComponent from "../../common/LoadingComponent.vue";
import {useFrontendRestaurantSignupStore} from "../../../stores/frontendRestaurantSignup.js";
import alertService from "../../../services/alertService.js";
import {useAuthStore} from "../../../stores/auth.js";
import {useCommonStore} from "../../../stores/common.js";

export default {
    name: "RestaurantVerifyComponent",
    components: {LoadingComponent},
    setup() {
        const authStore                     = useAuthStore();
        const commonStore                   = useCommonStore();
        const frontendRestaurantSignupStore = useFrontendRestaurantSignupStore();

        return {
            authStore,
            commonStore,
            frontendRestaurantSignupStore
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
                },
            },
            errors: ""
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
            const code  = this.frontendRestaurantSignupStore.code;
            const phone = this.frontendRestaurantSignupStore.phone;
            if (code && phone) {
                this.props.form.code  = code;
                this.props.form.phone = phone;
            } else {
                this.$router.push({name: 'auth.signupRestaurant'});
            }
        },
        resendCode: function () {
            try {
                this.loading.isActive = true;
                this.frontendRestaurantSignupStore.callPhone({
                    code: this.frontendRestaurantSignupStore.code,
                    phone: this.frontendRestaurantSignupStore.phone
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
        save: function () {
            try {
                this.loading.isActive = true;
                this.frontendRestaurantSignupStore.callVerify(this.props.form).then((res) => {
                    this.loading.isActive = false;
                    alertService.success(res.data.message);
                    this.props.form = {
                        code: "",
                        phone: "",
                        token: ""
                    };
                    this.errors     = '';
                    this.$router.push({
                        name: "auth.signupRestaurantOwner",
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
