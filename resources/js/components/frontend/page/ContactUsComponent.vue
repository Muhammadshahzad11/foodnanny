<template>
    <div>
        <h2 class="text-[22px] leading-[34px] font-medium capitalize mt-4 pt-4 mb-3">{{ $t('label.support') }}</h2>
        <ul class="flex flex-col gap-2">
            <li class="flex items-center gap-2.5">
                <i class="lab-line-mail lab-font-size-16"></i>
                <span class="text-sm leading-6 text-heading">{{ setting.company_email }}</span>
            </li>
            <li class="flex items-center gap-2.5">
                <i class="lab-line-calling lab-font-size-16"></i>
                <span class="text-sm font-medium leading-6 text-heading">{{ setting.company_phone }}</span>
            </li>
        </ul>
    </div>
</template>

<script>
import {useFrontendSettingStore} from "../../../stores/frontendSetting.js";
import statusEnum from "../../../enums/modules/statusEnum.js";

export default {
    name: "ContactUsComponent",
    setup() {
        const frontendSettingStore = useFrontendSettingStore();

        return {
            frontendSettingStore
        }
    },
      data() {
        return {
            enums: {
                statusEnum: statusEnum
            }
        };
    },
    computed: {
        setting: function () {
            return this.frontendSettingStore.lists;
        }
    }
}
</script>
