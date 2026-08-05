<template>
    <LoadingComponent :props="loading"/>
    <section class="pt-8 pb-16">
        <div class="w-full max-w-[360px] mx-auto mt-8 mb-12 p-5 rounded-2xl bg-white shadow-xs">
            <h3 class="capitalize text-xl mb-6 font-semibold text-center">
                {{ $t('label.lets_get_started') }}
            </h3>
            <form @submit.prevent="save">
                <div class="mb-6">
                    <label for="phone" class="field-title required">
                        {{ $t('label.phone') }}
                    </label>
                    <div class="relative group">
                        <div :class="[isOpen ? 'rounded-t-lg' : 'rounded-lg', errors.phone ? 'invalid' : '']"
                             class="flex w-full h-12 border border-[#d9dbe9] group-hover:border-primary/20 transition-all duration-300 group-focus-within:border-primary/20">
                            <button @click="isOpen = !isOpen" type="button" :class="errors.phone ? 'invalid' : ''"
                                    class="flex items-center gap-1 h-full pl-3 pr-2 border-r border-[#d9dbe9] group-hover:border-primary/20 group-focus-within:border-primary/20 transition-all duration-300">
                                <span class="text-xl flex-shrink-0">{{ flag }}</span>
                                <span class="text-sm">{{ countryCode }}</span>
                                <i class="lab-fill-arrow-down"></i>
                            </button>
                            <input v-model="props.form.phone" v-on:keypress="phoneNumber($event)" id="phone" type="text"
                                   class="pl-2 pr-3 w-full overflow-hidden h-full placeholder:text-sm">
                        </div>
                        <ul :class="isOpen ? 'scale-y-100' : 'scale-y-0'"
                            class="absolute top-12 left-0 z-10 w-full max-h-60 thin-scrolling rounded-b-lg shadow-paper origin-top border-x border-b group-hover:border-primary/20 group-focus-within:border-primary/20 border-[#d9dbe9] bg-white transition-all duration-300">
                            <li v-for="countryCode in countryCodes" @click="handleCountry(countryCode)"
                                class="flex items-center gap-2 px-3 py-1.5 cursor-pointer hover:bg-gray-100 transition-all duration-300">
                                <div class="text-xl flex-shrink-0">{{ countryCode.flag }}</div>
                                <span class="text-sm capitalize flex-auto">{{ countryCode.country_name }}</span>
                                <span class="text-sm">{{ countryCode.calling_code }}</span>
                            </li>
                        </ul>
                    </div>
                    <small class="db-field-alert" v-if="errors.phone">
                        {{ errors.phone[0] }}
                    </small>
                </div>
                <button type="submit" class="field-button mb-6">
                    {{ $t('label.next') }}
                </button>
                <div class="flex items-center justify-center gap-1.5">
                    <span class="text-xs text-paragraph">{{ $t('label.already_have_an_account') }}</span>
                    <router-link :to="{ name: 'auth.login' }" class="text-xs font-medium capitalize text-primary">
                        {{ $t('label.login') }}
                    </router-link>
                </div>
            </form>
        </div>
    </section>
</template>

<script>
import LoadingComponent from "../../common/LoadingComponent.vue";
import appService from "../../../services/appService.js";
import {useFrontendCountryCodeStore} from "../../../stores/frontendCountryCode.js";
import {useFrontendSettingStore} from "../../../stores/frontendSetting.js";
import {useAuthStore} from "../../../stores/auth.js";
import alertService from "../../../services/alertService.js";
import {useFrontendSignupStore} from "../../../stores/frontendSignup.js";
import askEnum from "../../../enums/modules/askEnum.js";
import ENV from "../../../config/env.js";
import {useCommonStore} from "../../../stores/common.js";

export default {
    name: "SignupPhoneComponent",
    components: {LoadingComponent},
    setup() {
        const authStore                = useAuthStore();
        const commonStore              = useCommonStore();
        const frontendSignup           = useFrontendSignupStore();
        const frontendSettingStore     = useFrontendSettingStore();
        const frontendCountryCodeStore = useFrontendCountryCodeStore();

        return {
            authStore,
            commonStore,
            frontendSignup,
            frontendSettingStore,
            frontendCountryCodeStore
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
                    phone: "",
                    code: "",
                },
            },
            demo: ENV.DEMO,
            defaultCountryCode: "",
            isOpen: false,
            flag: "",
            countryCode: "",
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

        this.loading.isActive = true;
        this.frontendCountryCodeStore.fetch().then(res => {
            this.loading.isActive = false;
        }).catch(err => {
            this.loading.isActive = false;
        });

        this.loading.isActive = true;
        this.frontendSettingStore.fetch().then(res => {
            this.defaultCountryCode = res.data.data.company_country_code;
            this.frontendCountryCodeStore.view(this.defaultCountryCode).then(res => {
                this.props.form.code  = res.data.data.calling_code;
                this.countryCode      = res.data.data.calling_code;
                this.flag             = res.data.data.flag_emoji;
                this.loading.isActive = false;
            }).catch((err) => {
                this.loading.isActive = false;
            });
            this.loading.isActive = false;
        }).catch((err) => {
            this.loading.isActive = false;
        });
    },
    computed: {
        countryCodes: function () {
            return this.frontendCountryCodeStore.lists;
        },
        setting: function () {
            return this.frontendSettingStore.lists;
        }
    },
    methods: {
        phoneNumber(e) {
            return appService.phoneNumber(e);
        },
        handleCountry: function (val) {
            this.frontendCountryCodeStore.fetchFind({
                country_code: val.calling_code
            }).then(res => {
                this.flag            = res.data.data.flag_emoji;
                this.countryCode     = res.data.data.calling_code;
                this.props.form.code = res.data.data.calling_code;
            }).catch(err => {
                this.loading.isActive = false;
            });
            this.isOpen = false;
        },
        save: function () {
            try {
                this.loading.isActive = true;
                this.frontendSignup.callPhone(this.props.form).then(async (res) => {
                    this.loading.isActive = false;
                    this.props.form       = {
                        phone: "",
                        code: "",
                    };
                    this.errors           = {};
                    if (this.demo.toLowerCase() === "true") {
                        this.$router.push({name: "auth.signupRegister"});
                    } else {
                        if (this.setting.site_phone_verification === this.enums.askEnum.NO) {
                            this.$router.push({name: "auth.signupRegister"});
                        } else {
                            alertService.success(res.data.message);
                            if (res.data?.otp) {
                                await alertService.showOtp(res.data.otp);
                            }
                            this.$router.push({name: "auth.signupVerify"});
                        }
                    }
                }).catch((err) => {
                    this.loading.isActive = false;
                    if (err?.response?.data?.errors) {
                        this.errors = err.response.data.errors;
                    } else if (err?.response?.data?.message) {
                        this.errors.phone = [err?.response?.data?.message];
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
