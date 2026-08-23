<template>
    <LoadingComponent :props="loading"/>
    <OrderDetailsComponent
        :order="order"
        :orderItems="orderItems"
        :orderUser="orderUser"
        :orderRestaurant="orderRestaurant"
        :orderAddress="orderAddress"
        :orderDeliveryBoy="orderDeliveryBoy"
    >
        <div class="flex flex-col items-stretch sm:items-end gap-3 w-full max-w-md">
            <div
                v-if="order.status !== enums.orderStatusEnum.REJECTED && order.status !== enums.orderStatusEnum.CANCELED"
                class="w-full h-10 rounded-md ps-3 p-[1px] border border-[#E5E7EB] flex items-center justify-between gap-3 bg-white"
            >
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
            <p
                v-if="order.status !== enums.orderStatusEnum.REJECTED && order.status !== enums.orderStatusEnum.CANCELED"
                class="text-[11px] leading-4 text-[#6E7191] text-right"
            >
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

            <div class="flex flex-wrap gap-3 justify-end" v-if="order.status === enums.orderStatusEnum.PENDING">
                <ReasonComponent/>
                <button type="button" @click="accept"
                        class="flex items-center justify-center text-white gap-2 px-4 h-[38px] rounded shadow-db-card bg-[#2AC769]">
                    <i class="lab lab-fill-save"></i>
                    <span class="text-sm capitalize text-white">{{ $t('button.accept') }}</span>
                </button>
            </div>

            <div
                class="flex flex-wrap gap-3 justify-end"
                v-else-if="order.status !== enums.orderStatusEnum.REJECTED && order.status !== enums.orderStatusEnum.CANCELED"
            >
                <div
                    v-if="!order.token && order.order_type === enums.orderTypeEnum.TAKEAWAY && ![enums.orderStatusEnum.DELIVERED, enums.orderStatusEnum.CANCELED, enums.orderStatusEnum.REJECTED, enums.orderStatusEnum.RETURNED].includes(order.status)"
                >
                    <OnlineOrderTokenComponent/>
                </div>

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
                        v-if="order.order_type === enums.orderTypeEnum.TAKEAWAY && order.status === enums.orderStatusEnum.PREPARED"
                        @click="delivered"
                        class="flex items-center justify-center text-white gap-2 px-4 h-[38px] rounded shadow-db-card bg-[#2AC769]">
                    <i class="lab lab-fill-delivered"></i>
                    <span class="text-sm capitalize text-white">{{ $t('button.confirm_takeaway') }}</span>
                </button>

                <button type="button"
                        @click.prevent="printKotOnly"
                        class="flex items-center justify-center gap-2 px-4 h-[38px] rounded shadow-db-card bg-amber-600">
                    <i class="lab lab-fill-printer lab-font-size-16 text-white"></i>
                    <span class="text-sm capitalize text-white">{{ $t('button.print_kot') }}</span>
                </button>

                <button type="button"
                        @click.prevent="printCustomerOnly"
                        class="flex items-center justify-center gap-2 px-4 h-[38px] rounded shadow-db-card bg-sky-600">
                    <i class="lab lab-fill-receipt lab-font-size-16 text-white"></i>
                    <span class="text-sm capitalize text-white">{{ $t('button.print_customer') || $t('button.print_invoice') }}</span>
                </button>

                <button type="button"
                        @click.prevent="printBoth"
                        class="flex items-center justify-center gap-2 px-4 h-[38px] rounded shadow-db-card bg-primary">
                    <i class="lab lab-fill-printer lab-font-size-16 text-white"></i>
                    <span class="text-sm capitalize text-white">{{ $t('button.print_both') }}</span>
                </button>
            </div>
        </div>
    </OrderDetailsComponent>

    <SimplePrintSetupModal v-model="showSimplePrintSetup" @ready="onSimplePrintReady"/>
</template>

<script>
import OrderDetailsComponent from "../components/OrderDetailsComponent.vue";
import {useOnlineOrderStore} from "../../../stores/onlineOrder.js";
import orderStatusEnum from "../../../enums/modules/orderStatusEnum.js";
import ReasonComponent from "../components/order/ReasonComponent.vue";
import alertService from "../../../services/alertService.js";
import LoadingComponent from "../../common/LoadingComponent.vue";
import orderTypeEnum from "../../../enums/modules/orderTypeEnum.js";
import VueSimpleAlert from "vue3-simple-alert";
import paymentTypeEnum from "../../../enums/modules/paymentTypeEnum.js";
import OnlineOrderTokenComponent from "./OnlineOrderTokenComponent.vue";
import SimplePrintSetupModal from "../pos/SimplePrintSetupModal.vue";
import {
    isPrintPreviewOn,
    setPrintPreviewOn,
    isSilentPrintReady,
    syncSilentPrintFromUrl,
} from "../../../services/printPreference.js";
import {printBillIframe, printKotIframe} from "../../../services/thermalIframePrint.js";
import {sendViaLocalBridge, probeLocalAgentInfo, localAgentSetupUrl} from "../../../services/localPrintBridge.js";

export default {
    name: "OnlineOrderShowComponent",
    components: {
        LoadingComponent,
        OrderDetailsComponent,
        ReasonComponent,
        OnlineOrderTokenComponent,
        SimplePrintSetupModal,
    },
    setup() {
        const onlineOrderStore = useOnlineOrderStore();

        return {
            onlineOrderStore
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
                paymentTypeEnum: paymentTypeEnum
            },
            printPreviewOn: false,
            silentPrintReady: false,
            showSimplePrintSetup: false,
        }
    },
    computed: {
        order: function () {
            return this.onlineOrderStore.show;
        },
        orderItems: function () {
            return this.onlineOrderStore.orderItems;
        },
        orderUser: function () {
            return this.onlineOrderStore.orderUser;
        },
        orderAddress: function () {
            return this.onlineOrderStore.orderAddress;
        },
        orderRestaurant: function () {
            return this.onlineOrderStore.orderRestaurant;
        },
        orderDeliveryBoy: function () {
            return this.onlineOrderStore.orderDeliveryBoy;
        }
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
        this.onlineOrderStore.view(this.$route.params.id).then(res => {
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
        async printKotOnly() {
            await this.runOnlinePrint('kot');
        },
        async printCustomerOnly() {
            await this.runOnlinePrint('invoice');
        },
        async printBoth() {
            await this.runOnlinePrint('both');
        },
        async runOnlinePrint(mode = 'both') {
            if (!this.order?.id) return;
            this.loading.isActive = true;
            try {
                if (this.printPreviewOn) {
                    if (mode === 'kot' || mode === 'both') {
                        await this.printBrowserKot();
                        await new Promise((r) => setTimeout(r, 400));
                    }
                    if (mode === 'invoice' || mode === 'both') {
                        await this.printBrowserInvoice();
                    }
                    alertService.success(this.$t('message.bill_printed') || 'Print sent');
                    return;
                }

                let res;
                if (mode === 'kot') {
                    res = await this.onlineOrderStore.printKot(this.order.id);
                } else if (mode === 'invoice') {
                    res = await this.onlineOrderStore.printInvoice(this.order.id);
                } else {
                    res = await this.onlineOrderStore.printBoth(this.order.id);
                }

                (res.data.warnings || []).forEach((w) => {
                    try { alertService.error(w); } catch (e) {}
                });
                await this.runPrintJobs(res.data.print_jobs || []);
                alertService.success(this.$t('message.bill_printed') || 'Print sent');
            } catch (err) {
                alertService.error(err.response?.data?.message || this.$t('message.something_wrong'));
            } finally {
                this.loading.isActive = false;
            }
        },
        buildKotPayloadFromOrder() {
            const items = Array.isArray(this.orderItems) ? this.orderItems : Object.values(this.orderItems || {});
            return {
                order_serial_no: this.order.order_serial_no,
                order_type: this.order.order_type,
                table_no: this.order.table?.table_number || this.order.table?.name || '',
                customer_name: this.orderUser?.name || this.order.customer_name || '',
                note: this.order.order_note || '',
                items: items.map((i) => ({
                    name: i.item_name || i.name,
                    quantity: i.quantity,
                    instruction: i.instruction || '',
                    variation_lines: Object.keys(i.item_variations || {}).length
                        ? Object.values(i.item_variations).map((v) => `${v.variation_name}: ${v.name}`)
                        : [],
                    extra_lines: (i.item_extras || []).map((e) => e.name),
                })),
            };
        },
        async printBrowserKot() {
            await printKotIframe(this.buildKotPayloadFromOrder(), 'Kitchen');
        },
        async printBrowserInvoice() {
            const items = Array.isArray(this.orderItems) ? this.orderItems : Object.values(this.orderItems || {});
            await printBillIframe(this.order || {}, {
                restaurant: this.orderRestaurant || {},
                items: items.map((i) => ({
                    name: i.item_name || i.name,
                    quantity: i.quantity,
                    total_price: i.total_currency_price || i.total_price,
                    item_variations: i.item_variations,
                    item_extras: i.item_extras,
                    instruction: i.instruction,
                })),
                paymentLabel: this.order.payment_method_label || '',
                tableLabel: this.order.table
                    ? [this.order.table.name, this.order.table.table_number].filter(Boolean).join(' · ')
                    : '',
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
                            (job.type === 'invoice'
                                ? this.$t('message.bill_auto_print_failed')
                                : this.$t('message.kot_auto_print_failed'))
                            + ' ' + (err?.message || '')
                        );
                    }
                }
                return;
            }

            const kotJobs = jobs.filter((j) =>
                j.type === 'kot'
                && j.status !== 'skipped'
                && j.status !== 'printed'
                && (j.mode === 'browser_popup' || j.status === 'pending_browser' || j.payload)
            );
            const invoiceJobs = jobs.filter((j) =>
                j.type === 'invoice'
                && (j.mode === 'browser_popup' || j.status === 'pending_browser')
            );

            for (const job of kotJobs) {
                try {
                    await printKotIframe(job.payload || this.buildKotPayloadFromOrder(), job.printer || '');
                    await new Promise((r) => setTimeout(r, 400));
                } catch (e) {}
            }

            if (invoiceJobs.length > 0) {
                try {
                    await this.printBrowserInvoice();
                } catch (e) {}
                return;
            }

            if (invoiceJobs.length === 0 && kotJobs.length === 0 && !this.silentPrintReady) {
                alertService.error(this.$t('message.printer_not_connected'));
                this.showSimplePrintSetup = true;
            }
        },
        accept: function () {
            return new VueSimpleAlert.confirm(
                this.$t('message.cancel_order'),
                this.$t('message.are_you_sure'),
                "warning",
                {
                    confirmButtonText: this.$t('button.yes_accept'),
                    cancelButtonText: this.$t('button.no_cancel'),
                    confirmButtonColor: "#1AB759",
                    cancelButtonColor: "#E93C3C"
                }
            ).then((res) => {
                try {
                    this.loading.isActive = true;
                    this.onlineOrderStore.changeStatus({
                        id: this.$route.params.id,
                        status: this.enums.orderStatusEnum.ACCEPT
                    }).then((res) => {
                        this.loading.isActive = false;
                        alertService.successFlip(1, this.$t("label.status"));
                    }).catch((err) => {
                        this.loading.isActive = false;
                        alertService.error(err.response.data.message);
                    });
                } catch (err) {
                    this.loading.isActive = false;
                    alertService.error(err.response.data.message);
                }
            }).catch((err) => {
                this.loading.isActive = false;
            });
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
                    this.onlineOrderStore.changeStatus({
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
                    this.onlineOrderStore.changeStatus({
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
                this.order.payment_method === this.enums.paymentTypeEnum.CASH_ON_DELIVERY ? this.$t('message.collect_the_fee', {amount : this.order.total_currency_price}) : this.$t('message.complete_order'),
                this.$t('message.are_you_sure'),
                "warning",
                {
                    confirmButtonText: this.order.payment_method === this.enums.paymentTypeEnum.CASH_ON_DELIVERY ? this.$t('button.yes_collected') : this.$t('button.yes_delivered'),
                    cancelButtonText: this.$t('button.no_cancel'),
                    confirmButtonColor: "#1AB759",
                    cancelButtonColor: "#E93C3C"
                }
            ).then(res => {
                try {
                    this.loading.isActive = true;
                    this.onlineOrderStore.changeStatus({
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
