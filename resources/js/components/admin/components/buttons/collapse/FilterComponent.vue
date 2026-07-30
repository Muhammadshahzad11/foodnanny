<template>
    <button
        class="h-9 px-3 py-4 text-sm tracking-wide font-medium capitalize rounded-md shadow flex items-center justify-center gap-1 border border-primary text-primary bg-white">
        <i class="lab lab-line-filter"></i>
        <span>{{ $t('button.filter') }}</span>
        <i class="lab lab-line-chevron-down font-semibold arrow-icon transition-all duration-300"></i>
    </button>
</template>

<script>
export default {
    name: "FilterComponent"
}
</script>
