<template>
    <LoadingComponent :props="loading"/>
    <OrderDetailsComponent :order="order" :orderItems="orderItems" :orderUser="orderUser" :orderRestaurant="{}" :orderAddress="{}">
        <div class="flex flex-col items-stretch sm:items-end gap-3 w-full max-w-md">
            <div class="w-full h-10 rounded-md ps-3 p-[1px] border border-[#E5E7EB] flex items-center justify-between gap-3 bg-white">
                <span class="text-[#6E7191] text-sm font-normal flex gap-1 items-center min-w-0">
                    {{ $t('label.print_preview') }}
                    <div class="group relative shrink-0">
                        <i class="lab-line-info-circle text-base text-[#6E7191]"></i>
                        <span class="inline-block absolute min-w-[220px] max-w-[280px] w-auto z-[999] -top-1 right-0 translate-y-10 text-xs rounded-md py-1.5 px-2 bg-gray-800 text-white before:absolute before:w-2 before:h-2 before:bg-gray-800 before:rotate-45 before:right-4 before:-top-1 group-hover:opacity-100 group-hover:visible group-hover:-top-2 opacity-0 invisible transition-all duration-300">
                            {{ $t('message.print_preview_help') }}
                        </span>
                    </div>
                </span>
                <nav class="w-fit h-full flex items-center justify-center p-0.5 rounded-md bg-[#FFF8F2] shrink-0">
                    <button
                        type="button"
                        @click.prevent="setPrintPreview(false)"
                        :class="!printPreviewOn ? 'text-white bg-[#6E7191]' : 'text-[#6E7191]'"
                        class="text-sm font-medium uppercase h-full px-2 rounded"
                    >
                        {{ $t('label.off') }}
                    </button>
                    <button
                        type="button"
                        @click.prevent="setPrintPreview(true)"
                        :class="printPreviewOn ? 'text-white bg-primary' : 'text-[#6E7191]'"
                        class="text-sm font-medium uppercase h-full px-2 rounded"
                    >
                        {{ $t('label.on') }}
                    </button>
                </nav>
            </div>
            <p class="text-[11px] leading-4 text-[#6E7191] text-right">
                <template v-if="printPreviewOn">
                    {{ $t('message.browser_popup_pos_hint') || 'Preview ON opens the browser print dialog.' }}
                </template>
                <template v-else>
                    {{ $t('message.local_print_agent_pos_hint') }}
                    <a class="underline font-semibold" href="/local-print-agent/" target="_blank" rel="noopener">
                        {{ $t('label.setup_local_print_agent') }}
                    </a>
                </template>
            </p>

            <div class="flex flex-wrap gap-3 justify-end"
                 v-if="order.status !== enums.orderStatusEnum.REJECTED && order.status !== enums.orderStatusEnum.CANCELED">
                <button type="button" v-if="order.status === enums.orderStatusEnum.ACCEPT" @click="preparing"
                        class="flex items-center justify-center text-white gap-2 px-4 h-[38px] rounded shadow-db-card bg-[#2AC769]">
                    <i class="lab lab-fill-preparing"></i>
                    <span class="text-sm capitalize text-white">{{ $t('label.preparing') }}</span>
                </button>

                <button type="button" v-if="order.status === enums.orderStatusEnum.PREPARING" @click="prepared"
                        class="flex items-center justify-center text-white gap-2 px-4 h-[38px] rounded shadow-db-card bg-[#2AC769]">
                    <i class="lab lab-fill-reserve"></i>
                    <span class="text-sm capitalize text-white">{{ $t('label.prepared') }}</span>
                </button>

                <button type="button"
                        v-if="order.order_type === enums.orderTypeEnum.POS && order.status === enums.orderStatusEnum.PREPARED"
                        @click="delivered"
                        class="flex items-center justify-center text-white gap-2 px-4 h-[38px] rounded shadow-db-card bg-[#2AC769]">
                    <i class="lab lab-fill-delivered"></i>
                    <span class="text-sm capitalize text-white">{{ $t('button.confirm_delivery') }}</span>
                </button>

                <button type="button"
                        v-if="canTakePayment"
                        @click.prevent="openPayment"
                        class="flex items-center justify-center gap-2 px-4 h-[38px] rounded shadow-db-card bg-[#FF8A00]">
                    <i class="lab lab-fill-moneys lab-font-size-16 text-white"></i>
                    <span class="text-sm capitalize text-white">{{ $t('label.payment') }}</span>
                </button>

                <button type="button"
                        @click.prevent="printInvoice"
                        class="flex items-center justify-center gap-2 px-4 h-[38px] rounded shadow-db-card bg-primary">
                    <i class="lab lab-fill-printer lab-font-size-16 text-white"></i>
                    <span class="text-sm capitalize text-white">{{ $t('button.print_invoice') }}</span>
                </button>
            </div>
        </div>

        <PosReceiptComponent ref="receiptRef" :order="order"/>
    </OrderDetailsComponent>

    <PaymentComponent ref="paymentRef" :method="onPaymentComplete" :props="checkoutProps"/>
    <SimplePrintSetupModal v-model="showSimplePrintSetup" @ready="onSimplePrintReady"/>
</template>
<script>
import LoadingComponent from "../../common/LoadingComponent.vue";
import {usePosOrderStore} from "../../../stores/posOrder.js";
import OrderDetailsComponent from "../components/OrderDetailsComponent.vue";
import orderStatusEnum from "../../../enums/modules/orderStatusEnum.js";
import VueSimpleAlert from "vue3-simple-alert";
import alertService from "../../../services/alertService.js";
import orderTypeEnum from "../../../enums/modules/orderTypeEnum.js";
import PosReceiptComponent from "../components/order/PosReceiptComponent.vue";
import PaymentComponent from "../pos/PaymentComponent.vue";
import SimplePrintSetupModal from "../pos/SimplePrintSetupModal.vue";
import {
    isPrintPreviewOn,
    setPrintPreviewOn,
    isSilentPrintReady,
    syncSilentPrintFromUrl,
} from "../../../services/printPreference.js";
import {printBillIframe} from "../../../services/thermalIframePrint.js";
import {sendViaLocalBridge, probeLocalAgentInfo, localAgentSetupUrl} from "../../../services/localPrintBridge.js";
import posPaymentMethodEnum from "../../../enums/modules/posPaymentMethodEnum.js";
import paymentStatusEnum from "../../../enums/modules/paymentStatusEnum.js";
import {useModal} from "../../../composables/modal.js";

export default {
    name: "PosOrderShowComponent",
    components: {
        SimplePrintSetupModal,
        PaymentComponent,
        PosReceiptComponent,
        OrderDetailsComponent,
        LoadingComponent,
    },
    setup() {
        const posOrderStore = usePosOrderStore();
        const {openModal} = useModal();

        return {
            posOrderStore,
            openModal,
        }
    },

    data() {
        return {
            loading: {
                isActive: false
            },
            enums: {
                orderStatusEnum: orderStatusEnum,
                orderTypeEnum: orderTypeEnum,
                paymentStatusEnum: paymentStatusEnum,
            },
            printPreviewOn: false,
            silentPrintReady: false,
            showSimplePrintSetup: false,
            checkoutProps: {
                form: {
                    subtotal: 0,
                    token: "",
                    discount: 0,
                    tax: 0,
                    total: 0,
                    items: "[]",
                    payment_method: posPaymentMethodEnum.CASH,
                    payment_note: null,
                    received_amount: null,
                    order_type: orderTypeEnum.TAKEAWAY,
                    table_id: '',
                    order_note: '',
                    customer_name: '',
                    customer_phone: '',
                    customer_address: '',
                    delivery_note: '',
                    place_only: false,
                    close_with_payment: true,
                    editing_order_id: null,
                }
            },
        }
    },
    computed: {
        order: function () {
            return this.posOrderStore.show;
        },
        orderItems: function () {
            return this.posOrderStore.orderItems;
        },
        orderUser: function () {
            return this.posOrderStore.orderUser;
        },
        canTakePayment() {
            if (!this.order?.id) return false;
            if (Number(this.order.payment_status) === paymentStatusEnum.PAID) return false;
            const status = Number(this.order.status);
            if ([orderStatusEnum.DELIVERED, orderStatusEnum.CANCELED, orderStatusEnum.REJECTED].includes(status)) {
                return false;
            }
            return true;
        },
        paymentLabel() {
            const method = Number(this.posOrderStore.posDetail?.payment_method);
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
            return [t.name, t.table_number].filter(Boolean).join(' · ');
        },
    },
    mounted() {
        syncSilentPrintFromUrl();
        this.printPreviewOn = isPrintPreviewOn();
        this.silentPrintReady = isSilentPrintReady();
        this._onPrintPreviewChanged = (e) => {
            this.printPreviewOn = !!(e?.detail?.on ?? isPrintPreviewOn());
            this.silentPrintReady = isSilentPrintReady();
        };
        window.addEventListener('fn-print-preview-changed', this._onPrintPreviewChanged);

        this.loading.isActive = true;
        this.posOrderStore.view(this.$route.params.id).then(res => {
            this.loading.isActive = false;
        }).catch((error) => {
            this.loading.isActive = false;
        });
    },
    beforeUnmount() {
        if (this._onPrintPreviewChanged) {
            window.removeEventListener('fn-print-preview-changed', this._onPrintPreviewChanged);
        }
    },
    methods: {
        num(v) {
            const n = parseFloat(v);
            return Number.isFinite(n) ? Math.round(n * 100) / 100 : 0;
        },
        buildPaymentItems() {
            const items = Array.isArray(this.orderItems) ? this.orderItems : Object.values(this.orderItems || {});
            if (!items.length) {
                return JSON.stringify([{item_id: 0, quantity: 1, item_price: 0, total_price: 0}]);
            }
            return JSON.stringify(items.map((item) => ({
                item_id: item.item_id || 0,
                item_price: this.num(item.convert_price),
                instruction: item.instruction || '',
                quantity: item.quantity || 1,
                discount: 0,
                total_price: this.num(item.total_convert_price),
                item_variation_total: this.num(item.item_variation_total),
                item_extra_total: this.num(item.item_extra_total),
                item_variations: item.item_variations || [],
                item_extras: item.item_extras || [],
                tax_name: item.tax_name || '',
                tax_rate: item.tax_rate || 0,
                tax_type: item.tax_type_value ?? 5,
                tax_amount: this.num(item.tax_amount),
            })));
        },
        openPayment() {
            if (!this.canTakePayment) return;
            const order = this.order;
            const orderType = Number(order.order_type) === orderTypeEnum.POS
                ? orderTypeEnum.TAKEAWAY
                : Number(order.order_type) || orderTypeEnum.TAKEAWAY;

            this.checkoutProps.form.subtotal = this.num(order.subtotal);
            this.checkoutProps.form.discount = this.num(order.discount);
            this.checkoutProps.form.tax = this.num(order.total_tax);
            this.checkoutProps.form.total = this.num(order.total);
            this.checkoutProps.form.token = order.token || '';
            this.checkoutProps.form.items = this.buildPaymentItems();
            this.checkoutProps.form.payment_method = posPaymentMethodEnum.CASH;
            this.checkoutProps.form.payment_note = null;
            this.checkoutProps.form.received_amount = null;
            this.checkoutProps.form.order_type = orderType;
            this.checkoutProps.form.table_id = order.table_id || order.table?.id || '';
            this.checkoutProps.form.order_note = order.order_note || '';
            this.checkoutProps.form.customer_name = order.customer_name || '';
            this.checkoutProps.form.customer_phone = order.customer_phone || '';
            this.checkoutProps.form.customer_address = order.customer_address || '';
            this.checkoutProps.form.delivery_note = order.delivery_note || '';
            this.checkoutProps.form.place_only = false;
            this.checkoutProps.form.close_with_payment = true;
            this.checkoutProps.form.editing_order_id = order.id;

            this.openModal('order-payment-modal');
            this.$nextTick(() => {
                this.$refs.paymentRef?.prefillCashAmount?.();
            });
        },
        async onPaymentComplete() {
            try {
                await this.posOrderStore.view(this.$route.params.id);
                alertService.success(this.$t('message.payment_successful') || 'Payment completed');
            } catch (e) {
                // page will still show previous data until refresh
            }
        },
        setPrintPreview(on) {
            setPrintPreviewOn(!!on);
            this.printPreviewOn = !!on;
            this.silentPrintReady = isSilentPrintReady();
            if (!on && !this.silentPrintReady) {
                this.showSimplePrintSetup = true;
            }
        },
        onSimplePrintReady() {
            this.silentPrintReady = isSilentPrintReady();
            this.printPreviewOn = false;
        },
        async printInvoice() {
            if (!this.order?.id) return;
            this.loading.isActive = true;
            try {
                if (this.printPreviewOn) {
                    await this.printBrowserInvoice();
                    alertService.success(this.$t('message.bill_printed') || 'Invoice sent to printer');
                    return;
                }

                const res = await this.posOrderStore.printInvoice(this.order.id);
                (res.data.warnings || []).forEach((w) => {
                    try { alertService.error(w); } catch (e) {}
                });
                await this.runPrintJobs(res.data.print_jobs || []);
                alertService.success(this.$t('message.bill_printed') || 'Invoice sent to printer');
            } catch (err) {
                alertService.error(err.response?.data?.message || this.$t('message.something_wrong'));
            } finally {
                this.loading.isActive = false;
            }
        },
        async printBrowserInvoice() {
            const items = Array.isArray(this.orderItems) ? this.orderItems : Object.values(this.orderItems || {});
            await printBillIframe(this.order || {}, {
                restaurant: this.posOrderStore.restaurant || {},
                items: items.map((i) => ({
                    name: i.item_name || i.name,
                    quantity: i.quantity,
                    total_price: i.total_currency_price || i.total_price,
                    item_variations: i.item_variations,
                    item_extras: i.item_extras,
                    instruction: i.instruction,
                })),
                cashierName: this.order?.waiter?.name || '',
                paymentLabel: this.paymentLabel,
                tableLabel: this.tableLabel,
            });
        },
        async runPrintJobs(printJobs = []) {
            const jobs = Array.isArray(printJobs) ? printJobs : [];
            const directJobs = jobs.filter((job) =>
                (job.mode === 'local_bridge' || job.mode === 'direct_print')
                && job.raw_base64
                && job.status !== 'printed'
                && job.status !== 'skipped'
            );

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
                        await new Promise((r) => setTimeout(r, 250));
                    } catch (err) {
                        alertService.error(
                            this.$t('message.bill_auto_print_failed') + ' ' + (err?.message || '')
                        );
                    }
                }
                return;
            }

            const browserJobs = jobs.filter((j) =>
                j.type === 'invoice'
                && (j.mode === 'browser_popup' || j.status === 'pending_browser')
            );
            if (browserJobs.length > 0 || jobs.length === 0) {
                await this.printBrowserInvoice();
                return;
            }

            if (this.silentPrintReady) {
                await this.printBrowserInvoice();
                return;
            }
            alertService.error(this.$t('message.printer_not_connected'));
            this.showSimplePrintSetup = true;
        },
        preparing: function () {
            return new VueSimpleAlert.confirm(
                this.$t('message.prepare_order'),
                this.$t('message.are_you_sure'),
                "warning",
                {
                    confirmButtonText: this.$t('button.yes_preparing'),
                    cancelButtonText: this.$t('button.no_cancel'),
                    confirmButtonColor: "#1AB759",
                    cancelButtonColor: "#E93C3C"
                }
            ).then(res => {
                try {
                    this.loading.isActive = true;
                    this.posOrderStore.changeStatus({
                        id: this.$route.params.id,
                        status: this.enums.orderStatusEnum.PREPARING
                    }).then((res) => {
                        this.loading.isActive = false;
                        alertService.successFlip(1, this.$t("label.status"));
                    }).catch((err) => {
                        this.loading.isActive = false;
                    });
                } catch (err) {
                    this.loading.isActive = false;
                }
            }).catch((err) => {
            })
        },
        prepared: function () {
            return new VueSimpleAlert.confirm(
                this.$t('message.prepared_order'),
                this.$t('message.are_you_sure'),
                "warning",
                {
                    confirmButtonText: this.$t('button.yes_prepared'),
                    cancelButtonText: this.$t('button.no_cancel'),
                    confirmButtonColor: "#1AB759",
                    cancelButtonColor: "#E93C3C"
                }
            ).then(res => {
                try {
                    this.loading.isActive = true;
                    this.posOrderStore.changeStatus({
                        id: this.$route.params.id,
                        status: this.enums.orderStatusEnum.PREPARED
                    }).then((res) => {
                        this.loading.isActive = false;
                        alertService.successFlip(1, this.$t("label.status"));
                    }).catch((err) => {
                        this.loading.isActive = false;
                    });
                } catch (err) {
                    this.loading.isActive = false;
                }
            }).catch((err) => {
            })
        },
        delivered: function () {
            return new VueSimpleAlert.confirm(
                this.$t('message.collect_the_fee', {amount : this.order.total_currency_price}),
                this.$t('message.are_you_sure'),
                "warning",
                {
                    confirmButtonText: this.$t('button.yes_collected'),
                    cancelButtonText: this.$t('button.no_cancel'),
                    confirmButtonColor: "#1AB759",
                    cancelButtonColor: "#E93C3C"
                }
            ).then(res => {
                try {
                    this.loading.isActive = true;
                    this.posOrderStore.changeStatus({
                        id: this.$route.params.id,
                        status: this.enums.orderStatusEnum.DELIVERED
                    }).then((res) => {
                        this.loading.isActive = false;
                        alertService.successFlip(1, this.$t("label.status"));
                    }).catch((err) => {
                        this.loading.isActive = false;
                    });
                } catch (err) {
                    this.loading.isActive = false;
                }
            }).catch((err) => {
            })
        }
    }
}
</script>
