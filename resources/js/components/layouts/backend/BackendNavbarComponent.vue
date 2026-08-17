<template>
    <LoadingComponent :props="loading"/>
    <header id="backend-header"
            class="w-full flex items-center justify-between gap-3 sm:gap-4 px-3 sm:px-4 h-16 sm:h-20 fixed top-0 left-0 z-30 bg-white shadow-xs">
        <div class="flex items-center gap-3 lg:gap-4 overflow-hidden">
            <router-link class="flex-shrink-0" :to="{ name: location ? 'frontend.restaurant' : 'frontend.home' }">
                <img
                    class="hidden sm:block h-12 sm:h-14 w-auto max-w-[220px] lg:max-w-[280px] object-contain object-left"
                    :src="setting.theme_logo"
                    alt="Cost to Cost Foods"
                >
                <img
                    class="block sm:hidden h-10 w-auto max-w-[160px] object-contain object-left"
                    :src="setting.theme_logo"
                    alt="Cost to Cost Foods"
                >
            </router-link>
            <hr v-if="Object.keys(restaurant).length >0" class="border-none w-px h-12 bg-gray-100">
            <a v-if="Object.keys(restaurant).length >0" href="#" class="group flex items-center gap-2 overflow-hidden">
                <img class="hidden lg:block w-9 flex-shrink-0" :src="restaurant.logo" alt="restaurant-logo">
                <span
                    class="font-semibold text-heading whitespace-nowrap overflow-hidden text-ellipsis transition-all duration-300 group-hover:text-primary">
                    {{ restaurant.name }}
                </span>
            </a>
        </div>

        <div class="flex items-center gap-3 sm:gap-4">
            <button @click.prevent="showModal"
                    v-if="authInfo.role_id === enums.roleEnum.ADMIN && restaurants.length > 0 && defaultAccess.restaurant_id === 0 && restaurantMenus.length > 0"
                    type="button"
                    class="inline-flex items-center justify-center gap-1.5 h-9 w-9 sm:w-auto sm:px-3 rounded-lg bg-primary/10 text-primary">
                <i class="lab-fill-restaurants mobile:text-lg"></i>
                <span class="hidden sm:block text-xs font-medium tracking-wide capitalize">
                    {{ $t('label.switch_to_restaurant') }}
                </span>
            </button>

            <button @click.prevent="switchAdministrator"
                    v-if="authInfo.role_id === enums.roleEnum.ADMIN && defaultAccess.restaurant_id > 0 && adminMenus.length > 0"
                    type="button"
                    class="inline-flex items-center justify-center gap-1.5 h-9 w-9 sm:w-auto sm:px-3 rounded-lg bg-primary/10 text-primary">
                <i class="lab-fill-profile-circle mobile:text-lg"></i>
                <span class="hidden sm:block text-xs font-medium tracking-wide capitalize">
                    {{ $t('label.switch_to_administrator') }}
                </span>
            </button>

            <div v-if="setting.site_language_switch === enums.activityEnum.ENABLE" class="paper-group">
                <button id="switchLanguageButton" @click.prevent="handlePaper"
                        class="paper-button flex items-center gap-2 h-9 px-2.5 sm:px-3 rounded-lg bg-primary/10">
                    <img :src="language.image" alt="flag" class="w-4 h-4 rounded-full flex-shrink-0">
                    <span class="hidden md:block whitespace-nowrap text-xs font-medium capitalize text-heading">
                        {{ language.name }}
                    </span>
                    <i class="hidden md:block lab-fill-arrow-down text-xs text-heading"></i>
                </button>
                <ul class="paper-content p-2 min-w-[180px] rounded-lg shadow-xl absolute top-14 ltr:right-0 rtl:left-0 border border-gray-200 bg-white">
                    <li v-for="(objLanguage, index) in languages" :key="index"
                        @click.prevent="changeLanguage(objLanguage.id, objLanguage.code, objLanguage.display_mode)"
                        :class="objLanguage.id === language.id ? 'bg-[#FFF8F2]' : ''"
                        class="flex items-center gap-2 py-1.5 px-2.5 rounded-md cursor-pointer hover:bg-gray-100">
                        <img :src="objLanguage.image" alt="flag" class="w-4 h-4 flex-shrink-0 rounded-full">
                        <span :class="objLanguage.id === language.id ? '!text-primary' : ''"
                              class="text-heading capitalize text-sm">{{ objLanguage.name }}</span>
                    </li>
                </ul>
            </div>

            <button v-if="$route.path.includes('pos')" @click.prevent="fullScreen"
                    class="w-9 h-9 leading-9 text-center rounded-lg bg-primary/10">
                <i class="lab-line-maximize text-primary"></i>
            </button>

            <router-link v-if="pos.permission" :to="{ path: '/admin/' + pos.url }"
                         class="w-9 h-9 leading-9 text-center rounded-lg bg-primary/10">
                <i class="lab-fill-pos text-primary"></i>
            </router-link>

            <NotificationBellComponent/>

            <PwaInstallButtonComponent/>

            <button @click.prevent="handleSidebar" class="w-9 h-9 leading-9 text-center rounded-lg bg-primary/10">
                <svg class="w-[18px] h-[18px] text-primary mx-auto" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true">
                    <path d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
            </button>

            <div class="paper-group">
                <button @click.prevent="handlePaper" class="paper-button flex items-center gap-2">
                    <img class="flex-shrink-0 w-9 h-9 object-cover rounded-lg" :src="authInfo.image" alt="avatar">
                    <dl class="hidden sm:block flex-shrink-0 ltr:text-left rtl:text-right">
                        <dt class="text-xs font-normal capitalize mb-0.5 text-heading">
                            {{ $t('label.welcome') }}
                        </dt>
                        <dt class="text-xs font-semibold capitalize whitespace-nowrap text-heading">
                            {{ textShortener(authInfo.name, 20) }}
                        </dt>
                    </dl>
                    <i class="hidden sm:block lab-fill-arrow-down text-sm text-heading"></i>
                </button>
                <div
                    class="paper-content w-full sm:w-80 mobile:max-h-[calc(100dvh_-_64px)] thin-scrolling fixed sm:absolute top-16 sm:top-14 ltr:right-0 rtl:left-0 shadow-paper rounded-t-xl sm:rounded-xl bg-white">
                    <figure class="flex items-center gap-3 p-4 mb-2">
                        <figure
                            class="flex-shrink-0 relative z-10 w-[68px] h-[68px] rounded-full border-2 border-dashed border-white bg-gradient-to-t from-[#FF7A00] to-[#FF016C] before:absolute before:inset-0 before:-z-10 before:rounded-full before:scale-[1.03] before:bg-white">
                            <a class="relative w-full h-full scale-[0.98] overflow-hidden shadow-avatar rounded-full">
                                <img class="w-full h-full rounded-full object-cover" :src="authInfo.image" alt="avatar">
                                <label for="avatar"
                                       class="block absolute bottom-0 w-full text-center cursor-pointer bg-white/90">
                                    <i class="lab-fill-gallery-export text-base text-heading"></i>
                                    <input @change="saveImage" accept="image/png, image/jpeg, image/jpg"
                                           ref="imageProperty" type="file" id="avatar"
                                           class="opacity-0 cursor-pointer absolute inset-0 -z-10">
                                </label>
                            </a>
                        </figure>
                        <figcaption class="flex-auto">
                            <h3 class="text-sm font-medium capitalize mb-0.5">{{ authInfo.name }}</h3>
                            <h4 class="text-xs text-paragraph mb-1.5">{{ authInfo.email }}</h4>
                            <h5 class="text-sm font-medium">{{ authInfo.currency_balance }}</h5>
                        </figcaption>
                    </figure>

                    <nav class="px-4">
                        <router-link :to="{ name: 'admin.profile.editProfile' }" aria-current="page"
                                     class="w-full flex items-center gap-3.5 py-2.5 border-t border-gray-100 transition-all duration-300 group hover:text-primary">
                            <i class="lab-line-edit text-lg text-paragraph group-hover:text-primary transition-all duration-300"></i>
                            <span class="text-sm leading-6 capitalize">{{ $t('button.edit_profile') }}</span>
                        </router-link>

                        <router-link :to="{ name: 'admin.profile.changePassword' }" aria-current="page"
                                     class="w-full flex items-center gap-3.5 py-2.5 border-t border-gray-100 transition-all duration-300 group hover:text-primary">
                            <i class="lab-line-key text-lg text-paragraph group-hover:text-primary transition-all duration-300"></i>
                            <span class="text-sm leading-6 capitalize">{{ $t('button.change_password') }}</span>
                        </router-link>

                        <button
                            v-if="authInfo.role_id === enums.roleEnum.ADMIN"
                            type="button"
                            :disabled="flushingCache"
                            @click="flushCache"
                            class="w-full flex items-center gap-3.5 py-2.5 border-t border-gray-100 transition-all duration-300 group hover:text-primary disabled:opacity-60"
                        >
                            <i class="lab-line-reset text-lg text-paragraph group-hover:text-primary transition-all duration-300" :class="{ 'animate-spin': flushingCache }"></i>
                            <span class="text-sm leading-6 capitalize">
                                {{ flushingCache ? $t('button.clearing_cache') : $t('button.clear_cache') }}
                            </span>
                        </button>

                        <div class="w-full border-t border-gray-100 py-2.5">
                            <PwaInstallButtonComponent
                                class="!w-full !justify-start !bg-transparent !text-heading !px-0 !h-auto !gap-3.5"
                                button-class="w-full flex items-center gap-3.5 text-sm leading-6 capitalize text-heading hover:text-primary"
                            />
                        </div>

                        <button @click="logout"
                                class="w-full flex items-center gap-3.5 py-2.5 border-t border-gray-100">
                            <i class="lab-line-logout text-lg text-danger"></i>
                            <span class="text-sm leading-6 capitalize text-danger">{{ $t('button.logout') }}</span>
                        </button>
                    </nav>
                </div>
            </div>
        </div>
    </header>

    <div v-if="restaurants.length > 0" id="restaurant-switch-modal"
         class="fixed inset-0 z-50 p-3 w-screen h-dvh overflow-y-auto bg-black/50 transition-all duration-300 opacity-0 invisible">
        <div class="max-w-lg w-full rounded-xl mx-auto bg-white transition-all duration-300">
            <div class="flex items-center justify-between gap-4 py-3.5 px-4 border-b border-slate-100">
                <h3 class="text-lg font-semibold capitalize">{{ $t('label.restaurant') }}</h3>
                <button @click="reset" class="lab-line-circle-cross text-lg text-danger"></button>
            </div>
            <div class="modal-body">
                <form @submit.prevent="switchRestaurant">
                    <div class="form-row">
                        <div class="form-col-12">
                            <label for="name" class="db-field-title required"> {{ $t("label.name") }} </label>
                            <vue-select class="db-field-control f-b-custom-select" id="name"
                                        v-bind:class="errors.restaurant_id ? 'invalid' : ''"
                                        v-model="restaurantForm.restaurant_id" :options="restaurants" label-by="name"
                                        value-by="id" :closeOnSelect="true" :searchable="true" :clearOnClose="true"
                                        placeholder="--" search-placeholder="--"/>
                            <small class="db-field-alert" v-if="errors.restaurant_id"> {{
                                    errors.restaurant_id[0]
                                }} </small>
                        </div>

                        <div class="form-col-12">
                            <div class="modal-btns">
                                <button type="button" class="modal-btn-outline modal-close"
                                        @click.prevent="closeModal('restaurant-switch-modal')">
                                    <i class="lab lab-fill-close-circle text-base"></i>
                                    <span>{{ $t("button.close") }}</span>
                                </button>

                                <button type="submit" class="db-btn py-2 text-white bg-primary">
                                    <i class="lab lab-fill-save text-base"></i>
                                    <span>{{ $t("label.active") }}</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>

<script>
import LoadingComponent from "../../common/LoadingComponent.vue";
import NotificationBellComponent from "./NotificationBellComponent.vue";
import PwaInstallButtonComponent from "../../common/PwaInstallButtonComponent.vue";
import {useFrontendSettingStore} from "../../../stores/frontendSetting.js";
import activityEnum from "../../../enums/modules/activityEnum.js";
import {usePaper} from "../../../composables/paper.js";
import {useModal} from "../../../composables/modal.js";
import {useAuthStore} from "../../../stores/auth.js";
import appService from "../../../services/appService.js";
import {useFrontendLanguageStore} from "../../../stores/frontendLanguage.js";
import {useDefaultAccessStore} from "../../../stores/defaultAccess.js";
import alertService from "../../../services/alertService.js";
import { clearClientPwaCaches } from "../../../services/pwaCacheClear.js";
import VueSimpleAlert from "vue3-simple-alert";
import {useCommonStore} from "../../../stores/common.js";
import {useRestaurantSwitchStore} from "../../../stores/restaurantSwitch.js";
import router from "../../../router/index.js";
import roleEnum from "../../../enums/modules/roleEnum.js";
import {useFrontendEditProfileStore} from "../../../stores/frontendEditProfile.js";
import {usePosCartStore} from "../../../stores/posCart.js";
import {useMyRestaurantStore} from "../../../stores/myRestaurant.js";
import {useAiStore} from "../../../stores/ai.js";
import {useInboxNotificationStore} from "../../../stores/inboxNotification.js";
import {useCacheStore} from "../../../stores/cache.js";

export default {
    name: "BackendNavbarComponent",
    components: {LoadingComponent, NotificationBellComponent, PwaInstallButtonComponent},
    setup() {
        const {openModal, closeModal}  = useModal()
        const {handlePaper}            = usePaper()
        const aiStore                  = useAiStore();
        const authStore                = useAuthStore();
        const commonStore              = useCommonStore();
        const posCartStore             = usePosCartStore();
        const myRestaurantStore        = useMyRestaurantStore();
        const defaultAccessStore       = useDefaultAccessStore();
        const frontendSettingStore     = useFrontendSettingStore();
        const frontendLanguageStore    = useFrontendLanguageStore();
        const restaurantSwitchStore    = useRestaurantSwitchStore();
        const frontendEditProfileStore = useFrontendEditProfileStore();
        const inboxNotificationStore   = useInboxNotificationStore();
        const cacheStore               = useCacheStore();


        return {
            handlePaper,
            openModal,
            closeModal,
            aiStore,
            authStore,
            commonStore,
            posCartStore,
            myRestaurantStore,
            defaultAccessStore,
            frontendSettingStore,
            frontendLanguageStore,
            restaurantSwitchStore,
            frontendEditProfileStore,
            inboxNotificationStore,
            cacheStore,
        }
    },
    data() {
        return {
            loading: {
                isActive: false
            },
            restaurantForm: {
                restaurant_id: null
            },
            enums: {
                roleEnum: roleEnum,
                activityEnum: activityEnum
            },
            pos: {
                permission: false,
                url: ''
            },
            fullscreenStatus: false,
            flushingCache: false,
            errors: {}
        }
    },
    computed: {
        setting: function () {
            return this.frontendSettingStore.lists;
        },
        authInfo: function () {
            return this.authStore.info;
        },
        languages: function () {
            return this.frontendLanguageStore.lists;
        },
        language: function () {
            return this.frontendLanguageStore.show;
        },
        defaultAccess: function () {
            return this.defaultAccessStore.lists;
        },
        adminMenus: function () {
            return this.authStore.adminMenu;
        },
        restaurantMenus: function () {
            return this.authStore.restaurantMenu;
        },
        restaurants: function () {
            return this.restaurantSwitchStore.lists;
        },
        location: function () {
            return this.commonStore.location;
        },
        restaurant: function () {
            return this.myRestaurantStore.defaultRestaurant;
        }
    },
    mounted() {
        this.defaultAccessStore.fetch();
        this.restaurantSwitchStore.fetch();
        this.myRestaurantStore.fetchDefaultRestaurant();
        this.posPermissionCheck();
        if (this.authInfo?.id) {
            this.inboxNotificationStore.subscribeUserChannel(this.authInfo.id);
            this.inboxNotificationStore.fetchUnreadCount().catch(() => {});
            this.inboxNotificationStore.fetchRecent().catch(() => {});
        }
    },
    beforeUnmount() {
        this.inboxNotificationStore.unsubscribe();
    },
    methods: {
        reset: function () {
            useModal().closeModal('restaurant-switch-modal');
            this.errors                       = {};
            this.restaurantForm.restaurant_id = null;
        },
        textShortener: function (text, number = 30) {
            return appService.textShortener(text, number);
        },
        logout: function () {
            this.inboxNotificationStore.unsubscribe();
            this.authStore.logout().then(async res => {
                await this.commonStore.update({
                    location: null,
                    latitude: null,
                    longitude: null
                })
                this.$router.push({name: "frontend.home"});
            }).catch();
        },
        flushCache: function () {
            if (this.flushingCache) {
                return;
            }
            this.flushingCache = true;
            this.loading.isActive = true;
            this.cacheStore.flush().then(async (res) => {
                this.flushingCache = false;
                this.loading.isActive = false;
                await clearClientPwaCaches(res.data?.data?.pwa_cache_version);
                alertService.success(res.data.message || this.$t('message.cache_cleared_successfully'));
            }).catch((err) => {
                this.flushingCache = false;
                this.loading.isActive = false;
                alertService.error(err.response?.data?.message || this.$t('message.cache_clear_failed'));
            });
        },
        handleSidebar: function () {
            this.commonStore.update({
                top_sidebar: !this.commonStore.top_sidebar
            })
        },
        fullScreen: function () {
            if (this.$route.path.includes('pos')) {
                this.toggleFullscreen();

                const mainElement   = document?.querySelector("main");
                const headerElement = document?.getElementById("backend-header");

                if (headerElement) {
                    mainElement.classList.remove("pt-20");
                    headerElement.classList.add("hidden");
                } else {
                    mainElement.classList.add("pt-20");
                    headerElement.classList.remove("hidden");
                }

                if (!this.fullscreenStatus) {
                    mainElement.classList.add("pt-20");
                    headerElement.classList.remove("hidden");
                }
            }
        },
        toggleFullscreen: function () {
            let elem = document.documentElement;
            if (!document.fullscreenElement) {
                if (elem.requestFullscreen) {
                    elem.requestFullscreen();
                } else if (elem.webkitRequestFullscreen) {
                    elem.webkitRequestFullscreen();
                } else if (elem.mozRequestFullScreen) {
                    elem.mozRequestFullScreen();
                } else if (elem.msRequestFullscreen) {
                    elem.msRequestFullscreen();
                }

                this.fullscreenStatus = true;
                document.addEventListener('mousemove', handleMouseMove);
            } else {
                if (document.exitFullscreen) {
                    document.exitFullscreen();
                } else if (document.webkitExitFullscreen) {
                    document.webkitExitFullscreen();
                } else if (document.mozCancelFullScreen) {
                    document.mozCancelFullScreen();
                } else if (document.msExitFullscreen) {
                    document.msExitFullscreen();
                }

                this.fullscreenStatus = false;
                document.removeEventListener('mousemove', handleMouseMove);
            }
        },
        posPermissionCheck: function () {
            this.pos.permission = false;
            if (this.defaultAccessStore.lists?.restaurant_id) {
                const permissions = this.authStore.restaurantPermission;
                if (permissions.length > 0) {
                    permissions.forEach((permission) => {
                        if (permission.name === 'pos') {
                            if (permission.access === true) {
                                this.pos.permission = true;
                                this.pos.url        = permission.url;
                            }
                        }
                    });
                }
            }
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
                }).catch();

                let element = document.getElementById("switchLanguageButton");
                if (element.parentElement.className.includes('active')) {
                    element.parentElement.classList.remove('active');
                }
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
                            alertService.error(err);
                        });
                    }).catch((err) => {
                        this.loading.isActive = false;
                        err.response.data.errors.image.map((error) => {
                            alertService.error(error);
                        });
                    });
                } catch (err) {
                    this.loading.isActive = false;
                    alertService.error(err.response.data.message);
                }
            }
        },
        showModal: function () {
            if (this.authInfo.restaurant_id > 0) {
                return new VueSimpleAlert.confirm(
                    this.$t('message.do_you_switch_to_restaurant'),
                    this.$t('message.are_you_sure'),
                    "warning", {
                        confirmButtonText: this.$t('button.yes_do'),
                        cancelButtonText: this.$t('button.no_cancel'),
                        confirmButtonColor: "#1AB759",
                        cancelButtonColor: "#E93C3C"
                    }).then(async (res) => {
                    try {
                        this.loading.isActive             = true;
                        this.restaurantForm.restaurant_id = null;
                        this.restaurantForm.restaurant_id = this.authInfo.restaurant_id;
                        await this.switchRestaurant();
                    } catch (err) {
                        this.loading.isActive = false;
                        alertService.error(err);
                    }
                }).catch((err) => {
                    this.loading.isActive = false;
                });
            } else {
                this.openModal('restaurant-switch-modal');
            }
        },
        switchRestaurant: async function () {
            this.loading.isActive = true;
            await this.restaurantSwitchStore.switch(this.restaurantForm).then(async (res) => {
                await this.authStore.permissionSwitch().then((res) => {
                    setTimeout(() => {
                        appService.recursiveRouter(router.options.routes, this.authStore.permission)
                    }, 1000);
                    this.posPermissionCheck();
                    this.aiStore.fetchStatus();
                    this.myRestaurantStore.fetchDefaultRestaurant();
                    this.posCartStore.resetCart();
                    this.loading.isActive = false;
                    this.closeModal('restaurant-switch-modal');
                    alertService.success(this.$t('message.switch_to_restaurant'));
                    router.push({path: "/admin/" + this.authStore.restaurantDefaultPermission?.url});
                }).catch((err) => {
                    this.loading.isActive = false;
                });
            }).catch((err) => {
                this.loading.isActive = false;
                this.errors           = err.response.data.errors;
            });
        },
        switchAdministrator: async function () {
            return new VueSimpleAlert.confirm(
                this.$t('message.do_you_switch_to_administrator'),
                this.$t('message.are_you_sure'),
                "warning", {
                    confirmButtonText: this.$t('button.yes_do'),
                    cancelButtonText: this.$t('button.no_cancel'),
                    confirmButtonColor: "#1AB759",
                    cancelButtonColor: "#E93C3C"
                }).then(async (res) => {
                try {
                    this.loading.isActive             = true;
                    this.restaurantForm.restaurant_id = null;
                    await this.restaurantSwitchStore.switch({restaurant_id: 0}).then(async (res) => {
                        await this.authStore.permissionSwitch().then((res) => {
                            this.loading.isActive = false;
                            setTimeout(() => {
                                appService.recursiveRouter(router.options.routes, this.authStore.permission)
                            }, 1000);
                            this.myRestaurantStore.resetDefaultRestaurant();
                            this.posPermissionCheck();
                            this.aiStore.fetchStatus();
                            alertService.success(this.$t('message.switch_to_administrator'));
                            router.push({path: "/admin/" + this.authStore.adminDefaultPermission?.url});
                        }).catch((err) => {
                            this.loading.isActive = false;
                        });
                    }).catch((err) => {
                        this.loading.isActive = false;
                        alertService.error(err.response.data.errors.restaurant_id);
                    });
                } catch (err) {
                    this.loading.isActive = false;
                    alertService.error(err.response.data.message);
                }
            }).catch((err) => {
                this.loading.isActive = false;
            });
        }
    }
}
</script>
