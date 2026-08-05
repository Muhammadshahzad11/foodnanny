<template>
    <button class="h-9 px-3 py-4 font-medium border border-primary flex items-center gap-1 text-sm tracking-wide capitalize rounded-md shadow text-white bg-primary" @click.prevent="openCanvas('sidebar')">
        <i class="lab lab-line-add-circle"></i>
        <span>{{ props.title }}</span>
    </button>
</template>

<script>
import { useCanvas } from "../../../../composables/canvas";

export default {
    name: "SmSidebarModalCreateComponent",
    props: ['props'],
    data() {
        return {
            openCanvas: useCanvas().openCanvas,
        }
    },
}
</script>
