<template>
    <div
        v-if="visible"
        class="mb-4 flex flex-wrap items-center justify-between gap-3 rounded-xl border border-primary/20 bg-primary/5 px-4 py-3">
        <div class="flex items-start gap-3">
            <i class="lab-line-fork-knife mt-0.5 text-xl text-primary"></i>
            <div>
                <p class="text-sm font-medium text-heading">{{ $t('message.dine_in_ordering_for_table') }}</p>
                <p class="text-sm text-paragraph">
                    {{ $t('label.table') }}: <span class="font-medium text-heading">{{ tableLabel }}</span>
                    <span v-if="zone"> · {{ zone }}</span>
                </p>
            </div>
        </div>
        <button
            v-if="allowExit"
            type="button"
            class="text-xs font-medium text-paragraph underline hover:text-primary"
            @click.prevent="$emit('exit')">
            {{ $t('button.exit_dine_in') }}
        </button>
    </div>
</template>

<script>
export default {
    name: "DineInBannerComponent",
    props: {
        visible: {type: Boolean, default: false},
        tableLabel: {type: String, default: ''},
        zone: {type: String, default: ''},
        allowExit: {type: Boolean, default: false},
    },
    emits: ['exit'],
};
</script>
