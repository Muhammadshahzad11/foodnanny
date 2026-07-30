<template>
    <LoadingComponent :props="loading" />

    <div id="itemTranslationModal" class="modal">
        <div class="modal-dialog !max-w-[647px]">
            <div class="modal-header">
                <h3 class="modal-title">{{ props.isEditing ? $t('label.edit_translation') : $t('label.add_translation') }}</h3>
                <button class="modal-close lab-line-close font-bold text-base text-slate-400 hover:text-red-500" @click.prevent="close"></button>
            </div>
            <div class="modal-body">
                <form @submit.prevent="save">
                    <div class="form-row">
                        <div class="form-col-12">
                            <label class="db-field-title required">{{ $t('label.select_language') }}</label>
                            <select v-model="props.selectedLanguage" class="db-field-control">
                                <option :value="null" disabled>{{ $t('label.select_language') }}</option>
                                <option v-for="lang in props.languages" :key="lang.id" :value="lang.code">{{ lang.name }} ({{ lang.code }})</option>
                            </select>
                        </div>

                        <template v-if="props.selectedLanguage && props.form.translations[props.selectedLanguage]">
                            <div class="form-col-12">
                                <label class="db-field-title">{{ $t('label.name') }} ({{ props.selectedLanguage }})</label>
                                <p v-if="props.item.name" class="text-xs text-paragraph bg-gray-50 rounded-md py-1.5 px-2.5 mb-1.5">
                                    <i class="lab lab-line-language"></i> {{ props.item.name }}
                                </p>
                                <input v-model="props.form.translations[props.selectedLanguage].name" :dir="isRtl ? 'rtl' : 'ltr'" type="text" class="db-field-control" />
                            </div>
                            <div class="form-col-12">
                                <label class="db-field-title">{{ $t('label.description') }} ({{ props.selectedLanguage }})</label>
                                <p v-if="props.item.description" class="text-xs text-paragraph bg-gray-50 rounded-md py-1.5 px-2.5 mb-1.5" v-html="props.item.description"></p>
                                <textarea v-model="props.form.translations[props.selectedLanguage].description" :dir="isRtl ? 'rtl' : 'ltr'" rows="4" class="db-field-control"></textarea>
                            </div>
                        </template>

                        <div class="form-col-12">
                            <div class="modal-btns">
                                <button type="button" class="modal-btn-outline modal-close" @click.prevent="close">
                                    <i class="lab lab-fill-close-circle"></i>
                                    <span>{{ $t('button.close') }}</span>
                                </button>
                                <button type="submit" class="db-btn py-2 text-white bg-primary" :disabled="!props.selectedLanguage">
                                    <i class="lab lab-fill-save"></i>
                                    <span>{{ $t('button.save') }}</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>

<script>
import LoadingComponent from "../../common/LoadingComponent.vue";
import alertService from "../../../services/alertService.js";
import { useModal } from "../../../composables/modal.js";
import { useItemStore } from "../../../stores/item.js";

const RTL_CODES = ["ar", "fa", "ur", "he", "ps", "sd"];

export default {
    name: "ItemTranslationModalComponent",
    components: { LoadingComponent },
    props: ["props"],
    setup() {
        const itemStore = useItemStore();
        return { itemStore };
    },
    data() {
        return {
            loading: { isActive: false }
        };
    },
    computed: {
        isRtl: function () {
            return RTL_CODES.includes(this.$props.props.selectedLanguage);
        }
    },
    methods: {
        close: function () {
            useModal().closeModal('itemTranslationModal');
        },
        save: function () {
            this.loading.isActive = true;
            this.itemStore.saveTranslations({ id: this.$props.props.id, form: this.$props.props.form }).then(res => {
                this.loading.isActive = false;
                useModal().closeModal('itemTranslationModal');
                alertService.success(res.data.message);
                this.itemStore.view(this.$props.props.id);
            }).catch(err => {
                this.loading.isActive = false;
                alertService.error(err.response?.data?.message);
            });
        }
    }
}
</script>
