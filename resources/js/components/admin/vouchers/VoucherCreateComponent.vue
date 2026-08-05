<template>
    <LoadingComponent :props="loading" />
    <SmSidebarModalCreateComponent :props="addButton" @click="addReset" />

    <div id="sidebar" @click="closeBackdrop"
        class="fixed inset-0 z-50 bg-black/50 duration-500 transition-all invisible opacity-0">
        <div
            class="w-full max-w-xl h-dvh overflow-x-hidden thin-scrolling bg-white ms-auto ltr:translate-x-full rtl:-translate-x-full">
            <div class="flex items-center justify-between p-4 border-b border-slate-100">
                <h3 class="drawer-title">{{ $t("menu.vouchers") }}</h3>
                <button class="lab-line-close font-bold text-base" @click="reset"></button>
            </div>
            <div class="drawer-body">
                <form @submit.prevent="save">
                    <div class="form-row">
                        <div class="form-col-12 sm:form-col-6">
                            <label for="name" class="db-field-title required">{{ $t("label.name") }}</label>
                            <input v-model="props.form.name" v-bind:class="errors.name ? 'invalid' : ''" type="text"
                                id="name" class="db-field-control" />
                            <small class="db-field-alert" v-if="errors.name">{{ errors.name[0] }}</small>
                        </div>

                        <div class="form-col-12 sm:form-col-6">
                            <label for="code" class="db-field-title required">{{ $t("label.code") }}</label>
                            <input v-model="props.form.code" v-bind:class="errors.code ? 'invalid' : ''" type="text"
                                id="code" class="db-field-control" />
                            <small class="db-field-alert" v-if="errors.code">{{errors.code[0]}}</small>
                        </div>

                        <div class="form-col-12 sm:form-col-6">
                            <label for="type" class="db-field-title required">
                                {{ $t("label.type") }}
                            </label>
                            <vue-select class="db-field-control f-b-custom-select" id="type" v-model="props.form.type"
                                :options="[
                                    { id: enums.discountEnum.DEFAULT, name: $t('label.default') },
                                    { id: enums.discountEnum.FREE_DELIVERY, name: $t('label.free_delivery') }
                                ]" label-by="name" value-by="id" :closeOnSelect="true" :searchable="true" :clearOnClose="true" placeholder="--"
                                search-placeholder="--" />

                            <small class="db-field-alert" v-if="errors.type">{{errors.type[0]}}</small>
                        </div>

                        <div class="form-col-12 sm:form-col-6" v-if="props.form.type === enums.discountEnum.DEFAULT">
                            <label for="discount" class="db-field-title required">{{ $t("label.discount")}}</label>
                            <input v-model="props.form.discount" v-on:keypress="floatNumber($event)"
                                v-bind:class="errors.discount ? 'invalid' : ''" type="text" id="discount"
                                class="db-field-control" />
                            <small class="db-field-alert" v-if="errors.discount">{{ errors.discount[0] }}</small>
                        </div>

                        <div class="form-col-12 sm:form-col-6" v-if="props.form.type === enums.discountEnum.DEFAULT">
                            <label class="db-field-title required" for="active">{{ $t("label.discount_type") }}</label>
                            <div class="db-field-radio-group">
                                <div class="db-field-radio">
                                    <div class="custom-radio">
                                        <input :value="enums.taxTypeEnum.FIXED" v-model="props.form.discount_type"
                                            id="fixed" type="radio" class="custom-radio-field" />
                                        <span class="custom-radio-span"></span>
                                    </div>
                                    <label for="fixed" class="db-field-label">{{$t("label.fixed")}}</label>
                                </div>
                                <div class="db-field-radio">
                                    <div class="custom-radio">
                                        <input :value="enums.taxTypeEnum.PERCENTAGE" v-model="props.form.discount_type"
                                            type="radio" id="percentage" class="custom-radio-field" />
                                        <span class="custom-radio-span"></span>
                                    </div>
                                    <label for="percentage" class="db-field-label">{{ $t("label.percentage") }}</label>
                                </div>
                            </div>
                            <small class="db-field-alert" v-if="errors.discount_type">{{ errors.discount_type[0]}}</small>
                        </div>

                        <div class="form-col-12 sm:form-col-6">
                            <label for="limit_per_user" class="db-field-title">{{$t("label.limit_per_user") }}</label>
                            <input v-model="props.form.limit_per_user" v-on:keypress="floatNumber($event)" v-bind:class="errors.limit_per_user ? 'invalid' : ''
                                " type="text" id="limit_per_user" class="db-field-control" />
                            <small class="db-field-alert" v-if="errors.limit_per_user">{{ errors.limit_per_user[0] }}</small>
                        </div>

                        <div class="form-col-12 sm:form-col-6">
                            <label for="start_date" class="db-field-title required">{{ $t("label.start_date") }}</label>
                            <Datepicker autoApply v-model="props.form.start_date" :enableTimePicker="true"
                                :is24="false" :monthChangeOnScroll="false" utc="false" :hideInputIcon="true"
                                :input-class-name="errors.start_date ? 'invalid' : ''">
                                <template #am-pm-button="{ toggle, value }">
                                    <button @click.prevent="toggle">{{ value }}</button>
                                </template>
                            </Datepicker>
                            <small class="db-field-alert" v-if="errors.start_date">{{ errors.start_date[0] }}</small>
                        </div>
                        <div class="form-col-12 sm:form-col-6">
                            <label for="end_date" class="db-field-title required">{{ $t("label.end_date") }}</label>
                            <Datepicker autoApply v-model="props.form.end_date" :enableTimePicker="true" :hideInputIcon="true"
                                :is24="false" :monthChangeOnScroll="false" utc="false"
                                :input-class-name="errors.end_date ? 'invalid' : ''">
                                <template #am-pm-button="{ toggle, value }">
                                    <button @click="toggle">{{ value }}</button>
                                </template>
                            </Datepicker>
                            <small class="db-field-alert" v-if="errors.end_date">{{ errors.end_date[0] }}</small>
                        </div>
                        <div class="form-col-12 sm:form-col-6">
                            <label for="minimum_order" class="db-field-title required">{{ $t("label.minimum_order")}}</label>
                            <input v-model="props.form.minimum_order" v-on:keypress="floatNumber($event)"
                                v-bind:class="errors.minimum_order ? 'invalid' : ''" type="text" id="minimum_order"
                                class="db-field-control" />
                            <small class="db-field-alert" v-if="errors.minimum_order">{{ errors.minimum_order[0] }}</small>
                        </div>
                        <div class="form-col-12 sm:form-col-6" v-if="props.form.type === enums.discountEnum.DEFAULT && props.form.discount_type === enums.taxTypeEnum.PERCENTAGE">
                            <label for="maximum_discount" class="db-field-title required">{{ $t("label.maximum_discount") }}</label>
                            <input v-model="props.form.maximum_discount" v-on:keypress="floatNumber($event)"
                                v-bind:class="errors.maximum_discount ? 'invalid' : ''" type="text"
                                id="maximum_discount" class="db-field-control" />
                            <small class="db-field-alert" v-if="errors.maximum_discount">{{errors.maximum_discount[0]}}</small>
                        </div>
                        <div class="form-col-12 sm:form-col-12">
                            <label for="description" class="db-field-title">{{ $t("label.description") }}</label>
                            <div :class="errors.description ? 'invalid textarea-error-box-style' : ''" class="custom-quill-editor">
                                <quill-editor id="description" v-model:value="props.form.description" class="!h-40 textarea-border-radius" />
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
import LoadingComponent from "../../common/LoadingComponent.vue";
import SmSidebarModalCreateComponent from "../components/buttons/SmSidebarModalCreateComponent.vue";
import Datepicker from "@vuepic/vue-datepicker";
import "@vuepic/vue-datepicker/dist/main.css";
import taxTypeEnum from "../../../enums/modules/taxTypeEnum.js";
import alertService from "../../../services/alertService.js";
import appService from "../../../services/appService.js";
import { useCanvas } from "../../../composables/canvas.js";
import { quillEditor } from 'vue3-quill'
import discountEnum from "../../../enums/modules/discountEnum.js";
import {useVoucherStore} from "../../../stores/voucher.js";

export default {
    name: "VoucherCreateComponent",
    components: { SmSidebarModalCreateComponent, LoadingComponent, Datepicker, quillEditor },
    props: ["props"],
    setup() {
        const voucherStore = useVoucherStore();
        return {
            voucherStore
        }
    },
    data() {
        return {
            loading: {
                isActive: false
            },
            enums: {
                taxTypeEnum: taxTypeEnum,
                discountEnum: discountEnum,
                taxTypeEnumArray: {
                    [taxTypeEnum.FIXED]: this.$t("label.fixed"),
                    [taxTypeEnum.PERCENTAGE]: this.$t("label.percentage")
                }
            },
            errors: {},
            closeBackdrop: useCanvas().closeBackdrop
        };
    },
    computed: {
        addButton: function () {
            return { title: this.$t('button.add_voucher') };
        }
    },
    methods: {
        floatNumber(e) {
            return appService.floatNumber(e);
        },
        addReset: function () {
            this.voucherStore.reset();
            this.errors = {};
            this.$props.props.form = {
                name: "",
                description: "",
                code: "",
                discount: "",
                discount_type: taxTypeEnum.PERCENTAGE,
                start_date: "",
                end_date: "",
                minimum_order: "",
                maximum_discount: "",
                limit_per_user: "",
                type: discountEnum.DEFAULT
            };
        },
        reset: function () {
            useCanvas().closeCanvas('sidebar');
            this.voucherStore.reset();
            this.errors = {};
            this.$props.props.form = {
                name: "",
                description: "",
                code: "",
                discount: "",
                discount_type: taxTypeEnum.PERCENTAGE,
                start_date: "",
                end_date: "",
                minimum_order: "",
                maximum_discount: "",
                limit_per_user: "",
                type: discountEnum.DEFAULT,
            };
        },
        save: function () {
            try {
                const tempId = this.voucherStore.temp.temp_id;
                this.loading.isActive = true;
                this.voucherStore.save(this.props).then((res) => {
                        useCanvas().closeCanvas('sidebar');
                        this.loading.isActive = false;
                        alertService.successFlip(tempId === null ? 0 : 1, this.$t("menu.vouchers"));
                        this.props.form = {
                            name: "",
                            description: "",
                            code: "",
                            discount: "",
                            discount_type: taxTypeEnum.PERCENTAGE,
                            start_date: "",
                            end_date: "",
                            minimum_order: "",
                            maximum_discount: "",
                            limit_per_user: "",
                            type: discountEnum.DEFAULT,
                        };
                        this.errors = {};
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
