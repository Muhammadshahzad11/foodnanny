<template>
     <section class="container md:py-20 py-14">
        <div class="flex flex-col items-center justify-center">
            <div class="max-w-[360px] w-full mb-8 sm:mb-12">
                <img class="w-full h-full" :src="setting.image_404" alt="404 Error Forbidden">
            </div>
            <h4 class="text-2xl sm:text-[32px] font-medium mb-3 sm:mb-4 text-center">{{ $t('number.404') }} {{ $t('message.page_not_found') }}</h4>
            <p class="text-sm sm:text-lg font-normal mb-4 sm:mb-6 text-center">{{ $t('message.we_can_not_seem') }}</p>
            <button @click.prevent="goBack" class="py-3 px-12 rounded-full font-semibold capitalize bg-primary text-white"> {{ $t('button.go_back')}}</button>
        </div>
    </section>
</template>
<script>
import router from "../../router/index.js";
import {useFrontendSettingStore} from "../../stores/frontendSetting.js";

export default {
    name: "NotFoundComponent",
    setup() {
        const frontendSettingStore = useFrontendSettingStore();

        return {
            frontendSettingStore
        }
    },
    computed: {
        setting: function() {
            return this.frontendSettingStore.lists;
        }
    },
    methods: {
        goBack: function() {
            router.go(-1);
        }
    }
}
</script>
