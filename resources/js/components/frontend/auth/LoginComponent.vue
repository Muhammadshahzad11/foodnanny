<template>
    <LoadingComponent :props="loading"/>
    <form @submit.prevent="login" class="w-full max-w-[360px] mx-auto mt-8 mb-12 p-5 rounded-2xl bg-white shadow-xs">
        <h3 class="capitalize text-xl mb-6 font-semibold text-center">{{ $t('label.welcome_back') }}</h3>
        <div v-if="errors.validation"
             class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 mb-5 rounded relative" role="alert">
            <span class="block sm:inline">{{ errors.validation }}</span>
            <span
                class="absolute -top-3 bottom-0 -right-3 w-6 h-6 text-center leading-6 rounded-full cursor-pointer bg-red-100 border border-red-400"
                @click="close">
                <i class="lab lab-fill-close-circle margin-top-5-px"></i>
            </span>
        </div>

        <div class="mb-4">
            <div class="flex items-center justify-between">
                <label :for="toggleInput ? 'email' : 'phone'" class="field-title required">
                    {{ inputLabel }}
                </label>

                <button @click="changeInput" type="button"
                        class="text-xs font-medium capitalize mb-1 underline text-primary">
                    {{ inputButton }}
                </button>
            </div>
            <input v-if="toggleInput" id="email" type="email" :class="errors.email ? 'invalid' : ''"
                   v-model="form.email" class="field-control">
            <div v-if="!toggleInput" class="relative group">
                <div :class="[isOpen ? 'rounded-t-lg' : 'rounded-lg', errors.phone ? 'invalid' : '']"
                     class="flex w-full h-12 border border-[#d9dbe9] group-hover:border-primary/20 transition-all duration-300 group-focus-within:border-primary/20">
                    <button @click="isOpen = !isOpen" type="button" :class="errors.phone ? 'invalid' : ''"
                            class="flex items-center gap-1 h-full pl-3 pr-2 border-r border-[#d9dbe9] group-hover:border-primary/20 group-focus-within:border-primary/20 transition-all duration-300">
                        <span class="text-xl flex-shrink-0">{{ flag }}</span>
                        <span class="text-sm">{{ countryCode }}</span>
                        <i class="lab-fill-arrow-down"></i>
                    </button>
                    <input v-model="form.phone" v-on:keypress="phoneNumber($event)" id="phone" type="text"
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
            <small class="db-field-alert" v-if="errors.email">{{ errors.email[0] }}</small>
            <small class="db-field-alert" v-if="errors.phone">{{ errors.phone[0] }}</small>
        </div>

        <div class="mb-4">
            <label for="password" class="field-title required">{{ $t('label.password') }}</label>
            <input id="password" type="password" :class="errors.password ? 'invalid' : ''" v-model="form.password"
                   class="field-control"/>
            <small class="db-field-alert" v-if="errors.password">{{ errors.password[0] }}</small>
        </div>

        <div class="flex items-center justify-between mb-6">
            <div class="flex items-center gap-2">
                <input type="checkbox" id="remember" class="field-checkbox">
                <label for="remember" class="field-label">{{ $t('label.remember_me') }}</label>
            </div>
            <router-link :to="{ name: 'auth.forgotPassword' }" class="text-xs font-medium capitalize text-primary">
                {{ $t('button.forgot_password') }}
            </router-link>
        </div>

        <button type="submit" class="field-button mb-6">
            {{ $t('button.login') }}
        </button>

        <div class="flex items-center justify-center gap-1.5">
            <span class="text-xs text-paragraph">{{ $t('message.have_account') }}</span>
            <router-link :to="{ name: 'auth.signupPhone' }" class="text-xs font-medium capitalize text-primary">
                {{ $t('button.signup') }}
            </router-link>
        </div>
        <p class="uppercase text-xs my-4 text-center text-paragraph">{{ $t('label.or') }}</p>
        <router-link :to="{ name: 'auth.guestLogin' }"
                     class="w-full h-12 leading-[46px] text-center font-medium rounded-full border border-primary text-primary">
            {{ $t('button.login_as_guest') }}
        </router-link>
    </form>

    <div v-if="demo === 'true' || demo === 'TRUE' || demo === 'True' || demo === '1' || demo === 1"
         class="container max-w-[360px] mb-16 py-6 p-4 sm:px-6 shadow-xs rounded-2xl bg-white">
        <h2 class="mb-6 text-center text-lg font-medium text-heading">{{ $t('message.for_quick_demo') }}</h2>
        <nav class="grid grid-cols-2 gap-3">
            <button @click.prevent="setupCredit('admin')"
                    class="click-to-prop w-full h-10 leading-10 rounded-lg text-center text-sm capitalize text-white bg-orange-500">
                {{ $t('label.admin') }}
            </button>
            <button @click.prevent="setupCredit('customer')"
                    class="click-to-prop w-full h-10 leading-10 rounded-lg text-center text-sm capitalize text-white bg-emerald-500">
                {{ $t('label.customer') }}
            </button>
            <button @click.prevent="setupCredit('restaurantOwner')"
                    class="click-to-prop w-full h-10 leading-10 rounded-lg text-center text-sm capitalize text-white bg-sky-600">
                {{ $t('label.restaurant_owner') }}
            </button>
            <button @click.prevent="setupCredit('deliveryBoy')"
                    class="click-to-prop w-full h-10 leading-10 rounded-lg text-center text-sm capitalize text-white bg-purple-500">
                {{ $t('label.delivery_boy') }}
            </button>
        </nav>
    </div>
</template>

<script>
import LoadingComponent from "../../common/LoadingComponent.vue";
import ENV from "../../../config/env.js";
import {useAuthStore} from "../../../stores/auth.js";
import router from "../../../router/index.js";
import appService from "../../../services/appService.js";
import alertService from "../../../services/alertService.js";
import {useDefaultAccessStore} from "../../../stores/defaultAccess.js";
import {useFrontendCartStore} from "../../../stores/frontendCart.js";
import {useCommonStore} from "../../../stores/common.js";
import {useFrontendCountryCodeStore} from "../../../stores/frontendCountryCode.js";
import {useFrontendSettingStore} from "../../../stores/frontendSetting.js";
import {useMyRestaurantStore} from "../../../stores/myRestaurant.js";

export default {
    name: "LoginComponent",
    components: {LoadingComponent},
    setup() {
        const authStore                = useAuthStore();
        const commonStore              = useCommonStore();
        const frontendCartStore        = useFrontendCartStore();
        const defaultAccessStore       = useDefaultAccessStore();
        const frontendSettingStore     = useFrontendSettingStore();
        const frontendCountryCodeStore = useFrontendCountryCodeStore();
        const myRestaurantStore        = useMyRestaurantStore();

        return {
            authStore,
            commonStore,
            myRestaurantStore,
            frontendCartStore,
            defaultAccessStore,
            frontendSettingStore,
            frontendCountryCodeStore
        }
    },
    data() {
        return {
            loading: {
                isActive: false,
            },
            form: {
                country_code: "",
                phone: "",
                email: "",
                password: ""
            },
            isOpen: false,
            flag: "",
            countryCode: "",
            errors: {},
            toggleInput: true,
            inputLabel: this.$t('label.email'),
            inputButton: this.$t('button.use_phone_instead'),
            demo: ENV.DEMO
        }
    },
    computed: {
        countryCodes: function () {
            return this.frontendCountryCodeStore.lists;
        },
        carts: function () {
            return this.frontendCartStore.lists;
        },
        location: function () {
            return this.commonStore.location;
        }
    },
    mounted() {
        this.loading.isActive = true;
        this.frontendCountryCodeStore.fetch().then(res => {
            this.loading.isActive = false;
        }).catch(err => {
            this.loading.isActive = false;
        })

        this.loading.isActive = true;
        this.frontendSettingStore.fetch().then(res => {
            this.defaultCountryCode = res.data.data.company_country_code;
            this.frontendCountryCodeStore.view(this.defaultCountryCode).then(res => {
                this.form.country_code = res.data.data.calling_code;
                this.countryCode       = res.data.data.calling_code;
                this.flag              = res.data.data.flag_emoji;
                this.loading.isActive  = false;
            }).catch((err) => {
                this.loading.isActive = false;
            });
            this.loading.isActive = false;
        }).catch((err) => {
            this.loading.isActive = false;
        })
    },
    methods: {
        phoneNumber(e) {
            return appService.phoneNumber(e);
        },
        handleCountry: function (val) {
            this.frontendCountryCodeStore.fetchFind({
                country_code: val.calling_code
            }).then(res => {
                this.flag              = res.data.data.flag_emoji;
                this.countryCode       = res.data.data.calling_code;
                this.form.country_code = res.data.data.calling_code;
            }).catch(err => {
                this.loading.isActive = false;
            });
            this.isOpen = false
        },
        login: async function () {
            try {
                this.loading.isActive = true;
                let method            = this.toggleInput ? this.authStore.login : this.authStore.phoneLogin;
                await method(this.form).then(async (res) => {
                    await this.defaultAccessStore.fetch();
                    await this.myRestaurantStore.resetDefaultRestaurant();
                    alertService.success(res.data.message);
                    setTimeout(() => {
                        appService.recursiveRouter(router.options.routes, this.authStore.permission)
                    }, 1000);

                    this.form             = {
                        phone: "",
                        email: "",
                        password: ""
                    };
                    this.loading.isActive = false;

                    if (this.carts.length > 0 && this.location) {
                        await this.$router.push({name: "frontend.checkout"});
                    } else if (this.location) {
                        await this.$router.push({name: "frontend.restaurant"});
                    } else {
                        await this.$router.push({name: "frontend.home"});
                    }
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
            }
        },
        close: function () {
            this.errors = {}
        },
        changeInput: function () {
            this.toggleInput = !this.toggleInput;
            if (this.toggleInput) {
                this.form.phone  = "";
                this.errors      = {};
                this.inputLabel  = this.$t('label.email');
                this.inputButton = this.$t('button.use_phone_instead');
            } else {
                this.form.email  = "";
                this.errors      = {};
                this.inputLabel  = this.$t('label.phone');
                this.inputButton = this.$t('button.use_email_instead');
            }
        },
        setupCredit: function (e) {
            if (e === 'admin') {
                this.form.country_code = '+880';
                this.form.phone        = '1728660901';
                this.form.email        = 'admin@example.com';
                this.form.password     = '123456';
            } else if (e === 'customer') {
                this.form.country_code = '+880';
                this.form.phone        = '1739558205';
                this.form.email        = 'customer@example.com';
                this.form.password     = '123456';
            } else if (e === 'restaurantOwner') {
                this.form.country_code = '+880';
                this.form.phone        = '1726449801';
                this.form.email        = 'restaurantowner@example.com';
                this.form.password     = '123456';
            } else if (e === 'deliveryBoy') {
                this.form.country_code = '+880';
                this.form.phone        = '1739558202';
                this.form.email        = 'deliveryboy@example.com';
                this.form.password     = '123456';
            }
        }
    }
}
</script>
