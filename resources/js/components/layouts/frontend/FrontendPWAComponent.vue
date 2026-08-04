<template>
    <div ref="pwaHeaderModal" v-if="isPwaViewed && showInstall" class="hidden lg:block modal active ff-modal">
        <div class="modal-dialog max-w-[360px] p-6 text-center relative">
            <button class="modal-close absolute top-4 right-4" @click.prevent="closePWAHeaderModal">
                <i class="lab-line-circle-cross text-xl"></i>
            </button>
            <h3 class="text-[18px] font-semibold leading-8 mb-6">
                {{ $t("label.install_the_app") }} ?
            </h3>
            <div class="flex gap-3 justify-center text-center">
                <button type="button" class=" modal-close modal-btn-outline " @click.prevent="closePWAHeaderModal">
                    <i class="lab lab-fill-close-circle"></i>
                    <span>{{ $t("button.close") }}</span>
                </button>
                <button id="installPWA" class="db-btn py-2 text-white bg-primary" @click.prevent="installPWA">
                    <i class="lab lab-fill-save"></i>
                    <span>{{ $t('button.install') }}</span>
                </button>
            </div>
        </div>
    </div>

    <div v-if="isPwaViewed && showInstall" ref="pwaStickyFooter"
         class="lg:hidden border-none bg-white p-4 fixed bottom-0 left-0 w-full z-80 rounded-tl-3xl rounded-tr-3xl shadow-paper">
        <div class="flex items-start gap-3 mb-3">
            <img :src="setting.theme_favicon_logo" alt="Cost to Cost Foods"
                 class="w-12 h-12 rounded-xl flex-shrink-0 shadow-xl object-contain bg-white">
            <h3 class="text-sm flex-auto text-[#008BBA]">
                {{ $t('message.add_app_to_your_home_screen', {title: setting.company_name}) }}
            </h3>
        </div>
        <div class="flex items-center justify-end gap-2">
            <button @click.prevent="closePWAFooterModal"
                    class="py-2 px-3 rounded-md capitalize text-sm border border-gray-200 text-primary">
                <i class="lab lab-fill-close-circle"></i>
                {{ $t('button.cancel') }}
            </button>
            <button @click.prevent="installPWA" class="py-2 px-3 rounded-md capitalize text-sm bg-primary text-white">
                <i class="lab lab-fill-save"></i>
                {{ $t('button.install') }}
            </button>
        </div>
    </div>
</template>
<script>
import {useFrontendSettingStore} from "../../../stores/frontendSetting.js";


export default {
    name: "FrontendPWAComponent",
    setup() {
        const frontendSettingStore = useFrontendSettingStore();

        return {
            frontendSettingStore,
        }
    },
    data() {
        return {
            showInstall: false,
            installPrompt: null
        }
    },
    computed: {
        setting: function () {
            return this.frontendSettingStore.lists;
        },
        isPwaViewed: function () {
            return this.getItemWithExpiry('pwaViewed');
        },
    },
    mounted() {
        window.addEventListener('beforeinstallprompt', (e) => {
            e.preventDefault();
            this.installPrompt = e;
            this.showInstall   = true;
        })
    },
    methods: {
        setItemWithExpiry: function (key, value, ttlMs) {
            const now  = new Date()
            const item = {
                value: value,
                expiry: now.getTime() + ttlMs,
            }
            localStorage.setItem(key, JSON.stringify(item))
        },
        getItemWithExpiry: function (key) {
            const itemStr = localStorage.getItem(key);

            if (itemStr) {
                const item = JSON.parse(itemStr)
                const now  = Date.now();

                if (now > item.expiry) {
                    localStorage.removeItem(key);
                    return true;
                }
                return false;
            }
            return true;
        },
        installPWA: async function () {
            if (!this.installPrompt) return
            await this.installPrompt.prompt();
            this.installPrompt = null;
            this.showInstall   = false;
        },
        closePWAHeaderModal: function () {
            const modalTarget = this.$refs.pwaHeaderModal;
            modalTarget?.classList?.add("hidden");
            modalTarget?.classList?.remove("lg:block");
            this.setItemWithExpiry('pwaViewed', true, 2 * 60 * 60 * 1000);
        },
        closePWAFooterModal: function () {
            const modalTarget = this.$refs.pwaStickyFooter;
            modalTarget?.classList?.add("hidden");
            this.setItemWithExpiry('pwaViewed', true, 2 * 60 * 60 * 1000);
        }
    }
}
</script>
