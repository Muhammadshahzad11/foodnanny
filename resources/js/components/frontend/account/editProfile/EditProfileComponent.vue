<template>
    <LoadingComponent :props="loading"/>
    <section class="pt-4 pb-12">
        <div class="container max-w-xl">
            <router-link :to="{ name: 'frontend.home' }" class="mb-6 inline-flex items-center gap-2 text-primary">
                <i class="lab-line-undo text-xl font-semibold"></i>
                <span class="text-base font-medium">{{ $t('label.back_to_home') }}</span>
            </router-link>
            <form @submit.prevent="save" class="w-full p-4 rounded-2xl bg-white shadow-xs">
                <h3 class="capitalize text-xl mb-6 font-semibold">
                    {{ $t('menu.edit_profile') }}
                </h3>
                <div class="row !-m-2">
                    <div class="col-12 sm:col-6 !p-2">
                        <label for="first_name" class="field-title">{{ $t('label.first_name') }}</label>
                        <input id="first_name" type="text" v-model="form.first_name"
                               :class="errors.first_name ? 'invalid' : ''" class="field-control">
                        <small class="db-field-alert" v-if="errors.first_name">
                            {{ errors.first_name[0] }}
                        </small>
                    </div>
                    <div class="col-12 sm:col-6 !p-2">
                        <label for="last_name" class="field-title">{{ $t('label.last_name') }}</label>
                        <input id="last_name" type="text" v-model="form.last_name"
                               :class="errors.last_name ? 'invalid' : ''" class="field-control">
                        <small class="db-field-alert" v-if="errors.last_name">
                            {{ errors.last_name[0] }}
                        </small>
                    </div>
                    <div class="col-12 sm:col-6 !p-2">
                        <label for="email" class="field-title">{{ $t('label.email') }}</label>
                        <input id="email" type="email" v-model="form.email" :class="errors.email ? 'invalid' : ''"
                               class="field-control">
                        <small class="db-field-alert" v-if="errors.email">
                            {{ errors.email[0] }}
                        </small>
                    </div>
                    <div class="col-12 sm:col-6 !p-2">
                        <label for="phone" class="field-title">{{ $t('label.phone') }}</label>
                        <div class="relative group">
                            <div :class="[isOpen ? 'rounded-t-lg' : 'rounded-lg', errors.phone ? 'invalid' : '']"
                                 class="flex w-full h-12 border text-sm border-[#d9dbe9] group-hover:border-primary/20 transition-all duration-300 group-focus-within:border-primary/20">
                                <button @click="isOpen = !isOpen" type="button" :class="errors.phone ? 'invalid' : ''"
                                        class="flex items-center text-sm gap-1 h-full pl-3 pr-2 border-r border-[#d9dbe9] group-hover:border-primary/20 group-focus-within:border-primary/20 transition-all duration-300">
                                    <span class="text-sm flex-shrink-0">{{ form.flag }}</span>
                                    <span class="text-sm">{{ form.country_code }}</span>
                                    <i class="lab-fill-arrow-down"></i>
                                </button>
                                <input v-model="form.phone" v-on:keypress="phoneNumber($event)" type="text"
                                       class="pl-2 pr-3 w-full overflow-hidden h-full placeholder:text-sm">
                            </div>
                            <ul :class="isOpen ? 'scale-y-100' : 'scale-y-0'"
                                class="absolute top-12  text-sm left-0 z-10 w-full max-h-60 thin-scrolling rounded-b-lg shadow-paper origin-top border-x border-b group-hover:border-primary/20 group-focus-within:border-primary/20 border-[#d9dbe9] bg-white transition-all duration-300">
                                <li v-for="countryCode in countryCodes" @click="handleCountry(countryCode)"
                                    class="flex items-center text-sm gap-2 px-3 py-1.5 cursor-pointer hover:bg-gray-100 transition-all duration-300">
                                    <div class="text-sm flex-shrink-0">{{ countryCode.flag }}</div>
                                    <span class="text-sm capitalize flex-auto">{{ countryCode.country_name }}</span>
                                    <span class="text-sm">{{ countryCode.calling_code }}</span>
                                </li>
                            </ul>
                        </div>
                        <small class="db-field-alert" v-if="errors.phone"> {{ errors.phone[0] }} </small>
                    </div>
                    <button type="submit" class="field-button mt-2">{{ $t('button.update_profile') }}</button>
                </div>
            </form>
        </div>
    </section>
</template>

<script>
import LoadingComponent from "../../../common/LoadingComponent.vue";
import alertService from "../../../../services/alertService.js";
import appService from "../../../../services/appService.js";
import {useAuthStore} from "../../../../stores/auth.js";
import {useCountryCodeStore} from "../../../../stores/countryCode.js";
import {useFrontendEditProfileStore} from "../../../../stores/frontendEditProfile.js";

export default {
    name: "EditProfileComponent",
    components: {LoadingComponent},
    setup() {
        const frontendEditProfileStore = useFrontendEditProfileStore();
        const authStore                = useAuthStore();
        const countryCodeStore         = useCountryCodeStore();

        return {
            frontendEditProfileStore,
            countryCodeStore,
            authStore
        }
    },
    data() {
        return {
            loading: {
                isActive: false
            },
            form: {
                first_name: "",
                last_name: "",
                email: "",
                phone: "",
                country_code: "",
                flag: ""
            },
            isOpen: false,
            errors: {},
        };
    },
    computed: {
        countryCodes: function () {
            return this.countryCodeStore.lists;
        }
    },
    async mounted() {
        await this.countryCodeStore.fetch().then(res => {
            this.loading.isActive = false;
        }).catch(err => {
            this.loading.isActive = false;
        });
        const profile          = this.authStore.info;
        this.form.first_name   = profile.first_name;
        this.form.last_name    = profile.last_name;
        this.form.email        = profile.email;
        this.form.phone        = profile.phone;
        this.form.country_code = profile.country_code;
        this.countryCodeStore.fetchFind({country_code: profile.country_code}).then(res => {
            this.form.flag = res.data.data.flag_emoji;
        }).catch()
    },
    methods: {
        phoneNumber(e) {
            return appService.phoneNumber(e);
        },
        handleCountry: async function (val) {
            await this.countryCodeStore.fetchFind({
                country_code: val.calling_code
            }).then(res => {
                this.form.country_code = res.data.data.calling_code;
                this.form.flag         = res.data.data.flag_emoji;
            }).catch(err => {
                this.loading.isActive = false;
            });
            this.isOpen = false
        },
        save: function () {
            try {
                this.loading.isActive = true;
                this.frontendEditProfileStore.updateProfile(this.form).then((res) => {
                    this.authStore.updateAuthInfo(res.data.data).then(res => {
                        this.loading.isActive = false;
                        alertService.successFlip(1, this.$t("menu.profile"));
                        this.errors = {};
                    }).catch((err) => {
                        this.loading.isActive = false;
                        alertService.error(err);
                    });
                }).catch((err) => {
                    this.loading.isActive = false;
                    this.errors           = err.response.data.errors;
                });
            } catch (err) {
                this.loading.isActive = false;
                alertService.error(err);
            }
        }
    }
}
</script>
