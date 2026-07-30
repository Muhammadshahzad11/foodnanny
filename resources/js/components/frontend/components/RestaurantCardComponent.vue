<template>
    <figure class="group w-full block overflow-hidden rounded-2xl border border-gray-100">
        <figure class="relative overflow-hidden">
            <label v-if="typeof offerRestaurant[restaurant.id] !== 'undefined'" class="absolute top-2 ltr:left-2 rtl:right-2 z-20 whitespace-nowrap text-xs sm:text-sm px-2 py-1 rounded-lg bg-primary text-white">{{ $t('message.percentage_off', {discount : offerRestaurant[restaurant.id].amount }) }}</label>
            <button @click.prevent="favorite(restaurant, restaurant.favorite = !restaurant.favorite)" class="absolute top-2 ltr:right-2 rtl:left-2 z-20 cursor-pointer">
                <i :class="restaurant.favorite ? 'lab-fill-heart text-primary' : 'lab-line-heart'" class="text-sm w-6 h-6 leading-6 text-center rounded-full bg-white"></i>
            </button>
            <div v-if="restaurant.status === enums.statusEnum.INACTIVE" class="absolute inset-0 z-10 flex flex-col items-center justify-center bg-black/60">
                <h3 class="text-base sm:text-lg capitalize text-center whitespace-nowrap mb-2 text-white">{{ $t('label.temporary_closed') }}</h3>
            </div>
            <div v-if="restaurant.status === enums.statusEnum.ACTIVE && restaurant.availability === enums.availabilityEnum.CLOSE" class="absolute inset-0 z-10 flex flex-col items-center justify-center bg-black/60">
                <h3 class="text-base sm:text-lg capitalize text-center whitespace-nowrap mb-2 text-white">{{ $t('label.close_now') }}</h3>
                <button @click.prevent="handleGoToRestaurant(restaurant)" class="px-2 sm:px-3 py-1.5 rounded-full capitalize text-xs sm:text-sm whitespace-nowrap text-primary bg-white">{{ $t('label.schedule_order') }}</button>
            </div>
            <img @click.prevent="handleGoToRestaurant(restaurant)" :src="restaurant.thumb" alt="restaurant" class="w-full cursor-pointer group-hover:rotate-3 group-hover:scale-110 transition-all duration-500 h-[148px] object-cover">
        </figure>
        <figcaption class="py-4 px-3 min-h-[114px] bg-white">
            <h3 @click.prevent="handleGoToRestaurant(restaurant)" class="mb-2 font-medium capitalize cursor-pointer whitespace-nowrap overflow-hidden text-ellipsis group-hover:text-primary transition-all duration-500">
                {{ textShortener(restaurant.name,) }}
            </h3>
            <p class="flex flex-wrap items-center gap-y-1 gap-x-1.5 mb-2.5 text-sm text-paragraph">
                <span v-if="restaurant.distance">{{ restaurant.distance }} {{ $t('label.km') }}</span>
                <span v-if="restaurant.distance" class="w-1 h-1 rounded-full bg-paragraph"></span>
                <span>{{ restaurant.preparation_time }} {{ $t('label.minute') }}</span>
            </p>
            <div v-if="restaurant.rating_star > 0" class="flex items-center gap-1">
                <i class="lab-fill-star text-sm -mt-1 text-admin-yellow"></i>
                <span class="text-sm text-secondary">
                    {{ (restaurant.rating_star / restaurant.rating_star_count).toFixed(1) }}
                </span>
                <span class="text-sm text-paragraph">({{ restaurant.rating_star_count }})</span>
            </div>
        </figcaption>
    </figure>
</template>

<script>
import statusEnum from "../../../enums/modules/statusEnum.js";
import availabilityEnum from "../../../enums/modules/availabilityEnum.js";
import appService from "../../../services/appService.js";
import router from "../../../router/index.js";
import {useFrontendFavoriteStore} from "../../../stores/frontendFavorite.js";

export default {
    name : "RestaurantCardComponent",
    props: {
        restaurant: Object,
        offerRestaurant: {
            type: Object,
            default: {},
            required: false
        }
    },
    setup() {
        const frontendFavoriteStore = useFrontendFavoriteStore();

        return {
            frontendFavoriteStore
        }
    },
    data() {
        return {
            enums: {
                statusEnum : statusEnum,
                availabilityEnum : availabilityEnum
            }
        }
    },
    methods: {
        textShortener: function (text, number = 30) {
            return appService.textShortener(text, number);
        },
        handleGoToRestaurant: function(restaurant) {
            if(restaurant.status === this.enums.statusEnum.ACTIVE) {
                this.$router.push({
                    name: 'frontend.singleRestaurant',
                    params: {
                        slug: restaurant.slug
                    }
                });
            }
        },
        favorite: function (restaurant, toggle) {
            this.frontendFavoriteStore.toggle({
                restaurant_id: restaurant.id,
                toggle: toggle
            }).then((res) => {
            }).catch((err) => {
                if (err.response.status === 401) {
                    restaurant.favorite = false;
                    router.push({name: "auth.login"});
                }
            })
        }
    }
}
</script>
