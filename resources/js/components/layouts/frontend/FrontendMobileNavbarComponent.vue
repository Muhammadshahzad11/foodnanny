<template>
    <nav v-if="mode === 'option' || mode === 'restaurant'" class="lg:hidden w-full flex items-center justify-between px-5 py-3 fixed bottom-0 left-0 z-30 shadow-widget bg-white">
        <router-link class="flex flex-col items-center gap-1 text-text transition-all duration-300 hover:text-primary" :to="{ name: location ? 'frontend.restaurant' : 'frontend.home' }">
            <i class="lab-fill-home text-lg leading-none"></i>
            <span class="text-xs capitalize">{{ $t('label.home') }}</span>
        </router-link>

        <router-link @click.prevent="selectSearch" class="flex flex-col items-center gap-1 text-text transition-all duration-300 hover:text-primary" :to="{ name: location ? 'frontend.restaurant' : 'frontend.home' }">
            <i class="lab-fill-search text-lg leading-none"></i>
            <span class="text-xs capitalize">{{ $t('label.explore') }}</span>
        </router-link>

        <button @click.prevent="openCanvas('cart-canvas')" type="button" class="relative isolate -mt-11">
            <i class="lab-fill-bag text-xl w-12 h-12 !leading-12 text-center rounded-full shadow-cart bg-primary text-white"></i>
            <span class="absolute top-5 ltr:right-1.5 rtl:left-1.5 text-[10px] font-medium h-4 px-1 leading-[14px] text-center rounded-full border border-primary bg-[#FFBC1F]">
                {{ carts.length }}
            </span>
        </button>

        <router-link class="flex flex-col items-center gap-1 text-text transition-all duration-300 hover:text-primary" :to="{ name: 'frontend.favorite' }">
            <i class="lab-fill-heart-2 text-lg leading-none"></i>
            <span class="text-xs capitalize">{{ $t('label.favorite') }}</span>
        </router-link>

        <button type="button" class="flex flex-col items-center gap-1 text-text transition-all duration-300 hover:text-primary" @click.prevent="openPwaInstall">
            <svg class="w-[18px] h-[18px]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                <path d="M12 3v12"/><path d="m8 11 4 4 4-4"/><path d="M5 21h14"/>
            </svg>
            <span class="text-xs capitalize">{{ $t('button.install') }}</span>
        </button>

        <RouterLink v-if="!logged" :to="{ name: 'auth.login' }" class="flex flex-col items-center gap-1 text-text transition-all duration-300 hover:text-primary">
            <i class="lab-fill-profile-circle text-lg leading-none"></i>
            <span class="text-xs capitalize">{{ $t('label.login') }}</span>
        </RouterLink>

        <button v-else @click.prevent="openCanvas('mobile-profile-canvas')" class="flex flex-col items-center gap-1 text-text transition-all duration-300 hover:text-primary">
            <i class="lab-fill-profile-circle text-lg leading-none"></i>
            <span class="text-xs capitalize">{{ $t('button.profile') }}</span>
        </button>
    </nav>

    <aside id="mobile-profile-canvas" class="fixed inset-0 z-50 bg-black/50 duration-500 transition-all invisible opacity-0">
        <div :class="displayMode === enums.displayModeEnum.LTR ? 'ms-auto ltr:translate-x-full rtl:-translate-x-full' : 'ltr:-translate-x-full rtl:translate-x-full'" class="sm:max-w-xs w-full h-dvh overflow-x-hidden thin-scrolling bg-white">
            <div class="relative">
                <button @click.prevent="closeCanvas('mobile-profile-canvas')" class=" absolute top-4 ltr:right-4 rtl:left-4 leading-none">
                    <i class="lab-line-circle-cross text-lg text-danger"></i>
                </button>
            </div>

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
                <router-link @click.prevent="closeCanvas('mobile-profile-canvas')" v-if="profile.role_id !== enums.roleEnum.CUSTOMER && Object.keys(authDefaultPermission).length > 0" :to="{ path: dashboardUrl }" class="w-full flex items-center gap-3.5 py-2.5 border-t border-gray-100 transition-all duration-300 group hover:text-primary">
                    <i class="lab-line-dashboard text-lg text-paragraph group-hover:text-primary transition-all duration-300"></i>
                    <span class="text-sm leading-6 capitalize">{{ $t('menu.dashboard') }}</span>
                </router-link>

                <router-link @click.prevent="closeCanvas('mobile-profile-canvas')" :to="{ name: 'frontend.myOrder' }" class="w-full flex items-center gap-3.5 py-2.5 border-t border-gray-100 transition-all duration-300 group hover:text-primary">
                    <i class="lab-line-reserve text-lg text-paragraph group-hover:text-primary transition-all duration-300"></i>
                    <span class="text-sm leading-6 capitalize">{{ $t('button.my_orders') }}</span>
                </router-link>

                <router-link @click.prevent="closeCanvas('mobile-profile-canvas')" :to="{ name: 'frontend.editProfile' }" class="w-full flex items-center gap-3.5 py-2.5 border-t border-gray-100 transition-all duration-300 group hover:text-primary">
                    <i class="lab-line-edit text-lg text-paragraph group-hover:text-primary transition-all duration-300"></i>
                    <span class="text-sm leading-6 capitalize">{{ $t('button.edit_profile') }}</span>
                </router-link>

                <router-link @click.prevent="closeCanvas('mobile-profile-canvas')" :to="{ name: 'frontend.favorite' }" class="w-full flex items-center gap-3.5 py-2.5 border-t border-gray-100 transition-all duration-300 group hover:text-primary">
                    <i class="lab-line-lovely text-lg text-paragraph group-hover:text-primary transition-all duration-300"></i>
                    <span class="text-sm leading-6 capitalize">{{ $t('button.my_favorite') }}</span>
                </router-link>

                <router-link @click.prevent="closeCanvas('mobile-profile-canvas')" :to="{ name: 'frontend.address' }" class="w-full flex items-center gap-3.5 py-2.5 border-t border-gray-100 transition-all duration-300 group hover:text-primary">
                    <i class="lab-line-map text-lg text-paragraph group-hover:text-primary transition-all duration-300"></i>
                    <span class="text-sm leading-6 capitalize">{{ $t('button.address') }}</span>
                </router-link>

                <router-link @click.prevent="closeCanvas('mobile-profile-canvas')" :to="{ name: 'frontend.changePassword' }" class="w-full flex items-center gap-3.5 py-2.5 border-t border-gray-100 transition-all duration-300 group hover:text-primary">
                    <i class="lab-line-key text-lg text-paragraph group-hover:text-primary transition-all duration-300"></i>
                    <span class="text-sm leading-6 capitalize">{{ $t('button.change_password') }}</span>
                </router-link>

                <button @click="logout"
                        class="w-full flex items-center gap-3.5 py-2.5 border-t border-gray-100 transition-all duration-300 group hover:text-primary">
                    <i class="lab-line-logout text-lg text-paragraph group-hover:text-primary transition-all duration-300"></i>
                    <span class="text-sm leading-6 capitalize">
                        {{ $t('button.logout') }}
                    </span>
                </button>
            </nav>
        </div>
    </aside>
</template>

<script>


import {useCanvas} from "../../../composables/canvas.js";
import {useCommonStore} from "../../../stores/common.js";
import {useFrontendCartStore} from "../../../stores/frontendCart.js";
import {useAuthStore} from "../../../stores/auth.js";
import displayModeEnum from "../../../enums/modules/displayModeEnum.js";
import {useFrontendEditProfileStore} from "../../../stores/frontendEditProfile.js";
import alertService from "../../../services/alertService.js";
import {useDefaultAccessStore} from "../../../stores/defaultAccess.js";
import roleEnum from "../../../enums/modules/roleEnum.js";
import {usePwaInstall} from "../../../composables/usePwaInstall.js";

export default {
    name: "FrontendMobileNavbarComponent",
    setup() {
        const {openCanvas, closeCanvas} = useCanvas();
        const authStore                 = useAuthStore();
        const commonStore               = useCommonStore();
        const frontendCartStore         = useFrontendCartStore();
        const defaultAccessStore        = useDefaultAccessStore();
        const frontendEditProfileStore  = useFrontendEditProfileStore();
        const pwaInstall                = usePwaInstall();

        return {
            openCanvas,
            closeCanvas,
            authStore,
            commonStore,
            frontendCartStore,
            defaultAccessStore,
            frontendEditProfileStore,
            ...pwaInstall,
        }
    },
    data() {
        return {
            mode: null,
            enums: {
                roleEnum: roleEnum,
                displayModeEnum: displayModeEnum
            }
        }
    },
    computed: {
        displayMode: function () {
            return this.commonStore.display_mode;
        },
        location: function () {
            return this.commonStore.location;
        },
        logged: function () {
            return this.authStore.status;
        },
        profile: function () {
            return this.authStore.info;
        },
        authDefaultPermission: function () {
            return this.authStore.defaultPermission;
        },
        defaultAccess: function () {
            return this.defaultAccessStore.lists;
        },
        authAdminDefaultPermission: function () {
            return this.authStore.adminDefaultPermission;
        },
        authRestaurantDefaultPermission: function () {
            return this.authStore.restaurantDefaultPermission;
        },
        dashboardUrl: function () {
            if (this.defaultAccess.restaurant_id === 0 && Object.keys(this.authAdminDefaultPermission).length > 0) {
                return "/admin/" + this.authAdminDefaultPermission.url;
            } else if (this.defaultAccess.restaurant_id > 0 && Object.keys(this.authRestaurantDefaultPermission).length > 0) {
                return "/admin/" + this.authRestaurantDefaultPermission.url;
            }
        },
        carts: function () {
            return this.frontendCartStore.lists;
        }
    },
    created() {
        if(this.$route.meta.mode !== "undefined" && this.$route.meta.mode) {
            this.mode = this.$route.meta.mode;
        }
    },
    methods: {
        openPwaInstall: async function () {
            await this.init();
            await this.promptInstall();
            this.openInstallUi();
        },
        logout: function () {
            this.closeCanvas('mobile-profile-canvas');
            this.authStore.logout().then(async res => {
                await this.commonStore.update({
                    location: null,
                    latitude: null,
                    longitude: null
                })
                this.$router.push({name: "frontend.home"});
            }).catch();
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
        },
        selectSearch: function () {
            setTimeout(() => {
                document.getElementById("restaurant-search").select();
            }, 300);
        },
    },
    watch: {
        $route(e) {
            if (e.meta.mode) {
                this.mode = e.meta.mode;
            }
        }
    }
}
</script>
