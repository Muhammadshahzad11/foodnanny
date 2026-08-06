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

    <PosReceiptComponent ref="receiptRef" :order="order"/>
    <CustomerReceiptPrintSheet
        :order="order"
        :restaurant="posOrderStore.restaurant"
        :items="receiptItems"
        :cashier-name="cashierName"
        :payment-label="paymentLabel"
        :table-label="tableLabel"
    />
    <KitchenTicketPrintSheet :payload="kotPayload"/>

    <!-- POS thank-you popup: KOT Print + Customer Print -->
    <teleport to="body">
        <div
            v-if="showThankYou"
            class="fixed inset-0 z-[90] flex items-center justify-center p-4 bg-black/45"
            @click.self="closeThankYou"
        >
            <div class="w-full max-w-md rounded-2xl bg-white shadow-2xl overflow-hidden max-h-[90vh] flex flex-col" @click.stop>
                <div class="p-6 text-center border-b border-[#EFF0F6]">
                    <div class="mx-auto mb-4 flex h-14 w-14 items-center justify-center rounded-full bg-[#1AB759]/10">
                        <span class="text-2xl text-[#1AB759]">✓</span>
                    </div>
                    <h3 class="text-xl font-semibold text-heading mb-1">{{ $t('label.thank_you') }}</h3>
                    <p class="text-base font-medium text-heading mb-1">{{ $t('label.order_placed') }}</p>
                    <p class="text-sm text-[#6E7191]" v-if="order.order_serial_no">
                        #{{ order.order_serial_no }}
                        <span v-if="tableLabel"> · {{ $t('label.table') }} {{ tableLabel }}</span>
                        <span v-if="orderTypeLabel"> · {{ orderTypeLabel }}</span>
                    </p>
                </div>

                <div class="px-6 py-4 overflow-y-auto text-left flex-1" v-if="receiptItems.length">
                    <p class="text-xs font-semibold uppercase tracking-wide text-[#6E7191] mb-2">
                        {{ $t('label.order_details') }}
                    </p>
                    <ul class="space-y-2 mb-4">
                        <li
                            v-for="(item, idx) in receiptItems"
                            :key="idx"
                            class="flex items-start justify-between gap-3 text-sm"
                        >
                            <p class="font-medium text-heading min-w-0">
                                {{ item.quantity }} × {{ item.item_name || item.name }}
                            </p>
                            <span class="shrink-0 text-heading">{{ item.total_currency_price }}</span>
                        </li>
                    </ul>
                    <div class="flex justify-between font-semibold text-heading text-sm border-t border-[#EFF0F6] pt-3">
                        <span>{{ $t('label.total') }}</span>
                        <span>{{ order.total_currency_price }}</span>
                    </div>
                </div>

                <div class="p-6 pt-2 flex flex-col gap-2 border-t border-[#EFF0F6]">
                    <div class="flex items-center justify-between gap-2 mb-1 px-1">
                        <span class="text-xs font-medium text-[#6E7191]">{{ $t('label.print_preview') }}</span>
                        <nav class="w-fit flex items-center justify-center p-0.5 rounded-md bg-[#FFF8F2]">
                            <button
                                type="button"
                                class="text-xs font-medium uppercase px-2 py-1 rounded"
                                :class="!printPreviewOn ? 'text-white bg-[#6E7191]' : 'text-[#6E7191]'"
                                @click.prevent="setPrintPreview(false)"
                            >{{ $t('label.off') }}</button>
                            <button
                                type="button"
                                class="text-xs font-medium uppercase px-2 py-1 rounded"
                                :class="printPreviewOn ? 'text-white bg-primary' : 'text-[#6E7191]'"
                                @click.prevent="setPrintPreview(true)"
                            >{{ $t('label.on') }}</button>
                        </nav>
                    </div>
                    <button
                        type="button"
                        class="capitalize text-sm font-medium leading-6 w-full text-center rounded-3xl py-2.5 text-white bg-amber-600"
                        :disabled="printingKot || printingReceipt"
                        @click.prevent="printKotOnly"
                    >
                        {{ printingKot ? 'Printing…' : $t('button.print_kot') }}
                    </button>
                    <button
                        type="button"
                        class="capitalize text-sm font-medium leading-6 w-full text-center rounded-3xl py-2.5 text-white bg-[#1AB759]"
                        :disabled="printingKot || printingReceipt"
                        @click.prevent="printCustomerOnly"
                    >
                        {{ printingReceipt ? 'Printing…' : $t('button.customer_print') }}
                    </button>
                    <button
                        type="button"
                        class="capitalize text-sm font-medium leading-6 w-full text-center rounded-3xl py-2.5 border border-[#EFF0F6] text-heading"
                        @click.prevent="closeThankYou"
                    >
                        {{ $t('button.done') }}
                    </button>
                </div>
            </div>
        </div>
    </teleport>
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
import CustomerReceiptPrintSheet from "../components/order/CustomerReceiptPrintSheet.vue";
import KitchenTicketPrintSheet from "../kitchen/KitchenTicketPrintSheet.vue";
import {printKot, printReceipt, PrintUnavailableError} from "../../../services/printService.js";
import {
    isPrintPreviewOn,
    isSilentPrintReady,
    setPrintPreviewOn,
} from "../../../services/printPreference.js";
import {useAuthStore} from "../../../stores/auth.js";
import orderTypeEnum from "../../../enums/modules/orderTypeEnum.js";

export default {
    name: "PaymentComponent",
    components: {
        PosReceiptComponent,
        CustomerReceiptPrintSheet,
        KitchenTicketPrintSheet,
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
        const authStore               = useAuthStore();

        return {
            handleTab,
            openModal,
            closeModal,
            posCartStore,
            posOrderStore,
            posOfferStore,
            createKeyboard,
            frontendSettingStore,
            authStore,
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
            order: {},
            kotPayload: null,
            showThankYou: false,
            placedOrderId: null,
            printingKot: false,
            printingReceipt: false,
            printPreviewOn: true,
        }
    },
    computed: {
        setting: function () {
            return this.frontendSettingStore.lists;
        },
        receiptItems() {
            const items = this.posOrderStore.orderItems;
            if (Array.isArray(items)) return items;
            return items ? Object.values(items) : [];
        },
        cashierName() {
            return this.authStore.info?.name || '';
        },
        paymentLabel() {
            const method = this.posOrderStore.posDetail?.payment_method;
            const map = {
                [posPaymentMethodEnum.CASH]: this.$t('label.cash'),
                [posPaymentMethodEnum.CARD]: this.$t('label.card'),
                [posPaymentMethodEnum.MOBILE_BANKING]: this.$t('label.mfs'),
                [posPaymentMethodEnum.OTHER]: this.$t('label.other'),
            };
            return map[method] || '';
        },
        tableLabel() {
            const t = this.order?.table;
            if (!t) return '';
            return t.table_number || t.name || '';
        },
        orderTypeLabel() {
            const t = Number(this.order?.order_type);
            if (t === orderTypeEnum.DINING_TABLE) return this.$t('label.dine_in');
            if (t === orderTypeEnum.DELIVERY) return this.$t('label.delivery');
            if (t === orderTypeEnum.TAKEAWAY || t === orderTypeEnum.POS) return this.$t('label.takeaway');
            return '';
        },
    },
    watch: {
        showThankYou(val) {
            if (val) {
                this.printPreviewOn = isPrintPreviewOn();
            }
        },
    },
    methods: {
        setPrintPreview(on) {
            setPrintPreviewOn(!!on);
            this.printPreviewOn = !!on;
            if (!on && !isSilentPrintReady()) {
                alertService.warning(this.$t('message.direct_print_setup_needed'));
            }
        },
        printErrorMessage(err, fallbackKey) {
            if (err instanceof PrintUnavailableError || err?.code === 'PRINT_UNAVAILABLE') {
                return this.$t('message.printer_not_connected');
            }
            return err?.response?.data?.message || err?.message || this.$t(fallbackKey);
        },
        currencyFormat: function (amount, decimal, currency, position) {
            return appService.currencyFormat(amount, decimal, currency, position);
        },
        floatNumber(e) {
            return appService.floatNumber(e);
        },
        prefillCashAmount() {
            const total = parseFloat(this.$props.props?.form?.total || 0);
            const decimals = parseInt(this.setting?.site_digit_after_decimal_point ?? 2, 10);
            const value = Number.isFinite(total) ? total.toFixed(decimals) : '0';
            if (this.$refs.paymentMethodCashInput) {
                this.$refs.paymentMethodCashInput.value = value;
            }
            this.$props.props.form.received_amount = value;
            this.inputIdName = 'paymentMethodCashInput';
        },
        reset: function () {
            Object.keys(this.$refs).forEach(refName => {
                if (this.$refs[refName]?.value !== undefined) {
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
                if (this.$refs[refName]?.value !== undefined) {
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

            // Exact cash amount by default — cashier can still overwrite via keypad
            if (method === posPaymentMethodEnum.CASH) {
                this.prefillCashAmount();
            }

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
        closeThankYou() {
            this.showThankYou = false;
            this.kotPayload = null;
            this.closeModal('receipt-modal');
        },
        async printCustomerOnly() {
            if (!this.placedOrderId) return;
            try {
                this.printingReceipt = true;
                await this.$nextTick();
                await printReceipt();
            } catch (err) {
                alertService.error(this.printErrorMessage(err, 'message.receipt_print_failed'));
            } finally {
                this.printingReceipt = false;
            }
        },
        async printKotOnly() {
            if (!this.placedOrderId) return;
            try {
                this.printingKot = true;
                const res = await this.posOrderStore.printKot(this.placedOrderId);
                this.kotPayload = res.data.data.payload;
                await this.$nextTick();
                await printKot();
            } catch (err) {
                alertService.error(this.printErrorMessage(err, 'message.kot_print_failed'));
            } finally {
                this.printingKot = false;
            }
        },
        confirmOrder: function () {
            try {
                if (this.$props.props.form.payment_method === posPaymentMethodEnum.CASH && this.$refs.paymentMethodCashInput.value) {
                    this.$props.props.form.received_amount = this.$refs.paymentMethodCashInput.value;
                } else {
                    this.$props.props.form.received_amount = null;
                }

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
                    const orderId = orderResponse.data.data.id;
                    this.placedOrderId = orderId;

                    this.$props.props.form.token                     = "";
                    this.$props.props.form.subtotal                  = 0;
                    this.$props.props.form.discount                  = 0;
                    this.$props.props.form.tax                       = 0;
                    this.$props.props.form.total                     = 0;
                    this.$props.props.form.items                     = [];
                    this.$props.props.form.payment_method            = posPaymentMethodEnum.CASH;
                    this.$props.props.form.payment_note              = null;
                    this.$props.props.form.received_amount           = null;
                    this.$props.props.form.order_type                = orderTypeEnum.TAKEAWAY;
                    this.$props.props.form.table_id                  = '';
                    this.$props.props.form.order_note                = '';
                    this.$refs.paymentMethodCashInput.value          = "";
                    this.$refs.paymentMethodCardInput.value          = "";
                    this.$refs.paymentMethodMobileBankingInput.value = "";
                    this.$refs.paymentMethodOtherInput.value         = "";
                    this.posCartStore.resetCart();

                    await this.posOfferStore.fetch().then(res => {
                        this.mainOffer = res.data.data;
                        this.offer     = this.mainOffer;
                    }).catch(() => {});

                    if (typeof this.$props.method === 'function') {
                        await this.$props.method({
                            mainOffer: this.mainOffer,
                            offer: this.offer,
                            discount: null,
                            discountType: discountTypeEnum.PERCENTAGE,
                        });
                    }

                    try {
                        const res = await this.posOrderStore.view(orderId);
                        this.order = res.data.data;
                    } catch (error) {
                        alertService.error(error.response?.data?.message || this.$t('message.something_wrong'));
                    }

                    this.loading.isActive = false;
                    this.closeModal('order-payment-modal');
                    // Thank-you with explicit KOT + Customer print buttons (no auto-print)
                    this.showThankYou = true;
                }).catch((err) => {
                    this.loading.isActive = false;
                    if (typeof err.response?.data?.errors === 'object') {
                        _.forEach(err.response.data.errors, (error) => {
                            alertService.error(error[0]);
                        });
                    } else {
                        alertService.error(err.response?.data?.message || this.$t('message.something_wrong'));
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
