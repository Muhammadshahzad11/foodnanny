<template>
    <button @click.prevent="$emit('open')" type="button"
            class="flex items-center gap-1.5 px-2.5 h-8 rounded-3xl text-primary bg-primary/5">
        <i class="lab-fill-add-circle text-sm -mt-[1px]"></i>
        <span class="text-sm font-medium capitalize whitespace-nowrap">{{ props.title }}</span>
    </button>
</template>

<script>
export default {
    name: "AddressCreateModalComponent",
    props: ['props'],
    emits: ['open'],
}
</script>
