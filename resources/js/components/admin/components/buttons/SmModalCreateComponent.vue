<template>
    <button type="button" @click="add"
        class="h-9 px-3 py-4 flex items-center gap-1 text-sm tracking-wide capitalize rounded-md shadow text-white bg-primary">
        <i class="lab lab-line-add-circle"></i>
        <span>{{ props.title }}</span>
    </button>
</template>

<script>
import {useModal} from "../../../../composables/modal.js";
export default {
    name: "SmModalCreateComponent",
    props: ['props'],
    methods: {
        add: function () {
            useModal().openModal('modal');
        }
    }
}
</script>
