<template>
    <div class="mb-6">
        <div class="flex items-center justify-between gap-1 mb-2">
            <div
                v-for="(step, index) in steps"
                :key="step.key"
                class="flex flex-1 items-center min-w-0"
            >
                <div class="flex flex-col items-center flex-1 min-w-0">
                    <div
                        :class="circleClass(step, index)"
                        class="flex h-7 w-7 sm:h-8 sm:w-8 items-center justify-center rounded-full text-[11px] sm:text-xs font-semibold transition-all duration-300"
                    >
                        <svg
                            v-if="isDone(index)"
                            xmlns="http://www.w3.org/2000/svg"
                            viewBox="0 0 24 24"
                            fill="none"
                            class="h-3.5 w-3.5 sm:h-4 sm:w-4"
                            aria-hidden="true"
                        >
                            <path d="M5 12.5l4.5 4.5L19 7.5" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        <span v-else>{{ index + 1 }}</span>
                    </div>
                    <span
                        :class="labelClass(step, index)"
                        class="mt-1.5 max-w-full truncate text-[10px] sm:text-xs font-medium text-center leading-tight"
                    >
                        {{ step.label }}
                    </span>
                </div>
                <div
                    v-if="index < steps.length - 1"
                    :class="isDone(index) ? 'bg-primary' : 'bg-[#d9dbe9]'"
                    class="mx-0.5 sm:mx-1 mb-5 h-0.5 flex-1 rounded-full transition-colors duration-300"
                ></div>
            </div>
        </div>
        <p class="text-center text-[11px] text-paragraph">
            {{ $t('label.step_of', { current: currentIndex + 1, total: steps.length }) }}
        </p>
    </div>
</template>

<script>
import askEnum from "../../../enums/modules/askEnum.js";
import {useFrontendSettingStore} from "../../../stores/frontendSetting.js";
import ENV from "../../../config/env.js";

export default {
    name: "RestaurantSignupStepsComponent",
    props: {
        /**
         * Active step key: phone | verify | owner | restaurant | done
         */
        current: {
            type: String,
            required: true
        }
    },
    setup() {
        const frontendSettingStore = useFrontendSettingStore();
        return {frontendSettingStore};
    },
    mounted() {
        if (!this.frontendSettingStore.lists || !Object.keys(this.frontendSettingStore.lists).length) {
            this.frontendSettingStore.fetch().catch(() => {});
        }
    },
    computed: {
        includeVerify() {
            const demo = String(ENV.DEMO || "").toLowerCase() === "true";
            if (demo) {
                return false;
            }
            const setting = this.frontendSettingStore.lists || {};
            return setting.site_phone_verification !== askEnum.NO;
        },
        steps() {
            const all = [
                {key: "phone", label: this.$t("label.phone")},
                {key: "verify", label: this.$t("label.verify")},
                {key: "owner", label: this.$t("label.owner")},
                {key: "restaurant", label: this.$t("label.restaurant")},
                {key: "done", label: this.$t("label.done")}
            ];
            return this.includeVerify ? all : all.filter((step) => step.key !== "verify");
        },
        currentIndex() {
            const index = this.steps.findIndex((step) => step.key === this.current);
            return index >= 0 ? index : 0;
        }
    },
    methods: {
        isDone(index) {
            return index < this.currentIndex || this.current === "done";
        },
        isActive(index) {
            return index === this.currentIndex && this.current !== "done";
        },
        circleClass(step, index) {
            if (this.isDone(index) || (this.current === "done" && index === this.steps.length - 1)) {
                return "bg-primary text-white";
            }
            if (this.isActive(index)) {
                return "bg-primary text-white ring-4 ring-primary/15";
            }
            return "bg-[#eef0f6] text-paragraph";
        },
        labelClass(step, index) {
            if (this.isDone(index) || this.isActive(index) || this.current === "done") {
                return "text-heading";
            }
            return "text-paragraph";
        }
    }
}
</script>
