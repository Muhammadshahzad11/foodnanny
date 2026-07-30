<template>
    <LoadingComponent :props="loading" />
    <AutoLocalizationComponent />
    <div v-if="theme === 'frontend'">
        <FrontendNavbarComponent />
        <router-view></router-view>
        <FrontendCartComponent />
        <FrontendMobileNavbarComponent />
        <FrontendPWAComponent />
        <FrontendCookiesComponent />
        <FrontendFooterComponent />
        <FirebaseNotificationComponent />
    </div>
    <div v-if="theme === 'admin'">
        <main :class="sidebar ? 'ltr:lg:pl-64 rtl:lg:pr-64' : 'ltr:pl-0 rtl:pr-0'"
            class="pt-[70px] md:pt-16 font-admin text-paragraph transition-all duration-300">
            <BackendNavbarComponent />
            <BackendMenuComponent />
            <section class="p-3 lg:p-5">
                <router-view></router-view>
            </section>
            <FirebaseNotificationComponent />
            <BackendAiSidebarComponent />
        </main>
    </div>
</template>

<script>
import DisplayModeEnum from "../enums/modules/displayModeEnum.js";
import { useFrontendSettingStore } from "../stores/frontendSetting.js";
import orderTypeEnum from "../enums/modules/orderTypeEnum.js";
import { useAuthStore } from "../stores/auth.js";
import BackendNavbarComponent from "./layouts/backend/BackendNavbarComponent.vue";
import FrontendNavbarComponent from "./layouts/frontend/FrontendNavbarComponent.vue";
import BackendMenuComponent from "./layouts/backend/BackendMenuComponent.vue";
import BackendAiSidebarComponent from "./layouts/backend/BackendAiSidebarComponent.vue";
import { useCommonStore } from "../stores/common.js";
import FrontendFooterComponent from "./layouts/frontend/FrontendFooterComponent.vue";
import FrontendCartComponent from "./layouts/frontend/FrontendCartComponent.vue";
import { useFrontendCartStore } from "../stores/frontendCart.js";
import { useAutoLocalizationStore } from "../stores/autoLocalization.js";
import FrontendCookiesComponent from "./layouts/frontend/FrontendCookiesComponent.vue";
import FrontendMobileNavbarComponent from "./layouts/frontend/FrontendMobileNavbarComponent.vue";
import FrontendPWAComponent from "./layouts/frontend/FrontendPWAComponent.vue";
import FirebaseNotificationComponent from "./common/FirebaseNotificationComponent.vue";
import LoadingComponent from "./common/LoadingComponent.vue";
import activityEnum from "../enums/modules/activityEnum.js";
import { useCanvas } from "../composables/canvas.js";
import AutoLocalizationComponent from "./common/AutoLocalizationComponent.vue";
import env from "../config/env.js";

export default {
    name: 'DefaultComponent',
    components: {
        LoadingComponent,
        FirebaseNotificationComponent,
        FrontendPWAComponent,
        FrontendFooterComponent,
        BackendMenuComponent,
        FrontendNavbarComponent,
        BackendNavbarComponent,
        BackendAiSidebarComponent,
        FrontendCartComponent,
        FrontendCookiesComponent,
        FrontendMobileNavbarComponent,
        AutoLocalizationComponent
    },
    setup() {
        const commonStore = useCommonStore();
        const frontendCartStore = useFrontendCartStore();
        const frontendSettingStore = useFrontendSettingStore();
        const authStore = useAuthStore();
        const autoLocalizationStore = useAutoLocalizationStore();

        return {
            commonStore,
            frontendCartStore,
            frontendSettingStore,
            authStore,
            autoLocalizationStore
        }
    },
    data() {
        return {
            theme: "",
            loading: {
                isActive: false
            },
            openCanvas: useCanvas().openCanvas,
            activityEnum: activityEnum,
        }
    },
    async beforeMount() {
        this.displayModeDefine();
        this.loading.isActive = true;
        await this.frontendSettingStore.fetch().then(async res => {
            await this.frontendCartStore.setServiceFee(res.data.data.site_service_fee);
            await this.commonStore.init({
                language_id: res.data.data.site_default_language,
                order_type: orderTypeEnum.DELIVERY,
                search_restaurant: null,
                location: null,
                latitude: null,
                longitude: null,
                cuisine_id: null,
                localization: false
            });

            this.loading.isActive = false;
        }).catch(err => {
            this.loading.isActive = false;
        });

        if (env.DEMO === "true" || env.DEMO === 'TRUE' || env.DEMO === true || env.DEMO === "1" || env.DEMO === 1) {
            this.authStore.isAuth().then(res => {
                if (res.data.status === false) {
                    this.$router.push({ name: "frontend.home" });
                }
            }).catch();
        }
    },
    computed: {
        sidebar() {
            return this.commonStore.top_sidebar;
        },
        displayMode: function () {
            return this.commonStore.display_mode;
        }
    },
    methods: {
        displayModeDefine: function () {
            let dir = "ltr";
            const attributes = {
                dir: "ltr",
            };
            if (this.commonStore.display_mode === DisplayModeEnum.LTR) {
                dir = "ltr";
            } else {
                dir = "rtl";
            }
            Object.keys(attributes).forEach(attr => {
                document.documentElement.setAttribute(attr, dir);
            });
        }
    },
    watch: {
        $route(e) {
            if (e.meta.template) {
                this.theme = e.meta.template;
                if (this.theme === "admin") {
                    document.body.style.backgroundColor = '#f7f7fc';
                } else {
                    document.body.style.backgroundColor = '#ffffff';
                }
            }
        },
        displayMode() {
            this.displayModeDefine();
        }
    }
}
</script>
