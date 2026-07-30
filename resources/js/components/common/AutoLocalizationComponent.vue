<template>

</template>
<script>
import {useAutoLocalizationStore} from "../../stores/autoLocalization.js";
import {useFrontendSettingStore} from "../../stores/frontendSetting.js";
import {useFrontendLanguageStore} from "../../stores/frontendLanguage.js";
import {useCommonStore} from "../../stores/common.js";
import activityEnum from "../../enums/modules/activityEnum.js";
import alertService from "../../services/alertService.js";
import env from "../../config/env.js";

export default {
    name: "AutoLocalizationComponent",
    setup() {
        const autoLocalizationStore = useAutoLocalizationStore();
        const frontendSettingStore  = useFrontendSettingStore();
        const frontendLanguageStore = useFrontendLanguageStore();
        const commonStore           = useCommonStore();

        return {
            autoLocalizationStore,
            frontendSettingStore,
            frontendLanguageStore,
            commonStore
        }
    },
    computed: {
        setting() {
            return this.frontendSettingStore.lists;
        },
    },
    mounted() {
        setTimeout(() => {
            if (this.commonStore.localization === false) {
                if (this.setting.site_auto_localization === activityEnum.ENABLE) {
                    if (env.DEMO) {
                        alertService.success(this.$t('message.your_language_has_been_set_automatically'));
                    }
                    this.autoLocalizationStore.fetch().then((res) => {
                        this.commonStore.update({
                            language_id: res.data.data.id,
                            language_code: res.data.data.code,
                            display_mode: res.data.data.display_mode
                        }).then(response => {
                            this.frontendLanguageStore.view(res.data.data.id).then(res => {
                                this.$i18n.locale = res.data.data.code;
                            }).catch();
                        }).catch()
                    }).catch(() => {
                    })
                }
                this.commonStore.update({localization: true});
            }
        }, 1000)
    }
}
</script>
