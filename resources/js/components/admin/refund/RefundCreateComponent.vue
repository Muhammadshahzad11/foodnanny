<template>
    <LoadingComponent :props="loading"/>
    <SmSidebarModalCreateComponent :props="addButton" @click="addReset"/>
    <div id="sidebar" @click="closeBackdrop"
         class="fixed inset-0 z-50 bg-black/50 duration-500 transition-all invisible opacity-0">
        <div
            class="w-full max-w-xl h-dvh overflow-x-hidden thin-scrolling bg-white ms-auto ltr:translate-x-full rtl:-translate-x-full">
            <div class="flex items-center justify-between p-4 border-b border-slate-100">
                <h3 class="drawer-title">{{ $t("menu.refunds") }}</h3>
                <button class="lab-line-close font-bold text-base" @click="reset"></button>
            </div>
            <div class="drawer-body">
                <form @submit.prevent="save">
                    <div class="form-row">
                        <div class="form-col-12 sm:form-col-12">
                            <label for="order_serial_no"
                                   class="db-field-title required">{{ $t("label.order_id") }}</label>
                            <input v-model="props.form.order_serial_no"
                                   v-bind:class="errors.order_serial_no ? 'invalid' : ''" type="text"
                                   id="order_serial_no" class="db-field-control"/>
                            <small class="db-field-alert" v-if="errors.order_serial_no">{{
                                    errors.order_serial_no[0]
                                }}</small>
                        </div>
                        <div class="form-col-12 sm:form-col-12">
                            <label for="refund_amount" class="db-field-title required">{{ $t("label.refund_amount") }} ({{ $t("label.to_customer") }})</label>
                            <input v-model="props.form.refund_amount" v-on:keypress="floatNumber($event)" v-bind:class="errors.refund_amount ? 'invalid' : ''" type="text" id="refund_amount" class="db-field-control"/>
                            <small class="db-field-alert" v-if="errors.refund_amount">{{ errors.refund_amount[0] }}</small>
                        </div>

                        <div class="form-col-12 sm:form-col-12">
                            <label for="deduction_amount" class="db-field-title required">{{ $t("label.deduction_amount") }} ({{ $t("label.from_responsible") }})</label>
                            <input v-model="props.form.deduction_amount" v-on:keypress="floatNumber($event)" v-bind:class="errors.deduction_amount ? 'invalid' : ''" type="text" id="deduction_amount" class="db-field-control"/>
                            <small class="db-field-alert" v-if="errors.deduction_amount">{{ errors.deduction_amount[0] }}</small>
                        </div>

                        <div class="form-col-12 sm:form-col-12">
                            <label for="responsible" class="db-field-title required">{{
                                    $t("label.responsible")
                                }}</label>
                            <vue-select :class="errors.responsible ? 'invalid' : ''"
                                        class="db-field-control f-b-custom-select" id="responsible"
                                        v-model="props.form.responsible"
                                        :options="[
                                    { id: enums.modelTypeEnum.RESTAURANT, name: $t('label.restaurant') },
                                    { id: enums.modelTypeEnum.DELIVERY_BOY, name: $t('label.delivery_boy') }
                                ]" label-by="name" value-by="id" :closeOnSelect="true" :searchable="true"
                                        :clearOnClose="true" placeholder="--" search-placeholder="--"/>
                            <small class="db-field-alert" v-if="errors.responsible">{{ errors.responsible[0] }}</small>
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
import alertService from "../../../services/alertService.js";
import appService from "../../../services/appService.js";
import {useCanvas} from "../../../composables/canvas.js";
import modelTypeEnum from "../../../enums/modules/modelTypeEnum.js";
import {useRefundStore} from "../../../stores/refund.js";
import SmSidebarModalCreateComponent from "../components/buttons/SmSidebarModalCreateComponent.vue";

export default {
    name: "RefundCreateComponent",
    components: {SmSidebarModalCreateComponent, LoadingComponent},
    props: ["props"],
    setup() {
        const refundStore = useRefundStore();
        return {
            refundStore
        }
    },
    data() {
        return {
            loading: {
                isActive: false
            },
            enums: {
                modelTypeEnum: modelTypeEnum
            },
            addButton: {
                title: this.$t("button.add_refund")
            },
            errors: {},
            closeBackdrop: useCanvas().closeBackdrop
        };
    },
    methods: {
        floatNumber(e) {
            return appService.floatNumber(e);
        },
        addReset: function () {
            this.errors            = {};
            this.$props.props.form = {
                order_serial_no: "",
                responsible: null,
                refund_amount: "",
                deduction_amount: ""
            }
        },
        reset: function () {
            useCanvas().closeCanvas('sidebar');
            this.errors            = {};
            this.$props.props.form = {
                order_serial_no: "",
                responsible: null,
                refund_amount: "",
                deduction_amount: ""
            }
        },
        save: function () {
            try {
                this.loading.isActive = true;
                this.refundStore.save(this.props).then((res) => {
                    useCanvas().closeCanvas('sidebar');
                    this.loading.isActive = false;
                    alertService.successFlip(0, this.$t("menu.refunds"));
                    this.errors     = {};
                    this.props.form = {
                        order_serial_no: "",
                        responsible: null,
                        refund_amount: "",
                        deduction_amount: ""
                    }
                }).catch((err) => {
                    this.loading.isActive = false;
                    this.errors           = err.response.data.errors;
                });
            } catch (err) {
                this.loading.isActive = false;
                alertService.error(err);
            }
        }
    }
}
</script>
