<template>
    <button
        v-if="canInstall"
        type="button"
        :class="buttonClass"
        @click.prevent="onClick"
        :title="$t('button.install_app')"
    >
        <slot>
            <svg class="w-4 h-4 flex-shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                <path d="M12 3v12"/><path d="m8 11 4 4 4-4"/><path d="M5 21h14"/>
            </svg>
            <span v-if="showLabel" class="hidden sm:inline">{{ $t('button.install_app') }}</span>
        </slot>
    </button>
</template>

<script>
import {usePwaInstall} from '../../composables/usePwaInstall.js';

export default {
    name: 'PwaInstallButtonComponent',
    props: {
        showLabel: {type: Boolean, default: true},
        buttonClass: {
            type: String,
            default: 'inline-flex items-center justify-center gap-1.5 h-9 px-2.5 sm:px-3 rounded-lg bg-primary/10 text-primary text-xs font-medium',
        },
    },
    setup() {
        return usePwaInstall();
    },
    async mounted() {
        await this.init();
    },
    methods: {
        async onClick() {
            // Prefer native prompt; always open UI so user sees help on every browser
            await this.promptInstall();
            if (!this.state.canPrompt) {
                this.openInstallUi();
            }
        },
    },
};
</script>
