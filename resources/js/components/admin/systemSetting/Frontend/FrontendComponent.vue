<template>
    <LoadingComponent :props="loading"/>
    <div id="order_setup" class="db-card db-tab-div active">
        <div class="db-card-header flex items-center justify-between">
            <h3 class="db-card-title">{{ $t("menu.frontend") }}</h3>
            <div v-if="languages.length > 0" class="relative" ref="langDropdown">
                <button type="button" @click="showLangPicker = !showLangPicker"
                    class="flex items-center gap-2 h-9 px-3.5 rounded-lg border transition-all duration-200"
                    :class="selectedLocale ? 'border-primary bg-primary/5 text-primary' : 'border-[#DBDEE0] bg-white hover:border-primary hover:text-primary'">
                    <template v-if="selectedLocale">
                        <img v-if="selectedLang && selectedLang.image" :src="selectedLang.image" alt="" class="w-4 h-4 rounded-sm flex-shrink-0">
                        <i v-else class="lab lab-line-language text-base"></i>
                        <span class="text-sm font-medium">{{ selectedLang ? selectedLang.name : selectedLocale }}</span>
                    </template>
                    <template v-else>
                        <i class="lab lab-line-language text-base"></i>
                        <span class="text-sm">{{ $t('label.add_translation') }}</span>
                    </template>
                    <i class="lab lab-line-chevron-down text-xs font-semibold transition-transform duration-200" :class="showLangPicker ? 'rotate-180' : ''"></i>
                </button>

                <div v-if="showLangPicker"
                    class="absolute right-0 top-11 w-52 bg-white rounded-xl shadow-[0_4px_24px_rgba(0,0,0,0.10)] border border-[#DBDEE0] z-30 py-1.5 overflow-hidden">
                    <button type="button" @click="selectLocale(null)"
                        class="flex items-center gap-2.5 w-full px-3 py-2 text-sm transition-colors duration-150"
                        :class="!selectedLocale ? 'bg-primary/10 text-primary font-medium' : 'hover:bg-slate-50 text-paragraph'">
                        <i class="lab lab-line-global text-base flex-shrink-0"></i>
                        <span>English</span>
                        <span class="text-xs opacity-60 ml-0.5">(Default)</span>
                        <i v-if="!selectedLocale" class="lab lab-line-check ml-auto text-primary text-sm"></i>
                    </button>
                    <div class="my-1 border-t border-[#F0F0F0]"></div>
                    <button v-for="lang in languages" :key="lang.id" type="button"
                        @click="selectLocale(lang.code)"
                        class="flex items-center gap-2.5 w-full px-3 py-2 text-sm transition-colors duration-150"
                        :class="selectedLocale === lang.code ? 'bg-primary/10 text-primary font-medium' : 'hover:bg-slate-50 text-paragraph'">
                        <img v-if="lang.image" :src="lang.image" alt="" class="w-4 h-4 rounded-sm flex-shrink-0 object-cover">
                        <i v-else class="lab lab-line-language text-base flex-shrink-0"></i>
                        <span>{{ lang.name }}</span>
                        <span class="text-xs opacity-50 ml-0.5 uppercase">{{ lang.code }}</span>
                        <i v-if="selectedLocale === lang.code" class="lab lab-line-check ml-auto text-primary text-sm"></i>
                    </button>
                </div>
            </div>
        </div>
        <div class="db-card-body">
            <form @submit.prevent="save">

                <!-- Hero section -->
                <fieldset class="p-4 mb-6 border border-[#DBDEE0]">
                    <legend class="py-1.5 px-4 text-base font-semibold capitalize border border-[#DBDEE0] text-primary">
                        {{ $t('menu.hero_section') }}
                    </legend>
                    <div class="form-row">
                        <div class="form-col-12 sm:form-col-6">
                            <label class="db-field-title" :class="isTranslating ? '' : 'required'">
                                {{ $t("label.title") }}
                            </label>
                            <template v-if="!isTranslating">
                                <input v-model="form.frontend_hero_section_title"
                                    :class="errors.frontend_hero_section_title ? 'invalid' : ''"
                                    type="text" id="frontend_hero_section_title" class="db-field-control"/>
                                <small class="db-field-alert" v-if="errors.frontend_hero_section_title">
                                    {{ errors.frontend_hero_section_title[0] }}
                                </small>
                            </template>
                            <template v-else>
                                <input v-model="translationForm.frontend_hero_section_title"
                                    type="text" class="db-field-control"
                                    :dir="isRtl ? 'rtl' : 'ltr'"
                                    :placeholder="form.frontend_hero_section_title || ''"/>
                            </template>
                        </div>

                        <div class="form-col-12 sm:form-col-6">
                            <label class="db-field-title" :class="isTranslating ? '' : 'required'">
                                {{ $t("label.sub_title") }}
                            </label>
                            <template v-if="!isTranslating">
                                <input v-model="form.frontend_hero_section_sub_title"
                                    :class="errors.frontend_hero_section_sub_title ? 'invalid' : ''"
                                    type="text" id="frontend_hero_section_sub_title" class="db-field-control"/>
                                <small class="db-field-alert" v-if="errors.frontend_hero_section_sub_title">
                                    {{ errors.frontend_hero_section_sub_title[0] }}
                                </small>
                            </template>
                            <template v-else>
                                <input v-model="translationForm.frontend_hero_section_sub_title"
                                    type="text" class="db-field-control"
                                    :dir="isRtl ? 'rtl' : 'ltr'"
                                    :placeholder="form.frontend_hero_section_sub_title || ''"/>
                            </template>
                        </div>

                        <div v-if="!isTranslating" class="form-col-12 sm:form-col-6">
                            <label for="hero_section_image" class="db-field-title">
                                {{ $t("label.image") }} (948px, 920px)
                            </label>
                            <input @change="changeHeaderSectionImage"
                                :class="errors.hero_section_image ? 'invalid' : ''"
                                id="hero_section_image" type="file" class="db-field-control"
                                ref="heroImageProperty" accept="image/png, image/jpeg, image/jpg"/>
                            <small class="db-field-alert" v-if="errors.hero_section_image">
                                {{ errors.hero_section_image[0] }}
                            </small>
                            <img class="max-w-[150px] max-h-[120px] object-fill rounded-lg mt-2"
                                alt="image" v-if="hero_section_image_reader" :src="hero_section_image_reader"/>
                        </div>
                    </div>
                </fieldset>

                <!-- About section -->
                <fieldset class="p-4 mb-6 border border-[#DBDEE0]">
                    <legend class="py-1.5 px-4 text-base font-semibold capitalize border border-[#DBDEE0] text-primary">
                        {{ $t('menu.about_section') }}
                    </legend>
                    <div class="form-row">
                        <div class="form-col-12 sm:form-col-6">
                            <label class="db-field-title" :class="isTranslating ? '' : 'required'">
                                {{ $t("label.title") }}
                            </label>
                            <template v-if="!isTranslating">
                                <input v-model="form.frontend_about_title"
                                    :class="errors.frontend_about_title ? 'invalid' : ''"
                                    type="text" id="frontend_about_title" class="db-field-control"/>
                                <small class="db-field-alert" v-if="errors.frontend_about_title">
                                    {{ errors.frontend_about_title[0] }}
                                </small>
                            </template>
                            <template v-else>
                                <input v-model="translationForm.frontend_about_title"
                                    type="text" class="db-field-control"
                                    :dir="isRtl ? 'rtl' : 'ltr'"
                                    :placeholder="form.frontend_about_title || ''"/>
                            </template>
                        </div>
                    </div>
                </fieldset>

                <!-- App section -->
                <fieldset class="p-4 mb-6 border border-[#DBDEE0]">
                    <legend class="py-1.5 px-4 text-base font-semibold capitalize border border-[#DBDEE0] text-primary">
                        {{ $t('menu.app_section') }}
                    </legend>
                    <div class="form-row">
                        <div class="form-col-12 sm:form-col-6">
                            <label class="db-field-title" :class="isTranslating ? '' : 'required'">
                                {{ $t("label.title") }}
                            </label>
                            <template v-if="!isTranslating">
                                <input v-model="form.frontend_app_section_title"
                                    :class="errors.frontend_app_section_title ? 'invalid' : ''"
                                    type="text" id="frontend_app_section_title" class="db-field-control"/>
                                <small class="db-field-alert" v-if="errors.frontend_app_section_title">
                                    {{ errors.frontend_app_section_title[0] }}
                                </small>
                            </template>
                            <template v-else>
                                <input v-model="translationForm.frontend_app_section_title"
                                    type="text" class="db-field-control"
                                    :dir="isRtl ? 'rtl' : 'ltr'"
                                    :placeholder="form.frontend_app_section_title || ''"/>
                            </template>
                        </div>

                        <div v-if="!isTranslating" class="form-col-12 sm:form-col-6">
                            <label for="frontend_app_section_android_app_link" class="db-field-title required">
                                {{ $t("label.android_app_link") }}
                            </label>
                            <input v-model="form.frontend_app_section_android_app_link"
                                :class="errors.frontend_app_section_android_app_link ? 'invalid' : ''"
                                type="text" id="frontend_app_section_android_app_link" class="db-field-control"/>
                            <small class="db-field-alert" v-if="errors.frontend_app_section_android_app_link">
                                {{ errors.frontend_app_section_android_app_link[0] }}
                            </small>
                        </div>

                        <div v-if="!isTranslating" class="form-col-12 sm:form-col-6">
                            <label for="frontend_app_section_iso_app_link" class="db-field-title required">
                                {{ $t("label.iso_app_link") }}
                            </label>
                            <input v-model="form.frontend_app_section_iso_app_link"
                                :class="errors.frontend_app_section_iso_app_link ? 'invalid' : ''"
                                type="text" id="frontend_app_section_iso_app_link" class="db-field-control"/>
                            <small class="db-field-alert" v-if="errors.frontend_app_section_iso_app_link">
                                {{ errors.frontend_app_section_iso_app_link[0] }}
                            </small>
                        </div>

                        <div v-if="!isTranslating" class="form-col-12 sm:form-col-6">
                            <label for="app_section_image" class="db-field-title">
                                {{ $t("label.image") }} (496px, 494px)
                            </label>
                            <input @change="changeAppSectionImage"
                                :class="errors.app_section_image ? 'invalid' : ''"
                                id="app_section_image" type="file" class="db-field-control"
                                ref="appImageProperty" accept="image/png, image/jpeg, image/jpg"/>
                            <small class="db-field-alert" v-if="errors.app_section_image">
                                {{ errors.app_section_image[0] }}
                            </small>
                            <img class="max-w-[150px] max-h-[120px] object-fill rounded-lg mt-2"
                                alt="image" v-if="app_section_image_reader" :src="app_section_image_reader"/>
                        </div>
                    </div>
                </fieldset>

                <!-- Benefit section -->
                <fieldset class="p-4 mb-6 border border-[#DBDEE0]">
                    <legend class="py-1.5 px-4 text-base font-semibold capitalize border border-[#DBDEE0] text-primary">
                        {{ $t('menu.benefit_section') }}
                    </legend>
                    <div class="form-row">
                        <div class="form-col-12 sm:form-col-6">
                            <label class="db-field-title" :class="isTranslating ? '' : 'required'">
                                {{ $t("label.title") }}
                            </label>
                            <template v-if="!isTranslating">
                                <input v-model="form.frontend_benefit_title"
                                    :class="errors.frontend_benefit_title ? 'invalid' : ''"
                                    type="text" id="frontend_benefit_title" class="db-field-control"/>
                                <small class="db-field-alert" v-if="errors.frontend_benefit_title">
                                    {{ errors.frontend_benefit_title[0] }}
                                </small>
                            </template>
                            <template v-else>
                                <input v-model="translationForm.frontend_benefit_title"
                                    type="text" class="db-field-control"
                                    :dir="isRtl ? 'rtl' : 'ltr'"
                                    :placeholder="form.frontend_benefit_title || ''"/>
                            </template>
                        </div>
                    </div>
                </fieldset>

                <!-- Restaurant section -->
                <fieldset class="p-4 mb-6 border border-[#DBDEE0]">
                    <legend class="py-1.5 px-4 text-base font-semibold capitalize border border-[#DBDEE0] text-primary">
                        {{ $t('menu.restaurant_section') }}
                    </legend>
                    <div class="form-row">
                        <div class="form-col-12 sm:form-col-6">
                            <label class="db-field-title" :class="isTranslating ? '' : 'required'">
                                {{ $t("label.title") }}
                            </label>
                            <template v-if="!isTranslating">
                                <input v-model="form.frontend_restaurant_section_title"
                                    :class="errors.frontend_restaurant_section_title ? 'invalid' : ''"
                                    type="text" id="frontend_restaurant_section_title" class="db-field-control"/>
                                <small class="db-field-alert" v-if="errors.frontend_restaurant_section_title">
                                    {{ errors.frontend_restaurant_section_title[0] }}
                                </small>
                            </template>
                            <template v-else>
                                <input v-model="translationForm.frontend_restaurant_section_title"
                                    type="text" class="db-field-control"
                                    :dir="isRtl ? 'rtl' : 'ltr'"
                                    :placeholder="form.frontend_restaurant_section_title || ''"/>
                            </template>
                        </div>

                        <div class="form-col-12 sm:form-col-6">
                            <label class="db-field-title" :class="isTranslating ? '' : 'required'">
                                {{ $t("label.sub_title") }}
                            </label>
                            <template v-if="!isTranslating">
                                <input v-model="form.frontend_restaurant_section_sub_title"
                                    :class="errors.frontend_restaurant_section_sub_title ? 'invalid' : ''"
                                    type="text" id="frontend_restaurant_section_sub_title" class="db-field-control"/>
                                <small class="db-field-alert" v-if="errors.frontend_restaurant_section_sub_title">
                                    {{ errors.frontend_restaurant_section_sub_title[0] }}
                                </small>
                            </template>
                            <template v-else>
                                <input v-model="translationForm.frontend_restaurant_section_sub_title"
                                    type="text" class="db-field-control"
                                    :dir="isRtl ? 'rtl' : 'ltr'"
                                    :placeholder="form.frontend_restaurant_section_sub_title || ''"/>
                            </template>
                        </div>

                        <div v-if="!isTranslating" class="form-col-12 sm:form-col-6">
                            <label for="restaurant_section_image" class="db-field-title">
                                {{ $t("label.image") }} (551px, 345px)
                            </label>
                            <input @change="changeRestaurantSectionImage"
                                :class="errors.restaurant_section_image ? 'invalid' : ''"
                                id="restaurant_section_image" type="file" class="db-field-control"
                                ref="restaurantImageProperty" accept="image/png, image/jpeg, image/jpg"/>
                            <small class="db-field-alert" v-if="errors.restaurant_section_image">
                                {{ errors.restaurant_section_image[0] }}
                            </small>
                            <img class="max-w-[150px] max-h-[120px] object-fill rounded-lg mt-2"
                                alt="image" v-if="restaurant_section_image_reader" :src="restaurant_section_image_reader"/>
                        </div>
                    </div>
                </fieldset>

                <!-- Delivery section -->
                <fieldset class="p-4 mb-6 border border-[#DBDEE0]">
                    <legend class="py-1.5 px-4 text-base font-semibold capitalize border border-[#DBDEE0] text-primary">
                        {{ $t('menu.delivery_section') }}
                    </legend>
                    <div class="form-row">
                        <div class="form-col-12 sm:form-col-6">
                            <label class="db-field-title" :class="isTranslating ? '' : 'required'">
                                {{ $t("label.title") }}
                            </label>
                            <template v-if="!isTranslating">
                                <input v-model="form.frontend_delivery_section_title"
                                    :class="errors.frontend_delivery_section_title ? 'invalid' : ''"
                                    type="text" id="frontend_delivery_section_title" class="db-field-control"/>
                                <small class="db-field-alert" v-if="errors.frontend_delivery_section_title">
                                    {{ errors.frontend_delivery_section_title[0] }}
                                </small>
                            </template>
                            <template v-else>
                                <input v-model="translationForm.frontend_delivery_section_title"
                                    type="text" class="db-field-control"
                                    :dir="isRtl ? 'rtl' : 'ltr'"
                                    :placeholder="form.frontend_delivery_section_title || ''"/>
                            </template>
                        </div>

                        <div class="form-col-12 sm:form-col-6">
                            <label class="db-field-title" :class="isTranslating ? '' : 'required'">
                                {{ $t("label.sub_title") }}
                            </label>
                            <template v-if="!isTranslating">
                                <input v-model="form.frontend_delivery_section_sub_title"
                                    :class="errors.frontend_delivery_section_sub_title ? 'invalid' : ''"
                                    type="text" id="frontend_delivery_section_sub_title" class="db-field-control"/>
                                <small class="db-field-alert" v-if="errors.frontend_delivery_section_sub_title">
                                    {{ errors.frontend_delivery_section_sub_title[0] }}
                                </small>
                            </template>
                            <template v-else>
                                <input v-model="translationForm.frontend_delivery_section_sub_title"
                                    type="text" class="db-field-control"
                                    :dir="isRtl ? 'rtl' : 'ltr'"
                                    :placeholder="form.frontend_delivery_section_sub_title || ''"/>
                            </template>
                        </div>

                        <div v-if="!isTranslating" class="form-col-12 sm:form-col-6">
                            <label for="delivery_section_image" class="db-field-title">
                                {{ $t("label.image") }} (551px, 345px)
                            </label>
                            <input @change="changeDeliverySectionImage"
                                :class="errors.delivery_section_image ? 'invalid' : ''"
                                id="delivery_section_image" type="file" class="db-field-control"
                                ref="deliveryImageProperty" accept="image/png, image/jpeg, image/jpg"/>
                            <small class="db-field-alert" v-if="errors.delivery_section_image">
                                {{ errors.delivery_section_image[0] }}
                            </small>
                            <img class="max-w-[150px] max-h-[120px] object-fill rounded-lg mt-2"
                                alt="image" v-if="delivery_section_image_reader" :src="delivery_section_image_reader"/>
                        </div>
                    </div>
                </fieldset>

                <button type="submit" class="db-btn text-white bg-primary mt-3">
                    <i class="lab lab-fill-save text-base"></i>
                    <span>{{ $t("button.save") }}</span>
                </button>
            </form>
        </div>
    </div>
</template>

<script>
import LoadingComponent from "../../../common/LoadingComponent.vue";
import alertService from "../../../../services/alertService.js";
import statusEnum from "../../../../enums/modules/statusEnum.js";
import { useFrontendStore } from "../../../../stores/frontend.js";
import { useLanguageStore } from "../../../../stores/language.js";

const RTL_CODES = ["ar", "he", "fa", "ur", "ps", "sd", "ug", "yi", "dv", "ku"];

const TRANSLATABLE_KEYS = [
    'frontend_hero_section_title',
    'frontend_hero_section_sub_title',
    'frontend_app_section_title',
    'frontend_about_title',
    'frontend_benefit_title',
    'frontend_restaurant_section_title',
    'frontend_restaurant_section_sub_title',
    'frontend_delivery_section_title',
    'frontend_delivery_section_sub_title',
];

function emptyTranslationForm() {
    return Object.fromEntries(TRANSLATABLE_KEYS.map(k => [k, '']));
}

export default {
    name: "FrontendComponent",
    components: { LoadingComponent },
    setup() {
        const frontendStore = useFrontendStore();
        const languageStore = useLanguageStore();
        return { frontendStore, languageStore };
    },
    data() {
        return {
            loading: { isActive: false },
            form: {
                frontend_hero_section_title          : null,
                frontend_hero_section_sub_title      : null,
                frontend_app_section_title           : null,
                frontend_app_section_android_app_link: null,
                frontend_app_section_iso_app_link    : null,
                frontend_about_title                 : null,
                frontend_benefit_title               : null,
                frontend_restaurant_section_title    : null,
                frontend_restaurant_section_sub_title: null,
                frontend_delivery_section_title      : null,
                frontend_delivery_section_sub_title  : null
            },
            hero_section_image             : "",
            hero_section_image_reader      : "",
            app_section_image              : "",
            app_section_image_reader       : "",
            restaurant_section_image       : "",
            restaurant_section_image_reader: "",
            delivery_section_image         : "",
            delivery_section_image_reader  : "",
            errors                         : {},
            selectedLocale                 : null,
            showLangPicker                 : false,
            languages                      : [],
            translationForm                : emptyTranslationForm(),
        };
    },
    computed: {
        isTranslating: function () {
            return this.selectedLocale !== null;
        },
        isRtl: function () {
            return RTL_CODES.includes(this.selectedLocale);
        },
        selectedLang: function () {
            return this.languages.find(l => l.code === this.selectedLocale) || null;
        },
    },
    mounted() {
        this.list();
        this.fetchLanguages();
        document.addEventListener('click', this.handleClickOutside);
    },
    beforeUnmount() {
        document.removeEventListener('click', this.handleClickOutside);
    },
    methods: {
        handleClickOutside: function (e) {
            if (this.$refs.langDropdown && !this.$refs.langDropdown.contains(e.target)) {
                this.showLangPicker = false;
            }
        },
        fetchLanguages: function () {
            this.languageStore.fetch({ paginate: 0, status: statusEnum.ACTIVE, order_type: 'asc', vuex: false }).then(res => {
                // English is the base language (served from the settings table, not translations),
                // so it is represented by the "English (Default)" option and excluded here.
                this.languages = res.data.data.filter(lang => lang.code !== 'en');
            }).catch(() => {});
        },
        selectLocale: function (code) {
            this.selectedLocale = code;
            this.showLangPicker = false;
            this.onLocaleChange();
        },
        onLocaleChange: function () {
            if (!this.selectedLocale) {
                this.translationForm = emptyTranslationForm();
                return;
            }
            this.loading.isActive = true;
            this.frontendStore.fetchTranslations(this.selectedLocale).then(res => {
                const data = res.data.data;
                this.translationForm = Object.fromEntries(TRANSLATABLE_KEYS.map(k => [k, data[k] || '']));
                this.loading.isActive = false;
            }).catch(() => {
                this.loading.isActive = false;
            });
        },
        changeHeaderSectionImage: function (e) {
            this.hero_section_image = e.target.files[0];
        },
        changeAppSectionImage: function (e) {
            this.app_section_image = e.target.files[0];
        },
        changeRestaurantSectionImage: function (e) {
            this.restaurant_section_image = e.target.files[0];
        },
        changeDeliverySectionImage: function (e) {
            this.delivery_section_image = e.target.files[0];
        },
        list: function () {
            try {
                this.loading.isActive = true;
                this.frontendStore.fetch().then(res => {
                    this.form = {
                        frontend_hero_section_title          : res.data.data.frontend_hero_section_title,
                        frontend_hero_section_sub_title      : res.data.data.frontend_hero_section_sub_title,
                        frontend_app_section_title           : res.data.data.frontend_app_section_title,
                        frontend_app_section_android_app_link: res.data.data.frontend_app_section_android_app_link,
                        frontend_app_section_iso_app_link    : res.data.data.frontend_app_section_iso_app_link,
                        frontend_about_title                 : res.data.data.frontend_about_title,
                        frontend_benefit_title               : res.data.data.frontend_benefit_title,
                        frontend_restaurant_section_title    : res.data.data.frontend_restaurant_section_title,
                        frontend_restaurant_section_sub_title: res.data.data.frontend_restaurant_section_sub_title,
                        frontend_delivery_section_title      : res.data.data.frontend_delivery_section_title,
                        frontend_delivery_section_sub_title  : res.data.data.frontend_delivery_section_sub_title
                    };
                    this.hero_section_image_reader       = res.data.data.frontend_hero_section_image;
                    this.app_section_image_reader        = res.data.data.frontend_app_section_image;
                    this.restaurant_section_image_reader = res.data.data.frontend_restaurant_section_image;
                    this.delivery_section_image_reader   = res.data.data.frontend_delivery_section_image;
                    this.loading.isActive                = false;
                }).catch(() => {
                    this.loading.isActive = false;
                });
            } catch (err) {
                this.loading.isActive = false;
            }
        },
        save: function () {
            if (this.isTranslating) {
                this.saveTranslations();
            } else {
                this.saveBase();
            }
        },
        saveTranslations: function () {
            try {
                this.loading.isActive = true;
                const formData = { locale: this.selectedLocale, ...this.translationForm };
                this.frontendStore.saveTranslations({ form: formData }).then(res => {
                    this.loading.isActive = false;
                    alertService.successFlip(1, this.$t("menu.frontend"));
                    const data = res.data.data;
                    this.translationForm = Object.fromEntries(TRANSLATABLE_KEYS.map(k => [k, data[k] || '']));
                }).catch(err => {
                    this.loading.isActive = false;
                    if (err.response?.data?.message) {
                        alertService.error(err.response.data.message);
                    }
                });
            } catch (err) {
                this.loading.isActive = false;
                alertService.error(err);
            }
        },
        saveBase: function () {
            try {
                this.loading.isActive = true;
                const fd = new FormData();
                fd.append("frontend_hero_section_title", this.form.frontend_hero_section_title);
                fd.append("frontend_hero_section_sub_title", this.form.frontend_hero_section_sub_title);
                fd.append("frontend_app_section_title", this.form.frontend_app_section_title);
                fd.append("frontend_app_section_android_app_link", this.form.frontend_app_section_android_app_link);
                fd.append("frontend_app_section_iso_app_link", this.form.frontend_app_section_iso_app_link);
                fd.append("frontend_about_title", this.form.frontend_about_title);
                fd.append("frontend_benefit_title", this.form.frontend_benefit_title);
                fd.append("frontend_restaurant_section_title", this.form.frontend_restaurant_section_title);
                fd.append("frontend_restaurant_section_sub_title", this.form.frontend_restaurant_section_sub_title);
                fd.append("frontend_delivery_section_title", this.form.frontend_delivery_section_title);
                fd.append("frontend_delivery_section_sub_title", this.form.frontend_delivery_section_sub_title);
                if (this.hero_section_image) {
                    fd.append("frontend_hero_section_image", this.hero_section_image);
                }
                if (this.app_section_image) {
                    fd.append("frontend_app_section_image", this.app_section_image);
                }
                if (this.restaurant_section_image) {
                    fd.append("frontend_restaurant_section_image", this.restaurant_section_image);
                }
                if (this.delivery_section_image) {
                    fd.append("frontend_delivery_section_image", this.delivery_section_image);
                }
                this.frontendStore.save({ form: fd }).then(() => {
                    this.loading.isActive                    = false;
                    alertService.successFlip(1, this.$t("menu.frontend"));
                    this.list();
                    this.hero_section_image                  = "";
                    this.hero_section_image_reader           = "";
                    this.app_section_image                   = "";
                    this.app_section_image_reader            = "";
                    this.restaurant_section_image            = "";
                    this.restaurant_section_image_reader     = "";
                    this.delivery_section_image              = "";
                    this.delivery_section_image_reader       = "";
                    this.errors                              = {};
                    this.$refs.heroImageProperty.value       = null;
                    this.$refs.appImageProperty.value        = null;
                    this.$refs.restaurantImageProperty.value = null;
                    this.$refs.deliveryImageProperty.value   = null;
                }).catch(err => {
                    this.loading.isActive = false;
                    if (err.response.data.status !== "undefined" && err.response.data.status === false) {
                        alertService.error(err.response.data.message);
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
}
</script>
