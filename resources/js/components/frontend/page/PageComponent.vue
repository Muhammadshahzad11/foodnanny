<template>
    <section class="pt-8 pb-16">
        <div class="container max-w-3xl">
            <div class="mb-6">
                <h2 class="text-[26px] leading-10 font-semibold capitalize mb-2">
                    {{ page.title }}
                </h2>
                <div v-if="page.image" class="w-full mb-6">
                    <img :src="page.image" alt="image">
                </div>
                <div class="editor-show-design" v-html="page.description"></div>
                <TemplateManagerComponent :templateId="page.template_id" />
            </div>
        </div>
    </section>
</template>

<script>
import TemplateManagerComponent from "../components/TemplateManagerComponent.vue";
import {useFrontendPageStore} from "../../../stores/frontendPage.js";

export default {
    name: "PageComponent",
    components: { TemplateManagerComponent },
    setup() {
        const frontendPageStore = useFrontendPageStore();
        return {
            frontendPageStore
        }
    },
    computed: {
        page: function () {
            return this.frontendPageStore.show;
        }
    },
    mounted() {
        this.pageSetup();
    },
    methods: {
        pageSetup: function () {
            if (Object.keys(this.$route.params).length > 0 && typeof this.$route.params.slug === 'string') {
                this.frontendPageStore.view(this.$route.params.slug);
            }
        }
    },
    watch: {
        $route() {
            this.pageSetup();
        }
    }
}
</script>
