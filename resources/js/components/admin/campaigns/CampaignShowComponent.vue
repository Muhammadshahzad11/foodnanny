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
                @click="handleTab($event, 'restaurant')"><i class="lab lab-line-restaurants lab-font-size-16"></i>
                {{ $t('label.restaurants') }}
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
                                <span class="db-list-item-title w-full sm:w-1/2">{{ $t('label.title') }}</span>
                                <span class="db-list-item-text w-full sm:w-1/2">{{ campaign.title }}</span>
                            </div>
                        </div>
                        <div class="col-12 sm:col-6 !py-1.5">
                            <div class="db-list-item p-0">
                                <span class="db-list-item-title w-full sm:w-1/2">{{ $t('label.date')
                                    }}</span>
                                <span class="db-list-item-text w-full sm:w-1/2">{{ campaign.convert_date }}</span>
                            </div>
                        </div>

                        <div class="col-12 sm:col-6 !py-1.5">
                            <div class="db-list-item p-0">
                                <span class="db-list-item-title w-full sm:w-1/2">{{ $t('label.time') }}</span>
                                <span class="db-list-item-text w-full sm:w-1/2">{{ campaign.convert_time }}</span>
                            </div>
                        </div>

                        <div class="col-12 sm:col-6 !py-1.5">
                            <div class="db-list-item p-0">
                                <span class="db-list-item-title w-full sm:w-1/2">{{ $t('label.type') }}</span>
                                <span class="db-list-item-text">
                                    {{ enums.campaignTypeEnumArray[campaign.type] }}
                                </span>
                            </div>
                        </div>

                        <div class="col-12 sm:col-6 !py-1.5">
                            <div class="db-list-item p-0">
                                <span class="db-list-item-title w-full sm:w-1/2">{{ $t('label.amount') }}</span>
                                <span class="db-list-item-text w-full sm:w-1/2">{{ campaign.flat_amount }}</span>
                            </div>
                        </div>

                        <div class="col-12 sm:col-6 !py-1.5">
                            <div class="db-list-item p-0">
                                <span class="db-list-item-title w-full sm:w-1/2">{{ $t('label.status') }}</span>
                                <span class="db-list-item-text">
                                     {{ enums.statusEnumArray[campaign.status] }}
                                </span>
                            </div>
                        </div>
                        <div class="col-12 !py-1.5">
                            <div class="db-list-item p-0">
                                <span class="db-list-item-text mt-2 w-full">
                                    <span class="mt-2 db-list-item-title">{{ $t('label.description') }}</span><br>
                                    <span class="mt-2" v-html="campaign.description"></span>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="tab-content db-card" id="image">
            <div class="db-card">
                <div class="db-card-body">
                    <div class="row">
                        <div class="form-col-12 xl:form-col-6">
                            <fieldset class="p-4 mb-6 border border-[#DBDEE0]">
                                <legend
                                    class="py-1.5 px-4 text-base font-semibold capitalize border border-[#DBDEE0] text-primary">
                                    {{ $t('label.thumbnail') }}
                                </legend>
                                <div class="row py-2">
                                    <form @submit.prevent="saveThumbnail" class="w-auto">
                                        <p class="mt-2 px-4">{{ $t('label.size') }}: (359px,184px)</p>
                                        <div class="flex gap-3 md:gap-4 p-4">
                                            <label for="thumbnail"
                                                class="db-btn relative cursor-pointer h-[38px] shadow-[0px_6px_10px_rgb(var(--primary)/0.24)] bg-primary text-white">
                                                <i class="lab lab-line-upload-image"></i>
                                                <span class="hidden sm:inline-block">{{
                                                    $t("button.upload_new_thumbnail")
                                                    }}</span>
                                                <input v-if="uploadThumbnailButton" @change="changePreviewThumbnail"
                                                    ref="thumbnailProperty" accept="image/png, image/jpeg, image/jpg"
                                                    type="file" id="thumbnail"
                                                    class="absolute top-0 left-0 w-full h-full -z-10 opacity-0" />
                                            </label>
                                            <button v-if="saveThumbnailButton" type="submit"
                                                class="db-btn h-[38px] shadow-[0px_6px_10px_rgba(26,_183,_89,_0.24)] text-white bg-[#1AB759]">
                                                <i class="lab lab-line-circle-check"></i>
                                                <span class="hidden sm:inline-block">{{ $t("button.save") }}</span>
                                            </button>
                                            <button v-if="resetThumbnailButton" @click="resetPreviewThumbnail"
                                                type="button"
                                                class="db-btn-outline h-[38px] shadow-[0px_6px_10px_rgba(251,_78,_78,_0.24)] !text-[#FB4E4E] !bg-white !border-[#FB4E4E]">
                                                <i class="lab lab-line-reset"></i>
                                                <span class="hidden sm:inline-block">{{ $t("button.reset") }}</span>
                                            </button>
                                        </div>
                                    </form>
                                </div>
                                <div class="col-12 sm:col-5">
                                    <img class="db-image" alt="campaign-thumbnail" :src="previewThumbnail" />
                                </div>
                            </fieldset>
                        </div>

                        <div class="form-col-12 xl:form-col-6">
                            <fieldset class="p-4 mb-6 border border-[#DBDEE0]">
                                <legend
                                    class="py-1.5 px-4 text-base font-semibold capitalize border border-[#DBDEE0] text-primary">
                                    {{ $t('label.cover') }}
                                </legend>
                                <div class="row py-2">
                                    <form @submit.prevent="saveCover" class="w-auto">
                                        <p class="mt-2 px-4">{{ $t('label.size') }}: (1120px,269px)</p>
                                        <div class="flex gap-3 md:gap-4 p-4">
                                            <label for="photo"
                                                class="db-btn relative cursor-pointer h-[38px] shadow-[0px_6px_10px_rgb(var(--primary)/0.24)] bg-primary text-white">
                                                <i class="lab lab-line-upload-image"></i>
                                                <span class="hidden sm:inline-block">{{
                                                    $t("button.upload_new_cover")
                                                    }}</span>
                                                <input v-if="uploadButton" @change="changePreviewCover"
                                                    ref="coverProperty" accept="image/png, image/jpeg, image/jpg"
                                                    type="file" id="photo"
                                                    class="absolute top-0 left-0 w-full h-full -z-10 opacity-0" />
                                            </label>
                                            <button v-if="saveButton" type="submit"
                                                class="db-btn h-[38px] shadow-[0px_6px_10px_rgba(26,_183,_89,_0.24)] text-white bg-[#1AB759]">
                                                <i class="lab lab-line-circle-check"></i>
                                                <span class="hidden sm:inline-block">{{ $t("button.save") }}</span>
                                            </button>
                                            <button v-if="resetButton" @click="resetPreviewCover" type="button"
                                                class="db-btn-outline h-[38px] shadow-[0px_6px_10px_rgba(251,_78,_78,_0.24)] !text-[#FB4E4E] !bg-white !border-[#FB4E4E]">
                                                <i class="lab lab-line-reset"></i>
                                                <span class="hidden sm:inline-block">{{ $t("button.reset") }}</span>
                                            </button>
                                        </div>
                                    </form>
                                </div>
                                <div class="col-12 sm:col-5">
                                    <img class="db-image" alt="campaign-cover" :src="previewCover" />
                                </div>
                            </fieldset>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="tab-content db-card" id="restaurant">
            <CampaignRestaurantListComponent :campaign="parseInt($route.params.id)" />
        </div>
        <div class="tab-content db-card" id="translations">
            <CampaignTranslationComponent />
        </div>
    </div>
</template>

<script>
import LoadingComponent from "../../common/LoadingComponent.vue";
import alertService from "../../../services/alertService.js";
import appService from "../../../services/appService.js";
import statusEnum from "../../../enums/modules/statusEnum.js";
import campaignTypeEnum from "../../../enums/modules/campaignTypeEnum.js";
import { useTab } from "../../../composables/tab.js";
import { useCampaignStore } from "../../../stores/campaign.js";
import CampaignRestaurantListComponent from "./restaurant/CampaignRestaurantListComponent.vue";
import CampaignTranslationComponent from "./CampaignTranslationComponent.vue";

export default {
    name: "CampaignShowComponent",
    components: {
        LoadingComponent,
        CampaignRestaurantListComponent,
        CampaignTranslationComponent
    },
    setup() {
        const campaignStore = useCampaignStore();
        return {
            campaignStore
        }
    },
    data() {
        return {
            loading: {
                isActive: false
            },
            enums: {
                statusEnum: statusEnum,
                campaignTypeEnum: campaignTypeEnum,
                statusEnumArray: {
                    [statusEnum.ACTIVE]: this.$t("label.active"),
                    [statusEnum.INACTIVE]: this.$t("label.inactive")
                },
                campaignTypeEnumArray: {
                    [campaignTypeEnum.FREE]: this.$t("label.free"),
                    [campaignTypeEnum.PAID]: this.$t("label.paid"),
                },
            },
            defaultCover: null,
            previewCover: null,
            uploadButton: true,
            resetButton: false,
            saveButton: false,

            defaultThumbnail: null,
            previewThumbnail: null,
            uploadThumbnailButton: true,
            resetThumbnailButton: false,
            saveThumbnailButton: false,
            handleTab: useTab().handleTab,
        }
    },
    computed: {
        campaign: function () {
            return this.campaignStore.show;
        }
    },
    mounted() {
        this.loading.isActive = true;
        this.campaignStore.view(this.$route.params.id).then(res => {
            this.defaultCover = res.data.data.cover;
            this.previewCover = res.data.data.cover;
            this.defaultThumbnail = res.data.data.thumbnail;
            this.previewThumbnail = res.data.data.thumbnail;
            this.loading.isActive = false;
        }).catch((error) => {
            this.loading.isActive = false;
        });
    },
    methods: {
        changePreviewThumbnail: function (e) {
            if (e.target.files[0]) {
                this.previewThumbnail = URL.createObjectURL(e.target.files[0]);
                this.saveThumbnailButton = true;
                this.resetThumbnailButton = true;
            }
        },
        resetPreviewThumbnail: function () {
            this.$refs.thumbnailProperty.value = null;
            this.previewThumbnail = this.defaultThumbnail;
            this.saveThumbnailButton = false;
            this.resetThumbnailButton = false;
        },
        saveThumbnail: function () {
            if (this.$refs.thumbnailProperty.files[0]) {
                try {
                    this.loading.isActive = true;
                    const formData = new FormData();
                    formData.append("image", this.$refs.thumbnailProperty.files[0]);
                    this.campaignStore.changeThumbnail({
                        id: this.$route.params.id,
                        form: formData,
                    }).then((res) => {
                        alertService.success(this.$t("message.thumbnail_update"));
                        this.defaultThumbnail = res.data.data.thumbnail;
                        this.previewThumbnail = res.data.data.thumbnail;
                        this.$refs.thumbnailProperty.value = null;
                        this.saveThumbnailButton = false;
                        this.resetThumbnailButton = false;
                        this.loading.isActive = false;
                    }).catch((err) => {
                        this.loading.isActive = false;
                        if (typeof err.response.data.status !== "undefined" && err.response.data.status === false) {
                            alertService.error(err.response.data.message);
                        } else if (err.response.data.errors && err.response.data.errors.image) {
                             err.response.data.errors.image.map((error) => {
                                alertService.error(error);
                            });
                        }
                    });
                } catch (err) {
                    this.loading.isActive = false;
                    alertService.error(err.response.data.message);
                }
            }
        },
        changePreviewCover: function (e) {
            if (e.target.files[0]) {
                this.previewCover = URL.createObjectURL(e.target.files[0]);
                this.saveButton = true;
                this.resetButton = true;
            }
        },
        resetPreviewCover: function () {
            this.$refs.coverProperty.value = null;
            this.previewCover = this.defaultCover;
            this.saveButton = false;
            this.resetButton = false;
        },
        saveCover: function () {
            if (this.$refs.coverProperty.files[0]) {
                try {
                    this.loading.isActive = true;
                    const formData = new FormData();
                    formData.append("image", this.$refs.coverProperty.files[0]);
                    this.campaignStore.changeCover({
                        id: this.$route.params.id,
                        form: formData,
                    }).then((res) => {
                        alertService.success(this.$t("message.cover_update"));
                        this.defaultCover = res.data.data.cover;
                        this.previewCover = res.data.data.cover;
                        this.$refs.coverProperty.value = null;
                        this.saveButton = false;
                        this.resetButton = false;
                        this.loading.isActive = false;
                    }).catch((err) => {
                        this.loading.isActive = false;
                         if (typeof err.response.data.status !== "undefined" && err.response.data.status === false) {
                            alertService.error(err.response.data.message);
                        } else if (err.response.data.errors && err.response.data.errors.image) {
                             err.response.data.errors.image.map((error) => {
                                alertService.error(error);
                            });
                        }
                    });
                } catch (err) {
                    this.loading.isActive = false;
                    alertService.error(err.response.data.message);
                }
            }
        },
    }
}

</script>
