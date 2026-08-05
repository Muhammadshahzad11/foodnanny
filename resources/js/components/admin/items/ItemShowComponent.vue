<template>
    <LoadingComponent :props="loading" />

    <div class="col-12">
        <div class="flex flex-col items-start sm:flex-row sm:items-center gap-1.5 mb-6">
            <button type="button"
                class="tab-active tab-button w-full flex items-center gap-2 px-4 h-10 rounded-lg bg-white hover:text-primary hover:bg-primary/10"
                @click="handleTab($event, 'information')">
                <i class="lab lab-line-info-circle lab-font-size-16"></i>
                {{ $t('label.information') }}
            </button>
            <button type="button" @click="handleTab($event, 'image')"
                class="tab-button w-full flex items-center gap-2 px-4 h-10 rounded-lg bg-white hover:text-primary hover:bg-primary/10">
                <i class="lab lab-line-upload-image lab-font-size-16"></i>
                <span class="capitalize text-sm">{{ $t("label.image") }}</span>
            </button>
            <button type="button"
                class="tab-button w-full flex items-center gap-2 px-4 h-10 rounded-lg bg-white hover:text-primary hover:bg-primary/10"
                @click="handleTab($event, 'variations')"><i class="lab lab-line-variation lab-font-size-16"></i>
                {{ $t('label.variation') }}
            </button>
            <button type="button"
                class="tab-button w-full flex items-center gap-2 px-4 h-10 rounded-lg bg-white hover:text-primary hover:bg-primary/10"
                @click="handleTab($event, 'extra')"><i class="lab lab-line-extra lab-font-size-16"></i>
                {{ $t('label.extra') }}
            </button>
            <button type="button"
                class="tab-button w-full flex items-center gap-2 px-4 h-10 rounded-lg bg-white hover:text-primary hover:bg-primary/10"
                @click="handleTab($event, 'addon')"><i class="lab lab-line-addon lab-font-size-16"></i>
                {{ $t('label.addon') }}
            </button>
            <button type="button"
                class="tab-button w-full flex items-center gap-2 px-4 h-10 rounded-lg bg-white hover:text-primary hover:bg-primary/10"
                @click="handleTab($event, 'translations')"><i class="lab lab-line-language lab-font-size-16"></i>
                {{ $t('label.translations') }}
            </button>
        </div>
        <div class="tab-content db-card tab-active" id="information">
            <div class="db-card">
                <div class="db-card-header">
                    <h3 class="db-card-title">{{ $t("label.basic_info") }}</h3>
                </div>
                <div class="db-card-body">
                    <div class="row py-2">
                        <div class="col-12 sm:col-6 !py-1.5">
                            <div class="db-list-item p-0">
                                <span class="db-list-item-title w-full sm:w-1/2">{{ $t('label.name') }}</span>
                                <span class="db-list-item-text w-full sm:w-1/2">{{ item.name }}</span>
                            </div>
                        </div>
                        <div class="col-12 sm:col-6 !py-1.5">
                            <div class="db-list-item p-0">
                                <span class="db-list-item-title w-full sm:w-1/2">{{ $t('label.price') }}</span>
                                <span class="db-list-item-text w-full sm:w-1/2">{{ item.flat_price }}</span>
                            </div>
                        </div>

                        <div class="col-12 sm:col-6 !py-1.5">
                            <div class="db-list-item p-0">
                                <span class="db-list-item-title w-full sm:w-1/2">{{ $t('label.category') }}</span>
                                <span class="db-list-item-text w-full sm:w-1/2">{{ item.category_name }}</span>
                            </div>
                        </div>

                        <div class="col-12 sm:col-6 !py-1.5">
                            <div class="db-list-item p-0">
                                <span class="db-list-item-title w-full sm:w-1/2">{{ $t('label.tax') }}</span>
                                <span class="db-list-item-text w-full sm:w-1/2">{{ item.tax_name }}</span>
                            </div>
                        </div>

                        <div class="col-12 sm:col-6 !py-1.5">
                            <div class="db-list-item p-0">
                                <span class="db-list-item-title w-full sm:w-1/2">{{ $t('label.type') }}</span>
                                <span class="db-list-item-text w-full sm:w-1/2">{{
                                    enums.itemTypeEnumArray[item.item_type]
                                    }}</span>
                            </div>
                        </div>

                        <div class="col-12 sm:col-6 !py-1.5">
                            <div class="db-list-item p-0">
                                <span class="db-list-item-title w-full sm:w-1/2">{{ $t('label.status') }}</span>
                                <span class="db-list-item-text w-full sm:w-1/2">
                                    {{enums.statusEnumArray[item.status]}}
                                </span>
                            </div>
                        </div>

                        <div class="col-12 sm:col-6 !py-1.5">
                            <div class="db-list-item p-0">
                                <span class="db-list-item-title w-full sm:w-1/2">{{ $t('label.halal') }}</span>
                                <span class="db-list-item-text w-full sm:w-1/2">{{
                                    enums.askEnumArray[item.halal]
                                    }}</span>
                            </div>
                        </div>

                        <div class="col-12 sm:col-6 !py-1.5">
                            <div class="db-list-item p-0">
                                <span class="db-list-item-title w-full sm:w-1/2">{{ $t('label.available_time_start')
                                    }}</span>
                                <span class="db-list-item-text w-full sm:w-1/2">{{ item.convert_available_time_start
                                    }}</span>
                            </div>
                        </div>

                        <div class="col-12 sm:col-6 !py-1.5">
                            <div class="db-list-item p-0">
                                <span class="db-list-item-title w-full sm:w-1/2">{{ $t('label.available_time_end')
                                    }}</span>
                                <span class="db-list-item-text w-full sm:w-1/2">{{ item.convert_available_time_end
                                    }}</span>
                            </div>
                        </div>

                        <div class="col-12 sm:col-6 !py-1.5">
                            <div class="db-list-item p-0">
                                <span class="db-list-item-title w-full sm:w-1/2">{{ $t('label.discount_type') }}</span>
                                <span class="db-list-item-text w-full sm:w-1/2">{{
                                    enums.taxTypeEnumArray[item.discount_type]
                                    }}</span>
                            </div>
                        </div>

                        <div class="col-12 sm:col-6 !py-1.5">
                            <div class="db-list-item p-0">
                                <span class="db-list-item-title w-full sm:w-1/2">{{ $t('label.discount')
                                    }}</span>
                                <span class="db-list-item-text w-full sm:w-1/2">{{ item.flat_discount }}</span>
                            </div>
                        </div>

                        <div class="col-12 sm:col-6 !py-1.5">
                            <div class="db-list-item p-0">
                                <span class="db-list-item-title w-full sm:w-1/2">{{
                                    $t('label.maximum_purchase_quantity')
                                    }}</span>
                                <span class="db-list-item-text w-full sm:w-1/2">{{ item.maximum_purchase_quantity
                                    }}</span>
                            </div>
                        </div>

                        <div class="col-12 !py-1.5">
                            <div class="db-list-item p-0">
                                <span class="db-list-item-text mt-2 w-full">
                                    <span class="mt-2 db-list-item-title">{{ $t('label.caution') }}</span> <br>
                                    <span class="mt-2" v-html="item.caution"></span>
                                </span>
                            </div>
                        </div>
                        <div class="col-12 !py-1.5">
                            <div class="db-list-item p-0">
                                <span class="db-list-item-text mt-2 w-full">
                                    <span class="mt-2 db-list-item-title">{{ $t('label.description') }}</span><br>
                                    <span class="mt-2" v-html="item.description"></span>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="tab-content db-card" id="image">
            <div class="db-card">
                <div class="db-card-header">
                    <h3 class="db-card-title">{{ $t("label.image") }}</h3>
                </div>
                <div class="db-card-body">
                    <div class="row py-2">
                        <div class="col-12 sm:col-3">
                            <img class="db-image" alt="item" :src="previewImage" />
                        </div>
                        <form @submit.prevent="saveImage" class="w-auto">
                            <p class="mt-2">{{ $t('label.size') }}: (111px,110px)</p>
                            <div class="flex gap-3 md:gap-4 py-4">
                                <label for="photo"
                                    class="db-btn relative cursor-pointer h-[38px] shadow-[0px_6px_10px_rgb(var(--primary)/0.24)] bg-primary text-white">
                                    <i class="lab lab-line-upload-image"></i>
                                    <span class="hidden sm:inline-block">{{
                                        $t("button.upload_new_image")
                                    }}</span>
                                    <input v-if="uploadButton" @change="changePreviewImage" ref="imageProperty"
                                        accept="image/png, image/jpeg, image/jpg" type="file" id="photo"
                                        class="absolute top-0 left-0 w-full h-full -z-10 opacity-0" />
                                </label>
                                <button v-if="saveButton" type="submit"
                                    class="db-btn h-[38px] shadow-[0px_6px_10px_rgba(26,_183,_89,_0.24)] text-white bg-[#1AB759]">
                                    <i class="lab lab-line-circle-check"></i>
                                    <span class="hidden sm:inline-block">{{ $t("button.save") }}</span>
                                </button>
                                <button v-if="resetButton" @click="resetPreviewImage" type="button"
                                    class="db-btn-outline h-[38px] shadow-[0px_6px_10px_rgba(251,_78,_78,_0.24)] !text-[#FB4E4E] !bg-white !border-[#FB4E4E]">
                                    <i class="lab lab-line-reset"></i>
                                    <span class="hidden sm:inline-block">{{ $t("button.reset") }}</span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="tab-content db-card" id="variations">
            <ItemVariationListComponent :item="parseInt($route.params.id)" />
        </div>
        <div class="tab-content db-card" id="extra">
            <ItemExtraListComponent :item="parseInt($route.params.id)" />
        </div>
       <div class="tab-content db-card" id="addon">
            <ItemAddonListComponent :item="parseInt($route.params.id)" />
        </div>
        <div class="tab-content db-card" id="translations">
            <ItemTranslationComponent />
        </div>
    </div>
</template>

<script>
import LoadingComponent from "../../common/LoadingComponent.vue";
import statusEnum from "../../../enums/modules/statusEnum.js";
import itemTypeEnum from "../../../enums/modules/itemTypeEnum.js";
import askEnum from "../../../enums/modules/askEnum.js";
import taxTypeEnum from "../../../enums/modules/taxTypeEnum.js";
import alertService from "../../../services/alertService.js";
import ItemVariationListComponent from "./variation/ItemVariationListComponent.vue";
import ItemExtraListComponent from "./extra/ItemExtraListComponent.vue";
import ItemAddonListComponent from "./addon/ItemAddonListComponent.vue";
import ItemTranslationComponent from "./ItemTranslationComponent.vue";
import { useTab } from "../../../composables/tab.js";
import {useItemStore} from "../../../stores/item.js";

export default {
    name: "ItemShowComponent",
    components: {
        ItemVariationListComponent,
        LoadingComponent,
        ItemExtraListComponent,
        ItemAddonListComponent,
        ItemTranslationComponent
    },
    setup() {
        const itemStore = useItemStore();
        return {
            itemStore
        }
    },
    data() {
        return {
            loading: {
                isActive: false
            },
            enums: {
                statusEnum: statusEnum,
                itemTypeEnum: itemTypeEnum,
                askEnum: askEnum,
                taxTypeEnum: taxTypeEnum,
                statusEnumArray: {
                    [statusEnum.ACTIVE]: this.$t("label.active"),
                    [statusEnum.INACTIVE]: this.$t("label.inactive")
                },
                itemTypeEnumArray: {
                    [itemTypeEnum.VEG]: this.$t("label.veg"),
                    [itemTypeEnum.NON_VEG]: this.$t("label.non_veg")
                },
                askEnumArray: {
                    [askEnum.YES]: this.$t("label.yes"),
                    [askEnum.NO]: this.$t("label.no")
                },
                taxTypeEnumArray: {
                    [taxTypeEnum.FIXED]: this.$t("label.fixed"),
                    [taxTypeEnum.PERCENTAGE]: this.$t("label.percentage")
                }
            },
            defaultImage: null,
            previewImage: null,
            uploadButton: true,
            resetButton: false,
            saveButton: false,
            handleTab: useTab().handleTab
        }
    },
    computed: {
        item: function () {
            return this.itemStore.show;
        }
    },
    mounted() {
        this.loading.isActive = true;
        this.itemStore.view(this.$route.params.id).then(res => {
            this.defaultImage = res.data.data.preview;
            this.previewImage = res.data.data.preview;
            this.loading.isActive = false;
        }).catch((error) => {
            this.loading.isActive = false;
        });
    },
    methods: {
        changePreviewImage: function (e) {
            if (e.target.files[0]) {
                this.previewImage = URL.createObjectURL(e.target.files[0]);
                this.saveButton = true;
                this.resetButton = true;
            }
        },
        resetPreviewImage: function () {
            this.$refs.imageProperty.value = null;
            this.previewImage = this.defaultImage;
            this.saveButton = false;
            this.resetButton = false;
        },
        saveImage: function () {
            if (this.$refs.imageProperty.files[0]) {
                try {
                    this.loading.isActive = true;
                    const formData = new FormData();
                    formData.append("image", this.$refs.imageProperty.files[0]);
                    this.itemStore.changeImage({
                        id: this.$route.params.id,
                        form: formData,
                    }).then((res) => {
                        alertService.success(this.$t("message.image_update"));
                        this.defaultImage = res.data.data.preview;
                        this.previewImage = res.data.data.preview;
                        this.$refs.imageProperty.value = null;
                        this.saveButton = false;
                        this.resetButton = false;
                        this.loading.isActive = false;
                    }).catch((err) => {
                        this.loading.isActive = false;
                        if (typeof err.response.data.status !== "undefined" && err.response.data.status === false) {
                            alertService.error(err.response.data.message);
                        } else if (err.response.data.errors && err.response.data.errors.image) {
                            err.response.data.errors.image.forEach((error) => {
                                alertService.error(error);
                            });
                        }
                    });
                } catch (err) {
                    this.loading.isActive = false;
                    alertService.error(err.response.data.message);
                }
            }
        }
    }
}
</script>
