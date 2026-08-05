<template>
    <div v-if="templateId === 1">
        <ContactUsComponent />
    </div>
</template>

<script>
import ContactUsComponent from "../page/ContactUsComponent.vue";

export default {
    name: "TemplateManagerComponent",
    props: ["templateId"],
    components: { ContactUsComponent },
    data() {
        return {

        }
    },
    mounted() {
    },
    methods: {
    },
    watch: {
    }
}
</script>
