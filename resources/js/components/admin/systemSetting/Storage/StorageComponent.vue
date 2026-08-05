<template>
    <LoadingComponent :props="loading" />
    <div class="row">
        <div v-for="(storage, index) in storages.slice(1, 3)" class="col-12 sm:col-6 xl:col-4 mb-5">
            <button @click="selectActive(index)" class="tab-button w-full flex items-center gap-2 px-4 h-10 rounded-lg bg-white hover:text-primary hover:bg-primary/10" :data-tab="'#' + storage.slug" :key="storage" :class="index === selectIndex ? 'tab-active' : ''">
                <span class="capitalize whitespace-nowrap text-[15px]">
                    {{ storage.name }}
                </span>
            </button>
        </div>
        <div v-if="storages.length > 3" class="col-12 sm:col-6 xl:col-4 mb-5">
            <div class="paper-group">
                <button @click="handlePaper($event)" class="paper-button w-full flex items-center gap-2 px-4 h-10 rounded-lg bg-white hover:text-primary hover:bg-primary/10">
                    <i class="lab-line-circle-more text-lg flex-shrink-0"></i>
                    <span class="flex-auto ltr:text-left rtl:text-right text-sm capitalize whitespace-nowrap text-ellipsis overflow-hidden">
                        {{ $t('label.more_gateway') }}</span>
                    <i class="lab-fill-arrow-down text-sm"></i>
                </button>

                <div class="paper-content w-full absolute top-[42px] right-0 z-30 p-1 rounded-md shadow-paper bg-white">
                    <button @click="selectActive(index + 2)" class="tab-button w-full flex items-center gap-2 px-2.5 h-8 rounded-lg hover:text-primary hover:bg-primary/5" :data-tab="'#' + storage.slug" v-for="(storage, index) in storages.slice(2, storages.length)" :key="storage" :class="index + 2 === selectIndex ? 'tab-active' : ''">
                        <span class="flex-auto ltr:text-left rtl:text-right text-sm capitalize whitespace-nowrap text-ellipsis overflow-hidden">
                            {{ storage.name }}</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
    <div :id="storage.slug" class="tab-content db-card" v-for="(storage, index) in storages.slice(1, 3)" :key="storage"
    :class="index === selectIndex ? 'tab-active' : ''">
        <div class="db-card-header">
            <h3 class="db-card-title">{{ storage.name }}</h3>
        </div>
        <div class="db-card-body">
            <form @submit.prevent="save(index)" :id="'formElem' + index">
                <div class="form-row">
                    <input type="hidden" :value="storage.slug" name="storage_type">
                    <div class="form-col-12 sm:form-col-6" v-for="storageOption in storage.options" :key="storageOption">
                        <label :for="storageOption.option" class="db-field-title">
                            {{ $t("label." + storageOption.option) }}
                        </label>
                        <input v-if="storageOption.type === enums.inputTypeEnum.TEXT" type="text" :value="storageOption.value" v-bind:class="errors[storageOption.option] ? 'invalid' : ''" :name="storageOption.option" :id="storageOption.option" class="db-field-control" />
                        <select v-else class="db-field-control" :id="storageOption.option" :name="storageOption.option" v-bind:class="errors[storageOption.option] ? 'invalid' : ''">
                            <option :value="index" :selected="index === storageOption.value"
                                v-for="(activity, index) in storageOption.activities">
                                {{ $t("label." + activity) }}
                            </option>
                        </select>

                        <small class="db-field-alert" v-if="errors[storageOption.option]">{{ errors[storageOption.option][0] }}</small>
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
import appService from "../../../../services/appService.js";
import inputTypeEnum from "../../../../enums/modules/inputTypeEnum.js";
import {useStorageStore} from "../../../../stores/storage.js";

export default {
    name: "StorageComponent",
    components: { LoadingComponent },
    setup(){
        const storageStore = useStorageStore();
        return {
            storageStore
        }
    },
    data() {
        return {
            loading: {
                isActive: false
            },
            search: {
                paginate: 0,
                order_column: "id",
                order_type: "asc"
            },
            selectIndex: 0,
            enums: {
                inputTypeEnum: inputTypeEnum
            },
            errors: {}
        };
    },
    computed: {
        storages: function () {
            return this.storageStore.lists;
        }
    },
    mounted() {
        try {
            this.loading.isActive = true;
            this.storageStore.fetch(this.search).then((res) => {
                this.loading.isActive = false;
            }).catch((err) => {
                this.loading.isActive = false;
            });
        } catch (err) {
            this.loading.isActive = false;
        }
    },
    methods: {
        save: function (index) {
            try {
                const form = document.getElementById("formElem" + index);
                const formDataObj = {};
                [...form.elements].filter((el) => el.tagName !== 'BUTTON').forEach((item) => {
                    formDataObj[item.name] = item.value;
                });
                this.loading.isActive = true;
                this.storageStore.save({ form: formDataObj, search: this.search }).then((res) => {
                    this.loading.isActive = false;
                    alertService.successFlip(1, this.$t("menu.storage"));
                    this.errors = {};
                }).catch((err) => {
                    this.loading.isActive = false;
                    this.errors = err.response.data.errors;
                });
            } catch (err) {
                this.loading.isActive = false;
                alertService.error(err);
            }
        },
        selectActive: function (index) {
            this.selectIndex = index;
        },
        handlePaper(e) {
            return appService.handlePaper(e);
        }
    }
};
</script>
