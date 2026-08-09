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
    <SimplePrintSetupModal v-model="showSimplePrintSetup"/>
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
import SimplePrintSetupModal from "./SimplePrintSetupModal.vue";
import {printKot, printReceipt, PrintUnavailableError} from "../../../services/printService.js";
import {
    isSilentPrintReady,
} from "../../../services/printPreference.js";
import {sendViaLocalBridge, probeLocalAgentInfo, localAgentSetupUrl} from "../../../services/localPrintBridge.js";
import {printBillIframe, printKotIframe} from "../../../services/thermalIframePrint.js";
import {useAuthStore} from "../../../stores/auth.js";
import orderTypeEnum from "../../../enums/modules/orderTypeEnum.js";

export default {
    name: "PaymentComponent",
    components: {
        PosReceiptComponent,
        CustomerReceiptPrintSheet,
        KitchenTicketPrintSheet,
        SimplePrintSetupModal,
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
            placedOrderId: null,
            showSimplePrintSetup: false,
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
            return this.order?.dining_table?.table_number
                || this.order?.dining_table?.name
                || this.order?.table_name
                || '';
        },
        orderTypeLabel() {
            const t = Number(this.order?.order_type);
            if (t === orderTypeEnum.DINING_TABLE) return this.$t('label.dine_in');
            if (t === orderTypeEnum.DELIVERY) return this.$t('label.delivery');
            if (t === orderTypeEnum.TAKEAWAY || t === orderTypeEnum.POS) return this.$t('label.takeaway');
            return '';
        },
    },
    methods: {
        printErrorMessage(err, fallbackKey) {
            if (err instanceof PrintUnavailableError || err?.code === 'PRINT_UNAVAILABLE') {
                return this.$t('message.printer_not_connected');
            }
            if (err?.code === 'LOCAL_BRIDGE_UNAVAILABLE') {
                return this.$t('message.printer_not_connected_direct');
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
        /**
         * Silent HTML print only — never opens Chrome print dialog.
         */
        async silentHtmlPrint(kind) {
            if (!isSilentPrintReady()) {
                throw new PrintUnavailableError(this.$t('message.printer_not_connected'));
            }
            if (kind === 'kot') {
                await printKot();
            } else {
                await printReceipt();
            }
        },
        async ensureKotPayload(printJobs = []) {
            const fromJob = printJobs.find((j) => j.type === 'kot' && j.payload);
            if (fromJob?.payload) {
                this.kotPayload = fromJob.payload;
                return this.kotPayload;
            }
            if (this.kotPayload) {
                return this.kotPayload;
            }
            if (!this.placedOrderId) {
                return null;
            }
            const res = await this.posOrderStore.printKot(this.placedOrderId);
            this.kotPayload = res.data?.data?.payload || null;
            return this.kotPayload;
        },
        /**
         * Automatic Direct Print via Local Agent (no Chrome popup).
         * KOT → network IP, Bill → Windows USB name — both through agent.
         */
        async runAutoPrintJobs(printJobs = []) {
            const jobs = Array.isArray(printJobs) ? printJobs : [];
            const expected = {
                kot: jobs.some((j) => j.type === 'kot'),
                invoice: jobs.some((j) => j.type === 'invoice'),
            };
            const done = { kot: false, invoice: false };

            jobs.forEach((job) => {
                if (job.status === 'printed') {
                    if (job.type === 'invoice') done.invoice = true;
                    if (job.type === 'kot') done.kot = true;
                }
            });

            const directJobs = jobs.filter((job) =>
                (job.mode === 'local_bridge' || job.mode === 'direct_print')
                && job.raw_base64
                && job.status !== 'printed'
            );

            // Prefer automatic Local Agent path (no popup)
            if (directJobs.length > 0) {
                const needsWindows = directJobs.some((j) => !!(j.windows_printer_name || '').trim());
                const agentInfo = await probeLocalAgentInfo(directJobs[0]?.bridge_port || 1811);
                if (!agentInfo.ok) {
                    alertService.error(this.$t('message.local_agent_required_auto_print'));
                    try {
                        window.open(localAgentSetupUrl(), '_blank', 'noopener');
                    } catch (e) {}
                    return;
                }
                if (needsWindows && (agentInfo.version < 3 || !agentInfo.features.includes('windows'))) {
                    alertService.error(this.$t('message.local_agent_outdated_usb'));
                    try {
                        window.open(localAgentSetupUrl(), '_blank', 'noopener');
                    } catch (e) {}
                    return;
                }

                for (const job of directJobs) {
                    try {
                        await sendViaLocalBridge(job);
                        if (job.type === 'invoice') done.invoice = true;
                        else done.kot = true;
                        await new Promise((r) => setTimeout(r, 250));
                    } catch (err) {
                        console.warn('Auto print failed', job?.type, err);
                        alertService.error(
                            (job.type === 'invoice'
                                ? this.$t('message.bill_auto_print_failed')
                                : this.$t('message.kot_auto_print_failed'))
                            + ' ' + (err?.message || '')
                        );
                    }
                }

                const kotOk = !expected.kot || done.kot;
                const invoiceOk = !expected.invoice || done.invoice;
                if (kotOk && invoiceOk) {
                    return;
                }
                return;
            }

            // Legacy Browser Popup jobs only (manual select) — not used for Direct Print auto mode
            const browserJobs = jobs.filter((j) => j.mode === 'browser_popup' || j.status === 'pending_browser');
            if (browserJobs.length > 0) {
                if (expected.kot && !done.kot) {
                    try {
                        const kotJob = browserJobs.find((j) => j.type === 'kot') || {};
                        await this.ensureKotPayload(jobs);
                        await this.$nextTick();
                        await printKotIframe(this.kotPayload || kotJob.payload || {}, kotJob.printer || '');
                        done.kot = true;
                        await new Promise((r) => setTimeout(r, 400));
                    } catch (err) {
                        console.warn('KOT browser print failed', err);
                    }
                }
                if (expected.invoice && !done.invoice) {
                    try {
                        const invJob = browserJobs.find((j) => j.type === 'invoice') || {};
                        await this.$nextTick();
                        if (!this.order?.id && this.placedOrderId) {
                            try {
                                const res = await this.posOrderStore.view(this.placedOrderId);
                                this.order = res.data.data;
                            } catch (e) {}
                        }
                        await printBillIframe(this.order || {}, {
                            restaurant: this.posOrderStore.restaurant || {},
                            items: this.receiptItems,
                            cashierName: this.cashierName,
                            paymentLabel: this.paymentLabel,
                            tableLabel: this.tableLabel,
                            printerHint: invJob.printer || '',
                        });
                        done.invoice = true;
                    } catch (err) {
                        console.warn('Bill browser print failed', err);
                    }
                }
            }

            const kotOk = !expected.kot || done.kot;
            const invoiceOk = !expected.invoice || done.invoice;
            if (!kotOk || !invoiceOk) {
                alertService.error(this.$t('message.print_jobs_incomplete'));
            }
        },
        confirmOrder: function () {
            try {
                if (this.$props.props.form.payment_method === posPaymentMethodEnum.CASH && this.$refs.paymentMethodCashInput.value) {
                    // Round to 2 decimals so 1967.86 is never rejected as "less than total"
                    const raw = parseFloat(this.$refs.paymentMethodCashInput.value);
                    const tot = parseFloat(this.$props.props.form.total);
                    this.$props.props.form.received_amount = Number.isFinite(raw) ? Math.round(raw * 100) / 100 : 0;
                    if (Number.isFinite(tot)) {
                        this.$props.props.form.total = Math.round(tot * 100) / 100;
                    }
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
                    const orderSerial = orderResponse.data.data.order_serial_no;
                    this.placedOrderId = orderId;
                    const printJobs = orderResponse.data.print_jobs || [];

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
                        // Order already saved — print can still proceed with payload from jobs
                    }

                    this.loading.isActive = false;
                    this.closeModal('order-payment-modal');

                    // No thank-you modal — short success toast only
                    const serial = orderSerial || this.order?.order_serial_no || orderId;
                    alertService.success(this.$t('message.order_placed_success', { serial: '#' + serial }));

                    await this.$nextTick();
                    await this.runAutoPrintJobs(printJobs);
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
        },
    }
}
</script>
