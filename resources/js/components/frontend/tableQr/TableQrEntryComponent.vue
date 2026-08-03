<template>
    <LoadingComponent :props="loading"/>
    <div class="min-h-[60vh] flex items-center justify-center px-4 py-16">
        <div class="w-full max-w-md rounded-2xl bg-white p-8 shadow-sm text-center" v-if="error">
            <i :class="errorIcon" class="mb-4 text-5xl text-paragraph"></i>
            <h1 class="mb-3 text-2xl font-semibold text-heading">{{ errorTitle }}</h1>
            <p class="mb-6 text-paragraph">{{ error }}</p>
            <router-link :to="{ name: 'frontend.home' }" class="field-button inline-flex justify-center">
                {{ $t('button.go_home') }}
            </router-link>
        </div>
        <div class="text-center text-paragraph" v-else>
            {{ $t('message.table_qr_loading') }}
        </div>
    </div>
</template>

<script>
import LoadingComponent from "../../common/LoadingComponent.vue";
import {useDineInContextStore} from "../../../stores/dineInContext.js";
import {useFrontendCartStore} from "../../../stores/frontendCart.js";
import router from "../../../router/index.js";

export default {
    name: "TableQrEntryComponent",
    components: {LoadingComponent},
    setup() {
        const dineInContextStore = useDineInContextStore();
        const frontendCartStore = useFrontendCartStore();
        return {dineInContextStore, frontendCartStore};
    },
    data() {
        return {
            loading: {isActive: true},
            error: null,
            errorTitle: '',
            errorIcon: 'lab-line-warning',
        };
    },
    mounted() {
        this.resolve();
    },
    methods: {
        resolve() {
            this.loading.isActive = true;
            this.error = null;
            this.dineInContextStore.resolveToken(this.$route.params.token).then((data) => {
                if (Object.keys(this.frontendCartStore.restaurant).length > 0
                    && Number(this.frontendCartStore.restaurant.id) !== Number(data.restaurant_id)) {
                    this.frontendCartStore.callResetCart();
                }
                this.frontendCartStore.applyDineInContext({
                    table_id: data.table_id,
                    qr_token: data.qr_token,
                });
                this.loading.isActive = false;
                router.replace({
                    name: 'frontend.singleRestaurant',
                    params: {slug: data.restaurant_slug},
                });
            }).catch((err) => {
                this.loading.isActive = false;
                const message = err.response?.data?.message || this.$t('message.table_qr_invalid');
                this.error = message;
                this.errorTitle = this.titleForMessage(message);
                this.errorIcon = this.iconForMessage(message);
            });
        },
        titleForMessage(message) {
            const lowered = (message || '').toLowerCase();
            if (lowered.includes('removed')) return this.$t('message.table_qr_removed_title');
            if (lowered.includes('inactive')) return this.$t('message.table_qr_inactive_title');
            if (lowered.includes('closed')) return this.$t('message.table_qr_restaurant_closed_title');
            if (lowered.includes('unavailable') && lowered.includes('restaurant')) {
                return this.$t('message.table_qr_restaurant_inactive_title');
            }
            return this.$t('message.table_qr_invalid_title');
        },
        iconForMessage(message) {
            const lowered = (message || '').toLowerCase();
            if (lowered.includes('closed')) return 'lab-line-clock';
            if (lowered.includes('removed')) return 'lab-line-trash';
            return 'lab-line-warning';
        }
    }
};
</script>
