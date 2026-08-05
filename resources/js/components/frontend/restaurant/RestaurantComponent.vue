<template>
    <LoadingComponent :props="mainLoading"/>
    <TrackOrderComponent/>

    <section v-if="cuisines.length > 0">
        <LoadingContentComponent :props="contentLoading"/>
        <div class="container" v-if="cuisineShowHide">
            <Swiper :dir="displayMode" :loop="false" :speed="1000" :navigation="true" :modules="modules" :breakpoints="breakPoints" class="middle-navigate !py-1 sm:!py-4">
                <SwiperSlide v-for="varCuisine in cuisines" class="mobile:!w-20 group">
                    <div @click="setCuisine(varCuisine.id)" :class="cuisine === varCuisine.id ? 'bg-primary/10 text-primary' : ''" class="cursor-pointer w-full flex flex-col items-center justify-center gap-2 sm:gap-3 py-3 sm:py-4 px-3 rounded-xl transition-all duration-300 group-hover:bg-primary/10">
                        <img :src="varCuisine.image" alt="category" class="h-10 sm:h-12">
                        <span
                            class="text-sm capitalize text-center w-full whitespace-nowrap overflow-hidden text-ellipsis group-hover:text-primary transition-all">
                            {{ varCuisine.name }}
                        </span>
                    </div>
                </SwiperSlide>
            </Swiper>
        </div>
    </section>

    <section v-if="search === null && (singleOffer && singleRestaurants.length > 0)" class="mb-5 sm:mb-8">
        <div v-if="singleOfferShowHide" class="container">
            <div class="p-4 sm:p-6 rounded-2xl bg-primary/5">
                <div class="mb-4 sm:-mb-11 flex items-center gap-3">
                    <img class="flex-shrink-0 w-12" :src="setting.image_offer" alt="offer">
                    <dl class="flex-auto">
                        <dt class="text-xl sm:text-2xl font-semibold capitalize text-primary">
                            {{ $t("message.get_discount_off", {discount: singleOffer.percentage}) }}
                        </dt>
                        <dd class="text-xs sm:text-sm capitalize text-primary">
                            {{
                                $t('message.until_offer', {
                                    start_time: singleOffer.start_time,
                                    end_time: singleOffer.end_time
                                })
                            }}
                        </dd>
                    </dl>
                </div>
                <Swiper :dir="displayMode" :loop="false" :speed="1000" :navigation="true" :modules="modules"
                        :breakpoints="restaurantBreakPoints" class="float-navigate">
                    <SwiperSlide v-for="restaurant in singleRestaurants" class="mobile:!w-60">
                        <RestaurantCardComponent :restaurant="restaurant"/>
                    </SwiperSlide>
                </Swiper>
            </div>
        </div>
    </section>

    <section v-if="search === null && offerAndCampaigns.length > 0" class="mb-9 sm:mb-12">
        <div v-if="offerAndCampaignsShowHide" class="container">
            <Swiper :dir="displayMode" :loop="false" :speed="1000" :navigation="true" :modules="modules"
                    :breakpoints="offerBreakPoints" class="middle-navigate">
                <SwiperSlide v-for="offerAndCampaign in offerAndCampaigns" class="mobile:!w-60">
                    <router-link
                        :to="{ name : 'frontend.offerAndCampaign', params : { slug : offerAndCampaign.slug, type : offerAndCampaign.type }}"
                        class="w-full block">
                        <img :src="offerAndCampaign.thumb" alt="promotion" class="w-full rounded-2xl">
                    </router-link>
                </SwiperSlide>
            </Swiper>
        </div>
    </section>

    <section v-if="showActiveRestaurants.length > 0" class="mb-8 md:mb-10 mt-3">
        <div class="container">
            <h2 class="mb-6 sm:mb-8 text-xl sm:text-2xl font-semibold capitalize">
                {{
                    search === null ? $t('label.all_restaurants_nearby') : $t('message.we_found', {
                        length: showActiveRestaurants.length,
                        search: search
                    })
                }}
            </h2>
            <div class="mb-4 grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3 sm:gap-6">
                <RestaurantCardComponent :restaurant="showActiveRestaurant" :offerRestaurant="findRestaurants" v-for="showActiveRestaurant in showActiveRestaurants"/>
            </div>
            <div v-if="lastPage > 1 && form.page <= lastPage" class="flex flex-row justify-center items-center">
                <button @click.prevent="list"
                        class="h-full flex items-center mt-3 px-4 py-2 sm:px-4 rounded-full text-base sm:text-lg font-medium bg-primary/90 hover:bg-primary text-white">
                    {{ $t('label.see_more') }} <i class="lab-line-chevron-down mt-1"></i>
                </button>
            </div>
        </div>
    </section>

    <section v-if="closeRestaurants.length > 0 && totalRestaurant === (activeRestaurantsCounter.length + closeRestaurants.length + temporaryRestaurants.length)" class="mb-8 md:mb-10 mt-3">
        <div class="container">
            <h2 class="mb-6 sm:mb-8 text-xl sm:text-2xl font-semibold capitalize">
                {{ $t('label.closed_for_now') }}
            </h2>
            <div class="mb-4 grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3 sm:gap-6">
                <RestaurantCardComponent :restaurant="closeRestaurant" v-for="closeRestaurant in closeRestaurants"/>
            </div>
        </div>
    </section>

    <section v-if="temporaryRestaurants.length > 0 && totalRestaurant === (activeRestaurantsCounter.length + closeRestaurants.length + temporaryRestaurants.length)" class="mb-8 md:mb-10 mt-3">
        <div class="container">
            <h2 class="mb-6 sm:mb-8 text-xl sm:text-2xl font-semibold capitalize">
                {{ $t('label.temporarily_unavailable') }}
            </h2>
            <div class="mb-4 grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3 sm:gap-6">
                <RestaurantCardComponent :restaurant="temporaryRestaurant"
                                         v-for="temporaryRestaurant in temporaryRestaurants"/>
            </div>
        </div>
    </section>

    <section v-if="totalRestaurant === 0" class="mb-8 md:mb-10 mt-3">
        <div class="container">
            <div class="w-full py-10 flex flex-col items-center justify-center text-center">
                <img class="w-40" :src="setting.image_restaurant" alt="empty">
                <h3 class="text-lg text-gray-300">{{ $t('message.not_the_restaurant_yet') }}</h3>
            </div>
        </div>
    </section>
</template>

<script>
import {Navigation} from 'swiper/modules';
import {Swiper, SwiperSlide} from 'swiper/vue';
import DisplayModeEnum from "../../../enums/modules/displayModeEnum";
import LoadingComponent from "../../common/LoadingComponent.vue";
import offerAndCampaignEnum from "../../../enums/modules/offerAndCampaignEnum.js";
import LoadingContentComponent from "../../common/LoadingContentComponent.vue";
import {useFrontendRestaurantStore} from "../../../stores/frontendRestaurant.js";
import {useCommonStore} from "../../../stores/common.js";
import _ from "lodash";
import statusEnum from "../../../enums/modules/statusEnum.js";
import {useFrontendCuisineStore} from "../../../stores/frontendCuisine.js";
import appService from "../../../services/appService.js";
import availabilityEnum from "../../../enums/modules/availabilityEnum.js";
import RestaurantCardComponent from "../components/RestaurantCardComponent.vue";
import {useFrontendSettingStore} from "../../../stores/frontendSetting.js";
import {useFrontendOfferStore} from "../../../stores/frontendOffer.js";
import {useFrontendCampaignStore} from "../../../stores/frontendCampaign.js";
import TrackOrderComponent from "../components/TrackOrderComponent.vue";


export default {
    name: "RestaurantComponent",
    components: {
        TrackOrderComponent,
        RestaurantCardComponent,
        LoadingContentComponent,
        LoadingComponent,
        Swiper,
        SwiperSlide,
    },
    setup() {
        const commonStore             = useCommonStore();
        const frontendOfferStore      = useFrontendOfferStore();
        const frontendSettingStore    = useFrontendSettingStore();
        const frontendCuisineStore    = useFrontendCuisineStore();
        const frontendCampaignStore   = useFrontendCampaignStore();
        const frontendRestaurantStore = useFrontendRestaurantStore();

        return {
            commonStore,
            frontendOfferStore,
            frontendSettingStore,
            frontendCuisineStore,
            frontendCampaignStore,
            frontendRestaurantStore,
            modules: [Navigation],
        }
    },
    data() {
        return {
            cuisineShowHide: true,
            singleOfferShowHide: true,
            offerAndCampaignsShowHide: true,
            activeRestaurantsCounter: [],
            showActiveRestaurants: [],
            activeRestaurants: [],
            temporaryRestaurants: [],
            closeRestaurants: [],
            offerAndCampaigns: [],
            lastPage: null,
            totalRestaurant: 0,
            enums: {
                offerAndCampaignEnum: offerAndCampaignEnum
            },
            mainLoading: {
                isActive: false,
            },
            contentLoading: {
                isActive: false,
            },
            breakPoints: {
                0: {slidesPerView: 'auto'},
                640: {slidesPerView: 6},
                768: {slidesPerView: 8},
                1024: {slidesPerView: 10},
            },
            restaurantBreakPoints: {
                0: {slidesPerView: 'auto', spaceBetween: 16},
                640: {slidesPerView: 2, spaceBetween: 24},
                768: {slidesPerView: 3, spaceBetween: 24},
                1024: {slidesPerView: 4, spaceBetween: 24}
            },
            offerBreakPoints: {
                0: {slidesPerView: 'auto', spaceBetween: 16},
                640: {slidesPerView: 2, spaceBetween: 24},
                768: {slidesPerView: 3, spaceBetween: 24}
            },
            form: {
                paginate: 1,
                page: 1,
                per_page: 12,
                order_column: 'distance',
                order_type: "asc",
                latitude: null,
                longitude: null,
                city: null,
                district: null,
                state: null,
                delivery_order_type: null,
                name: null,
                cuisine_id: null
            }
        }
    },
    beforeMount() {
        this.getInitialList();
    },
    computed: {
        displayMode: function () {
            return this.commonStore.display_mode === DisplayModeEnum.LTR ? 'ltr' : 'rtl';
        },
        setting: function () {
            return this.frontendSettingStore.lists;
        },
        cuisines: function () {
            return this.frontendCuisineStore.lists;
        },
        location: function () {
            return this.commonStore.location;
        },
        latitude: function () {
            return this.commonStore.latitude;
        },
        longitude: function () {
            return this.commonStore.longitude;
        },
        city: function () {
            return this.commonStore.city;
        },
        district: function () {
            return this.commonStore.district;
        },
        state: function () {
            return this.commonStore.state;
        },
        orderType: function () {
            return this.commonStore.order_type;
        },
        search: function () {
            return this.commonStore.search_restaurant;
        },
        cuisine: function () {
            return this.commonStore.cuisine_id;
        },
        singleOffer: function () {
            return this.frontendOfferStore.single;
        },
        singleRestaurants: function () {
            return this.frontendOfferStore.singleRestaurants;
        },
        findRestaurants: function () {
            return this.frontendOfferStore.find;
        }
    },
    async mounted() {
        if (!this.commonStore.location) {
            this.$router.push({name: 'frontend.home'});
        }
        await this.getInitialOffer();
    },
    methods: {
        textShortener: function (text, number = 30) {
            return appService.textShortener(text, number);
        },
        setCuisine: function (cuisineId) {
            this.commonStore.update({
                cuisine_id: this.cuisine !== cuisineId ? cuisineId : null
            });
        },
        getInitialList() {
            this.activeRestaurantsCounter = [];
            this.showActiveRestaurants    = [];
            this.activeRestaurants        = [];
            this.temporaryRestaurants     = [];
            this.closeRestaurants         = [];
            this.form.latitude            = this.latitude;
            this.form.longitude           = this.longitude;
            this.form.city                = this.city;
            this.form.district            = this.district;
            this.form.state               = this.state;
            this.form.delivery_order_type = this.orderType;
            this.form.name                = this.search;
            this.form.cuisine_id          = this.cuisine;
            this.mainLoading.isActive     = true;

            if (this.form.latitude && this.form.longitude) {
                this.frontendRestaurantStore.fetch(this.form).then(res => {
                    this.mainLoading.isActive = false;
                    this.totalRestaurant      = res.data.meta.total;
                    this.lastPage             = res.data.meta.last_page;

                    for (let i = 0; i < res.data.data.length; i++) {
                        if (res.data.data[i].status === statusEnum.ACTIVE && res.data.data[i].availability === availabilityEnum.OPEN) {
                            this.activeRestaurants.push(res.data.data[i]);
                            this.activeRestaurantsCounter.push(res.data.data[i]);
                        } else if (res.data.data[i].status === statusEnum.INACTIVE && res.data.data[i].availability === availabilityEnum.OPEN) {
                            this.temporaryRestaurants.push(res.data.data[i]);
                        } else if (res.data.data[i].status === statusEnum.ACTIVE && res.data.data[i].availability === availabilityEnum.CLOSE) {
                            this.closeRestaurants.push(res.data.data[i]);
                        } else if (res.data.data[i].status === statusEnum.INACTIVE && res.data.data[i].availability === availabilityEnum.CLOSE) {
                            this.temporaryRestaurants.push(res.data.data[i]);
                        }
                    }

                    if (this.form.page < res.data.meta.last_page) {
                        this.form.page++;
                    }
                    this.autoCurl();
                }).catch((err) => {
                    this.mainLoading.isActive = false;
                })
            }
        },
        async getInitialOffer() {
            await this.frontendCuisineStore.fetch({
                order_column: 'sort',
                order_type: 'asc',
                status: statusEnum.ACTIVE
            });
            await this.frontendOfferStore.fetchFind({
                latitude: this.latitude,
                longitude: this.longitude,
                delivery_order_type: this.orderType
            })
            await this.frontendOfferStore.fetchSingle({
                latitude: this.latitude,
                longitude: this.longitude,
                delivery_order_type: this.orderType
            });
            await this.frontendCampaignStore.fetch({
                latitude: this.latitude,
                longitude: this.longitude,
                delivery_order_type: this.orderType
            }).then(res => {
                if (res.data.data.length > 0) {
                    _.forEach(res.data.data, (obj) => {
                        let restaurantCheck = false;
                        if (obj.restaurants.length > 0) {
                            _.forEach(obj.restaurants, (restaurant) => {
                                if (restaurant.id > 0) {
                                    restaurantCheck = true;
                                }
                            });
                            if (restaurantCheck) {
                                this.offerAndCampaigns.push(obj);
                                restaurantCheck = false;
                            }
                        }
                    });
                }
            }).catch();
            await this.frontendOfferStore.fetchMulti({
                latitude: this.latitude,
                longitude: this.longitude,
                delivery_order_type: this.orderType
            }).then(res => {
                if (res.data.data.length > 0) {
                    _.forEach(res.data.data, (obj) => {
                        let restaurantCheck = false;
                        if (obj.restaurants.length > 0) {
                            _.forEach(obj.restaurants, (restaurant) => {
                                if (restaurant.id > 0) {
                                    restaurantCheck = true;
                                }
                            });
                            if (restaurantCheck) {
                                this.offerAndCampaigns.push(obj);
                                restaurantCheck = false;
                            }
                        }
                    });
                }
            }).catch();
        },
        autoCurl: function () {
            if (this.activeRestaurants.length >= this.form.per_page || this.form.page === this.lastPage) {
                _.forEach(this.activeRestaurants, (restaurant, key) => {
                    if (key < this.form.per_page) {
                        this.showActiveRestaurants.push(restaurant);
                    }
                });
                this.activeRestaurants.splice(0, this.form.per_page);
            } else {
                setTimeout(() => {
                    this.list();
                    this.mainLoading.isActive = false;
                }, 300);
            }

            setTimeout(() => {
                if ((this.totalRestaurant === (this.activeRestaurantsCounter.length + this.closeRestaurants.length + this.temporaryRestaurants.length)) && this.activeRestaurants.length > 0) {
                    _.forEach(this.activeRestaurants, (restaurant, key) => {
                        if (key < this.form.per_page) {
                            this.showActiveRestaurants.push(restaurant);
                        }
                    });
                    this.activeRestaurants = [];
                }
            }, 300);
        },
        list: function () {
            this.form.latitude            = this.latitude;
            this.form.longitude           = this.longitude;
            this.form.city                = this.city;
            this.form.district            = this.district;
            this.form.state               = this.state;
            this.form.delivery_order_type = this.orderType;
            this.form.name                = this.search;
            this.form.cuisine_id          = this.cuisine;
            this.mainLoading.isActive     = true;

            if (this.form.latitude && this.form.longitude) {
                if (this.form.page <= this.lastPage) {
                    this.frontendRestaurantStore.fetch(this.form).then(res => {
                        this.mainLoading.isActive = false;
                        for (let i = 0; i < res.data.data.length; i++) {
                            if (res.data.data[i].status === statusEnum.ACTIVE && res.data.data[i].availability === availabilityEnum.OPEN) {
                                this.activeRestaurants.push(res.data.data[i]);
                                this.activeRestaurantsCounter.push(res.data.data[i]);
                            } else if (res.data.data[i].status === statusEnum.INACTIVE && res.data.data[i].availability === availabilityEnum.OPEN) {
                                this.temporaryRestaurants.push(res.data.data[i]);
                            } else if (res.data.data[i].status === statusEnum.ACTIVE && res.data.data[i].availability === availabilityEnum.CLOSE) {
                                this.closeRestaurants.push(res.data.data[i]);
                            } else if (res.data.data[i].status === statusEnum.INACTIVE && res.data.data[i].availability === availabilityEnum.CLOSE) {
                                this.temporaryRestaurants.push(res.data.data[i]);
                            }
                        }

                        this.autoCurl();
                        this.form.page++;
                    }).catch((err) => {
                        this.mainLoading.isActive = false;
                    })
                }
            }

        }
    },
    watch: {
        location: function () {
            this.form.page = 1;
            this.getInitialList();
        },
        orderType: function () {
            this.form.page = 1;
            this.getInitialList();
            this.getInitialOffer();
        },
        search: function () {
            this.form.page = 1;
            this.getInitialList();
        },
        cuisine: function () {
            this.form.page = 1;
            this.getInitialList();
        },
        displayMode: function () {
            this.contentLoading.isActive = true;
            window.setTimeout(() => {
                this.cuisineShowHide           = false;
                this.singleOfferShowHide       = false;
                this.offerAndCampaignsShowHide = false;
            }, 100);
            window.setTimeout(() => {
                this.contentLoading.isActive   = false;
                this.cuisineShowHide           = true;
                this.singleOfferShowHide       = true;
                this.offerAndCampaignsShowHide = true;
            }, 100);
        }
    }
}
</script>
