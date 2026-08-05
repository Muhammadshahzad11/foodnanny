<template>
    <LoadingComponent :props="loading" />
    <SmSidebarModalCreateComponent :props="addButton" @click="addReset" />
    <div id="sidebar" @click="closeBackdrop"
        class="fixed inset-0 z-50 bg-black/50 duration-500 transition-all invisible opacity-0">
        <div
            class="w-full max-w-2xl h-dvh overflow-x-hidden thin-scrolling bg-white ms-auto ltr:translate-x-full rtl:-translate-x-full">
            <div class="flex items-center justify-between p-4 border-b border-slate-100">
                <h3 class="drawer-title">{{ $t("menu.items") }}</h3>
                <button class="lab-line-close font-bold text-base" @click="reset"></button>
            </div>
            <div class="drawer-body">
                <form @submit.prevent="save">
                    <div class="form-row">
                        <div class="form-col-12 sm:form-col-6">
                            <div class="flex items-center justify-between">
                                <label for="name" class="db-field-title">{{ $t("label.name") }}</label>
                                <button v-if="aiStatus" type="button" @click="generateAiName" class="text-primary text-xs flex items-center cursor-pointer">
                                    <i class="lab-fill-ai text-[#8B5CF6]"></i>
                                    <span :class="aiNameLoading ? '' : 'hidden'" class="ai-text-animation" role="status">
                                        {{ $t("label.just_a_second") }}
                                    </span>
                                    <span :class="!aiNameLoading && !props.form.name ? '' : 'hidden'" class="btn-text">
                                        {{ $t("label.generate") }}
                                    </span>
                                    <span :class="!aiNameLoading && props.form.name ? '' : 'hidden'" class="btn-text">
                                        {{ $t("label.regenerate") }}
                                    </span>
                                </button>
                            </div>
                            <input v-model="props.form.name" v-bind:class="errors.name ? 'invalid' : ''" type="text" id="name" class="db-field-control">
                            <small class="db-field-alert" v-if="errors.name">{{ errors.name[0] }}</small>
                        </div>

                        <div class="form-col-12 sm:form-col-6">
                            <label for="price" class="db-field-title required">{{ $t("label.price") }}</label>
                            <input v-model="props.form.price" v-on:keypress="floatNumber($event)"
                                v-bind:class="errors.price ? 'invalid' : ''" type="text" id="price"
                                class="db-field-control">
                            <small class="db-field-alert" v-if="errors.price">{{ errors.price[0] }}</small>
                        </div>

                        <div class="form-col-12 sm:form-col-6">
                            <label for="item_category_id" class="db-field-title required">{{
                                $t("label.category") }}</label>
                            <vue-select class="db-field-control f-b-custom-select" id="item_category_id"
                                v-bind:class="errors.item_category_id ? 'invalid' : ''"
                                v-model="props.form.item_category_id" :options="itemCategories" label-by="name"
                                value-by="id" :closeOnSelect="true" :searchable="true" :clearOnClose="true"
                                placeholder="--" search-placeholder="--" />
                            <small class="db-field-alert" v-if="errors.item_category_id">{{ errors.item_category_id[0]
                            }}</small>
                        </div>

                        <div class="form-col-12 sm:form-col-6">
                            <label for="tax_id" class="db-field-title">{{ $t("label.tax") }} ({{
                                $t("label.excluding") }})</label>
                            <vue-select class="db-field-control f-b-custom-select" id="tax_id"
                                v-bind:class="errors.tax_id ? 'invalid' : ''" v-model="props.form.tax_id"
                                :options="taxes" label-by="code" value-by="id" :closeOnSelect="true" :searchable="true"
                                :clearOnClose="true" placeholder="--" search-placeholder="--" />
                            <small class="db-field-alert" v-if="errors.tax_id">{{ errors.tax_id[0] }}</small>
                        </div>

                        <div class="form-col-12 sm:form-col-6">
                            <label class="db-field-title">{{ $t("label.image") }}</label>
                            <input @change="changeImage" v-bind:class="errors.image ? 'invalid' : ''" id="image"
                                type="file" class="db-field-control" ref="imageProperty"
                                accept="image/png, image/jpeg, image/jpg">
                            <small class="db-field-alert" v-if="errors.image">{{ errors.image[0] }}</small>
                        </div>

                        <div class="form-col-12 sm:form-col-6">
                            <label for="item_type" class="db-field-title">{{ $t("label.item_type") }}</label>
                            <vue-select class="db-field-control f-b-custom-select" id="item_type"
                                v-model="props.form.item_type" :options="[
                                    { id: enums.itemTypeEnum.VEG, name: $t('label.veg') },
                                    { id: enums.itemTypeEnum.NON_VEG, name: $t('label.non_veg') }
                                ]" label-by="name" value-by="id" :closeOnSelect="true" :searchable="true"
                                :clearOnClose="true" placeholder="--" search-placeholder="--" />
                            <small class="db-field-alert" v-if="errors.item_type">{{ errors.item_type[0] }}</small>
                        </div>

                        <div class="form-col-12 sm:form-col-6">
                            <label class="db-field-title required" for="halalYes">{{ $t("label.is_it_halal") }}</label>
                            <div class="db-field-radio-group">
                                <div class="db-field-radio">
                                    <div class="custom-radio">
                                        <input type="radio" v-model="props.form.is_halal" id="halalYes"
                                            :value="enums.askEnum.YES" class="custom-radio-field">
                                        <span class="custom-radio-span"></span>
                                    </div>
                                    <label for="halalYes" class="db-field-label">{{ $t('label.yes') }}</label>
                                </div>
                                <div class="db-field-radio">
                                    <div class="custom-radio">
                                        <input type="radio" class="custom-radio-field" v-model="props.form.is_halal"
                                            id="halalNo" :value="enums.askEnum.NO">
                                        <span class="custom-radio-span"></span>
                                    </div>
                                    <label for="halalNo" class="db-field-label">{{ $t('label.no') }}</label>
                                </div>
                            </div>
                        </div>
                        <div class="form-col-12 sm:form-col-6">
                            <label for="available_time_start" class="db-field-title">{{ $t("label.available_time_start")
                                }}</label>
                            <Datepicker @update:modelValue="handleTimeStart" hideInputIcon v-model="props.startTime"
                                :time-picker="true" :time-picker-only="true" :is24="false" utc="false"
                                :input-class-name="errors.available_time_start ? 'invalid' : ''">
                            </Datepicker>
                            <small class="db-field-alert" v-if="errors.available_time_start">{{
                                errors.available_time_start[0] }}</small>
                        </div>

                        <div class="form-col-12 sm:form-col-6">
                            <label for="available_time_end" class="db-field-title">{{ $t("label.available_time_end")
                                }}</label>
                            <Datepicker @update:modelValue="handleTimeEnd" hideInputIcon v-model="props.endTime"
                                :time-picker="true" :time-picker-only="true" :is24="false" utc="false"
                                :input-class-name="errors.available_time_end ? 'invalid' : ''">
                            </Datepicker>
                            <small class="db-field-alert" v-if="errors.available_time_end">{{
                                errors.available_time_end[0] }}</small>
                        </div>

                        <div class="form-col-12 sm:form-col-6">
                            <label for="discount_type" class="db-field-title">{{ $t("label.discount_type") }}</label>
                            <vue-select class="db-field-control f-b-custom-select" id="discount_type"
                                v-model="props.form.discount_type" :options="[
                                    { id: enums.taxTypeEnum.FIXED, name: $t('label.fixed') },
                                    { id: enums.taxTypeEnum.PERCENTAGE, name: $t('label.percentage') }
                                ]" label-by="name" value-by="id" :closeOnSelect="true" :searchable="true"
                                :clearOnClose="true" placeholder="--" search-placeholder="--" />
                            <small class="db-field-alert" v-if="errors.discount_type">{{
                                errors.discount_type[0] }}</small>
                        </div>

                        <div class="form-col-12 sm:form-col-6" v-if="props.form.discount_type">
                            <label for="discount" class="db-field-title required">{{ $t("label.discount") }}</label>
                            <input v-model="props.form.discount" v-on:keypress="floatNumber($event)"
                                v-bind:class="errors.discount ? 'invalid' : ''" type="text" id="discount"
                                class="db-field-control" />
                            <small class="db-field-alert" v-if="errors.discount">{{ errors.discount[0] }}</small>
                        </div>

                        <div class="form-col-12 sm:form-col-6">
                            <label for="maximum_purchase_quantity" class="db-field-title required">{{
                                $t("label.maximum_purchase_quantity") }}</label>
                            <input v-model="props.form.maximum_purchase_quantity" v-on:keypress="onlyNumber($event)"
                                v-bind:class="errors.maximum_purchase_quantity ? 'invalid' : ''
                                    " type="text" id="maximum_purchase_quantity" class="db-field-control" />
                            <small class="db-field-alert" v-if="errors.maximum_purchase_quantity">{{
                                errors.maximum_purchase_quantity[0] }}</small>
                        </div>

                        <div class="form-col-12 sm:form-col-6">
                            <label class="db-field-title required">{{ $t("label.status") }}</label>
                            <div class="db-field-radio-group">
                                <div class="db-field-radio">
                                    <div class="custom-radio">
                                        <input type="radio" v-model="props.form.status" id="active"
                                            :value="enums.statusEnum.ACTIVE" class="custom-radio-field">
                                        <span class="custom-radio-span"></span>
                                    </div>
                                    <label for="active" class="db-field-label">{{ $t('label.active') }}</label>
                                </div>
                                <div class="db-field-radio">
                                    <div class="custom-radio">
                                        <input type="radio" class="custom-radio-field" v-model="props.form.status"
                                            id="inactive" :value="enums.statusEnum.INACTIVE">
                                        <span class="custom-radio-span"></span>
                                    </div>
                                    <label for="inactive" class="db-field-label">{{ $t('label.inactive') }}</label>
                                </div>
                            </div>
                        </div>

                        <div class="form-col-12">
                            <label for="caution" class="db-field-title">{{ $t("label.caution") }}</label>
                            <div :class="errors.caution ? 'invalid textarea-error-box-style' : ''"
                                class="custom-quill-editor">
                                <quill-editor id="caution" v-model:value="props.form.caution"
                                    class="!h-40 textarea-border-radius" />
                            </div>
                            <small class="db-field-alert" v-if="errors.caution">{{ errors.caution[0] }}</small>
                        </div>

                        <div class="form-col-12">
                            <div class="flex items-center justify-between">
                                <label for="description" class="db-field-title">{{ $t("label.description") }}</label>
                                <button v-if="aiStatus" type="button" @click="generateAiDescription" class="text-primary text-xs flex items-center cursor-pointer">
                                    <i class="lab-fill-ai text-[#8B5CF6]"></i>
                                    <span :class="aiDescriptionLoading ? '' : 'hidden'" class="ai-text-animation" role="status">
                                        {{ $t("label.just_a_second") }}
                                    </span>
                                    <span :class="!aiDescriptionLoading && !props.form.description ? '' : 'hidden'" class="btn-text">
                                        {{ $t("label.generate") }}
                                    </span>
                                    <span :class="!aiDescriptionLoading && props.form.description ? '' : 'hidden'" class="btn-text">
                                        {{ $t("label.regenerate") }}
                                    </span>
                                </button>
                            </div>
                            <div :class="errors.description ? 'invalid textarea-error-box-style' : ''"
                                class="custom-quill-editor">
                                <quill-editor id="description" v-model:value="props.form.description"
                                    class="!h-40 textarea-border-radius" />
                            </div>
                            <small class="db-field-alert" v-if="errors.description">{{ errors.description[0] }}</small>
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
import LoadingComponent from "../../common/LoadingComponent.vue";
import itemTypeEnum from "../../../enums/modules/itemTypeEnum.js";
import askEnum from "../../../enums/modules/askEnum.js";
import taxTypeEnum from "../../../enums/modules/taxTypeEnum.js";
import statusEnum from "../../../enums/modules/statusEnum.js";
import alertService from "../../../services/alertService.js";
import appService from "../../../services/appService.js";
import { useCanvas } from "../../../composables/canvas.js";
import { quillEditor } from 'vue3-quill';
import Datepicker from "@vuepic/vue-datepicker";
import "@vuepic/vue-datepicker/dist/main.css";
import { useItemCategoryStore } from "../../../stores/itemCategory.js";
import { useItemStore } from "../../../stores/item.js";
import { useTaxStore } from "../../../stores/tax.js";
import { useAiStore } from "../../../stores/ai.js";
import {useCommonStore} from "../../../stores/common.js";


export default {
    name: "ItemCreateComponent",
    components: { SmSidebarModalCreateComponent, LoadingComponent, quillEditor, Datepicker },
    props: ['props'],
    setup() {
        const itemCategoryStore = useItemCategoryStore();
        const itemStore = useItemStore();
        const taxStore = useTaxStore();
        const aiStore = useAiStore();
        const commonStore = useCommonStore();

        return {
            itemStore,
            itemCategoryStore,
            taxStore,
            aiStore,
            commonStore
        }
    },
    data() {
        return {
            loading: {
                isActive: false
            },
            aiNameLoading: false,
            aiDescriptionLoading: false,
            addButton: {
                title: this.$t("button.add_item")
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
                askEnumArray: {
                    [askEnum.YES]: this.$t("label.yes"),
                    [askEnum.NO]: this.$t("label.no")
                },
                taxTypeEnumArray: {
                    [taxTypeEnum.FIXED]: this.$t("label.fixed"),
                    [taxTypeEnum.PERCENTAGE]: this.$t("label.percentage")
                }
            },
            image: "",
            errors: {},
            closeBackdrop: useCanvas().closeBackdrop
        }
    },
    computed: {
        itemCategories: function () {
            return this.itemCategoryStore.lists;
        },
        taxes: function () {
            return this.taxStore.lists;
        },
        aiStatus: function () {
            return this.aiStore.status
        },
    },
    mounted() {
        this.loading.isActive = true;
        this.itemCategoryStore.fetch({
            order_column: 'id',
            order_type: 'asc',
            status: statusEnum.ACTIVE
        });
        this.taxStore.fetch({
            order_column: 'id',
            order_type: 'asc'
        });
        this.loading.isActive = false;
    },
    methods: {
        floatNumber(e) {
            return appService.floatNumber(e);
        },
        onlyNumber(e) {
            return appService.onlyNumber(e);
        },
        changeImage: function (e) {
            this.image = e.target.files[0];
        },
        handleTimeStart: function (e) {
            if (e) {
                this.props.form.available_time_start = e.hours + ':' + e.minutes + ':' + e.seconds;
            } else {
                this.props.form.available_time_start = null;
            }
        },
        handleTimeEnd: function (e) {
            if (e) {
                this.props.form.available_time_end = e.hours + ':' + e.minutes + ':' + e.seconds;
            } else {
                this.props.form.available_time_end = null;
            }
        },
        addReset: function () {
            this.itemStore.reset();
            this.errors = {};
            this.$props.props.startTime = '';
            this.$props.props.endTime = '';
            this.$props.props.form = {
                name: "",
                price: "",
                description: "",
                caution: "",
                item_type: null,
                item_category_id: null,
                tax_id: null,
                status: statusEnum.ACTIVE,
                is_halal: askEnum.NO,
                available_time_start: "",
                available_time_end: "",
                discount_type: null,
                discount: "",
                maximum_purchase_quantity: ""
            };
            if (this.image) {
                this.image = "";
                this.$refs.imageProperty.value = null;
            }
        },
        reset: function () {
            useCanvas().closeCanvas('sidebar');
            this.itemStore.reset();
            this.errors = {};
            this.$props.props.startTime = '';
            this.$props.props.endTime = '';
            this.$props.props.form = {
                name: "",
                price: "",
                description: "",
                caution: "",
                item_type: null,
                item_category_id: null,
                tax_id: null,
                status: statusEnum.ACTIVE,
                is_halal: askEnum.NO,
                available_time_start: "",
                available_time_end: "",
                discount_type: null,
                discount: "",
                maximum_purchase_quantity: ""
            };
            if (this.image) {
                this.image = "";
                this.$refs.imageProperty.value = null;
            }
        },
        save: function () {
            try {
                const fd = new FormData();
                fd.append('name', this.props.form.name);
                fd.append('price', this.props.form.price);
                fd.append('item_category_id', this.props.form.item_category_id == null ? '' : this.props.form.item_category_id);
                fd.append('tax_id', this.props.form.tax_id == null ? '' : this.props.form.tax_id);
                fd.append('item_type', this.props.form.item_type == null ? '' : this.props.form.item_type);
                fd.append('description', this.props.form.description);
                fd.append('caution', this.props.form.caution);
                fd.append('order', 1);
                fd.append('status', this.props.form.status);
                fd.append('is_halal', this.props.form.is_halal);
                fd.append('available_time_start', this.props.form.available_time_start == null ? '' : this.props.form.available_time_start);
                fd.append('available_time_end', this.props.form.available_time_end == null ? '' : this.props.form.available_time_end);
                fd.append('discount_type', this.props.form.discount_type == null ? '' : this.props.form.discount_type);
                fd.append('discount', this.props.form.discount_type == null ? 0 : this.props.form.discount);
                fd.append('maximum_purchase_quantity', this.props.form.maximum_purchase_quantity);
                if (this.image) {
                    fd.append('image', this.image);
                }
                const tempId = this.itemStore.temp.temp_id;
                this.loading.isActive = true;
                this.itemStore.save({
                    form: fd,
                    search: this.props.search
                }).then((res) => {
                    useCanvas().closeCanvas('sidebar');
                    this.loading.isActive = false;
                    alertService.successFlip((tempId === null ? 0 : 1), this.$t('menu.items'));
                    this.props.form = {
                        name: "",
                        price: "",
                        description: "",
                        caution: "",
                        item_type: null,
                        item_category_id: null,
                        tax_id: null,
                        status: statusEnum.ACTIVE,
                        is_halal: askEnum.NO,
                        available_time_start: "",
                        available_time_end: "",
                        discount_type: null,
                        discount: "",
                        maximum_purchase_quantity: ""
                    };
                    this.$props.props.startTime = '';
                    this.$props.props.endTime = '';
                    this.image = "";
                    this.errors = {};
                    this.$refs.imageProperty.value = null;
                }).catch((err) => {
                    this.loading.isActive = false;
                    if (err.response.data.status !== "undefined" && err.response.data.status === false) {
                        alertService.error(err.response.data.message)
                    } else {
                        this.errors = err.response.data.errors;
                    }
                })
            } catch (err) {
                this.loading.isActive = false;
                alertService.error(err)
            }
        },
        generateAiName: function () {
            if (!this.props.form.name) {
                alertService.warning(this.$t('message.item_name_is_required_to_generate_name'));
                return;
            }
            this.aiNameLoading = true;
            this.aiStore.fetchName({name: this.props.form.name}).then((res) => {
                this.aiNameLoading = false;
                if (res.data.data) {
                    this.props.form.name = res.data.data;
                }
            }).catch((err) => {
                this.aiNameLoading = false;
                alertService.error(err.response?.data?.message || err.message);
            });
        },
        generateAiDescription: function () {
            if (!this.props.form.name) {
                alertService.warning(this.$t('message.item_name_is_required_to_generate_description'));
                return;
            }
            this.aiDescriptionLoading = true;
            this.aiStore.fetchDescription({name: this.props.form.name}).then((res) => {
                this.aiDescriptionLoading = false;
                if (res.data.data) {
                    this.props.form.description = res.data.data;
                }
            }).catch((err) => {
                this.aiDescriptionLoading = false;
                alertService.error(err.response?.data?.message || err.message);
            });
        }
    }
}
</script>
