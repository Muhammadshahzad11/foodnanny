<template>
    <teleport to="body">
        <div
            v-if="state.updateReady"
            class="fixed inset-x-0 bottom-0 z-[92] p-4 flex justify-center pointer-events-none"
        >
            <div class="pointer-events-auto w-full max-w-md rounded-2xl bg-white border border-[#EFF0F6] shadow-2xl p-4 flex flex-wrap items-center justify-between gap-3">
                <div>
                    <p class="font-semibold text-heading">{{ $t('label.new_version_available') }}</p>
                    <p class="text-sm text-[#6E7191]">{{ $t('message.pwa_update_hint') }}</p>
                </div>
                <div class="flex gap-2">
                    <button type="button" class="db-btn py-2 border border-[#EFF0F6] text-heading" @click="updateLater">
                        {{ $t('button.later') }}
                    </button>
                    <button type="button" class="db-btn py-2 text-white bg-primary" @click="updateNow">
                        {{ $t('button.update_now') }}
                    </button>
                </div>
            </div>
        </div>
    </teleport>
</template>

<script>
import {usePwaUpdate} from '../../composables/usePwaUpdate.js';

export default {
    name: 'PwaUpdatePromptComponent',
    setup() {
        return usePwaUpdate();
    },
    async mounted() {
        await this.init();
    },
};
</script>
