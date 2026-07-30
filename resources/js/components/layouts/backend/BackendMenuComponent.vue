<template>
    <aside :class="!sidebar ? 'max-lg:!translate-x-0 ltr:-translate-x-full rtl:translate-x-full' : ''" class="flex-shrink-0 w-64 h-dvh lg:h-[calc(100dvh_-_68px)] fixed top-0 lg:top-16 ltr:left-0 rtl:right-0 z-30 thin-scrolling bg-white ltr:max-lg:-translate-x-full rtl:max-lg:translate-x-full ltr:max-lg:shadow-db-sidebar-right rtl:max-lg:shadow-db-sidebar-left">
        <div class="flex lg:hidden items-center justify-between p-4">
            <router-link :to="{ name: location ? 'frontend.restaurant' : 'frontend.home' }">
                <img class="w-28" :src="setting.theme_logo" alt="logo">
            </router-link>
            <button @click.prevent="closeSidebar" class="lab-line-circle-cross text-lg text-danger"></button>
        </div>

        <div v-if="defaultAccess.restaurant_id > 0 && permissionChecker('restaurant-settings')">
            <RestaurantStatusComponent/>
        </div>

        <nav @click="handleCloseSidebar" class="db-sidebar-nav">
            <ul class="db-sidebar-nav-list" v-if="defaultAccess.restaurant_id === 0 && adminMenus.length > 0"
                v-for="adminMenu in adminMenus" :key="adminMenu">
                <li class="db-sidebar-nav-item" v-if="adminMenu.url === '#'">
                    <a href="javascript:void(0);" class="db-sidebar-nav-title">
                        {{ $t('menu.' + adminMenu.language) }}
                    </a>
                </li>

                <li class="db-sidebar-nav-item" v-else>
                    <router-link :to="'/admin/' + adminMenu.url" class="db-sidebar-nav-menu">
                        <i class="text-sm" :class="adminMenu.icon"></i>
                        <span class="text-base flex-auto">{{ $t('menu.' + adminMenu.language) }}</span>
                    </router-link>
                </li>

                <li class="db-sidebar-nav-item" v-if="adminMenu.children"
                    v-for="adminMenuChildren in adminMenu.children">
                    <router-link :to="'/admin/' + adminMenuChildren.url" class="db-sidebar-nav-menu">
                        <i class="text-sm" :class="adminMenuChildren.icon"></i>
                        <span class="text-base flex-auto">{{ $t('menu.' + adminMenuChildren.language) }}</span>
                    </router-link>
                </li>
            </ul>

            <ul class="db-sidebar-nav-list" v-if="defaultAccess.restaurant_id > 0 && restaurantMenus.length > 0"
                v-for="restaurantMenu in restaurantMenus" :key="restaurantMenu">
                <li class="db-sidebar-nav-item" v-if="restaurantMenu.url === '#'">
                    <a href="javascript:void(0);" class="db-sidebar-nav-title">
                        {{ $t('menu.' + restaurantMenu.language) }}
                    </a>
                </li>

                <li class="db-sidebar-nav-item" v-else>
                    <router-link :to="'/admin/' + restaurantMenu.url" class="db-sidebar-nav-menu">
                        <i class="text-sm" :class="restaurantMenu.icon"></i>
                        <span class="text-base flex-auto">{{ $t('menu.' + restaurantMenu.language) }}</span>
                    </router-link>
                </li>

                <li class="db-sidebar-nav-item" v-if="restaurantMenu.children"
                    v-for="restaurantMenuChildren in restaurantMenu.children">
                    <router-link :to="'/admin/' + restaurantMenuChildren.url" class="db-sidebar-nav-menu">
                        <i class="text-sm" :class="restaurantMenuChildren.icon"></i>
                        <span class="text-base flex-auto">{{ $t('menu.' + restaurantMenuChildren.language) }}</span>
                    </router-link>
                </li>
            </ul>
        </nav>
    </aside>
</template>
<script>

import {useDefaultAccessStore} from "../../../stores/defaultAccess.js";
import {useFrontendSettingStore} from "../../../stores/frontendSetting.js";
import {useAuthStore} from "../../../stores/auth.js";
import appService from "../../../services/appService.js";
import RestaurantStatusComponent from "../../admin/components/RestaurantStatusComponent.vue";
import {useCommonStore} from "../../../stores/common.js";

export default {
    name: "BackendMenuComponent",
    components: {RestaurantStatusComponent},
    setup() {
        const authStore            = useAuthStore();
        const commonStore          = useCommonStore();
        const defaultAccessStore   = useDefaultAccessStore();
        const frontendSettingStore = useFrontendSettingStore();

        return {
            authStore,
            commonStore,
            defaultAccessStore,
            frontendSettingStore,
        }
    },
    computed: {
        setting: function () {
            return this.frontendSettingStore.lists;
        },
        sidebar() {
            return this.commonStore.top_sidebar;
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
        location: function () {
            return this.commonStore.location;
        },
    },
    methods: {
        closeSidebar: function () {
            this.commonStore.update({
                top_sidebar: !this.commonStore.top_sidebar
            })
        },
        permissionChecker: function (permissionName) {
            return appService.permissionChecker(permissionName);
        },
        handleCloseSidebar: function() {
            if (window.innerWidth <= 1024) {
                this.commonStore.update({
                    top_sidebar: !this.commonStore.top_sidebar
                });
            }
        }
    }
}
</script>
