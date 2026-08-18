<template>
    <LoadingComponent :props="loading" />
    <SmSidebarModalCreateComponent :props="addButton" @click="addReset"/>

    <div id="sidebar" @click="closeBackdrop"
        class="fixed inset-0 z-50 bg-black/50 duration-500 transition-all invisible opacity-0">
        <div
            class="w-full max-w-xl h-dvh overflow-x-hidden thin-scrolling bg-white ms-auto ltr:translate-x-full rtl:-translate-x-full">
            <div class="flex items-center justify-between p-4 border-b border-slate-100">
                <h3 class="drawer-title">{{ $t('menu.campaigns') }}</h3>
                <button class="lab-line-close font-bold text-base" @click="reset"></button>
            </div>
            <div class="drawer-body">
                <form @submit.prevent="save">
                    <div class="form-row">
                        <div class="form-col-12 sm:form-col-6">
                            <label for="title" class="db-field-title required">{{ $t("label.title") }}</label>
                            <input v-model="props.form.title" v-bind:class="errors.title ? 'invalid' : ''" type="text"
                                id="title" class="db-field-control" />
                            <small class="db-field-alert" v-if="errors.title">{{errors.title[0]}}</small>
                        </div>

                        <div class="form-col-12 sm:form-col-6">
                            <label for="tag" class="db-field-title">{{ $t("label.offer_tag") }}</label>
                            <input v-model="props.form.tag" v-bind:class="errors.tag ? 'invalid' : ''" type="text"
                                id="tag" class="db-field-control" :placeholder="$t('label.offer_tag_placeholder')" />
                            <small class="text-xs text-paragraph mt-1 block">{{ $t('label.offer_tag_hint') }}</small>
                            <small class="db-field-alert" v-if="errors.tag">{{ errors.tag[0]}}</small>
                        </div>

                        <div class="form-col-12 sm:form-col-6">
                            <label class="db-field-title required">{{ $t("label.status") }}</label>
                            <div class="db-field-radio-group">
                                <div class="db-field-radio">
                                    <div class="custom-radio">
                                        <input type="radio" v-model="props.form.status" id="active"
                                            :value="enums.statusEnum.ACTIVE" class="custom-radio-field" checked />
                                        <span class="custom-radio-span"></span>
                                    </div>
                                    <label for="active" class="db-field-label">{{$t("label.active")}}</label>
                                </div>
                                <div class="db-field-radio">
                                    <div class="custom-radio">
                                        <input type="radio" class="custom-radio-field" v-model="props.form.status"
                                            id="inactive" :value="enums.statusEnum.INACTIVE" />
                                        <span class="custom-radio-span"></span>
                                    </div>
                                    <label for="inactive" class="db-field-label">{{$t("label.inactive")}}</label>
                                </div>
                            </div>
                        </div>

                        <div class="form-col-12 sm:form-col-6">
                            <label for="start_date" class="db-field-title required">{{ $t("label.start_date") }}</label>
                            <Datepicker hideInputIcon autoApply v-model="props.form.start_date" :is24="false"
                                :monthChangeOnScroll="false" utc="false" :enable-time-picker="false"
                                :input-class-name="errors.start_date ? 'invalid' : ''">
                            </Datepicker>
                            <small class="db-field-alert" v-if="errors.start_date">{{ errors.start_date[0] }}</small>
                        </div>
                        <div class="form-col-12 sm:form-col-6">
                            <label for="end_date" class="db-field-title required">{{ $t("label.end_date") }}</label>
                            <Datepicker hideInputIcon autoApply v-model="props.form.end_date" :is24="false"
                                :monthChangeOnScroll="false" utc="false" :enable-time-picker="false"
                                :input-class-name="errors.end_date ? 'invalid' : ''">
                            </Datepicker>
                            <small class="db-field-alert" v-if="errors.end_date">{{errors.end_date[0]}}</small>
                        </div>

                        <div class="form-col-12 sm:form-col-6">
                            <label for="start_time" class="db-field-title required">{{$t("label.start_time") }}</label>
                            <Datepicker @update:modelValue="handleTimeStart" hideInputIcon v-model="props.startTime"
                                :time-picker="true" :time-picker-only="true" :is24="false" utc="false"
                                :input-class-name="errors.start_time ? 'invalid' : ''">
                            </Datepicker>
                            <small class="db-field-alert" v-if="errors.start_time">{{errors.start_time[0] }}</small>
                        </div>

                        <div class="form-col-12 sm:form-col-6">
                            <label for="end_time" class="db-field-title required">{{
                                $t("label.end_time") }}</label>
                            <Datepicker @update:modelValue="handleTimeEnd" hideInputIcon v-model="props.endTime"
                                :time-picker="true" :time-picker-only="true" :is24="false" utc="false"
                                :input-class-name="errors.end_time ? 'invalid' : ''">
                            </Datepicker>
                            <small class="db-field-alert" v-if="errors.end_time">{{errors.end_time[0]}}</small>
                        </div>

                        <div class="form-col-12 sm:form-col-6">
                            <label class="db-field-title required">{{ $t("label.thumbnail") }} (359px,184px)</label>
                            <input @change="changeThumbnail" v-bind:class="errors.thumbnail ? 'invalid' : ''" id="thumbnail"
                                type="file" class="db-field-control" ref="thumbnailImageProperty"
                                accept="image/png, image/jpeg, image/jpg" />
                            <small class="db-field-alert" v-if="errors.thumbnail">{{ errors.thumbnail[0] }}</small>
                        </div>

                        <div class="form-col-12 sm:form-col-6">
                            <label class="db-field-title required">{{ $t("label.cover") }} (1120px,269px)</label>
                            <input @change="changeCover" v-bind:class="errors.cover ? 'invalid' : ''" id="cover"
                                type="file" class="db-field-control" ref="coverImageProperty"
                                accept="image/png, image/jpeg, image/jpg" />
                            <small class="db-field-alert" v-if="errors.cover">{{ errors.cover[0] }}</small>
                        </div>

                        <div class="form-col-12 sm:form-col-6">
                            <label class="db-field-title required" for="active">{{ $t("label.type") }}</label>
                            <div class="db-field-radio-group">
                                <div class="db-field-radio">
                                    <div class="custom-radio">
                                        <input :value="enums.campaignTypeEnum.FREE" v-model="props.form.type" id="free"
                                            type="radio" class="custom-radio-field" />
                                        <span class="custom-radio-span"></span>
                                    </div>
                                    <label for="free" class="db-field-label">{{$t("label.free")}}</label>
                                </div>
                                <div class="db-field-radio">
                                    <div class="custom-radio">
                                        <input :value="enums.campaignTypeEnum.PAID" v-model="props.form.type"
                                            type="radio" id="paid" class="custom-radio-field" />
                                        <span class="custom-radio-span"></span>
                                    </div>
                                    <label for="paid" class="db-field-label">{{ $t("label.paid") }}</label>
                                </div>
                            </div>
                            <small class="db-field-alert" v-if="errors.type">{{ errors.type[0]}}</small>
                        </div>

                        <div class="form-col-12 sm:form-col-6" v-if="props.form.type === enums.campaignTypeEnum.PAID">
                            <label for="amount" class="db-field-title required">
                                {{ $t("label.amount") }}
                            </label>
                            <input v-model="props.form.amount" v-on:keypress="floatNumber($event)"
                                v-bind:class="errors.amount ? 'invalid' : ''" type="text" id="amount"
                                class="db-field-control" />
                            <small class="db-field-alert" v-if="errors.amount">{{ errors.amount[0] }}</small>
                        </div>

                        <div class="form-col-12">
                            <label for="description" class="db-field-title required">{{ $t("label.description")
                                }}</label>
                            <div :class="errors.description ? 'invalid textarea-error-box-style' : ''" class="custom-quill-editor">
                                <quill-editor id="description" v-model:value="props.form.description"
                                    class="!h-40 textarea-border-radius" />
                            </div>
                            <small class="db-field-alert" v-if="errors.description">{{errors.description[0]}}</small>
                        </div>
                        <div class="form-col-12">
                            <div class="flex flex-wrap gap-3 mt-4">
                                <button type="submit" class="db-btn py-2 text-white bg-primary">
                                    <i class="lab lab-fill-save text-base"></i>
                                    <span>{{ $t("label.save") }}</span>
                                </button>
                                <button type="button" class="modal-btn-outline modal-close" @click="reset">
                                    <i class="lab lab-fill-close-circle text-base"></i>
                                    <span>{{ $t("button.close") }}</span>
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
import SmSidebarModalCreateComponent from "../components/buttons/SmSidebarModalCreateComponent.vue";
import Datepicker from "@vuepic/vue-datepicker";
import "@vuepic/vue-datepicker/dist/main.css";
import LoadingComponent from "../../common/LoadingComponent.vue";
import statusEnum from "../../../enums/modules/statusEnum.js";
import campaignTypeEnum from "../../../enums/modules/campaignTypeEnum.js";
import alertService from "../../../services/alertService.js";
import appService from "../../../services/appService.js";
import { useCanvas } from "../../../composables/canvas";
import { quillEditor } from 'vue3-quill';
import { useCampaignStore } from "../../../stores/campaign.js";

export default {
    name: "CampaignCreateComponent",
    components: { SmSidebarModalCreateComponent, LoadingComponent, Datepicker, quillEditor },
    props: ["props"],
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
                    [campaignTypeEnum.PAID]: this.$t("label.paid")
                },
            },
            thumbnail: "",
            cover: "",
            errors: {},
            closeBackdrop: useCanvas().closeBackdrop
        };
    },
    computed: {
        addButton: function () {
            return { title: this.$t('button.add_campaign') };
        }
    },
    methods: {
        floatNumber(e) {
            return appService.floatNumber(e);
        },
        changeThumbnail: function (e) {
            this.thumbnail = e.target.files[0];
        },
        changeCover: function (e) {
            this.cover = e.target.files[0];
        },
        handleTimeStart: function (e) {
            if (e) {
                this.props.form.start_time = e.hours + ':' + e.minutes + ':' + e.seconds;
            } else {
                this.props.form.start_time = null;
            }
        },
        handleTimeEnd: function (e) {
            if (e) {
                this.props.form.end_time = e.hours + ':' + e.minutes + ':' + e.seconds;
            } else {
                this.props.form.end_time = null;
            }
        },
        addReset: function () {
            this.campaignStore.reset();
            this.errors = {};
            this.$props.props.startTime = '';
            this.$props.props.endTime = '';
            this.$props.props.form = {
                title: "",
                tag: "",
                amount: "",
                start_date: "",
                end_date: "",
                start_time: "",
                end_time: "",
                type: campaignTypeEnum.FREE,
                status: statusEnum.ACTIVE,
                description: ""
            };
            if (this.thumbnail) {
                this.thumbnail = "";
                this.$refs.thumbnailImageProperty.value = null;
            }
            if (this.cover) {
                this.cover = "";
                this.$refs.coverImageProperty.value = null;
            }
        },
        reset: function () {
            useCanvas().closeCanvas('sidebar');
            this.campaignStore.reset();
            this.errors = {};
            this.$props.props.startTime = '';
            this.$props.props.endTime = '';
            this.$props.props.form = {
                title: "",
                tag: "",
                amount: "",
                start_date: "",
                end_date: "",
                start_time: "",
                end_time: "",
                type: campaignTypeEnum.FREE,
                status: statusEnum.ACTIVE,
                description: ""
            };
            if (this.thumbnail) {
                this.thumbnail = "";
                this.$refs.thumbnailImageProperty.value = null;
            }
            if (this.cover) {
                this.cover = "";
                this.$refs.coverImageProperty.value = null;
            }
        },
        save: function () {
            try {
                const fd = new FormData();
                fd.append("title", this.props.form.title);
                fd.append("tag", this.props.form.tag || '');
                fd.append("start_date", this.props.form.start_date);
                fd.append("end_date", this.props.form.end_date);
                fd.append("start_time", this.props.form.start_time);
                fd.append("end_time", this.props.form.end_time);
                fd.append("description", this.props.form.description);
                fd.append("type", this.props.form.type);
                fd.append("amount", this.props.form.amount);
                fd.append("status", this.props.form.status);
                if (this.thumbnail) {
                    fd.append("thumbnail", this.thumbnail);
                }
                if (this.cover) {
                    fd.append("cover", this.cover);
                }
                const tempId = this.campaignStore.temp.temp_id;
                this.loading.isActive = true;
                this.campaignStore.save({form: fd, search: this.props.search}).then((res) => {
                        useCanvas().closeCanvas('sidebar');
                        this.loading.isActive = false;
                        alertService.successFlip(tempId === null ? 0 : 1, this.$t("menu.campaigns"));
                        this.$props.props.form = {
                            title: "",
                            tag: "",
                            amount: "",
                            start_date: "",
                            end_date: "",
                            start_time: "",
                            end_time: "",
                            type: campaignTypeEnum.FREE,
                            status: statusEnum.ACTIVE,
                            description: ""
                        };
                        this.$props.props.startTime = '';
                        this.$props.props.endTime = '';
                        this.thumbnail = "";
                        this.cover = "";
                        this.errors = {};
                        this.$refs.thumbnailImageProperty.value = null;
                        this.$refs.coverImageProperty.value = null;
                    }).catch((err) => {
                        this.loading.isActive = false;
                        this.errors = err.response.data.errors;
                    });
            } catch (err) {
                this.loading.isActive = false;
                alertService.error(err);
            }
        }
    }
};
</script>
