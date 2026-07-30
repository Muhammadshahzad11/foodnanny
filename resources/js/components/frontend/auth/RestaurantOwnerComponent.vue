<template>
    <LoadingComponent :props="loading"/>
    <section class="pb-16">
        <div class="w-full max-w-[550px] mx-auto mt-8 mb-12 p-5 rounded-2xl bg-white shadow-xs">
            <h2 class="capitalize mb-6 text-center text-[22px] font-semibold leading-[34px] text-heading">
                {{ $t('label.owner_information') }}
            </h2>
            <form @submit.prevent="save">
                <div class="row">
                    <div class="col-12 sm:col-6 !p-2">
                        <label for="name" class="field-title required">{{ $t('label.owner_name') }}</label>
                        <input id="name" type="text" :class="errors.name ? 'invalid' : ''" v-model="props.form.name"
                               class="field-control">
                        <small class="db-field-alert" v-if="errors.name">
                            {{ errors.name[0] }}
                        </small>
                    </div>
                    <div class="col-12 sm:col-6 !p-2">
                        <label for="email" class="field-title required">{{ $t('label.owner_email') }}</label>
                        <input id="email" type="email" :class="errors.email ? 'invalid' : ''" v-model="props.form.email"
                               class="field-control">
                        <small class="db-field-alert" v-if="errors.email">
                            {{ errors.email[0] }}
                        </small>
                    </div>
                    <div class="col-12 sm:col-6 !p-2">
                        <label for="password" class="field-title required">{{ $t('label.password') }}</label>
                        <input id="password" type="password" :class="errors.password ? 'invalid' : ''"
                               v-model="props.form.password"
                               class="field-control" autocomplete="off">
                        <small class="db-field-alert" v-if="errors.password">
                            {{ errors.password[0] }}
                        </small>
                    </div>
                    <div class="col-12 sm:col-6 !p-2">
                        <label for="confirm_password" class="field-title required">{{
                                $t('label.confirm_password')
                            }}</label>
                        <input type="password" id="confirm_password" :class="errors.confirm_password ? 'invalid' : ''"
                               v-model="props.form.confirm_password"
                               class="field-control" autocomplete="off">
                        <small class="db-field-alert" v-if="errors.confirm_password">
                            {{ errors.confirm_password[0] }}
                        </small>
                    </div>

                    <button type="submit" class="field-button mt-6">
                        {{ $t('label.continue') }}
                    </button>
                </div>
            </form>
        </div>
    </section>
</template>

<script>
import LoadingComponent from "../../common/LoadingComponent.vue";
import {useFrontendRestaurantSignupStore} from "../../../stores/frontendRestaurantSignup.js";
import {useAuthStore} from "../../../stores/auth.js";
import {useCommonStore} from "../../../stores/common.js";
import alertService from "../../../services/alertService.js";
import ENV from "../../../config/env.js";
import askEnum from "../../../enums/modules/askEnum.js";
import {useFrontendSettingStore} from "../../../stores/frontendSetting.js";

export default {
    name: "RestaurantOwnerComponent",
    components: {LoadingComponent},
    setup() {
        const authStore                     = useAuthStore();
        const commonStore                   = useCommonStore();
        const frontendSettingStore          = useFrontendSettingStore();
        const frontendRestaurantSignupStore = useFrontendRestaurantSignupStore();

        return {
            authStore,
            commonStore,
            frontendSettingStore,
            frontendRestaurantSignupStore
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
                    country_code: "",
                    phone: ""
                }
            },
            demo: ENV.DEMO,
            errors: {},
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
        await this.phoneChecking();
    },
    methods: {
        phoneChecking: function () {
            if (this.demo.toLowerCase() === "false") {
                if (this.setting.site_phone_verification === this.enums.askEnum.YES) {
                    if (!this.frontendRestaurantSignupStore.verify) {
                        this.$router.push({name: 'auth.signupRestaurant'});
                    }
                }
            }

            const code  = this.frontendRestaurantSignupStore.code;
            const phone = this.frontendRestaurantSignupStore.phone;
            if (code && phone) {
                this.props.form.country_code = code;
                this.props.form.phone        = phone;
            }

            this.props.form.name             = this.frontendRestaurantSignupStore.ownerName;
            this.props.form.email            = this.frontendRestaurantSignupStore.ownerEmail;
            this.props.form.password         = this.frontendRestaurantSignupStore.ownerPassword;
            this.props.form.confirm_password = this.frontendRestaurantSignupStore.ownerPassword;
        },
        save: function () {
            try {
                this.loading.isActive = true;
                this.frontendRestaurantSignupStore.callOwner(this.props.form).then((res) => {
                    alertService.success(res.data.message);
                    this.errors = {};
                    this.$router.push({name: "auth.signupRestaurantInfo"});
                }).catch((err) => {
                    this.loading.isActive = false;
                    if (err?.response?.data?.errors) {
                        this.errors = err.response.data.errors;
                    } else if (err?.response?.data?.message) {
                        alertService.error(err.response.data.message);
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
