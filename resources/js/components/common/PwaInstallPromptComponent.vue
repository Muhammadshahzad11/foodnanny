<template>
    <teleport to="body">
        <div
            v-if="state.showPopup && !state.installed"
            class="fixed inset-0 z-[90] flex items-end sm:items-center justify-center p-3 sm:p-6 bg-black/45"
            @click.self="closePopup"
        >
            <div class="w-full max-w-md rounded-2xl bg-white shadow-2xl overflow-hidden animate-[fadeIn_.25s_ease]" @click.stop>
                <div class="p-5 sm:p-6">
                    <div class="flex items-start gap-3 mb-4">
                        <img
                            :src="iconSrc"
                            alt="Cost to Cost Foods"
                            width="56"
                            height="56"
                            class="w-14 h-14 rounded-2xl object-contain bg-[#F7F7FC] border border-[#EFF0F6]"
                            @error="onIconError"
                        >
                        <div class="min-w-0">
                            <h3 class="text-lg font-semibold text-heading">
                                {{ $t('label.install_application') }}
                            </h3>
                            <p class="text-sm text-[#6E7191] mt-0.5 truncate">
                                {{ state.config?.name || $t('menu.progressive_web_app') }}
                            </p>
                        </div>
                    </div>

                    <ul class="space-y-2 mb-5 text-sm text-heading">
                        <li class="flex gap-2"><span class="text-primary">✓</span> {{ $t('message.pwa_benefit_home') }}</li>
                        <li class="flex gap-2"><span class="text-primary">✓</span> {{ $t('message.pwa_benefit_fast') }}</li>
                        <li class="flex gap-2"><span class="text-primary">✓</span> {{ $t('message.pwa_benefit_offline') }}</li>
                    </ul>

                    <div
                        v-if="state.showManualHelp || state.isIos || !state.canPrompt"
                        class="mb-4 text-xs text-[#6E7191] rounded-xl bg-[#F7F7FC] p-3 space-y-1.5"
                    >
                        <p v-if="state.isIos">{{ $t('message.pwa_ios_install_hint') }}</p>
                        <p v-else-if="state.isAndroid">{{ $t('message.pwa_android_install_hint') }}</p>
                        <p v-else>{{ $t('message.pwa_desktop_install_hint') }}</p>
                    </div>

                    <div class="flex flex-col gap-2">
                        <button
                            type="button"
                            class="db-btn py-3 text-white bg-primary w-full justify-center disabled:opacity-60"
                            :disabled="state.installing"
                            @click.stop.prevent="onInstallClick"
                        >
                            {{ state.installing ? 'Installing…' : $t('button.install_app') }}
                        </button>
                        <div class="grid grid-cols-2 gap-2">
                            <button type="button" class="db-btn py-2 border border-[#EFF0F6] text-heading" @click.stop.prevent="maybeLater">
                                {{ $t('button.maybe_later') }}
                            </button>
                            <button type="button" class="db-btn py-2 border border-[#EFF0F6] text-[#6E7191]" @click.stop.prevent="neverAgain">
                                {{ $t('button.never_show_again') }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div
            v-if="state.justInstalled"
            class="fixed top-24 right-4 z-[95] rounded-xl bg-primary text-white px-4 py-3 shadow-lg text-sm font-medium"
        >
            {{ $t('message.pwa_installed_successfully') }}
        </div>
    </teleport>
</template>

<script>
import {usePwaInstall} from '../../composables/usePwaInstall.js';

export default {
    name: 'PwaInstallPromptComponent',
    data() {
        return {
            iconSrc: `${window.location.origin}/images/default/pwa/icons/icon-192x192.png`,
        };
    },
    setup() {
        return usePwaInstall();
    },
    async mounted() {
        await this.init();
    },
    methods: {
        async onInstallClick() {
            await this.promptInstall();
        },
        onIconError(e) {
            // Bypass any stale service-worker cache for the icon
            const bust = `${window.location.origin}/images/default/pwa/icons/icon-192x192.png?v=${Date.now()}`;
            if (e?.target && e.target.src !== bust) {
                e.target.src = bust;
            }
        },
    },
};
</script>
