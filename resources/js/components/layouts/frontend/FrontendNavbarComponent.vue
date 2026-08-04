<template>
    <LoadingComponent :props="loading"/>
    <header :class="isSticky ? 'fixed top-0 left-0 z-30 shadow-xs bg-white' : '', isHome ? 'bg-[#FFF8F2]' : '', mode !== 'main' ? 'shadow-xs bg-white' : ''" class="w-full py-3 sm:py-4 transition-all duration-500">
        <div class="container">
            <div :class="mode !== 'main' ? 'flex-col' : ''" class="flex lg:flex-row items-center justify-between gap-4">
                <div :class="mode === 'main' ? 'flex items-center justify-between' : 'max-lg:w-full max-lg:justify-between max-lg:p-0 flex-shrink-0 flex items-center gap-6'">
                    <router-link class="flex-shrink-0" :to="{ name: location ? 'frontend.restaurant' : 'frontend.home' }">
                        <img
                            class="h-12 sm:h-14 md:h-16 w-auto max-w-[200px] sm:max-w-[260px] md:max-w-[320px] object-contain object-left"
                            :src="setting.theme_logo"
                            alt="Cost to Cost Foods"
                        >
                    </router-link>
                    <button v-if="mode === 'option'" @click="openModal('delivery-address')" class="flex-auto flex items-center gap-2 w-full max-w-[150px] h-10 rounded-full px-3 bg-mate">
                        <i class="lab-fill-location flex-shrink-0 text-lg text-primary"></i>
                        <span class="text-sm w-full mt-[1px] whitespace-nowrap overflow-hidden text-ellipsis">
                            {{ location }}
                        </span>
                    </button>
                </div>

                <div v-if="mode === 'option'" :class="isScrollingUp ? 'max-lg:!flex' : 'max-lg:!hidden'" class="w-full max-lg:flex flex items-center gap-3 max-lg:origin-top max-lg:py-3 max-lg:border-t max-lg:border-slate-100">
                    <nav class="flex-shrink-0 flex items-center p-1 rounded-full bg-mate">
                        <button @click.prevent="changeOrderType(enums.orderTypeEnum.DELIVERY)" :class="orderType === enums.orderTypeEnum.DELIVERY ? 'text-primary bg-white' : ''" class="text-sm capitalize h-8 px-3 rounded-full">
                            {{ $t('button.delivery') }}
                        </button>
                        <button @click.prevent="changeOrderType(enums.orderTypeEnum.TAKEAWAY)" :class="orderType === enums.orderTypeEnum.TAKEAWAY ? 'text-primary bg-white' : ''" class="text-sm capitalize h-8 px-3 rounded-full">
                            {{ $t('button.takeaway') }}
                        </button>
                    </nav>

                    <form @submit.prevent="searchRestaurant" class="group w-full h-10 rounded-full flex-auto flex items-center gap-2 px-3 bg-mate">
                        <button type="submit" class="lab-line-search text-xl text-primary"></button>
                        <input type="search" id="restaurant-search" v-model="localRestaurant" :placeholder="$t('label.search_restaurant_or_foods')" class="w-full placeholder:text-sm">
                        <button v-if="localRestaurant !== null && localRestaurant !== ''" @click.prevent="resetSearchRestaurant" class="lab-fill-close-circle text-danger"></button>
                    </form>
                </div>

                <div :class="mode !== 'main' ? 'max-lg:hidden' : ''" class="flex-shrink-0 flex items-center gap-3">
                    <div v-if="mode === 'main' && !logged" class="paper-group relative">
                        <button @click.prevent="handlePaper"
                                class="paper-button flex items-center justify-center gap-1 mobile:w-9 sm:px-3.5 h-9 rounded-full border border-border bg-white">
                            <i class="lab-line-add-square flex-shrink-0 sm:ltr:mr-0.5 sm:rtl:ml-0.5 text-lg"></i>
                            <span class="mobile:hidden text-sm first-letter:capitalize">
                                {{ $t('button.partner_with_us') }}
                            </span>
                            <i class="mobile:hidden lab-line-chevron-down text-sm font-semibold text-primary"></i>
                        </button>
                        <nav class="paper-content w-[95%] sm:w-48 fixed sm:absolute top-16 sm:top-14 ltr:right-1/2 sm:ltr:right-0 rtl:left-1/2 sm:rtl:left-0 mobile:ltr:translate-x-1/2 mobile:rtl:-translate-x-1/2 shadow-paper rounded-lg z-10 p-2 bg-white">
                            <router-link :to="{ name: 'auth.signupRestaurant' }" class="px-2 py-1.5 text-sm rounded-lg w-full transition-all duration-300 hover:bg-slate-100">
                                {{ $t('button.list_your_restaurant') }}
                            </router-link>
                            <router-link :to="{ name: 'auth.signupDeliveryBoy' }" class="px-2 py-1.5 text-sm rounded-lg w-full transition-all duration-300 hover:bg-slate-100">
                                {{ $t('button.become_a_delivery_boy') }}
                            </router-link>
                        </nav>
                    </div>

                    <div v-if="setting.site_language_switch === enums.activityEnum.ENABLE" class="paper-group">
                        <button id="switchLanguageButton" @click.prevent="handlePaper"
                                class="paper-button flex items-center justify-center gap-1 mobile:w-9 sm:px-3.5 h-9 rounded-full border border-border bg-white">
                            <img :src="language.image" alt="flag"
                                 class="w-4 h-4 flex-shrink-0 sm:ltr:mr-0.5 sm:rtl:ml-0.5">
                            <span class="mobile:hidden text-sm first-letter:capitalize">{{ language.name }}</span>
                            <i class="mobile:hidden lab-line-chevron-down text-sm font-semibold text-primary"></i>
                        </button>
                        <ul class="paper-content w-[95%] sm:w-44 fixed sm:absolute top-16 sm:top-14 ltr:right-1/2 sm:ltr:right-0 rtl:left-1/2 sm:rtl:left-0 mobile:ltr:translate-x-1/2 mobile:rtl:-translate-x-1/2 shadow-paper rounded-lg z-10 p-2 bg-white">
                            <li v-for="(objLanguage, index) in languages" :key="index" @click.prevent="changeLanguage(objLanguage.id, objLanguage.code, objLanguage.display_mode)" :class="objLanguage.id === language.id ? 'bg-[#FFF8F2] !text-primary' : ''" class="flex items-center gap-3 px-2 py-1.5 rounded-lg relative w-full cursor-pointer transition-all duration-300 hover:bg-slate-100">
                                <img :src="objLanguage.image" alt="flags" class="w-4 h-4 flex-shrink-0"/>
                                <span class="text-sm first-letter:capitalize">{{ objLanguage.name }}</span>
                            </li>
                        </ul>
                    </div>

                    <button @click.prevent="openCanvas('cart-canvas')" type="button" v-if="mode !== 'main' && currentRoute !== 'frontend.checkout'" class="flex-shrink-0 flex items-center gap-1 mobile:px-2 px-3.5 h-9 rounded-full bg-secondary text-white">
                        <i class="lab-fill-bag text-lg"></i>
                        <span class="mobile:hidden text-sm">
                            {{
                                currencyFormat(subtotal, setting.site_digit_after_decimal_point, setting.site_default_currency_symbol, setting.site_currency_position)
                            }}
                        </span>
                    </button>

                    <router-link v-if="!logged" :to="{ name: 'auth.login' }" class="flex-shrink-0 flex items-center gap-1 mobile:px-2 px-3.5 h-9 rounded-full bg-primary text-white">
                        <i class="lab-fill-profile-circle text-lg"></i>
                        <span class="mobile:hidden text-sm font-medium first-letter:capitalize whitespace-nowrap">
                            {{ $t('label.login') }}
                        </span>
                    </router-link>
                    <div v-else class="paper-group">
                        <button @click.prevent="handlePaper" class="paper-button flex-shrink-0 flex items-center justify-center gap-1 mobile:w-9 sm:px-3.5 h-9 rounded-full bg-primary text-white">
                            <i class="lab-fill-profile-circle text-xl sm:text-lg"></i>
                            <span class="mobile:hidden text-sm first-letter:capitalize">
                                {{ textShortener(profile.name, 10) }}
                            </span>
                            <i class="mobile:hidden lab-line-chevron-down text-sm font-semibold"></i>
                        </button>

                        <div class="paper-content w-full sm:w-80 mobile:max-h-[calc(100dvh_-_64px)] thin-scrolling fixed sm:absolute top-16 sm:top-14 ltr:right-0 rtl:left-0 shadow-paper rounded-t-xl sm:rounded-xl bg-white">
                            <figure class="flex items-center gap-3 p-4 mb-2">
                                <figure class="flex-shrink-0 relative z-10 w-[68px] h-[68px] rounded-full border-2 border-dashed border-white bg-gradient-to-t from-[#FF7A00] to-[#FF016C] before:absolute before:inset-0 before:-z-10 before:rounded-full before:scale-[1.03] before:bg-white">
                                    <a class="relative w-full h-full scale-[0.98] overflow-hidden shadow-avatar rounded-full">
                                        <img class="w-full h-full rounded-full object-cover" :src="profile.image" alt="avatar">
                                        <label for="avatar" class="block absolute bottom-0 w-full text-center cursor-pointer bg-white/90">
                                            <i class="lab-fill-gallery-export text-base text-heading"></i>
                                            <input type="file" id="avatar" @change="saveImage" ref="imageProperty" accept="image/png, image/jpeg, image/jpg" class="opacity-0 cursor-pointer absolute inset-0 -z-10">
                                        </label>
                                    </a>
                                </figure>
                                <figcaption class="flex-auto">
                                    <h3 class="text-sm font-medium capitalize mb-0.5">{{ profile.name }}</h3>
                                    <h4 class="text-xs text-paragraph mb-1.5">{{ profile.email }}</h4>
                                    <h5 class="text-sm font-medium">{{ profile.currency_balance }}</h5>
                                </figcaption>
                            </figure>
                            <nav class="px-4">
                                <router-link v-if="profile.role_id !== enums.roleEnum.CUSTOMER && Object.keys(authDefaultPermission).length > 0" :to="{ path: dashboardUrl }" class="w-full flex items-center gap-3.5 py-2.5 border-t border-gray-100 transition-all duration-300 group hover:text-primary">
                                    <i class="lab-line-dashboard text-lg text-paragraph group-hover:text-primary transition-all duration-300"></i>
                                    <span class="text-sm leading-6 capitalize">{{ $t('menu.dashboard') }}</span>
                                </router-link>

                                <router-link :to="{ name: 'frontend.myOrder' }" class="w-full flex items-center gap-3.5 py-2.5 border-t border-gray-100 transition-all duration-300 group hover:text-primary">
                                    <i class="lab-line-reserve text-lg text-paragraph group-hover:text-primary transition-all duration-300"></i>
                                    <span class="text-sm leading-6 capitalize">{{ $t('button.my_orders') }}</span>
                                </router-link>

                                <router-link :to="{ name: 'frontend.editProfile' }" class="w-full flex items-center gap-3.5 py-2.5 border-t border-gray-100 transition-all duration-300 group hover:text-primary">
                                    <i class="lab-line-edit text-lg text-paragraph group-hover:text-primary transition-all duration-300"></i>
                                    <span class="text-sm leading-6 capitalize">{{ $t('button.edit_profile') }}</span>
                                </router-link>

                                <router-link :to="{ name: 'frontend.favorite' }" class="w-full flex items-center gap-3.5 py-2.5 border-t border-gray-100 transition-all duration-300 group hover:text-primary">
                                    <i class="lab-line-lovely text-lg text-paragraph group-hover:text-primary transition-all duration-300"></i>
                                    <span class="text-sm leading-6 capitalize">{{ $t('button.my_favorite') }}</span>
                                </router-link>

                                <router-link :to="{ name: 'frontend.address' }" class="w-full flex items-center gap-3.5 py-2.5 border-t border-gray-100 transition-all duration-300 group hover:text-primary">
                                    <i class="lab-line-map text-lg text-paragraph group-hover:text-primary transition-all duration-300"></i>
                                    <span class="text-sm leading-6 capitalize">{{ $t('button.address') }}</span>
                                </router-link>

                                <router-link :to="{ name: 'frontend.changePassword' }" class="w-full flex items-center gap-3.5 py-2.5 border-t border-gray-100 transition-all duration-300 group hover:text-primary">
                                    <i class="lab-line-key text-lg text-paragraph group-hover:text-primary transition-all duration-300"></i>
                                    <span class="text-sm leading-6 capitalize">{{ $t('button.change_password') }}</span>
                                </router-link>

                                <button @click="logout" class="w-full flex items-center gap-3.5 py-2.5 border-t border-gray-100 transition-all duration-300 group hover:text-primary">
                                    <i class="lab-line-logout text-lg text-paragraph group-hover:text-primary transition-all duration-300"></i>
                                    <span class="text-sm leading-6 capitalize">{{ $t('button.logout') }}</span>
                                </button>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>
    <NavbarDeliveryAddress v-if="location"/>
    <NavbarDeliveryEditAddress v-if="location && editLocation"/>
</template>

<script>
import LoadingComponent from "../../common/LoadingComponent.vue";
import {useSticky} from "../../../composables/sticky.js";
import {useModal} from "../../../composables/modal.js";
import {useFrontendSettingStore} from "../../../stores/frontendSetting.js";
import {useDefaultAccessStore} from "../../../stores/defaultAccess.js";
import {usePaper} from "../../../composables/paper.js";
import {useScroll} from "../../../composables/scroll.js";
import {useCanvas} from "../../../composables/canvas.js";
import orderTypeEnum from "../../../enums/modules/orderTypeEnum.js";
import activityEnum from "../../../enums/modules/activityEnum.js";
import roleEnum from "../../../enums/modules/roleEnum.js";
import statusEnum from "../../../enums/modules/statusEnum.js";
import {useFrontendLanguageStore} from "../../../stores/frontendLanguage.js";
import {useAuthStore} from "../../../stores/auth.js";
import appService from "../../../services/appService.js";
import alertService from "../../../services/alertService.js";
import {useCommonStore} from "../../../stores/common.js";
import NavbarDeliveryAddress from "../../frontend/components/NavbarDeliveryAddress.vue";
import NavbarDeliveryEditAddress from "../../frontend/components/NavBarDeliveryEditAddress.vue";
import {useFrontendCartStore} from "../../../stores/frontendCart.js";
import {useFrontendEditProfileStore} from "../../../stores/frontendEditProfile.js";

export default {
    name: "FrontendNavbarComponent",
    components: {
        NavbarDeliveryEditAddress,
        LoadingComponent,
        NavbarDeliveryAddress
    },
    setup() {
        const {isSticky}               = useSticky();
        const {handlePaper}            = usePaper();
        const {isScrollingUp}          = useScroll();
        const {openCanvas}             = useCanvas();
        const {openModal}              = useModal();
        const authStore                = useAuthStore();
        const commonStore              = useCommonStore();
        const frontendCartStore        = useFrontendCartStore();
        const defaultAccessStore       = useDefaultAccessStore();
        const frontendSettingStore     = useFrontendSettingStore();
        const frontendLanguageStore    = useFrontendLanguageStore();
        const frontendEditProfileStore = useFrontendEditProfileStore();
        return {
            isSticky,
            handlePaper,
            isScrollingUp,
            openCanvas,
            openModal,
            authStore,
            commonStore,
            frontendCartStore,
            defaultAccessStore,
            frontendSettingStore,
            frontendLanguageStore,
            frontendEditProfileStore
        }
    },
    data() {
        return {
            loading: {
                isActive: false,
            },
            enums: {
                roleEnum: roleEnum,
                orderTypeEnum: orderTypeEnum,
                activityEnum: activityEnum
            },
            localRestaurant: null,
            isHome: false,
            mode: null,
            defaultLanguage: null,
            defaultCountryCode: null,
            languageProps: {
                paginate: 0,
                order_column: "id",
                order_type: "asc",
                status: statusEnum.ACTIVE
            }
        }
    },
    computed: {
        setting: function () {
            return this.frontendSettingStore.lists;
        },
        defaultAccess: function () {
            return this.defaultAccessStore.lists;
        },
        orderType: function () {
            return this.commonStore.order_type;
        },
        location: function () {
            return this.commonStore.location;
        },
        editLocation: function () {
            return this.commonStore.edit_address_id;
        },
        authAdminDefaultPermission: function () {
            return this.authStore.adminDefaultPermission;
        },
        authRestaurantDefaultPermission: function () {
            return this.authStore.restaurantDefaultPermission;
        },
        currentRoute: function () {
            return this.$route.name;
        },
        logged: function () {
            return this.authStore.status;
        },
        authDefaultPermission: function () {
            return this.authStore.defaultPermission;
        },
        profile: function () {
            return this.authStore.info;
        },
        languages: function () {
            return this.frontendLanguageStore.lists;
        },
        language: function () {
            return this.frontendLanguageStore.show;
        },
        subtotal: function () {
            return this.frontendCartStore.subtotal;
        },
        dashboardUrl: function () {
            if (this.defaultAccess.restaurant_id === 0 && Object.keys(this.authAdminDefaultPermission).length > 0) {
                return "/admin/" + this.authAdminDefaultPermission.url;
            } else if (this.defaultAccess.restaurant_id > 0 && Object.keys(this.authRestaurantDefaultPermission).length > 0) {
                return "/admin/" + this.authRestaurantDefaultPermission.url;
            }
        }
    },
    created() {
        if(this.$route.name !== "undefined" && this.$route.name === 'frontend.home') {
            this.isHome = this.$route.name === 'frontend.home';
        }

        if(this.$route.meta.mode !== "undefined" && this.$route.meta.mode) {
            this.mode = this.$route.meta.mode;
        }
    },
    async mounted() {
        this.loading.isActive = true;
        await this.frontendSettingStore.fetch().then(res => {
            this.commonStore.init({
                top_sidebar: true
            })

            this.localRestaurant = this.commonStore.search_restaurant;
            this.defaultLanguage = res.data.data.site_default_language;
            if (this.commonStore.language_id > 0) {
                this.defaultLanguage = this.commonStore.language_id;
            }

            this.loading.isActive = false;
            this.frontendLanguageStore.fetch(this.languageProps);
            this.frontendLanguageStore.view(this.defaultLanguage).then(res => {
                this.$i18n.locale = res.data.data.code;
                this.commonStore.init({
                    language_code: res.data.data.code,
                    display_mode: res.data.data.display_mode
                });
            }).catch()
        }).catch((err) => {
            this.loading.isActive = false;
        })
    },
    methods: {
        textShortener: function (text, number = 30) {
            return appService.textShortener(text, number);
        },
        logout: function () {
            this.authStore.logout().then(async res => {
                await this.commonStore.update({
                    location: null,
                    latitude: null,
                    longitude: null
                })
                this.$router.push({name: "frontend.home"});
            }).catch();
        },
        currencyFormat(amount, decimal, currency, position) {
            return appService.currencyFormat(amount, decimal, currency, position);
        },
        changeLanguage: function (id, code, mode) {
            this.defaultLanguage = id;
            this.commonStore.update({
                language_id: id,
                language_code: code,
                display_mode: mode
            }).then(res => {
                this.frontendLanguageStore.view(id).then(res => {
                    this.$i18n.locale = res.data.data.code;
                    window.location.reload();
                }).catch();
            }).catch()
        },
        changeOrderType: function (id) {
            this.commonStore.update({
                order_type: id
            });
        },
        searchRestaurant: function () {
            if (this.localRestaurant === '') {
                this.localRestaurant = null;
            }

            this.commonStore.update({
                search_restaurant: this.localRestaurant
            })
        },
        resetSearchRestaurant: function () {
            this.localRestaurant = null;
            this.commonStore.update({
                search_restaurant: this.localRestaurant
            })
        },
        saveImage: function () {
            if (this.$refs.imageProperty.files[0]) {
                try {
                    this.loading.isActive = true;
                    const formData        = new FormData();
                    formData.append("image", this.$refs.imageProperty.files[0]);
                    this.frontendEditProfileStore.changeImage({form: formData}).then((res) => {
                        this.authStore.updateAuthInfo(res.data.data).then(res => {
                            this.loading.isActive = false;
                            alertService.success(this.$t("message.photo_update"));
                            this.$refs.imageProperty.value = null;
                        }).catch((err) => {
                            this.loading.isActive = false;
                            err.response.data.errors.image.map((error) => {
                                alertService.error(error);
                            });
                        });
                    }).catch((err) => {
                        this.loading.isActive = false;
                        alertService.error(err.response.data.message);
                    });
                } catch (err) {
                    this.loading.isActive = false;
                    alertService.error(err.response.data.message);
                }
            }
        }
    },
    watch: {
        $route(e) {
            this.isHome = e.name === 'frontend.home';
            if (e.meta.mode) {
                this.mode = e.meta.mode;
            }
        }
    }
}
</script>
