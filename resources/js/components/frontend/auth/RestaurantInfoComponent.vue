<template>
    <LoadingComponent :props="loading"/>
    <section class="pb-16">
        <div class="w-full max-w-[550px] mx-auto mt-8 mb-12 p-5 rounded-2xl bg-white shadow-xs">
            <RestaurantSignupStepsComponent current="restaurant"/>
            <h2 class="capitalize mb-6 text-center text-[22px] font-semibold leading-[34px] text-heading">
                {{ $t('label.restaurant_information') }}
            </h2>
            <form @submit.prevent="save">
                <div class="row">
                    <div class="col-12 sm:col-6 !p-2">
                        <label for="name" class="field-title required">{{ $t('label.restaurant_name') }}</label>
                        <input id="name" type="text" :class="errors.restaurant_name ? 'invalid' : ''"
                               v-model="props.form.restaurant_name"
                               class="field-control">
                        <small class="db-field-alert" v-if="errors.restaurant_name">
                            {{ errors.restaurant_name[0] }}
                        </small>
                    </div>
                    <div class="col-12 sm:col-6 !p-2">
                        <label for="email" class="field-title required">{{ $t('label.restaurant_email') }}</label>
                        <input id="email" type="email" :class="errors.restaurant_email ? 'invalid' : ''"
                               v-model="props.form.restaurant_email" class="field-control">
                        <small class="db-field-alert" v-if="errors.restaurant_email">
                            {{ errors.restaurant_email[0] }}
                        </small>
                    </div>
                    <div class="col-12 !p-2">
                        <label for="phone" class="field-title required">{{ $t('label.restaurant_phone') }}</label>
                        <div class="relative group">
                            <div
                                :class="[isOpen ? 'rounded-t-lg' : 'rounded-lg', errors.restaurant_phone ? 'invalid' : '']"
                                class="flex w-full h-12 border border-[#d9dbe9] group-hover:border-primary/20 transition-all duration-300 group-focus-within:border-primary/20">
                                <button @click="isOpen = !isOpen" type="button"
                                        :class="errors.restaurant_phone ? 'invalid' : ''"
                                        class="flex items-center gap-1 h-full pl-3 pr-2 border-r border-[#d9dbe9] group-hover:border-primary/20 group-focus-within:border-primary/20 transition-all duration-300">
                                    <span class="text-xl flex-shrink-0">{{ flag }}</span>
                                    <span class="text-sm">{{ countryCode }}</span>
                                    <i class="lab-fill-arrow-down"></i>
                                </button>
                                <input id="phone" v-model="props.form.restaurant_phone"
                                       v-on:keypress="phoneNumber($event)"
                                       type="text" class="pl-2 pr-3 w-full overflow-hidden h-full placeholder:text-sm">
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
                        <small class="db-field-alert" v-if="errors.restaurant_phone">
                            {{ errors.restaurant_phone[0] }}
                        </small>
                    </div>

                    <div class="col-12 !p-2">
                        <label for="restaurant-address-autocomplete" class="field-title required">
                            {{ $t('label.restaurant_address') }}
                        </label>
                        <div class="relative">
                            <input id="restaurant-address-autocomplete"
                                   type="text"
                                   ref="addressInput"
                                   :class="errors.restaurant_address ? 'invalid' : ''"
                                   v-model="props.form.restaurant_address"
                                   class="field-control ltr:pr-12 rtl:pl-12"
                                   :placeholder="$t('label.enter_your_location')"
                                   autocomplete="off">
                            <button type="button"
                                    title="Use current location"
                                    :disabled="locating"
                                    @click.prevent="useCurrentLocation"
                                    class="lab-line-gps absolute top-1/2 -translate-y-1/2 ltr:right-3 rtl:left-3 text-xl text-primary disabled:opacity-40"></button>
                        </div>
                        <small class="text-xs text-[#6E7191] mt-1 block">
                            Start typing and select an address — city, state, zip & map pin fill automatically.
                        </small>
                        <small class="db-field-alert" v-if="errors.restaurant_address">
                            {{ errors.restaurant_address[0] }}
                        </small>
                    </div>

                    <div class="col-12 sm:col-4 !p-2">
                        <label for="city" class="field-title required">{{ $t('label.city') }}</label>
                        <input id="city" type="text" :class="errors.restaurant_city ? 'invalid' : ''"
                               v-model="props.form.restaurant_city" class="field-control">
                        <small class="db-field-alert" v-if="errors.restaurant_city">{{ errors.restaurant_city[0] }}</small>
                    </div>
                    <div class="col-12 sm:col-4 !p-2">
                        <label for="state" class="field-title required">{{ $t('label.state') }}</label>
                        <input id="state" type="text" :class="errors.restaurant_state ? 'invalid' : ''"
                               v-model="props.form.restaurant_state" class="field-control">
                        <small class="db-field-alert" v-if="errors.restaurant_state">{{ errors.restaurant_state[0] }}</small>
                    </div>
                    <div class="col-12 sm:col-4 !p-2">
                        <label for="zip_code" class="field-title required">{{ $t('label.zip_code') }}</label>
                        <input id="zip_code" type="text" :class="errors.restaurant_zip_code ? 'invalid' : ''"
                               v-model="props.form.restaurant_zip_code" class="field-control">
                        <small class="db-field-alert" v-if="errors.restaurant_zip_code">{{ errors.restaurant_zip_code[0] }}</small>
                    </div>

                    <div class="flex items-start gap-2 mt-4 !pl-2" v-if="slug !== 'not-found'">
                        <input @click="checkTermsAndCondition($event)" type="checkbox" id="terms_and_conditions"
                               :value="enums.askEnum.YES" class="cs-custom-checkbox"/>
                        <label for="terms_and_conditions" class="text-xs -mt-[1px] cursor-pointer">
                            {{ $t('message.singing_up_agree') }}
                            <router-link :to="{ name: 'frontend.page', params: { slug: slug } }" target="_blank"
                                         class="text-primary">
                                {{ $t('menu.terms_and_conditions') }}
                            </router-link>
                        </label>
                    </div>
                    <div class="col-12 sm:col-6 !p-2">
                        <router-link :to="{ name: 'auth.signupRestaurantOwner' }"
                                     class="field-button mt-2 border border-primary text-primary bg-white">
                            {{ $t('label.previous') }}
                        </router-link>
                    </div>
                    <div class="col-12 sm:col-6 !p-2">
                        <button type="submit" class="field-button mt-2">{{ $t('button.sign_up') }}</button>
                    </div>
                </div>
            </form>
        </div>
    </section>
</template>

<script>
import LoadingComponent from "../../common/LoadingComponent.vue";
import RestaurantSignupStepsComponent from "./RestaurantSignupStepsComponent.vue";
import {useAuthStore} from "../../../stores/auth.js";
import {useCommonStore} from "../../../stores/common.js";
import {useFrontendRestaurantSignupStore} from "../../../stores/frontendRestaurantSignup.js";
import askEnum from "../../../enums/modules/askEnum.js";
import {useFrontendSettingStore} from "../../../stores/frontendSetting.js";
import {useFrontendPageStore} from "../../../stores/frontendPage.js";
import appService from "../../../services/appService.js";
import {useFrontendCountryCodeStore} from "../../../stores/frontendCountryCode.js";
import alertService from "../../../services/alertService.js";
import ENV from "../../../config/env.js";
import locationService from "../../../services/locationService.js";

export default {
    name: "RestaurantInfoComponent",
    components: {LoadingComponent, RestaurantSignupStepsComponent},
    setup() {
        const authStore                     = useAuthStore();
        const commonStore                   = useCommonStore();
        const frontendPageStore             = useFrontendPageStore();
        const frontendSettingStore          = useFrontendSettingStore();
        const frontendCountryCodeStore      = useFrontendCountryCodeStore();
        const frontendRestaurantSignupStore = useFrontendRestaurantSignupStore();

        return {
            authStore,
            commonStore,
            frontendPageStore,
            frontendSettingStore,
            frontendCountryCodeStore,
            frontendRestaurantSignupStore
        }
    },
    data() {
        return {
            loading: {
                isActive: false,
            },
            locating: false,
            enums: {
                askEnum: askEnum,
            },
            props: {
                form: {
                    restaurant_name: "",
                    restaurant_email: "",
                    restaurant_address: "",
                    restaurant_city: "",
                    restaurant_state: "",
                    restaurant_zip_code: "",
                    restaurant_latitude: "",
                    restaurant_longitude: "",
                    restaurant_country_code: "",
                    restaurant_phone: "",
                    terms_and_conditions: "",
                    page_id: null,
                }
            },
            defaultCountryCode: "",
            countryCode: "",
            flag: "",
            isOpen: false,
            slug: "not-found",
            errors: {},
            autocomplete: null,
        }
    },
    computed: {
        countryCodes: function () {
            return this.frontendCountryCodeStore.lists;
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
        await this.ownerChecking();
        await this.termsAndConditionsChecking();

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
                this.props.form.restaurant_country_code = res.data.data.calling_code;
                this.countryCode                        = res.data.data.calling_code;
                this.flag                               = res.data.data.flag_emoji;
                this.loading.isActive                   = false;
            }).catch((err) => {
                this.loading.isActive = false;
            });
            this.loading.isActive = false;
        }).catch((err) => {
            this.loading.isActive = false;
        })

        await this.initAddressAutocomplete();
    },
    beforeUnmount() {
        if (this.autocomplete && window.google?.maps?.event) {
            google.maps.event.clearInstanceListeners(this.autocomplete);
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
                this.flag                               = res.data.data.flag_emoji;
                this.countryCode                        = res.data.data.calling_code;
                this.props.form.restaurant_country_code = res.data.data.calling_code;
            }).catch(err => {
                this.loading.isActive = false;
            });
            this.isOpen = false
        },
        ownerChecking: function () {
            if (!this.frontendRestaurantSignupStore.ownerVerify) {
                this.$router.push({name: 'auth.signupRestaurantOwner'});
            }
        },
        termsAndConditionsChecking: async function () {
            await this.frontendSettingStore.fetch().then(async res => {
                if (res.data.data.terms_and_conditions_restaurant_page_id > 0) {
                    this.props.form.page_id = res.data.data.terms_and_conditions_restaurant_page_id;
                    await this.frontendPageStore.info(res.data.data.terms_and_conditions_restaurant_page_id).then(res => {
                        this.slug = res.data.data.slug;
                    }).catch()
                }
            }).catch(err => {
                this.loading.isActive = false;
                this.errors           = err.response.data.errors;
            })
        },
        checkTermsAndCondition: function (e) {
            this.props.form.terms_and_conditions = e.target.checked === true ? askEnum.YES : "";
        },
        async initAddressAutocomplete() {
            if (!ENV.GOOGLE_MAP_KEY || typeof google === 'undefined') {
                return;
            }
            try {
                const Places = await google.maps.importLibrary("places");
                const input = this.$refs.addressInput;
                if (!input) return;

                this.autocomplete = new Places.Autocomplete(input, {
                    fields: ['address_components', 'formatted_address', 'geometry', 'name'],
                });
                this.autocomplete.addListener('place_changed', () => {
                    const place = this.autocomplete.getPlace();
                    this.applyGooglePlace(place);
                });
            } catch (e) {
                // Autocomplete optional; GPS still works
            }
        },
        componentOf(components, type, useShort = false) {
            const match = (components || []).find((c) => c.types?.includes(type));
            if (!match) return '';
            return useShort ? (match.short_name || match.long_name || '') : (match.long_name || '');
        },
        applyGooglePlace(place) {
            if (!place?.geometry?.location) {
                return;
            }
            const components = place.address_components || [];
            const lat = place.geometry.location.lat();
            const lng = place.geometry.location.lng();

            this.props.form.restaurant_address = place.formatted_address || place.name || this.props.form.restaurant_address;
            this.props.form.restaurant_latitude = String(lat);
            this.props.form.restaurant_longitude = String(lng);
            this.props.form.restaurant_city =
                this.componentOf(components, 'locality')
                || this.componentOf(components, 'postal_town')
                || this.componentOf(components, 'sublocality')
                || this.componentOf(components, 'administrative_area_level_2');
            this.props.form.restaurant_state = this.componentOf(components, 'administrative_area_level_1');
            this.props.form.restaurant_zip_code = this.componentOf(components, 'postal_code');
            this.errors = {};
        },
        applyParsedAddress({ address, city, state, zip, lat, lng }) {
            if (address) this.props.form.restaurant_address = address;
            if (city) this.props.form.restaurant_city = city;
            if (state) this.props.form.restaurant_state = state;
            if (zip) this.props.form.restaurant_zip_code = zip;
            if (lat != null && lat !== '') this.props.form.restaurant_latitude = String(lat);
            if (lng != null && lng !== '') this.props.form.restaurant_longitude = String(lng);
            this.errors = {};
        },
        async useCurrentLocation() {
            this.locating = true;
            try {
                const coords = await locationService.getCurrentPosition();
                if (ENV.GOOGLE_MAP_KEY && typeof google !== 'undefined') {
                    const geocoder = new google.maps.Geocoder();
                    const res = await geocoder.geocode({
                        location: { lat: coords.lat, lng: coords.lng },
                    });
                    if (res.results?.length) {
                        this.applyGooglePlace(res.results[0]);
                        return;
                    }
                }
                const geo = await locationService.reverseGeocode(coords.lat, coords.lng);
                this.applyParsedAddress({
                    address: geo.name,
                    lat: coords.lat,
                    lng: coords.lng,
                });
            } catch (e) {
                alertService.error('Could not get your current location. Please allow location access or type an address.');
            } finally {
                this.locating = false;
            }
        },
        emptyForm() {
            return {
                restaurant_name: "",
                restaurant_email: "",
                restaurant_address: "",
                restaurant_city: "",
                restaurant_state: "",
                restaurant_zip_code: "",
                restaurant_latitude: "",
                restaurant_longitude: "",
                restaurant_country_code: "",
                restaurant_phone: "",
                terms_and_conditions: "",
                page_id: null,
            };
        },
        save: async function () {
            const formData = {
                owner_name: this.frontendRestaurantSignupStore.ownerName,
                owner_email: this.frontendRestaurantSignupStore.ownerEmail,
                owner_phone: String(this.frontendRestaurantSignupStore.phone || ''),
                owner_country_code: this.frontendRestaurantSignupStore.code,
                owner_password: this.frontendRestaurantSignupStore.ownerPassword,
                restaurant_name: this.props.form.restaurant_name,
                restaurant_email: this.props.form.restaurant_email,
                restaurant_address: this.props.form.restaurant_address,
                restaurant_city: this.props.form.restaurant_city,
                restaurant_state: this.props.form.restaurant_state,
                restaurant_zip_code: String(this.props.form.restaurant_zip_code || ''),
                restaurant_latitude: this.props.form.restaurant_latitude !== '' && this.props.form.restaurant_latitude != null
                    ? String(this.props.form.restaurant_latitude)
                    : null,
                restaurant_longitude: this.props.form.restaurant_longitude !== '' && this.props.form.restaurant_longitude != null
                    ? String(this.props.form.restaurant_longitude)
                    : null,
                restaurant_country_code: this.props.form.restaurant_country_code,
                restaurant_phone: String(this.props.form.restaurant_phone || ''),
                terms_and_conditions: this.props.form.terms_and_conditions,
                page_id: this.props.form.page_id,
                token: this.frontendRestaurantSignupStore.token
            }

            if (!formData.restaurant_city || !formData.restaurant_state || !formData.restaurant_zip_code) {
                alertService.error('Please select an address from the suggestions so city, state and zip can be filled.');
                return;
            }

            try {
                this.loading.isActive = true;
                await this.frontendRestaurantSignupStore.callRegister(formData).then(async (res) => {
                    this.loading.isActive = false;
                    this.errors           = {};
                    this.props.form       = this.emptyForm();
                    await this.$router.push({name: "auth.signupRestaurantThankYou"});
                }).catch((err) => {
                    this.loading.isActive = false;
                    if (err?.response?.data?.errors) {
                        this.errors = err.response.data.errors;
                    } else if (err?.response?.data?.message) {
                        alertService.error(err.response.data.message);
                    }

                    if (err.response?.data?.errors?.terms_and_conditions) {
                        alertService.error(err.response.data.errors.terms_and_conditions[0]);
                    }

                    if (err.response?.data?.errors?.token) {
                        alertService.error(err.response.data.errors.token[0]);
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
