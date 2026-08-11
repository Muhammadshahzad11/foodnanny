<template>
    <LoadingComponent :props="loading"/>
    <div class="col-12">
        <div class="db-card">
            <div class="db-card-header border-none">
                <h3 class="db-card-title">{{ $t('menu.pos_orders') }}</h3>
                <nav class="flex flex-wrap gap-2 mobile:justify-center">
                    <TableLimitComponent :method="list" :search="props.search" :page="paginationPage"/>
                    <FilterComponent @click.prevent="handleSlide('pos-order')"/>
                    <div class="paper-group">
                        <ExportComponent @click.prevent="handlePaper"/>
                        <nav
                            class="paper-content absolute top-9 right-1/2 translate-x-1/2 z-30 min-w-[80px] w-fit rounded-md shadow-paper bg-white">
                            <PrintComponent :props="printObj"/>
                            <ExcelComponent :method="xls"/>
                        </nav>
                    </div>
                </nav>
            </div>

            <div class="table-filter-div" id="pos-order">
                <form class="p-4 sm:p-5 mb-5" @submit.prevent="search">
                    <div class="row">
                        <div class="col-12 sm:col-6 md:col-4 xl:col-3">
                            <label for="order_id" class="db-field-title after:hidden">{{ $t('label.order_id') }}</label>
                            <input id="order_id" v-model="props.search.order_serial_no" type="text"
                                   class="db-field-control">
                        </div>

                        <div class="col-12 sm:col-6 md:col-4 xl:col-3">
                            <label for="searchStatus" class="db-field-title after:hidden">
                                {{ $t('label.status') }}
                            </label>
                            <vue-select class="db-field-control f-b-custom-select" id="searchStatus"
                                        v-model="props.search.status"
                                        :options="[{ id: enums.orderStatusEnum.ACCEPT, name: $t('label.accept') }, { id: enums.orderStatusEnum.PREPARING, name: $t('label.preparing') }, { id: enums.orderStatusEnum.PREPARED, name: $t('label.prepared') }, { id: enums.orderStatusEnum.DELIVERED, name: $t('label.delivered') }]"
                                        label-by="name" value-by="id" :closeOnSelect="true" :searchable="true"
                                        :clearOnClose="true" placeholder="--" search-placeholder="--"/>
                        </div>

                        <div class="col-12 sm:col-6 md:col-4 xl:col-3">
                            <label for="searchUserId" class="db-field-title">
                                {{ $t("label.customer") }}
                            </label>
                            <vue-select class="db-field-control f-b-custom-select" id="searchUserId"
                                        v-model="props.search.user_id" :options="customers" label-by="name"
                                        value-by="id" :closeOnSelect="true" :searchable="true" :clearOnClose="true"
                                        placeholder="--" search-placeholder="--"/>
                        </div>

                        <div class="col-12 sm:col-6 md:col-4 xl:col-3">
                            <label for="searchDate" class="db-field-title after:hidden">
                                {{ $t('label.date') }}
                            </label>
                            <DatePickerComponent id="searchDate" :hideInputIcon="true" @update:modelValue="handleDate"
                                                 inputStyle="filter" :range="true" v-model="modelValue"/>
                        </div>

                        <div class="col-12">
                            <div class="flex flex-wrap gap-3 mt-4">
                                <button class="db-btn py-2 text-white bg-primary">
                                    <i class="lab lab-line-search lab-font-size-16"></i>
                                    <span>{{ $t('button.search') }}</span>
                                </button>
                                <button class="db-btn py-2 text-white bg-gray-600" @click="clear">
                                    <i class="lab lab-line-cross lab-font-size-22"></i>
                                    <span>{{ $t('button.clear') }}</span>
                                </button>
                            </div>
                        </div>

                    </div>
                </form>
            </div>

            <div class="db-table-responsive">
                <table class="db-table stripe" id="print">
                    <thead class="db-table-head">
                    <tr class="db-table-head-tr">
                        <th class="db-table-head-th">{{ $t('label.order_id') }}</th>
                        <th class="db-table-head-th">{{ $t('label.customer') }}</th>
                        <th class="db-table-head-th">{{ $t('label.amount') }}</th>
                        <th class="db-table-head-th">{{ $t('label.date') }}</th>
                        <th class="db-table-head-th">{{ $t('label.status') }}</th>
                        <th v-if="permissionChecker('pos-orders_show') || permissionChecker('pos-orders_delete')"
                            class="db-table-head-th hidden-print">
                            {{ $t('label.action') }}
                        </th>
                    </tr>
                    </thead>
                    <tbody class="db-table-body" v-if="orders.length > 0">
                    <tr class="db-table-body-tr" v-for="order in orders" :key="order.id">
                        <td class="db-table-body-td">
                            {{ order.order_serial_no }}
                        </td>
                        <td class="db-table-body-td">
                            {{ order.customer.name }}
                        </td>
                        <td class="db-table-body-td">{{ order.total_amount_price }}</td>
                        <td class="db-table-body-td">{{ order.order_datetime }}</td>
                        <td class="db-table-body-td">
                            <div class="flex flex-wrap items-center gap-1">
                                <span
                                    v-if="Number(order.payment_status) === enums.paymentStatusEnum.UNPAID"
                                    class="text-[10px] font-semibold px-1.5 py-0.5 rounded bg-red-100 text-red-600"
                                >
                                    {{ $t('label.unpaid') }}
                                </span>
                                <span :class="orderStatusClassForTable(order.status)">
                                    {{ enums.orderStatusEnumArray[order.status] || '—' }}
                                </span>
                            </div>
                        </td>
                        <td class="db-table-body-td hidden-print"
                            v-if="permissionChecker('pos-orders_show') || permissionChecker('pos-orders_delete')">
                            <div class="flex justify-start items-center sm:items-start sm:justify-start gap-1.5">
                                <SmIconViewComponent :link="'admin.pos.orders.show'" :id="order.id"
                                                     v-if="permissionChecker('pos-orders_show')"/>
                                <button
                                    v-if="permissionChecker('pos-orders_show') && canTakePayment(order)"
                                    type="button"
                                    class="db-table-action pay"
                                    @click.prevent="openPayment(order)"
                                >
                                    <i class="lab lab-fill-moneys"></i>
                                    <span class="db-tooltip">{{ $t('label.payment') }}</span>
                                </button>
                                <button
                                    v-if="permissionChecker('pos-orders_show') && canPrintInvoice(order)"
                                    type="button"
                                    class="db-table-action print"
                                    @click.prevent="printInvoice(order)"
                                >
                                    <i class="lab lab-fill-printer"></i>
                                    <span class="db-tooltip">{{ $t('button.print_invoice') }}</span>
                                </button>
                                <SmIconDeleteComponent
                                    v-if="permissionChecker('pos-orders_delete')"
                                    @click="destroy(order.id)"
                                />
                            </div>
                        </td>
                    </tr>
                    </tbody>

                    <tbody v-else class="db-table-body">
                    <tr class="db-table-body-tr">
                        <td class="db-table-body-td" colspan="6">
                            <div class="p-4">
                                <img class="m-auto not-found max-w-[300px]" :src="setting.data_not_found"
                                     alt="Not Found">
                                <span class="block mt-3 text-center text-lg">{{ $t('message.no_data_found') }}</span>
                            </div>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>

            <div class="flex items-center justify-between border-t border-gray-200 bg-white px-4 py-6">
                <PaginationSMBox :pagination="pagination" :method="list"/>
                <div class="hidden sm:flex sm:flex-1 sm:items-center sm:justify-between">
                    <PaginationTextComponent :props="{ page: paginationPage }"/>
                    <PaginationBox :pagination="pagination" :method="list"/>
                </div>
            </div>
        </div>
    </div>

    <PaymentComponent ref="paymentRef" :method="onPaymentComplete" :props="checkoutProps"/>
    <SimplePrintSetupModal v-model="showSimplePrintSetup" @ready="onSimplePrintReady"/>
</template>

<script>
import LoadingComponent from "../../common/LoadingComponent.vue";
import {usePaper} from "../../../composables/paper.js";
import ExportComponent from "../components/buttons/export/ExportComponent.vue";
import PrintComponent from "../components/buttons/export/PrintComponent.vue";
import ExcelComponent from "../components/buttons/export/ExcelComponent.vue";
import TableLimitComponent from "../components/TableLimitComponent.vue";
import FilterComponent from "../components/buttons/collapse/FilterComponent.vue";
import {useSlide} from "../../../composables/slide.js";
import {useUserStore} from "../../../stores/user.js";
import {usePosOrderStore} from "../../../stores/posOrder.js";
import orderStatusEnum from "../../../enums/modules/orderStatusEnum.js";
import DatePickerComponent from "../components/DatePickerComponent.vue";
import appService from "../../../services/appService.js";
import PaginationSMBox from "../components/pagination/PaginationSMBox.vue";
import PaginationTextComponent from "../components/pagination/PaginationTextComponent.vue";
import PaginationBox from "../components/pagination/PaginationBox.vue";
import {useFrontendSettingStore} from "../../../stores/frontendSetting.js";
import SmIconViewComponent from "../components/buttons/SmIconViewComponent.vue";
import SmIconDeleteComponent from "../components/buttons/SmIconDeleteComponent.vue";
import VueSimpleAlert from "vue3-simple-alert";
import orderTypeEnum from "../../../enums/modules/orderTypeEnum.js";
import alertService from "../../../services/alertService.js";
import PaymentComponent from "../pos/PaymentComponent.vue";
import SimplePrintSetupModal from "../pos/SimplePrintSetupModal.vue";
import posPaymentMethodEnum from "../../../enums/modules/posPaymentMethodEnum.js";
import paymentStatusEnum from "../../../enums/modules/paymentStatusEnum.js";
import {useModal} from "../../../composables/modal.js";
import {
    isPrintPreviewOn,
    setPrintPreviewOn,
    isSilentPrintReady,
    syncSilentPrintFromUrl,
} from "../../../services/printPreference.js";
import {printBillIframe} from "../../../services/thermalIframePrint.js";
import {sendViaLocalBridge, probeLocalAgentInfo, localAgentSetupUrl} from "../../../services/localPrintBridge.js";

export default {
    name: "PosOrderListComponent",
    components: {
        SmIconViewComponent,
        SmIconDeleteComponent,
        PaginationBox,
        PaginationTextComponent,
        PaginationSMBox,
        FilterComponent,
        ExcelComponent,
        PrintComponent,
        ExportComponent,
        LoadingComponent,
        TableLimitComponent,
        DatePickerComponent,
        PaymentComponent,
        SimplePrintSetupModal,
    },
    setup() {
        const userStore            = useUserStore();
        const {handlePaper}        = usePaper();
        const {handleSlide}        = useSlide();
        const posOrderStore        = usePosOrderStore();
        const frontendSettingStore = useFrontendSettingStore();
        const {openModal}          = useModal();

        return {
            userStore,
            handlePaper,
            handleSlide,
            posOrderStore,
            frontendSettingStore,
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
                paymentStatusEnum: paymentStatusEnum,
                orderStatusEnumArray: {
                    [orderStatusEnum.ACCEPT]: this.$t("label.accept"),
                    [orderStatusEnum.PREPARING]: this.$t("label.preparing"),
                    [orderStatusEnum.PREPARED]: this.$t("label.prepared"),
                    [orderStatusEnum.DELIVERED]: this.$t("label.delivered"),
                }
            },
            printObj: {
                id: "print",
                popTitle: this.$t("menu.pos_orders"),
            },
            props: {
                search: {
                    paginate: 1,
                    page: 1,
                    per_page: 10,
                    order_column: 'id',
                    order_by: "desc",
                    order_serial_no: "",
                    excepts: orderTypeEnum.DELIVERY + '|' + orderTypeEnum.TAKEAWAY,
                    user_id: null,
                    status: null,
                    from_date: "",
                    to_date: "",
                }
            },
            modelValue: null,
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
    mounted() {
        syncSilentPrintFromUrl();
        this.printPreviewOn = isPrintPreviewOn();
        this.silentPrintReady = isSilentPrintReady();
        this.list();
        this.userStore.fetch();
    },
    computed: {
        setting: function () {
            return this.frontendSettingStore.lists;
        },
        orders: function () {
            return this.posOrderStore.lists;
        },
        customers: function () {
            return this.userStore.lists;
        },
        pagination: function () {
            return this.posOrderStore.pagination;
        },
        paginationPage: function () {
            return this.posOrderStore.page;
        }
    },
    methods: {
        permissionChecker(e) {
            return appService.permissionChecker(e);
        },
        orderStatusClassForTable: function (status) {
            return appService.orderStatusClassForTable(status);
        },
        canTakePayment(order) {
            if (!order?.id) return false;
            if (Number(order.payment_status) === paymentStatusEnum.PAID) return false;
            const status = Number(order.status);
            return ![orderStatusEnum.DELIVERED, orderStatusEnum.CANCELED, orderStatusEnum.REJECTED].includes(status);
        },
        canPrintInvoice(order) {
            if (!order?.id) return false;
            const status = Number(order.status);
            return ![orderStatusEnum.CANCELED, orderStatusEnum.REJECTED].includes(status);
        },
        num(v) {
            const n = parseFloat(v);
            return Number.isFinite(n) ? Math.round(n * 100) / 100 : 0;
        },
        buildPaymentItems(orderItems) {
            const items = Array.isArray(orderItems) ? orderItems : Object.values(orderItems || {});
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
        async openPayment(row) {
            if (!this.canTakePayment(row)) return;
            this.loading.isActive = true;
            try {
                await this.posOrderStore.view(row.id);
                const order = this.posOrderStore.show;
                const orderType = Number(order.order_type) === orderTypeEnum.POS
                    ? orderTypeEnum.TAKEAWAY
                    : Number(order.order_type) || orderTypeEnum.TAKEAWAY;

                this.checkoutProps.form.subtotal = this.num(order.subtotal);
                this.checkoutProps.form.discount = this.num(order.discount);
                this.checkoutProps.form.tax = this.num(order.total_tax);
                this.checkoutProps.form.total = this.num(order.total);
                this.checkoutProps.form.token = order.token || '';
                this.checkoutProps.form.items = this.buildPaymentItems(this.posOrderStore.orderItems);
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
            } catch (err) {
                alertService.error(err.response?.data?.message || this.$t('message.something_wrong'));
            } finally {
                this.loading.isActive = false;
            }
        },
        async onPaymentComplete() {
            alertService.success(this.$t('message.payment_successful') || 'Payment completed');
            this.list(this.props.search.page || 1);
        },
        onSimplePrintReady() {
            this.silentPrintReady = isSilentPrintReady();
            setPrintPreviewOn(false);
            this.printPreviewOn = false;
        },
        async printInvoice(row) {
            if (!this.canPrintInvoice(row)) return;
            this.loading.isActive = true;
            try {
                this.printPreviewOn = isPrintPreviewOn();
                this.silentPrintReady = isSilentPrintReady();

                if (this.printPreviewOn) {
                    await this.posOrderStore.view(row.id);
                    await this.printBrowserInvoice();
                    alertService.success(this.$t('message.bill_printed') || 'Invoice sent to printer');
                    return;
                }

                const res = await this.posOrderStore.printInvoice(row.id);
                (res.data.warnings || []).forEach((w) => {
                    try { alertService.error(w); } catch (e) {}
                });
                await this.runPrintJobs(res.data.print_jobs || [], row.id);
                alertService.success(this.$t('message.bill_printed') || 'Invoice sent to printer');
            } catch (err) {
                alertService.error(err.response?.data?.message || this.$t('message.something_wrong'));
            } finally {
                this.loading.isActive = false;
            }
        },
        async printBrowserInvoice() {
            const order = this.posOrderStore.show || {};
            const items = Array.isArray(this.posOrderStore.orderItems)
                ? this.posOrderStore.orderItems
                : Object.values(this.posOrderStore.orderItems || {});
            await printBillIframe(order, {
                restaurant: this.posOrderStore.restaurant || {},
                items: items.map((i) => ({
                    name: i.item_name || i.name,
                    quantity: i.quantity,
                    total_price: i.total_currency_price || i.total_price,
                    item_variations: i.item_variations,
                    item_extras: i.item_extras,
                    instruction: i.instruction,
                })),
                cashierName: order?.waiter?.name || '',
                tableLabel: order?.table
                    ? [order.table.name, order.table.table_number].filter(Boolean).join(' · ')
                    : '',
            });
        },
        async runPrintJobs(printJobs = [], orderId = null) {
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
                if (orderId) {
                    await this.posOrderStore.view(orderId);
                }
                await this.printBrowserInvoice();
                return;
            }

            if (this.silentPrintReady) {
                if (orderId) {
                    await this.posOrderStore.view(orderId);
                }
                await this.printBrowserInvoice();
                return;
            }
            alertService.error(this.$t('message.printer_not_connected'));
            this.showSimplePrintSetup = true;
        },
        search: function () {
            this.list();
        },
        handleDate: function (e) {
            if (e) {
                this.props.search.from_date = e[0];
                this.props.search.to_date   = e[1];
            } else {
                this.props.search.from_date = null;
                this.props.search.to_date   = null;
            }
        },
        destroy: async function (id) {
            // Step 1: warn — order is NOT deleted yet
            try {
                await new VueSimpleAlert.confirm(
                    this.$t('message.delete_requires_otp')
                        || 'To delete this order you must enter an OTP. Continue?',
                    this.$t('message.are_you_sure') || 'Are you sure?',
                    'warning',
                    {
                        confirmButtonText: this.$t('button.continue') || 'Continue',
                        cancelButtonText: this.$t('button.no_cancel') || 'Cancel',
                        confirmButtonColor: '#1AB759',
                        cancelButtonColor: '#E93C3C',
                    }
                );
            } catch (e) {
                return;
            }

            this.loading.isActive = true;
            try {
                // Step 2: generate OTP (order still not deleted)
                const otpRes = await this.posOrderStore.requestDeleteOtp(id);
                const otp = otpRes.data?.otp ? String(otpRes.data.otp) : '';
                this.loading.isActive = false;

                if (otp) {
                    await alertService.showOtp(otp);
                }

                // Step 3: MUST enter OTP — cancel / empty = no delete
                let entered = '';
                try {
                    entered = await VueSimpleAlert.prompt(
                        this.$t('message.enter_delete_otp')
                            || 'Enter the OTP to delete this order. Leave empty / cancel to keep the order.',
                        '',
                        this.$t('label.otp') || 'OTP',
                        'question',
                        {
                            confirmButtonText: this.$t('button.delete') || 'Delete',
                            cancelButtonText: this.$t('button.cancel') || 'Cancel',
                        }
                    );
                } catch (e) {
                    alertService.error(this.$t('message.delete_cancelled') || 'Delete cancelled. Order was not deleted.');
                    return;
                }

                entered = String(entered || '').trim();
                if (!entered) {
                    alertService.error(this.$t('message.otp_required_to_delete') || 'OTP is required. Order was not deleted.');
                    return;
                }

                // Step 4: only now call API with OTP
                this.loading.isActive = true;
                await this.posOrderStore.destroy({
                    id,
                    otp: entered,
                    search: this.props.search,
                });
                this.loading.isActive = false;
                alertService.successFlip(null, this.$t('menu.pos_orders'));
            } catch (err) {
                this.loading.isActive = false;
                alertService.error(
                    err.response?.data?.message
                    || this.$t('message.otp_invalid_order_kept')
                    || 'Invalid OTP. Order was not deleted.'
                );
            }
        },
        clear: function () {
            this.props.search.paginate        = 1;
            this.props.search.page            = 1;
            this.props.search.order_serial_no = "";
            this.props.search.status          = null;
            this.props.search.user_id         = null;
            this.props.search.from_date       = "";
            this.props.search.to_date         = "";
            this.modelValue                   = null;
            this.list();
        },
        list: function (page = 1) {
            this.loading.isActive  = true;
            this.props.search.page = page;
            this.posOrderStore.fetch(this.props.search).then(res => {
                this.loading.isActive = false;
            }).catch((err) => {
                this.loading.isActive = false;
            });
        },
        xls: function () {
            this.loading.isActive = true;
            this.posOrderStore.export(this.props.search).then((res) => {
                this.loading.isActive = false;
                const blob            = new Blob([res.data], {
                    type: "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet",
                });
                const link            = document.createElement("a");
                link.href             = URL.createObjectURL(blob);
                link.download         = this.$t("menu.pos_orders");
                link.click();
                URL.revokeObjectURL(link.href);
            }).catch((err) => {
                this.loading.isActive = false;
                alertService.error(err.response.data.message);
            });
        },
    }
}
</script>
