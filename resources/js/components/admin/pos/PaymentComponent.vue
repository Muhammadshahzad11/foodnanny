<template>
    <LoadingComponent :props="loading"/>

    <div id="order-payment-modal" class="modal">
        <div class="modal-dialog max-w-[428px] w-full">
            <div class="modal-header pb-3 border-b border-[#D9DBE9]">
                <h3 class="capitalize font-medium">{{ $t('label.payment') }}</h3>
                <button class="modal-close lab-line-circle-cross text-xl" @click="reset"></button>
            </div>
            <div class="modal-body">
                <div class="mb-4">
                    <div
                        class="flex justify-between items-center h-12 w-full rounded-lg py-1.5 px-2 placeholder:text-[10px] placeholder:text-[#6E7191] bg-[#F7F7FC]">
                        <span class="text-sm font-normal text-[#2E2F38]">{{ $t('label.total_amount') }}</span>
                        <span class="text-primary text-base font-medium">
                            {{
                                currencyFormat(props.form.total, setting.site_digit_after_decimal_point, setting.site_default_currency_symbol, setting.site_currency_position)
                            }}
                        </span>
                    </div>
                </div>

                <div class="mb-4">
                    <h3 class="capitalize font-medium mb-2">{{ $t('label.payment_method') }}</h3>
                    <nav class="flex flex-wrap gap-4">
                        <button type="button"
                                class="w-fit flex flex-col items-center gap-2 rounded-lg py-3 px-7 border bg-[#F7F7FC] border-[#F7F7FC]"
                                :class="props.form.payment_method === enums.posPaymentMethodEnum.CASH ? 'method-title active' : ''"
                                @click="paymentMethod($event, enums.posPaymentMethodEnum.CASH, 'payment-method-cash-div' , 'paymentMethodCashInput')">
                            <i class="lab lab-fill-moneys text-2xl"></i>
                            <span class="text-xs font-normal leading-none text-heading">{{ $t("label.cash") }}</span>
                        </button>
                        <button type="button"
                                class="w-fit flex flex-col items-center gap-2 rounded-lg py-3 px-7 border bg-[#F7F7FC] border-[#F7F7FC]"
                                :class="props.form.payment_method === enums.posPaymentMethodEnum.CARD ? 'method-title active' : ''"
                                @click="paymentMethod($event, enums.posPaymentMethodEnum.CARD, 'payment-method-card-div', 'paymentMethodCardInput')">
                            <i class="lab lab-fill-card text-2xl"></i>
                            <span class="text-xs font-normal leading-none text-heading">{{ $t("label.card") }}</span>
                        </button>
                        <button type="button"
                                class="other-tabBtn w-fit flex flex-col items-center gap-2 rounded-lg py-3 px-7 border bg-[#F7F7FC] border-[#F7F7FC]"
                                :class="props.form.payment_method === enums.posPaymentMethodEnum.MOBILE_BANKING ? 'method-title active' : ''"
                                @click="paymentMethod($event, enums.posPaymentMethodEnum.MOBILE_BANKING, 'payment-method-mobile-banking-div', 'paymentMethodMobileBankingInput')">
                            <i class="lab lab-fill-mobile-banking text-2xl"></i>
                            <span class="text-xs font-normal leading-none text-heading">{{ $t("label.mfs") }}</span>
                        </button>
                        <button type="button"
                                class="other-tabBtn w-fit flex flex-col items-center gap-2 rounded-lg py-3 px-7 border bg-[#F7F7FC] border-[#F7F7FC]"
                                :class="props.form.payment_method === enums.posPaymentMethodEnum.OTHER ? 'method-title active' : ''"
                                @click="paymentMethod($event, enums.posPaymentMethodEnum.OTHER, 'payment-method-other-div', 'paymentMethodOtherInput')">
                            <i class="lab lab-fill-receipt text-2xl"></i>
                            <span class="text-xs font-normal leading-none text-heading">{{ $t("label.other") }}</span>
                        </button>
                    </nav>
                </div>

                <div id="payment-method-cash-div" class="tab-content tab-active">
                    <div class="mb-4">
                        <h3 class="capitalize font-medium mb-2">{{ $t("label.received_amount") }}</h3>
                        <input id="paymentMethodCashInput" ref="paymentMethodCashInput" type="text"
                               v-on:keypress="floatNumber($event)"
                               class="h-12 w-full rounded-lg border py-1.5 px-4 border-[#D9DBE9] text-black">
                    </div>
                </div>

                <div id="payment-method-card-div" class="tab-content">
                    <div class="mb-4">
                        <h3 class="capitalize font-medium mb-2">{{ $t('label.enter_card_last_4_digits') }}</h3>
                        <input id="paymentMethodCardInput" type="number" ref="paymentMethodCardInput"
                               class="h-12 w-full rounded-lg border py-1.5 px-4 border-[#D9DBE9] text-black">
                    </div>
                </div>

                <div id="payment-method-mobile-banking-div" class="tab-content">
                    <div class="mb-4">
                        <h3 class="capitalize font-medium mb-2">{{ $t('label.enter_transaction_id') }}</h3>
                        <input id="paymentMethodMobileBankingInput mfs-trans" type="text"
                               ref="paymentMethodMobileBankingInput"
                               class="h-12 w-full rounded-lg border py-1.5 px-4 placeholder:text-xs border-[#D9DBE9]">
                    </div>
                    <div class="board grid grid-cols-10 justify-between gap-1.5 mb-6"></div>
                </div>

                <div id="payment-method-other-div" class="tab-content">
                    <div class="mb-4">
                        <h3 class="capitalize font-medium mb-2">{{ $t('label.enter_payment_note') }}</h3>
                        <input id="paymentMethodOtherInput" type="text" ref="paymentMethodOtherInput"
                               class="h-12 w-full rounded-lg border py-1.5 px-4 placeholder:text-xs border-[#D9DBE9]">
                    </div>
                    <div class="board grid grid-cols-10 justify-between gap-1.5 mb-6"></div>
                </div>

                <div class="grid grid-cols-4 gap-x-4 gap-y-3.5 mb-6"
                     v-if="props.form.payment_method === enums.posPaymentMethodEnum.CASH || props.form.payment_method === enums.posPaymentMethodEnum.CARD">
                    <button @click="solve(1, inputIdName)" value="1" type="button"
                            class="num bg-[#F7F7FC] rounded-lg p-2.5 flex items-center justify-center text-base font-medium text-[#1F1F39]">
                        {{ $t('number.1') }}
                    </button>
                    <button @click="solve(2, inputIdName)" value="2" type="button"
                            class="num bg-[#F7F7FC] rounded-lg p-2.5 flex items-center justify-center text-base font-medium text-[#1F1F39]">
                        {{ $t('number.2') }}
                    </button>
                    <button @click="solve(3, inputIdName)" value="3" type="button"
                            class="num bg-[#F7F7FC] rounded-lg p-2.5 flex items-center justify-center text-base font-medium text-[#1F1F39]">
                        {{ $t('number.3') }}
                    </button>
                    <button @click="solve('back', inputIdName)" value="back" type="button"
                            class="num bg-[#F7F7FC] rounded-lg p-2.5 flex items-center justify-center text-base font-medium text-[#1F1F39] row-span-2">
                        <i class="lab lab-fill-tag-cross text-2xl"></i>
                    </button>
                    <button @click="solve(4, inputIdName)" value="4" type="button"
                            class="num bg-[#F7F7FC] rounded-lg p-2.5 flex items-center justify-center text-base font-medium text-[#1F1F39]">
                        {{ $t('number.4') }}
                    </button>
                    <button @click="solve(5, inputIdName)" value="5" type="button"
                            class="num bg-[#F7F7FC] rounded-lg p-2.5 flex items-center justify-center text-base font-medium text-[#1F1F39]">
                        {{ $t('number.5') }}
                    </button>
                    <button @click="solve(6, inputIdName)" value="6" type="button"
                            class="num bg-[#F7F7FC] rounded-lg p-2.5 flex items-center justify-center text-base font-medium text-[#1F1F39]">
                        {{ $t('number.6') }}
                    </button>
                    <button @click="solve(7, inputIdName)" value="7" type="button"
                            class="num bg-[#F7F7FC] rounded-lg p-2.5 flex items-center justify-center text-base font-medium text-[#1F1F39]">
                        {{ $t('number.7') }}
                    </button>
                    <button @click="solve(8, inputIdName)" value="8" type="button"
                            class="num bg-[#F7F7FC] rounded-lg p-2.5 flex items-center justify-center text-base font-medium text-[#1F1F39]">
                        {{ $t('number.8') }}
                    </button>
                    <button @click="solve(9, inputIdName)" value="9" type="button"
                            class="num bg-[#F7F7FC] rounded-lg p-2.5 flex items-center justify-center text-base font-medium text-[#1F1F39]">
                        {{ $t('number.9') }}
                    </button>
                    <button @click="solve('clear', inputIdName)" value="clear" type="button"
                            class="num bg-[#F7F7FC] rounded-lg p-2.5 flex items-center justify-center text-base font-medium text-[#1F1F39] row-span-2">
                        {{ $t('button.clear') }}
                    </button>
                    <button @click="solve('00', inputIdName)" value="00" type="button"
                            class="num bg-[#F7F7FC] rounded-lg p-2.5 flex items-center justify-center text-base font-medium text-[#1F1F39]">
                        {{ $t('number.00') }}
                    </button>
                    <button @click="solve(0, inputIdName)" value="0" type="button"
                            class="num bg-[#F7F7FC] rounded-lg p-2.5 flex items-center justify-center text-base font-medium text-[#1F1F39]">
                        {{ $t('number.0') }}
                    </button>
                    <button @click="solve('.', inputIdName)" value="point" type="button"
                            class="num bg-[#F7F7FC] rounded-lg p-2.5 flex items-center justify-center text-base font-medium text-[#1F1F39]">
                        .
                    </button>
                </div>

                <button @click="confirmOrder" type="button"
                        class="rounded-3xl text-base py-2 px-3 font-medium w-full text-white bg-primary">
                    {{ $t('button.confirm_and_print_receipt') }}
                </button>
            </div>
        </div>
    </div>

    <PosReceiptComponent :order="order"/>
</template>
<script>
import {useModal} from "../../../composables/modal.js";
import appService from "../../../services/appService.js";
import {useFrontendSettingStore} from "../../../stores/frontendSetting.js";
import posPaymentMethodEnum from "../../../enums/modules/posPaymentMethodEnum.js";
import {useTab} from "../../../composables/tab.js";
import LoadingComponent from "../../common/LoadingComponent.vue";
import {useKeyboard} from "../../../composables/keyboard.js";
import {usePosOrderStore} from "../../../stores/posOrder.js";
import alertService from "../../../services/alertService.js";
import _ from "lodash";
import {usePosCartStore} from "../../../stores/posCart.js";
import discountTypeEnum from "../../../enums/modules/discountTypeEnum.js";
import {usePosOfferStore} from "../../../stores/posOffer.js";
import PosReceiptComponent from "../components/order/PosReceiptComponent.vue";

export default {
    name: "PaymentComponent",
    components: {
        PosReceiptComponent,
        LoadingComponent
    },
    props: {
        props: Object,
        method: {
            type: Function,
            required: false
        },
    },
    setup() {
        const {handleTab}             = useTab();
        const {openModal, closeModal} = useModal();
        const {createKeyboard}        = useKeyboard();
        const posCartStore            = usePosCartStore();
        const posOrderStore           = usePosOrderStore();
        const posOfferStore           = usePosOfferStore();
        const frontendSettingStore    = useFrontendSettingStore();

        return {
            handleTab,
            openModal,
            closeModal,
            posCartStore,
            posOrderStore,
            posOfferStore,
            createKeyboard,
            frontendSettingStore
        }
    },
    data() {
        return {
            loading: {
                isActive: false,
            },
            enums: {
                posPaymentMethodEnum: posPaymentMethodEnum
            },
            inputIdName: "paymentMethodCashInput",
            mainOffer: {},
            offer: {},
            order: {}
        }
    },
    computed: {
        setting: function () {
            return this.frontendSettingStore.lists;
        },
    },
    methods: {
        currencyFormat: function (amount, decimal, currency, position) {
            return appService.currencyFormat(amount, decimal, currency, position);
        },
        floatNumber(e) {
            return appService.floatNumber(e);
        },
        reset: function () {
            Object.keys(this.$refs).forEach(refName => {
                if (this.$refs[refName].value !== undefined) {
                    this.$refs[refName].value = "";
                }
            });
            this.$props.props.form.payment_note              = null;
            this.$refs.paymentMethodCashInput.value          = "";
            this.$refs.paymentMethodCardInput.value          = "";
            this.$refs.paymentMethodMobileBankingInput.value = "";
            this.$refs.paymentMethodOtherInput.value         = "";
            this.closeModal('order-payment-modal');
        },
        paymentMethod: function (event, method, id, refName) {
            Object.keys(this.$refs).forEach(refName => {
                if (this.$refs[refName].value !== undefined) {
                    this.$refs[refName].value = "";
                }
            });

            this.handleTab(event, id);
            this.inputIdName                                 = refName;
            this.$props.props.form.payment_method            = method;
            this.$props.props.form.payment_note              = null;
            this.$refs.paymentMethodCashInput.value          = "";
            this.$refs.paymentMethodCardInput.value          = "";
            this.$refs.paymentMethodMobileBankingInput.value = "";
            this.$refs.paymentMethodOtherInput.value         = "";

            if (method === posPaymentMethodEnum.MOBILE_BANKING || method === posPaymentMethodEnum.OTHER) {
                this.createKeyboard(id);
            }
        },
        solve: function (val, id) {
            let v = document.getElementById(id);
            if (val === 'back') {
                v.value = v.value.slice(0, -1);
            } else if (val === 'clear') {
                v.value = '';
            } else {
                v.value += val;
            }
        },
        confirmOrder: function () {
            try {
                if (this.$props.props.form.payment_method === posPaymentMethodEnum.CASH && this.$refs.paymentMethodCashInput.value) {
                    this.$props.props.form.received_amount = this.$refs.paymentMethodCashInput.value;
                } else {
                    this.$props.props.form.received_amount = null;
                }


                this.$refs.paymentMethodCashInput.value;
                this.$refs.paymentMethodCardInput.value;
                this.$refs.paymentMethodMobileBankingInput.value;
                this.$refs.paymentMethodOtherInput.value;

                if (this.$props.props.form.payment_method === posPaymentMethodEnum.CARD && this.$refs.paymentMethodCardInput.value) {
                    this.$props.props.form.payment_note = this.$refs.paymentMethodCardInput.value;
                } else if (this.$props.props.form.payment_method === posPaymentMethodEnum.MOBILE_BANKING && this.$refs.paymentMethodMobileBankingInput.value) {
                    this.$props.props.form.payment_note = this.$refs.paymentMethodMobileBankingInput.value;
                } else if (this.$props.props.form.payment_method === posPaymentMethodEnum.OTHER && this.$refs.paymentMethodOtherInput.value) {
                    this.$props.props.form.payment_note = this.$refs.paymentMethodOtherInput.value;
                } else {
                    this.$props.props.form.payment_note = null;
                }

                this.loading.isActive = true;
                this.posOrderStore.save(this.$props.props.form).then(async orderResponse => {
                    this.$props.props.form.token                     = "";
                    this.$props.props.form.subtotal                  = 0;
                    this.$props.props.form.discount                  = 0;
                    this.$props.props.form.tax                       = 0;
                    this.$props.props.form.total                     = 0;
                    this.$props.props.form.items                     = [];
                    this.$props.props.form.payment_method            = posPaymentMethodEnum.CASH;
                    this.$props.props.form.payment_note              = null;
                    this.$props.props.form.received_amount           = null;
                    this.$refs.paymentMethodCashInput.value          = "";
                    this.$refs.paymentMethodCardInput.value          = "";
                    this.$refs.paymentMethodMobileBankingInput.value = "";
                    this.$refs.paymentMethodOtherInput.value         = "";
                    this.posCartStore.resetCart();

                    await this.posOfferStore.fetch().then(res => {
                        this.mainOffer = res.data.data;
                        this.offer     = this.mainOffer;
                    }).catch()

                    await this.$props.method({
                        mainOffer: this.mainOffer,
                        offer: this.offer,
                        discount: null,
                        discountType: discountTypeEnum.PERCENTAGE,
                    })

                    await this.posOrderStore.view(orderResponse.data.data.id).then(res => {
                        this.order            = res.data.data;
                        this.loading.isActive = false;
                    }).catch((error) => {
                        this.loading.isActive = false;
                        alertService.error(error.response.data.message);
                    });
                    this.closeModal('order-payment-modal');
                    this.openModal('receipt-modal');
                }).catch((err) => {
                    this.loading.isActive = false;
                    if (typeof err.response.data.errors === 'object') {
                        _.forEach(err.response.data.errors, (error) => {
                            alertService.error(error[0]);
                        });
                    }
                })
            } catch (err) {
                this.loading.isActive = false;
                alertService.error(err);
            }
        }
    }
}
</script>
