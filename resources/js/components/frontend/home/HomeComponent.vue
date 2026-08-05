<template>
    <LoadingComponent :props="loading"/>
    <section class="pt-10 sm:pt-16 pb-14 mb-16 sm:mb-24 bg-[#FFF8F2]">
        <div class="container">
            <div class="row items-center">
                <div class="col-12 md:col-6">
                    <div class="ltr:lg:mr-20 rtl:lg:ml-20">
                        <h1 class="text-4xl text-primary leading-12 sm:text-[52px] sm:leading-[60px] font-normal font-secondary mb-6 w-full mobile:max-w-xs tablet:max-w-[500px]">
                            {{ setting.frontend_hero_section_title }}
                        </h1>
                        <p class="mb-12 w-full mobile:max-w-sm tablet:max-w-lg">
                            {{ setting.frontend_hero_section_sub_title }}
                        </p>
                        <h3 class="text-xl sm:text-2xl font-medium mb-4">
                            {{ $t('message.search_restaurants_in_your_area') }}</h3>
                        <form @submit.prevent="searchLocation"
                              class="flex items-center gap-4 w-full max-w-md sm:max-w-lg h-12 sm:h-[60px] rounded-full shadow-xs bg-white relative">
                            <button type="button" id="map-current-location"
                                    title="Use current location"
                                    :disabled="locating"
                                    @click.prevent="useCurrentLocation"
                                    class="lab-line-gps text-xl flex-shrink-0 text-primary ltr:ml-3 rtl:mr-3 absolute ltr:left-2 rtl:right-2 z-[1] hover:opacity-80 disabled:opacity-40"></button>
                            <input id="map-autocomplete-input" type="search" v-model="modelLocation"
                                   ref="homeLocationName" :placeholder="$t('label.enter_your_location')"
                                   class="w-full h-full ltr:pl-12 ltr:pr-28 ltr:sm:pr-36 rtl:pl-28 rtl:sm:pl-36 rtl:pr-12">
                            <button v-if="modelLocation"
                                    type="button"
                                    title="Clear location"
                                    @click.prevent="clearLocationInput"
                                    class="lab-fill-close-circle text-lg text-danger absolute ltr:right-[5.5rem] sm:ltr:right-[7.5rem] rtl:left-[5.5rem] sm:rtl:left-[7.5rem] z-[1]"></button>
                            <button type="submit"
                                    :class="!canSearch ? 'bg-primary/50' : ''"
                                    :disabled="!canSearch || locating"
                                    class="h-full px-4 sm:px-6 rounded-full text-base sm:text-lg capitalize font-medium bg-primary text-white absolute ltr:right-0 rtl:left-0">
                                {{ locating ? '...' : $t('button.search') }}
                            </button>
                        </form>
                    </div>
                </div>
                <div class="col-12 md:col-6">
                    <figure class="flex justify-center lg:justify-end max-md:mt-8 max-md:px-8">
                        <img :src="setting.frontend_hero_section_image" alt="hero" class="w-full max-w-[460px]">
                    </figure>
                </div>
            </div>
        </div>
    </section>

    <section class="mb-10 sm:mb-24">
        <div class="container">
            <div class="text-center mb-7 sm:mb-14">
                <span class="block text-lg sm:text-xl capitalize mb-3 sm:mb-5">{{ $t('label.about_us') }}</span>
                <h2 class="text-3xl sm:text-5xl font-normal font-secondary capitalize">
                    {{ setting.frontend_about_title }}
                </h2>
            </div>
            <Swiper :dir="displayMode" :speed="1000" :pagination="{ clickable: true }" :modules="modules"
                    class="about-swiper pb-10 lg:pb-0" :breakpoints="breakpoints">
                <SwiperSlide v-for="aboutStep in aboutSteps">
                    <div class="flex flex-col items-center justify-center text-center w-full max-w-[250px] pb-8 mx-auto">
                        <img :src="aboutStep.thumb" alt="about" class="h-[60px] mb-6">
                        <h3 class="text-xl font-medium capitalize mb-2">{{ aboutStep.title }}</h3>
                        <p class="text-sm font-light">{{ aboutStep.description }}</p>
                    </div>
                </SwiperSlide>
            </Swiper>
        </div>
    </section>

    <section class="max-md:pt-14 md:py-10 lg:py-12 mb-16 sm:mb-24">
        <div class="max-md:pb-14 bg-secondary">
            <div class="container">
                <div class="flex flex-col-reverse md:flex-row items-center justify-between h-auto md:h-80 lg:h-96">
                    <div class="max-md:text-center">
                        <h3 class="mb-6 text-4xl lg:text-5xl font-normal font-secondary text-white">
                            {{ setting.frontend_app_section_title }}</h3>
                        <p class="mb-8 text-lg lg:text-xl font-medium text-white">
                            {{ $t('message.click_sit_back_and_enjoy') }}</p>
                        <nav class="flex flex-wrap gap-3 max-md:justify-center">
                            <a target="_blank" :href="setting.frontend_app_section_android_app_link">
                                <img class="h-10 lg:h-11 rounded-lg" :src="setting.image_play_store" alt="store">
                            </a>
                            <a target="_blank" :href="setting.frontend_app_section_iso_app_link">
                                <img class="h-10 lg:h-11 rounded-lg" :src="setting.image_app_store" alt="store">
                            </a>
                        </nav>
                    </div>
                    <div class="max-md:-mt-14 max-md:mb-6">
                        <img :src="setting.frontend_app_section_image" alt="mockup"
                             class="w-[290px] sm:w-[350px] md:w-[400px] lg:w-[500px]">
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="mb-10 sm:mb-24">
        <div class="container">
            <div class="text-center mb-7 sm:mb-14">
                <h2 class="text-3xl sm:text-5xl font-normal font-secondary capitalize">
                    {{ setting.frontend_benefit_title }}</h2>
            </div>
            <Swiper :dir="displayMode" :speed="1000" :pagination="{ clickable: false }" :modules="modules"
                    class="about-swiper pb-10 md:pb-0" :breakpoints="benefitBreakpoints">
                <SwiperSlide v-for="benefit in benefits">
                    <div :dir="displayMode" class="w-full max-w-[250px] mx-auto flex items-start gap-3 pb-8">
                        <img :src="benefit.thumb" alt="benefit" class="w-8 flex-shrink-0">
                        <dl>
                            <dt class="text-xl font-medium capitalize mb-2">{{ benefit.title }}</dt>
                            <dd class="text-sm font-light">{{ benefit.description }}</dd>
                        </dl>
                    </div>
                </SwiperSlide>
            </Swiper>
        </div>
    </section>

    <section class="mb-14 sm:mb-24">
        <div class="container">
            <div class="row">
                <div class="col-12 sm:col-6">
                    <div class="w-full h-[250px] md:h-[350px] relative rounded-3xl overflow-hidden">
                        <img :src="setting.frontend_restaurant_section_image" alt="actions"
                             class="w-full h-full object-cover">
                        <div
                            class="absolute top-0 left-0 p-5 w-full h-full flex flex-col justify-end bg-gradient-to-b from-transparent via-black/25 to-black">
                            <h3 class="text-xl font-semibold capitalize mb-1 w-full max-w-[423px] text-white">
                                {{ setting.frontend_restaurant_section_title }}</h3>
                            <p class="text-sm mb-4 w-full max-w-[423px] text-white">
                                {{ setting.frontend_restaurant_section_sub_title }}</p>
                            <router-link :to="{ name : 'auth.signupRestaurant' }"
                                         class="flex items-center justify-center gap-1 md:gap-2 rounded-3xl w-32 md:w-36 py-2 md:py-3 text-primary bg-white transition-all duration-500 hover:gap-2 md:hover:gap-4 hover:text-white hover:bg-primary">
                                <span class="text-sm capitalize">{{ $t('button.get_started') }}</span>
                                <i class="lab-line-long-arrow-right text-xl leading-none"></i>
                            </router-link>
                        </div>
                    </div>
                </div>

                <div class="col-12 sm:col-6">
                    <div class="w-full h-[250px] md:h-[350px] relative rounded-3xl overflow-hidden">
                        <img :src="setting.frontend_delivery_section_image" alt="actions"
                             class="w-full h-full object-cover">
                        <div
                            class="absolute top-0 left-0 p-5 w-full h-full flex flex-col justify-end bg-gradient-to-b from-transparent via-black/25 to-black">
                            <h3 class="text-xl font-semibold capitalize mb-1 w-full max-w-[423px] text-white">
                                {{ setting.frontend_delivery_section_title }}</h3>
                            <p class="text-sm mb-4 w-full max-w-[423px] text-white">
                                {{ setting.frontend_delivery_section_sub_title }}</p>
                            <router-link :to="{ name : 'auth.signupDeliveryBoy' }"
                                         class="flex items-center justify-center gap-1 md:gap-2 rounded-3xl w-32 md:w-36 py-2 md:py-3 text-primary bg-white transition-all duration-500 hover:gap-2 md:hover:gap-4 hover:text-white hover:bg-primary">
                                <span class="text-sm capitalize">{{ $t('button.get_started') }}</span>
                                <i class="lab-line-long-arrow-right text-xl leading-none"></i>
                            </router-link>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>

<script>
import {useCommonStore} from "../../../stores/common.js";
import LoadingComponent from "../../common/LoadingComponent.vue";
import {useFrontendSettingStore} from "../../../stores/frontendSetting.js";
import {Pagination} from 'swiper/modules';
import {Swiper, SwiperSlide} from 'swiper/vue';
import DisplayModeEnum from "../../../enums/modules/displayModeEnum.js";
import {useFrontendAboutStepsStore} from "../../../stores/frontendAboutSteps.js";
import StatusEnum from "../../../enums/modules/statusEnum.js";
import {useFrontendBenefitStore} from "../../../stores/frontendBenefit.js";
import ENV from "../../../config/env.js";
import locationService from "../../../services/locationService.js";
import alertService from "../../../services/alertService.js";

export default {
    name: "HomeComponent",
    components: {
        LoadingComponent,
        Swiper,
        SwiperSlide,
    },
    setup() {
        const commonStore             = useCommonStore();
        const frontendSettingStore    = useFrontendSettingStore();
        const frontendBenefitStore    = useFrontendBenefitStore();
        const frontendAboutStepsStore = useFrontendAboutStepsStore();

        return {
            commonStore,
            frontendSettingStore,
            frontendBenefitStore,
            frontendAboutStepsStore
        }
    },
    data() {
        return {
            loading: {
                isActive: false,
            },
            locating: false,
            modelLocation: null,
            position: {
                name: null,
                address: null,
                other: {},
                location: {
                    lat: null,
                    lng: null
                }
            },
            modules: [Pagination],
            breakpoints: {
                0: {slidesPerView: 1},
                640: {slidesPerView: 2},
                767: {slidesPerView: 3},
                1024: {slidesPerView: 4},
            },
            benefitBreakpoints: {
                0: {slidesPerView: 1},
                640: {slidesPerView: 2},
                767: {slidesPerView: 3}
            }
        }
    },
    async mounted() {
        if (this.commonStore.location) {
            await this.$router.push({name: 'frontend.restaurant'});
        }

        this.loading.isActive = true;
        await this.frontendAboutStepsStore.fetch({
            order_column: "sort",
            order_type: "asc",
            status: StatusEnum.ACTIVE
        }).then(res => {
            this.loading.isActive = false;
        }).catch(err => {
            this.loading.isActive = false;
        });

        this.loading.isActive = true;
        await this.frontendBenefitStore.fetch({
            order_column: "sort",
            order_type: "asc",
            status: StatusEnum.ACTIVE
        }).then(res => {
            this.loading.isActive = false;
        }).catch(err => {
            this.loading.isActive = false;
        });

        await this.initPlacesAutocomplete();
    },
    computed: {
        setting: function () {
            return this.frontendSettingStore.lists;
        },
        displayMode: function () {
            return this.commonStore.display_mode === DisplayModeEnum.LTR ? 'ltr' : 'rtl';
        },
        aboutSteps: function () {
            return this.frontendAboutStepsStore.lists;
        },
        benefits: function () {
            return this.frontendBenefitStore.lists;
        },
        canSearch() {
            const typed = String(this.modelLocation || '').trim();
            return typed.length > 0 || (this.position.location.lat != null && this.position.location.lng != null);
        }
    },
    methods: {
        async initPlacesAutocomplete() {
            if (!ENV.GOOGLE_MAP_KEY || typeof google === 'undefined') {
                return;
            }
            try {
                const Places = await google.maps.importLibrary("places");
                const input = document.getElementById('map-autocomplete-input');
                if (!input) return;
                const autocomplete = new Places.Autocomplete(input, {
                    fields: ['address_components', 'formatted_address', 'geometry', 'name'],
                });
                autocomplete.addListener('place_changed', () => {
                    const place = autocomplete.getPlace();
                    if (!place?.geometry?.location) return;
                    const lat = place.geometry.location.lat();
                    const lng = place.geometry.location.lng();
                    const name = place.formatted_address || this.$refs.homeLocationName?.value || '';
                    const area = locationService.areaFromPlace(place);
                    this.modelLocation = name;
                    this.applyCoords(lat, lng, name, area);
                });
            } catch (e) {
                // Autocomplete optional; GPS + typed search still work
            }
        },
        applyCoords(lat, lng, name, area = {}) {
            const label = name || this.modelLocation || 'Current location';
            this.position = {
                name: label,
                address: label,
                other: {
                    city: area.city || null,
                    district: area.district || null,
                    state: area.state || null,
                },
                location: { lat, lng }
            };
        },
        clearLocationInput() {
            this.modelLocation = null;
            this.position = {
                name: null,
                address: null,
                other: {},
                location: { lat: null, lng: null }
            };
            this.commonStore.clearLocation();
        },
        async goToRestaurants(lat, lng, name, area = {}) {
            const payload = {
                location: name || 'Current location',
                latitude: lat,
                longitude: lng,
                city: area.city || null,
                district: area.district || null,
                state: area.state || null,
            };
            if (!this.commonStore.order_type) {
                payload.order_type = 5; // Delivery
            }
            await this.commonStore.update(payload);
            this.$router.push({ name: 'frontend.restaurant' });
        },
        async useCurrentLocation() {
            this.locating = true;
            this.loading.isActive = true;
            try {
                const coords = await locationService.getCurrentPosition();
                const geo = await locationService.reverseGeocode(coords.lat, coords.lng);
                this.modelLocation = geo.name;
                this.applyCoords(coords.lat, coords.lng, geo.name, geo);
                await this.goToRestaurants(coords.lat, coords.lng, geo.name, geo);
            } catch (e) {
                const msg = e?.message === 'geolocation_unsupported'
                    ? "Your browser doesn't support geolocation."
                    : 'Could not get your current location. Please allow location access and try again.';
                alertService.error(msg);
            } finally {
                this.locating = false;
                this.loading.isActive = false;
            }
        },
        async searchLocation() {
            if (this.locating) return;
            this.locating = true;
            this.loading.isActive = true;
            try {
                const typed = String(this.modelLocation || '').trim();
                let lat = this.position.location.lat;
                let lng = this.position.location.lng;
                let name = this.position.name || typed;
                let area = {
                    city: this.position.other?.city || this.commonStore.city,
                    district: this.position.other?.district || this.commonStore.district,
                    state: this.position.other?.state || this.commonStore.state,
                };

                const coordsMatchTyped = lat != null && lng != null
                    && typed
                    && String(this.position.name || '').trim() === typed;

                if (!coordsMatchTyped) {
                    if (!typed) {
                        alertService.error(this.$t('label.enter_your_location') || 'Enter your location');
                        return;
                    }
                    const geo = await locationService.forwardGeocode(typed);
                    lat = geo.lat;
                    lng = geo.lng;
                    name = geo.name;
                    area = geo;
                    this.modelLocation = geo.name;
                    this.applyCoords(lat, lng, name, geo);
                } else if (!area.city && !area.district && !area.state && lat != null && lng != null) {
                    area = await locationService.reverseGeocode(lat, lng);
                }

                await this.goToRestaurants(lat, lng, name, area);
            } catch (e) {
                alertService.error('Location not found. Try another address or use current location.');
            } finally {
                this.locating = false;
                this.loading.isActive = false;
            }
        }
    }
}
</script>
