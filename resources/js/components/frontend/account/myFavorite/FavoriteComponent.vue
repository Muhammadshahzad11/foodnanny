<template>
    <LoadingComponent :props="loading"/>
    <section v-if="logged" class="mb-8 mt-4 md:mb-10 md:mt-6">
        <div class="container">
            <h2 class="mb-6 sm:mb-8 text-xl sm:text-2xl font-semibold capitalize">
                {{ $t('label.favorite_restaurants') }}
            </h2>

            <div v-if="restaurants.length > 0">
                <div class="mb-4 grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3 sm:gap-6">
                    <RestaurantCardComponent :restaurant="restaurant" v-for="restaurant in restaurants"/>
                </div>

                <div v-if="form.page <= lastPage" class="flex flex-row justify-center items-center">
                    <button @click="favoriteRestaurants" class="h-full flex items-center mt-3 px-4 py-2 sm:px-4 rounded-full text-base sm:text-lg font-medium bg-primary/90 hover:bg-primary text-white">
                        {{ $t('label.see_more') }} <i class="lab-line-chevron-down mt-1"></i>
                    </button>
                </div>
            </div>

            <div v-else class="w-full py-10 flex flex-col items-center justify-center text-center">
                <img class="w-40" :src="setting.image_restaurant" alt="empty">
                <h3 class="text-lg text-gray-300">{{ $t('message.not_the_restaurant_yet') }}</h3>
            </div>
        </div>
    </section>
</template>

<script>
import LoadingComponent from "../../../common/LoadingComponent.vue";
import {useFrontendRestaurantStore} from "../../../../stores/frontendRestaurant.js";
import {useAuthStore} from "../../../../stores/auth.js";
import RestaurantCardComponent from "../../components/RestaurantCardComponent.vue";
import {useFrontendSettingStore} from "../../../../stores/frontendSetting.js";

export default {
    name: "FavoriteComponent",
    components: {
        RestaurantCardComponent,
        LoadingComponent
    },
    setup() {
        const authStore               = useAuthStore();
        const frontendSettingStore    = useFrontendSettingStore();
        const frontendRestaurantStore = useFrontendRestaurantStore();

        return {
            authStore,
            frontendSettingStore,
            frontendRestaurantStore
        }
    },
    data() {
        return {
            loading: {
                isActive: false
            },
            loadingContent: {
                isActive: false
            },
            restaurants: [],
            lastPage: null,
            form: {
                paginate: 1,
                per_page: 12,
                page: 1
            }
        }
    },
    computed: {
        logged: function () {
            return this.authStore.status;
        },
        setting: function () {
            return this.frontendSettingStore.lists;
        }
    },
    mounted() {
        this.favoriteRestaurants();
    },
    methods: {
        favoriteRestaurants: function () {
            this.loadingContent.isActive = true;
            this.frontendRestaurantStore.fetchFavoriteRestaurants(this.form).then((res) => {
                for (let i = 0; i < res.data.data.length; i++) {
                    this.restaurants.push(res.data.data[i]);
                }
                if (res.data.meta.last_page >= res.data.meta.current_page) {
                    this.form.page++;
                }
                this.lastPage                = res.data.meta.last_page;
                this.loadingContent.isActive = false;
            }).catch((err) => {
                this.loadingContent.isActive = false;
            });
        }
    }
}
</script>
