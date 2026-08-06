<template>
    <LoadingComponent :props="loading"/>
    <div class="db-card">
        <div class="db-card-header">
            <h3 class="db-card-title">{{ $t('menu.cache') }}</h3>
        </div>
        <div class="db-card-body">
            <div class="rounded-xl border border-primary/15 bg-gradient-to-br from-emerald-50 via-white to-slate-50 p-5 sm:p-6">
                <div class="flex flex-col sm:flex-row sm:items-start gap-4">
                    <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-primary text-white">
                        <i class="lab-line-reset text-2xl"></i>
                    </div>
                    <div class="min-w-0 flex-1">
                        <h4 class="text-lg font-semibold text-heading mb-1">
                            {{ $t('label.clear_cache') }}
                        </h4>
                        <p class="text-sm leading-6 text-paragraph mb-4">
                            {{ $t('message.clear_cache_help') }}
                        </p>
                        <ul class="mb-5 grid grid-cols-1 sm:grid-cols-2 gap-2 text-sm text-heading">
                            <li class="flex items-center gap-2">
                                <i class="lab-line-tick-circle text-primary"></i>
                                {{ $t('label.cache_application') }}
                            </li>
                            <li class="flex items-center gap-2">
                                <i class="lab-line-tick-circle text-primary"></i>
                                {{ $t('label.cache_config') }}
                            </li>
                            <li class="flex items-center gap-2">
                                <i class="lab-line-tick-circle text-primary"></i>
                                {{ $t('label.cache_routes') }}
                            </li>
                            <li class="flex items-center gap-2">
                                <i class="lab-line-tick-circle text-primary"></i>
                                {{ $t('label.cache_views') }}
                            </li>
                        </ul>
                        <button
                            type="button"
                            :disabled="flushing"
                            @click.prevent="flushCache"
                            class="db-btn text-white bg-primary disabled:opacity-60"
                        >
                            <i class="lab-line-reset text-base" :class="{ 'animate-spin': flushing }"></i>
                            <span>{{ flushing ? $t('button.clearing_cache') : $t('button.clear_cache') }}</span>
                        </button>
                        <p v-if="lastCleared.length" class="mt-4 text-xs text-paragraph">
                            {{ $t('message.cache_cleared_items') }}:
                            <span class="font-medium text-heading">{{ lastCleared.join(', ') }}</span>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import LoadingComponent from "../../../common/LoadingComponent.vue";
import alertService from "../../../../services/alertService.js";
import { useCacheStore } from "../../../../stores/cache.js";

export default {
    name: "CacheComponent",
    components: { LoadingComponent },
    setup() {
        const cacheStore = useCacheStore();
        return { cacheStore };
    },
    data() {
        return {
            loading: { isActive: false },
            flushing: false,
        };
    },
    computed: {
        lastCleared() {
            return this.cacheStore.lastCleared || [];
        },
    },
    methods: {
        flushCache() {
            this.flushing = true;
            this.loading.isActive = true;
            this.cacheStore.flush().then((res) => {
                this.loading.isActive = false;
                this.flushing = false;
                alertService.success(res.data.message || this.$t('message.cache_cleared_successfully'));
            }).catch((err) => {
                this.loading.isActive = false;
                this.flushing = false;
                alertService.error(err.response?.data?.message || this.$t('message.cache_clear_failed'));
            });
        },
    },
};
</script>
