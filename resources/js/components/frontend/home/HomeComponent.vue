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
                                    class="lab-line-gps text-xl flex-shrink-0 text-primary ltr:ml-3 rtl:mr-3 absolute ltr:left-2 rtl:right-2 z-[1]"></button>
                            <input id="map-autocomplete-input" type="search" v-model="modelLocation"
                                   ref="homeLocationName" :placeholder="$t('label.enter_your_location')"
                                   class="w-full h-full ltr:pl-12 ltr:pr-24 ltr:sm:pr-32 rtl:pl-24 rtl:sm:pl-32 rtl:pr-12">
                            <button :class="modelLocation === '' || modelLocation === null ? 'bg-primary/50' : ''"
                                    :disabled="modelLocation === '' || modelLocation === null"
                                    class="h-full px-4 sm:px-6 rounded-full text-base sm:text-lg capitalize font-medium bg-primary text-white absolute ltr:right-0 rtl:left-0">
                                {{ $t('button.search') }}
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
import _ from "lodash";
import ENV from "../../../config/env.js";

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
            currentLocation: {},
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

        await this.mainMap();
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
        }
    },
    methods: {
        mainMap: async function () {
            if(ENV.GOOGLE_MAP_KEY) {
                const Places       = await google.maps.importLibrary("places")
                let input          = document.getElementById('map-autocomplete-input');
                const autocomplete = new Places.Autocomplete(input);

                autocomplete.addListener('place_changed', () => {
                    const place          = autocomplete.getPlace();
                    this.currentLocation = {
                        lat: place.geometry.location.lat(),
                        lng: place.geometry.location.lng()
                    };
                    this.setPosition();
                });

                await this.setCurrentLocation();

                let currentLocationButton = document.getElementById('map-current-location');
                currentLocationButton.addEventListener("click", async () => {
                    await this.setCurrentLocation();
                });
            }
        },
        setPosition: function () {
            let other             = {
                "roadNo": null,
                "block": null,
                "area": null,
                "city": null,
                "zipCode": null,
                "state": null,
                "country": null,
            };
            let formatted_address = "";
            const latLngLiteral   = new google.maps.LatLng(this.currentLocation.lat, this.currentLocation.lng);
            const geocoder        = new google.maps.Geocoder();
            geocoder.geocode({latLng: latLngLiteral}).then(res => {
                for (let i = 0; i < res.results.length; i++) {
                    for (let j = 0; j < res.results[i].address_components.length; j++) {

                        if (res.results[i].address_components[j].types[0] === "route" && other.roadNo === null) {
                            other.roadNo = res.results[i].address_components[j].long_name;
                        }

                        if (res.results[i].address_components[j].types[0] === "neighborhood" && res.results[i].address_components[j].types[1] === "political" && other.block === null) {
                            other.block = res.results[i].address_components[j].long_name;
                        }

                        if (res.results[i].address_components[j].types[0] === "political" && res.results[i].address_components[j].types[1] === "sublocality" && res.results[i].address_components[j].types[2] === "sublocality_level_1" && other.area === null) {
                            other.area = res.results[i].address_components[j].long_name;
                        }

                        if (res.results[i].address_components[j].types[0] === "locality" && res.results[i].address_components[j].types[1] === "political" && other.city === null) {
                            other.city = res.results[i].address_components[j].long_name;
                        }

                        for (let k = 0; k < res.results[i].address_components[j].types.length; k++) {
                            if (res.results[i].address_components[j].types[k] === "postal_code" && other.zipCode === null) {
                                other.zipCode = res.results[i].address_components[j].long_name;
                            }
                        }

                        if (res.results[i].address_components[j].types[0] === "administrative_area_level_1" && res.results[i].address_components[j].types[1] === "political" && other.state === null) {
                            other.state = res.results[i].address_components[j].long_name;
                        }


                        if (res.results[i].address_components[j].types[0] === "country" && other.country === null) {
                            other.country = res.results[i].address_components[j].long_name;
                        }
                    }
                }

                _.forEach(other, (value, index) => {
                    if (value !== null && value !== "") {
                        formatted_address += value;
                        if (index !== "country") {
                            formatted_address += ", ";
                        }
                        formatted_address += "";
                    }
                });
                this.position = {
                    name: this.$refs.homeLocationName.value,
                    address: formatted_address,
                    other: other,
                    location: this.currentLocation
                };
            }).catch((error) => {
                this.position = {
                    name: null,
                    address: null,
                    other: {},
                    location: {
                        lat: null,
                        lng: null
                    }
                };
            });
        },
        setCurrentLocation: async function () {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(
                    (position) => {
                        this.currentLocation = {
                            lat: position.coords.latitude,
                            lng: position.coords.longitude,
                        };

                        const latLngLiteral = new google.maps.LatLng(this.currentLocation.lat, this.currentLocation.lng);
                        const geocoder      = new google.maps.Geocoder();
                        geocoder.geocode({latLng: latLngLiteral}).then(res => {
                            if (res.results.length > 0) {
                                this.modelLocation = res.results[0].formatted_address;
                                this.setPosition();
                            }
                        });
                    }, () => {
                        alert('The Geolocation service failed.');
                    }
                );
            } else {
                alert("Your browser doesn't support geolocation.");
            }
        },
        searchLocation: async function () {
            if (this.position.location.lat !== null && this.position.location.lng !== null) {
                await this.commonStore.update({
                    location: this.position.name,
                    latitude: this.position.location.lat,
                    longitude: this.position.location.lng
                });
                this.$router.push({name: 'frontend.restaurant'});
            }
        }
    }
}
</script>
