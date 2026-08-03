<template>
    <LoadingComponent :props="loading"/>
    <div class="min-h-[60vh] flex items-center justify-center px-4 py-16">
        <div class="w-full max-w-md rounded-2xl bg-white p-8 shadow-sm text-center" v-if="error">
            <h1 class="mb-3 text-2xl font-semibold text-heading">{{ $t('message.table_qr_invalid_title') }}</h1>
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
import router from "../../../router/index.js";

export default {
    name: "TableQrEntryComponent",
    components: {LoadingComponent},
    setup() {
        const dineInContextStore = useDineInContextStore();
        return {dineInContextStore};
    },
    data() {
        return {
            loading: {isActive: true},
            error: null,
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
                this.loading.isActive = false;
                router.replace({
                    name: 'frontend.singleRestaurant',
                    params: {slug: data.restaurant_slug},
                });
            }).catch((err) => {
                this.loading.isActive = false;
                this.error = err.response?.data?.message || this.$t('message.table_qr_invalid');
            });
        }
    }
};
</script>
