<template>
    <LoadingComponent :props="loading" />
    <button @click="isMenuOpen = !isMenuOpen"
            class="w-full md:hidden flex items-center justify-center gap-2 p-2 rounded bg-primary text-white">
        <span class="capitalize">{{ $t('menu.delivery_boy_settings') }}</span>
        <i class="rotating-icon rotate-180 transition-transform duration-300 lab-line-chevron-down text-lg font-semibold"></i>
    </button>
    <div :class="{'h-0 md:h-auto overflow-hidden md:overflow-auto': !isMenuOpen}" class="transition-all duration-300">
        <nav class="db-card p-3">
            <router-link class="db-tab-btn" v-for="menu in menus" :to="'/admin/delivery-boy-settings/' + menu.url">
                <i :class="menu.icon"></i>
                {{ $t('menu.' + menu.language) }}
            </router-link>
        </nav>
    </div>
</template>

<script>
import {useDeliveryBoySettingMenuStore} from "../../../stores/deliveryBoySettingMenu.js";
import LoadingComponent from "../../common/LoadingComponent.vue";

export default {
    name: "MenuComponent",
    components: {LoadingComponent},
    setup() {
        const deliveryBoySettingMenu = useDeliveryBoySettingMenuStore();
        return {deliveryBoySettingMenu}
    },
    data() {
        return {
            isMenuOpen: false,
            loading: {
                isActive: false
            }
        }
    },
    computed: {
        menus: function () {
            return this.deliveryBoySettingMenu.lists;
        }
    },
    mounted() {
        this.loading.isActive = true;
        this.deliveryBoySettingMenu.fetch().then(res => {
            this.loading.isActive = false;
        }).catch(err => {
            this.loading.isActive = false;
        })
    }
}
</script>
