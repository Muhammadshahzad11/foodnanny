<template>
    <LoadingComponent :props="loading" />

    <div class="db-card-header border-none flex items-center justify-between">
        <h3 class="db-card-title">{{ $t('label.translations') }}</h3>
        <button v-if="languages.length > 0" type="button" class="db-btn text-white bg-primary py-2 px-3" @click="openForm()">
            <i class="lab lab-line-add-circle"></i>
            <span>{{ $t('label.add_translation') }}</span>
        </button>
    </div>

    <div class="db-card-body">
        <div v-if="languages.length === 0" class="text-sm text-paragraph">
            {{ $t('label.no_language_found') }}
        </div>

        <template v-else>
            <div class="mb-6">
                <div class="flex items-center justify-between mb-1">
                    <span class="db-field-title mb-0 uppercase">{{ $t('label.translation_progress') }}</span>
                    <span class="text-sm font-semibold text-primary">{{ completedCount }}/{{ languages.length }}</span>
                </div>
                <div class="w-full h-2 bg-gray-200 rounded-full overflow-hidden">
                    <div class="h-full bg-primary rounded-full transition-all" :style="{ width: overallProgress + '%' }"></div>
                </div>
            </div>

            <label class="db-field-title uppercase">{{ $t('label.translated_languages') }}</label>
            <div class="db-table-responsive mt-2">
                <table class="db-table stripe">
                    <thead class="db-table-head">
                        <tr class="db-table-head-tr">
                            <th class="db-table-head-th">{{ $t('label.language') }}</th>
                            <th class="db-table-head-th">{{ $t('label.name') }}</th>
                            <th class="db-table-head-th">{{ $t('label.status') }}</th>
                            <th class="db-table-head-th">{{ $t('label.action') }}</th>
                        </tr>
                    </thead>
                    <tbody class="db-table-body">
                        <tr v-for="lang in languages" :key="lang.id" class="db-table-body-tr">
                            <td class="db-table-body-td">{{ lang.name }} ({{ lang.code }})</td>
                            <td class="db-table-body-td">
                                <span v-if="translationName(lang.code)">{{ textShortener(translationName(lang.code), 40) }}</span>
                                <span v-else class="text-gray-400">—</span>
                            </td>
                            <td class="db-table-body-td">
                                <span :class="statusCompleted(lang.code) ? 'db-table-badge text-green-600 bg-green-100' : 'db-table-badge text-red-600 bg-red-100'">
                                    {{ statusCompleted(lang.code) ? $t('label.completed') : $t('label.pending') }}
                                </span>
                            </td>
                            <td class="db-table-body-td">
                                <button type="button" @click="openForm(lang.code)"
                                    class="w-8 h-8 inline-flex items-center justify-center rounded-md border border-green-500 text-green-600 transition hover:bg-green-50">
                                    <i class="lab lab-line-edit"></i>
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </template>
    </div>

    <ItemTranslationModalComponent :props="modalProps" />
</template>

<script>
import LoadingComponent from "../../common/LoadingComponent.vue";
import appService from "../../../services/appService.js";
import statusEnum from "../../../enums/modules/statusEnum.js";
import { useModal } from "../../../composables/modal.js";
import { useItemStore } from "../../../stores/item.js";
import { useLanguageStore } from "../../../stores/language.js";
import ItemTranslationModalComponent from "./ItemTranslationModalComponent.vue";

const KEYS = ["name", "description"];

export default {
    name: "ItemTranslationComponent",
    components: { LoadingComponent, ItemTranslationModalComponent },
    setup() {
        const itemStore = useItemStore();
        const languageStore = useLanguageStore();
        return { itemStore, languageStore };
    },
    data() {
        return {
            loading: { isActive: false },
            languages: [],
            totalKeys: KEYS.length,
            textShortener: appService.textShortener,
            modalProps: {
                id: null,
                item: {},
                languages: [],
                form: { translations: {} },
                selectedLanguage: null,
                isEditing: false,
            },
        };
    },
    computed: {
        item: function () {
            return this.itemStore.show || {};
        },
        completedCount: function () {
            return this.languages.filter(lang => this.filledCount(lang.code) === this.totalKeys).length;
        },
        overallProgress: function () {
            if (this.languages.length === 0) return 0;
            return Math.round((this.completedCount / this.languages.length) * 100);
        },
    },
    watch: {
        "item.translations": {
            handler: function (translations) {
                if (translations && this.languages.length > 0) {
                    this.initForm();
                }
            },
            deep: true,
        },
    },
    mounted() {
        this.modalProps.id = this.$route.params.id;
        this.fetchLanguages();
    },
    methods: {
        fetchLanguages: function () {
            this.loading.isActive = true;
            this.languageStore.fetch({ paginate: 0, status: statusEnum.ACTIVE, order_type: "asc", vuex: false }).then(res => {
                this.languages = res.data.data;
                this.modalProps.languages = res.data.data;
                this.initForm();
                this.loading.isActive = false;
            }).catch(() => {
                this.loading.isActive = false;
            });
        },
        initForm: function () {
            const existing = this.item.translations || [];
            const translations = {};
            this.languages.forEach(lang => {
                const row = {};
                KEYS.forEach(key => {
                    const found = existing.find(t => t.locale === lang.code && t.key === key);
                    row[key] = found ? found.value : "";
                });
                translations[lang.code] = row;
            });
            this.modalProps.form.translations = translations;
        },
        openForm: function (code = null) {
            this.modalProps.item = this.item;
            this.modalProps.isEditing = !!code;
            this.modalProps.selectedLanguage = code || (this.languages.length ? this.languages[0].code : null);
            useModal().openModal("itemTranslationModal");
        },
        hasValue: function (value) {
            if (!value) return false;
            return value.toString().replace(/<[^>]*>/g, "").trim().length > 0;
        },
        filledCount: function (code) {
            const t = this.modalProps.form.translations[code];
            if (!t) return 0;
            return KEYS.filter(key => this.hasValue(t[key])).length;
        },
        statusCompleted: function (code) {
            return this.filledCount(code) === this.totalKeys;
        },
        translationName: function (code) {
            const t = this.modalProps.form.translations[code];
            return t && this.hasValue(t.name) ? t.name : "";
        },
    },
};
</script>
