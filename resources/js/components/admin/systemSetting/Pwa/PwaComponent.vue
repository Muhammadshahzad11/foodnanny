<template>
    <LoadingComponent :props="loading"/>
    <div class="db-card db-tab-div active">
        <div class="db-card-header border-none !items-start gap-3">
            <div>
                <h3 class="db-card-title">{{ $t("menu.progressive_web_app") }}</h3>
                <p class="text-sm text-[#6E7191] mt-1">{{ $t('message.pwa_settings_hint') }}</p>
            </div>
            <PwaInstallButtonComponent/>
        </div>

        <div class="mb-4 mx-4 bg-amber-50 border border-amber-100 p-3 rounded-xl">
            <h2 class="mb-1 font-semibold text-heading">{{ $t('label.reminder') }}</h2>
            <p class="text-sm text-[#6E7191]">{{ $t('message.pwa_image_remainder') }}</p>
        </div>

        <div class="db-card-body">
            <form @submit.prevent="save" class="space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="db-field-title">{{ $t('label.application_name') }}</label>
                        <input v-model="form.name" type="text" class="db-field-control"/>
                    </div>
                    <div>
                        <label class="db-field-title">{{ $t('label.short_name') }}</label>
                        <input v-model="form.short_name" type="text" class="db-field-control"/>
                    </div>
                    <div class="md:col-span-2">
                        <label class="db-field-title">{{ $t('label.description') }}</label>
                        <textarea v-model="form.description" rows="2" class="db-field-control"></textarea>
                    </div>
                    <div>
                        <label class="db-field-title">{{ $t('label.theme_color') }}</label>
                        <input v-model="form.theme_color" type="color" class="db-field-control h-11 p-1"/>
                    </div>
                    <div>
                        <label class="db-field-title">{{ $t('label.background_color') }}</label>
                        <input v-model="form.background_color" type="color" class="db-field-control h-11 p-1"/>
                    </div>
                    <div>
                        <label class="db-field-title">{{ $t('label.orientation') }}</label>
                        <select v-model="form.orientation" class="db-field-control">
                            <option value="any">any</option>
                            <option value="natural">natural</option>
                            <option value="portrait">portrait</option>
                            <option value="landscape">landscape</option>
                        </select>
                    </div>
                    <div>
                        <label class="db-field-title">{{ $t('label.display_mode') }}</label>
                        <select v-model="form.display_mode" class="db-field-control">
                            <option value="standalone">standalone</option>
                            <option value="fullscreen">fullscreen</option>
                            <option value="minimal-ui">minimal-ui</option>
                            <option value="browser">browser</option>
                        </select>
                    </div>
                    <div>
                        <label class="db-field-title">{{ $t('label.cache_strategy') }}</label>
                        <select v-model="form.cache_strategy" class="db-field-control">
                            <option value="balanced">balanced</option>
                            <option value="aggressive">aggressive</option>
                            <option value="network_first">network_first</option>
                        </select>
                    </div>
                    <div>
                        <label class="db-field-title">{{ $t('label.popup_delay_seconds') }}</label>
                        <input v-model.number="form.popup_delay_seconds" type="number" min="0" max="120" class="db-field-control"/>
                    </div>
                    <div>
                        <label class="db-field-title">{{ $t('label.popup_frequency_hours') }}</label>
                        <input v-model.number="form.popup_frequency_hours" type="number" min="1" max="720" class="db-field-control"/>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                    <label class="flex items-center gap-2 p-3 rounded-xl border border-[#EFF0F6]">
                        <input v-model="form.offline_mode" type="checkbox" class="rounded"/>
                        <span class="text-sm">{{ $t('label.offline_mode') }}</span>
                    </label>
                    <label class="flex items-center gap-2 p-3 rounded-xl border border-[#EFF0F6]">
                        <input v-model="form.auto_update" type="checkbox" class="rounded"/>
                        <span class="text-sm">{{ $t('label.auto_update') }}</span>
                    </label>
                    <label class="flex items-center gap-2 p-3 rounded-xl border border-[#EFF0F6]">
                        <input v-model="form.enable_install_popup" type="checkbox" class="rounded"/>
                        <span class="text-sm">{{ $t('label.enable_install_popup') }}</span>
                    </label>
                    <div class="p-3 rounded-xl border border-[#EFF0F6] text-sm text-[#6E7191]">
                        {{ $t('label.cache_version') }}: <strong class="text-heading">{{ pwa.cache_version || 1 }}</strong>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="splash" class="db-field-title">
                            {{ $t("label.splash") }} (2048×2732)
                        </label>
                        <input @change="changeSplash" id="splash" type="file" class="db-field-control"
                               ref="splashProperty" accept="image/png, image/jpeg, image/jpg"/>
                        <small class="db-field-alert" v-if="errors.pwa_splash">{{ errors.pwa_splash[0] }}</small>
                    </div>
                    <div>
                        <label for="icon" class="db-field-title">
                            {{ $t("label.icon") }} (512×512)
                        </label>
                        <input @change="changeIcon" id="icon" type="file" class="db-field-control"
                               ref="iconProperty" accept="image/png, image/jpeg, image/jpg"/>
                        <small class="db-field-alert" v-if="errors.pwa_icon">{{ errors.pwa_icon[0] }}</small>
                    </div>
                </div>

                <div class="flex flex-wrap gap-2">
                    <button type="submit" class="db-btn text-white bg-primary">
                        <span>{{ $t("button.save") }}</span>
                    </button>
                    <button type="button" class="db-btn border border-[#EFF0F6] text-heading" @click="forceUpdate">
                        {{ $t('button.force_update') }}
                    </button>
                </div>
            </form>

            <div class="row mt-6">
                <div class="col-6 sm:col-3">
                    <h3 class="text-lg font-medium capitalize mb-2 text-paragraph">{{ $t("label.splash") }}</h3>
                    <img class="db-image" alt="splash" :src="pwa.splash"/>
                </div>
                <div class="col-6 sm:col-3">
                    <h3 class="text-lg font-medium capitalize mb-2 text-paragraph">{{ $t("label.icon") }}</h3>
                    <img class="db-image" alt="icon" :src="pwa.icon"/>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import LoadingComponent from "../../../common/LoadingComponent.vue";
import PwaInstallButtonComponent from "../../../common/PwaInstallButtonComponent.vue";
import alertService from "../../../../services/alertService.js";
import {usePwaStore} from "../../../../stores/pwa.js";

export default {
    name: "PwaComponent",
    components: {LoadingComponent, PwaInstallButtonComponent},
    setup() {
        return {pwaStore: usePwaStore()};
    },
    data() {
        return {
            loading: {isActive: false},
            splash: "",
            icon: "",
            errors: {},
            form: {
                name: '',
                short_name: '',
                description: '',
                theme_color: '#148A3C',
                background_color: '#FFFFFF',
                orientation: 'any',
                display_mode: 'standalone',
                offline_mode: true,
                auto_update: true,
                cache_strategy: 'balanced',
                enable_install_popup: true,
                popup_delay_seconds: 3,
                popup_frequency_hours: 24,
            },
        };
    },
    mounted() {
        this.load();
    },
    computed: {
        pwa() {
            return this.pwaStore.lists || {};
        }
    },
    methods: {
        load() {
            this.loading.isActive = true;
            this.pwaStore.fetch().then(() => {
                this.hydrateForm();
                this.loading.isActive = false;
            }).catch(() => {
                this.loading.isActive = false;
            });
        },
        hydrateForm() {
            const p = this.pwa;
            this.form = {
                name: p.name || '',
                short_name: p.short_name || '',
                description: p.description || '',
                theme_color: p.theme_color || '#148A3C',
                background_color: p.background_color || '#FFFFFF',
                orientation: p.orientation || 'any',
                display_mode: p.display_mode || 'standalone',
                offline_mode: p.offline_mode !== false,
                auto_update: p.auto_update !== false,
                cache_strategy: p.cache_strategy || 'balanced',
                enable_install_popup: p.enable_install_popup !== false,
                popup_delay_seconds: p.popup_delay_seconds ?? 3,
                popup_frequency_hours: p.popup_frequency_hours ?? 24,
            };
        },
        changeSplash(e) {
            this.splash = e.target.files[0];
        },
        changeIcon(e) {
            this.icon = e.target.files[0];
        },
        save() {
            const form = new FormData();
            Object.entries(this.form).forEach(([key, value]) => {
                if (typeof value === 'boolean') {
                    form.append(key, value ? '1' : '0');
                } else if (value !== null && value !== undefined) {
                    form.append(key, value);
                }
            });
            if (this.splash) form.append('pwa_splash', this.splash);
            if (this.icon) form.append('pwa_icon', this.icon);

            this.loading.isActive = true;
            this.pwaStore.save({form}).then(() => {
                this.loading.isActive = false;
                this.hydrateForm();
                alertService.successFlip(1, this.$t("menu.progressive_web_app"));
                this.errors = {};
                if (this.$refs.splashProperty) this.$refs.splashProperty.value = null;
                if (this.$refs.iconProperty) this.$refs.iconProperty.value = null;
                this.splash = '';
                this.icon = '';
            }).catch((err) => {
                this.loading.isActive = false;
                if (err.response?.data?.status === false) {
                    alertService.error(err.response.data.message);
                } else {
                    this.errors = err.response?.data?.errors || {};
                }
            });
        },
        forceUpdate() {
            this.loading.isActive = true;
            this.pwaStore.forceUpdate().then(() => {
                this.loading.isActive = false;
                alertService.successFlip(1, this.$t('button.force_update'));
                if ('serviceWorker' in navigator) {
                    navigator.serviceWorker.ready.then((reg) => {
                        const version = this.pwa.cache_version;
                        // Safe clear: only CTC/PWA caches, then set version (no full wipe)
                        reg.active?.postMessage({type: 'CLEAR_CACHES', version});
                        reg.active?.postMessage({type: 'SET_CACHE_VERSION', version});
                        reg.update().catch(() => {});
                    }).catch(() => {});
                }
            }).catch((err) => {
                this.loading.isActive = false;
                alertService.error(err.response?.data?.message || this.$t('message.something_wrong'));
            });
        }
    }
}
</script>
