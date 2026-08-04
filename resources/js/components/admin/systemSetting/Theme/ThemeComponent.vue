<template>
    <LoadingComponent :props="loading"/>

    <div id="company" class="db-card db-tab-div active">
        <div class="db-card-header">
            <h3 class="db-card-title">{{ $t("menu.theme") }}</h3>
        </div>
        <div class="db-card-body">
            <form @submit.prevent="save">
                <div class="form-row">
                    <div class="form-col-12 sm:form-col-6">
                        <label for="theme_logo" class="db-field-title">{{ $t("label.logo") }} (recommended 320×130)</label>
                        <input @change="changeLogo" v-bind:class="errors.theme_logo ? 'invalid' : ''" id="theme_logo" type="file" class="db-field-control" ref="themeLogoProperty" accept="image/png, image/jpeg, image/jpg"/>
                        <small class="db-field-alert" v-if="errors.theme_logo">{{ errors.theme_logo[0] }}</small>
                        <img class="h-16 w-auto max-w-[280px] object-contain rounded-lg mt-2 bg-slate-50 p-2" alt="logo" v-if="theme_logo_reader" :src="theme_logo_reader"/>
                    </div>

                    <div class="form-col-12 sm:form-col-6">
                        <label for="fav_icon" class="db-field-title"> {{ $t("label.fav_icon") }} (1:1) </label>
                        <input @change="changeFavIcon" v-bind:class="errors.theme_favicon_logo ? 'invalid' : ''" id="fav_icon" type="file" class="db-field-control" ref="themeFaviconLogoProperty" accept="image/png, image/jpeg, image/jpg"/>
                        <small class="db-field-alert" v-if="errors.theme_favicon_logo">{{ errors.theme_favicon_logo[0] }}</small>
                        <img class="w-16 h-16 object-contain rounded-lg mt-2 bg-slate-50 p-2" alt="logo" v-if="theme_favicon_logo_reader" :src="theme_favicon_logo_reader"/>
                    </div>
                    <div class="form-col-12 sm:form-col-6">
                        <label for="footer_logo" class="db-field-title"> {{ $t("label.footer_logo") }} (recommended 320×130) </label>
                        <input @change="changeFooterLogo" v-bind:class="errors.theme_footer_logo ? 'invalid' : ''" id="footer_logo" type="file" class="db-field-control" ref="themeFooterLogoProperty" accept="image/png, image/jpeg, image/jpg"/>
                        <small class="db-field-alert" v-if="errors.theme_footer_logo">{{ errors.theme_footer_logo[0] }}</small>
                        <img class="h-16 w-auto max-w-[280px] object-contain rounded-lg mt-2 bg-slate-50 p-2" alt="logo" v-if="theme_footer_logo_reader" :src="theme_footer_logo_reader"/>
                    </div>

                    <div class="form-col-12 sm:form-col-6">
                        <label for="theme_primary_color" class="db-field-title">{{ $t("label.primary_color") }}</label>
                        <div class="flex items-center gap-2">
                            <input id="theme_primary_color" type="color" v-model="theme_primary_color" class="w-12 h-10 p-1 rounded-lg border border-slate-200 cursor-pointer bg-white shrink-0"/>
                            <input v-model="theme_primary_color" type="text" v-bind:class="errors.theme_primary_color ? 'invalid' : ''" class="db-field-control uppercase" placeholder="#148A3C" maxlength="7"/>
                        </div>
                        <small class="db-field-alert" v-if="errors.theme_primary_color">{{ errors.theme_primary_color[0] }}</small>
                    </div>

                    <div class="form-col-12 sm:form-col-6">
                        <label for="theme_secondary_color" class="db-field-title">{{ $t("label.secondary_color") }}</label>
                        <div class="flex items-center gap-2">
                            <input id="theme_secondary_color" type="color" v-model="theme_secondary_color" class="w-12 h-10 p-1 rounded-lg border border-slate-200 cursor-pointer bg-white shrink-0"/>
                            <input v-model="theme_secondary_color" type="text" v-bind:class="errors.theme_secondary_color ? 'invalid' : ''" class="db-field-control uppercase" placeholder="#0A3D28" maxlength="7"/>
                        </div>
                        <small class="db-field-alert" v-if="errors.theme_secondary_color">{{ errors.theme_secondary_color[0] }}</small>
                    </div>

                    <div class="form-col-12">
                        <label class="db-field-title">{{ $t("label.color_presets") }}</label>
                        <div class="flex flex-wrap items-center gap-3">
                            <button v-for="(preset, index) in colorPresets" :key="index" type="button" @click="applyPreset(preset)" :title="preset.name"
                                    class="w-9 h-9 rounded-full border border-slate-200 shadow-sm overflow-hidden flex transition-transform duration-200 hover:scale-110">
                                <span class="w-1/2 h-full" :style="{ backgroundColor: preset.primary }"></span>
                                <span class="w-1/2 h-full" :style="{ backgroundColor: preset.secondary }"></span>
                            </button>
                            <button type="button" @click="resetColors" class="inline-flex items-center gap-1 px-4 h-9 rounded-lg border border-slate-200 text-sm text-heading hover:bg-slate-50">
                                <i class="lab lab-line-rotate-right text-base"></i>
                                <span>{{ $t("button.reset") }}</span>
                            </button>
                        </div>
                    </div>

                    <div class="form-col-12 mt-5">
                        <button type="submit" class="db-btn text-white bg-primary">
                            <i class="lab lab-fill-save text-base"></i>
                            <span>{{ $t("button.save") }}</span>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</template>

<script>
import LoadingComponent from "../../../common/LoadingComponent.vue";
import alertService from "../../../../services/alertService.js";
import {useThemeStore} from "../../../../stores/theme.js";

const DEFAULT_PRIMARY_COLOR = "#148A3C";
const DEFAULT_SECONDARY_COLOR = "#0A3D28";

export default {
    name: "ThemeComponent",
    components: {LoadingComponent},
    setup() {
        const themeStore = useThemeStore();
        return {
            themeStore
        }
    },
    data() {
        return {
            loading: {
                isActive: false
            },
            theme_logo               : "",
            theme_logo_reader        : "",
            theme_favicon_logo       : "",
            theme_favicon_logo_reader: "",
            theme_footer_logo        : "",
            theme_footer_logo_reader : "",
            theme_primary_color      : DEFAULT_PRIMARY_COLOR,
            theme_secondary_color    : DEFAULT_SECONDARY_COLOR,
            colorPresets             : [
                {name: "Cost to Cost", primary: "#148A3C", secondary: "#0A3D28"},
                {name: "Fresh Leaf", primary: "#52B448", secondary: "#06402B"},
                {name: "Forest", primary: "#0B6B38", secondary: "#052A1C"},
                {name: "Orange", primary: "#F36805", secondary: "#1F1F39"},
                {name: "Red", primary: "#E63946", secondary: "#1D3557"},
                {name: "Blue", primary: "#426EFF", secondary: "#0F172A"},
                {name: "Purple", primary: "#A953FF", secondary: "#2E1065"},
                {name: "Pink", primary: "#FF4773", secondary: "#831843"}
            ],
            errors                   : {}
        };
    },
    watch: {
        theme_primary_color() {
            this.applyPreview();
        },
        theme_secondary_color() {
            this.applyPreview();
        }
    },
    mounted() {
        this.list();
    },
    methods: {
        changeLogo: function (e) {
            this.theme_logo = e.target.files[0];
        },
        changeFavIcon: function (e) {
            this.theme_favicon_logo = e.target.files[0];
        },
        changeFooterLogo: function (e) {
            this.theme_footer_logo = e.target.files[0];
        },
        hexToRgbTriplet: function (hex) {
            let value = (hex || "").toString().replace("#", "").trim();
            if (value.length === 3) {
                value = value.split("").map((c) => c + c).join("");
            }
            if (!/^[0-9a-fA-F]{6}$/.test(value)) {
                return null;
            }
            const r = parseInt(value.substring(0, 2), 16);
            const g = parseInt(value.substring(2, 4), 16);
            const b = parseInt(value.substring(4, 6), 16);
            return `${r} ${g} ${b}`;
        },
        applyPreview: function () {
            const primary = this.hexToRgbTriplet(this.theme_primary_color);
            const secondary = this.hexToRgbTriplet(this.theme_secondary_color);
            if (primary) {
                document.documentElement.style.setProperty("--primary", primary);
            }
            if (secondary) {
                document.documentElement.style.setProperty("--secondary", secondary);
            }
        },
        applyPreset: function (preset) {
            this.theme_primary_color = preset.primary;
            this.theme_secondary_color = preset.secondary;
        },
        resetColors: function () {
            this.theme_primary_color = DEFAULT_PRIMARY_COLOR;
            this.theme_secondary_color = DEFAULT_SECONDARY_COLOR;
        },
        list: function () {
            this.loading.isActive = true;
            this.themeStore.fetch().then((res) => {
                this.theme_logo_reader         = res.data.data.theme_logo;
                this.theme_favicon_logo_reader = res.data.data.theme_favicon_logo;
                this.theme_footer_logo_reader  = res.data.data.theme_footer_logo;
                this.theme_primary_color       = res.data.data.theme_primary_color || DEFAULT_PRIMARY_COLOR;
                this.theme_secondary_color     = res.data.data.theme_secondary_color || DEFAULT_SECONDARY_COLOR;
                this.loading.isActive          = false;
            }).catch((err) => {
                this.loading.isActive = false;
            });
        },
        save: function () {
            try {
                const fd = new FormData();
                if (this.theme_logo) {
                    fd.append("theme_logo", this.theme_logo);
                }
                if (this.theme_favicon_logo) {
                    fd.append("theme_favicon_logo", this.theme_favicon_logo);
                }
                if (this.theme_footer_logo) {
                    fd.append("theme_footer_logo", this.theme_footer_logo);
                }
                fd.append("theme_primary_color", this.theme_primary_color);
                fd.append("theme_secondary_color", this.theme_secondary_color);
                this.loading.isActive = true;
                this.themeStore.save({form: fd}).then((res) => {
                    this.loading.isActive = false;
                    alertService.successFlip(1, this.$t("menu.theme"));
                    this.list();
                    this.theme_logo                           = "";
                    this.theme_favicon_logo                   = "";
                    this.theme_footer_logo                    = "";
                    this.errors                               = {};
                    this.$refs.themeLogoProperty.value        = null;
                    this.$refs.themeFaviconLogoProperty.value = null;
                    this.$refs.themeFooterLogoProperty.value  = null;
                }).catch((err) => {
                    this.loading.isActive = false;
                    if (err.response.data.status !== "undefined" && err.response.data.status === false) {
                        alertService.error(err.response.data.message)
                    } else {
                        this.errors = err.response.data.errors;
                    }
                });
            } catch (err) {
                this.loading.isActive = false;
                alertService.error(err);
            }
        }
    }
};
</script>
