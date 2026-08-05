<template>
    <button class="db-table-action edit modal-btn">
        <i class="lab lab-line-shield"></i>
        <span class="db-tooltip">{{ tooltip }}</span>
    </button>
</template>

<script>
export default {
    name: "SmIconModalVerifyComponent",
    props: {
        label: {
            type: String,
            default: ""
        }
    },
    computed: {
        tooltip() {
            return this.label || this.$t('button.verify');
        }
    }
}
</script>
