<template>
    <LoadingComponent :props="loading"/>
    <div class="col-12">
        <div class="db-card">
            <div class="db-card-header border-none">
                <h3 class="db-card-title">{{ $t('menu.online_orders') }}</h3>
                <nav class="flex flex-wrap gap-2 mobile:justify-center">
                    <TableLimitComponent :method="list" :search="props.search" :page="paginationPage"/>
                    <FilterComponent @click.prevent="handleSlide('online-order')"/>
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

            <div class="table-filter-div" id="online-order">
                <form class="p-4 sm:p-5 mb-5" @submit.prevent="search">
                    <div class="row">
                        <div class="col-12 sm:col-6 md:col-4 xl:col-3">
                            <label for="searchOrderId" class="db-field-title after:hidden">{{
                                    $t('label.order_id')
                                }}</label>
                            <input id="searchOrderId" v-model="props.search.order_serial_no" type="text"
                                   class="db-field-control">
                        </div>

                        <div class="col-12 sm:col-6 md:col-4 xl:col-3">
                            <label for="searchStatus" class="db-field-title after:hidden">
                                {{ $t('label.status') }}
                            </label>
                            <vue-select class="db-field-control f-b-custom-select" id="searchStatus"
                                        v-model="props.search.status" :options="[
                                    { id: enums.orderStatusEnum.PENDING, name: $t('label.pending') },
                                    { id: enums.orderStatusEnum.ACCEPT, name: $t('label.accept') },
                                    { id: enums.orderStatusEnum.PREPARING, name: $t('label.preparing') },
                                    { id: enums.orderStatusEnum.PREPARED, name: $t('label.prepared') },
                                    { id: enums.orderStatusEnum.OUT_FOR_DELIVERY, name: $t('label.out_for_delivery') },
                                    { id: enums.orderStatusEnum.DELIVERED, name: $t('label.delivered') },
                                    { id: enums.orderStatusEnum.CANCELED, name: $t('label.canceled') },
                                    { id: enums.orderStatusEnum.REJECTED, name: $t('label.rejected') },
                                    { id: enums.orderStatusEnum.RETURNED, name: $t('label.returned') }]" label-by="name"
                                        value-by="id"
                                        :closeOnSelect="true" :searchable="true" :clearOnClose="true" placeholder="--"
                                        search-placeholder="--"/>
                        </div>

                        <div class="col-12 sm:col-6 md:col-4 xl:col-3">
                            <label for="searchDate" class="db-field-title after:hidden">
                                {{ $t('label.date') }}
                            </label>
                            <DatePickerComponent id="searchDate" :hideInputIcon="true" @update:modelValue="handleDate" inputStyle="filter" :range="true" v-model="modelValue"/>
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
                        <th class="db-table-head-th">{{ $t('label.order_type') }}</th>
                        <th class="db-table-head-th">{{ $t('label.customer') }}</th>
                        <th class="db-table-head-th">{{ $t('label.amount') }}</th>
                        <th class="db-table-head-th">{{ $t('label.date') }}</th>
                        <th class="db-table-head-th">{{ $t('label.status') }}</th>
                        <th class="db-table-head-th hidden-print" v-if="permissionChecker('online-orders')">
                            {{ $t('label.action') }}
                        </th>
                    </tr>
                    </thead>
                    <tbody class="db-table-body" v-if="orders.length > 0">
                    <tr class="db-table-body-tr" v-for="order in orders" :key="order">
                        <td class="db-table-body-td">
                            {{ order.order_serial_no }}
                        </td>
                        <td class="db-table-body-td">
                            <span :class="statusClass(order.order_type)">
                                {{ enums.orderTypeEnumArray[order.order_type] }}
                            </span>
                        </td>
                        <td class="db-table-body-td">
                            {{ textShortener(order.customer.name, 20) }}
                        </td>
                        <td class="db-table-body-td">{{ order.total_amount_price }}</td>
                        <td class="db-table-body-td">{{ order.order_datetime }}</td>
                        <td class="db-table-body-td">
                            <span :class="orderStatusClassForTable(order.status)">
                                {{ enums.orderStatusEnumArray[order.status] }}
                            </span>
                            <span v-if="order.is_advance_order === enums.isAdvanceOrderEnum.YES"
                                  class="ml-1 !bg-[#1AB759] !text-white capitalize px-2 py-1 rounded-md">
                                {{ $t('label.advance') }}
                            </span>
                        </td>
                        <td class="db-table-body-td hidden-print" v-if="permissionChecker('online-orders')">
                            <div class="flex justify-start items-center sm:items-start sm:justify-start gap-1.5">
                                <SmIconViewComponent :link="'admin.order.show'" :id="order.id"/>
                                <button
                                    v-if="canPrint(order)"
                                    type="button"
                                    class="db-table-action pay"
                                    title="Print KOT"
                                    @click.prevent="quickPrint(order, 'kot')"
                                >
                                    <i class="lab lab-fill-reserve"></i>
                                    <span class="db-tooltip">{{ $t('button.print_kot') }}</span>
                                </button>
                                <button
                                    v-if="canPrint(order)"
                                    type="button"
                                    class="db-table-action print"
                                    @click.prevent="quickPrint(order, 'invoice')"
                                >
                                    <i class="lab lab-fill-receipt"></i>
                                    <span class="db-tooltip">{{ $t('button.print_customer') }}</span>
                                </button>
                                <button
                                    v-if="canPrint(order)"
                                    type="button"
                                    class="db-table-action view"
                                    @click.prevent="quickPrint(order, 'both')"
                                >
                                    <i class="lab lab-fill-printer"></i>
                                    <span class="db-tooltip">{{ $t('button.print_both') }}</span>
                                </button>
                            </div>
                        </td>
                    </tr>
                    </tbody>
                    <tbody class="db-table-body" v-else>
                    <tr class="db-table-body-tr">
                        <td class="db-table-body-td" colspan="7">
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

    <SimplePrintSetupModal v-model="showSimplePrintSetup" @ready="onSimplePrintReady"/>
</template>

<script>
import LoadingComponent from "../../common/LoadingComponent.vue";
import alertService from "../../../services/alertService.js";
import PaginationTextComponent from "../components/pagination/PaginationTextComponent.vue";
import PaginationBox from "../components/pagination/PaginationBox.vue";
import PaginationSMBox from "../components/pagination/PaginationSMBox.vue";
import appService from "../../../services/appService.js";
import orderStatusEnum from "../../../enums/modules/orderStatusEnum.js";
import orderTypeEnum from "../../../enums/modules/orderTypeEnum.js";
import TableLimitComponent from "../components/TableLimitComponent.vue";
import SmIconViewComponent from "../components/buttons/SmIconViewComponent.vue";
import FilterComponent from "../components/buttons/collapse/FilterComponent.vue";
import ExportComponent from "../components/buttons/export/ExportComponent.vue";
import PrintComponent from "../components/buttons/export/PrintComponent.vue";
import ExcelComponent from "../components/buttons/export/ExcelComponent.vue";
import DatePickerComponent from "../components/DatePickerComponent.vue";
import isAdvanceOrderEnum from "../../../enums/modules/isAdvanceOrderEnum.js";
import {usePaper} from "../../../composables/paper.js";
import {useSlide} from "../../../composables/slide.js";
import {useOnlineOrderStore} from "../../../stores/onlineOrder.js";
import {useFrontendSettingStore} from "../../../stores/frontendSetting.js";
import SimplePrintSetupModal from "../pos/SimplePrintSetupModal.vue";
import {
    isPrintPreviewOn,
    isSilentPrintReady,
    syncSilentPrintFromUrl,
} from "../../../services/printPreference.js";
import {printBillIframe, printKotIframe} from "../../../services/thermalIframePrint.js";
import {sendViaLocalBridge, probeLocalAgentInfo, localAgentSetupUrl} from "../../../services/localPrintBridge.js";


export default {
    name: "OnlineOrderListComponent",
    components: {
        TableLimitComponent,
        PaginationSMBox,
        PaginationBox,
        PaginationTextComponent,
        LoadingComponent,
        SmIconViewComponent,
        FilterComponent,
        ExportComponent,
        PrintComponent,
        ExcelComponent,
        DatePickerComponent,
        SimplePrintSetupModal,
    },
    setup() {
        const onlineOrderStore     = useOnlineOrderStore();
        const frontendSettingStore = useFrontendSettingStore();

        return {
            onlineOrderStore,
            frontendSettingStore
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
                isAdvanceOrderEnum: isAdvanceOrderEnum,
                orderStatusEnumArray: {
                    [orderStatusEnum.PENDING]: this.$t("label.pending"),
                    [orderStatusEnum.ACCEPT]: this.$t("label.accept"),
                    [orderStatusEnum.PREPARING]: this.$t("label.preparing"),
                    [orderStatusEnum.PREPARED]: this.$t("label.prepared"),
                    [orderStatusEnum.OUT_FOR_DELIVERY]: this.$t("label.out_for_delivery"),
                    [orderStatusEnum.DELIVERED]: this.$t("label.delivered"),
                    [orderStatusEnum.CANCELED]: this.$t("label.canceled"),
                    [orderStatusEnum.REJECTED]: this.$t("label.rejected"),
                    [orderStatusEnum.RETURNED]: this.$t("label.returned")
                },
                orderTypeEnumArray: {
                    [orderTypeEnum.DELIVERY]: this.$t("label.delivery"),
                    [orderTypeEnum.TAKEAWAY]: this.$t("label.takeaway"),
                    [orderTypeEnum.DINING_TABLE]: this.$t("label.dining_table")
                }
            },
            printLoading: true,
            printObj: {
                id: "print",
                popTitle: this.$t("menu.online_orders"),
            },
            props: {
                search: {
                    paginate: 1,
                    page: 1,
                    per_page: 10,
                    order_column: 'id',
                    order_by: "desc",
                    order_serial_no: "",
                    channel: 'online',
                    excepts: orderTypeEnum.POS + '|' + orderTypeEnum.DINING_TABLE,
                    status: null,
                    from_date: "",
                    to_date: "",
                }
            },
            modelValue: null,
            handlePaper: usePaper().handlePaper,
            handleSlide: useSlide().handleSlide,
            printPreviewOn: false,
            silentPrintReady: false,
            showSimplePrintSetup: false,
        }
    },
    mounted() {
        syncSilentPrintFromUrl();
        this.printPreviewOn = isPrintPreviewOn();
        this.silentPrintReady = isSilentPrintReady();
        this.list();
    },
    computed: {
        setting: function () {
            return this.frontendSettingStore.lists;
        },
        orders: function () {
            return this.onlineOrderStore.lists;
        },
        pagination: function () {
            return this.onlineOrderStore.pagination;
        },
        paginationPage: function () {
            return this.onlineOrderStore.page;
        }
    },
    methods: {
        permissionChecker(e) {
            return appService.permissionChecker(e);
        },
        statusClass: function (status) {
            return appService.statusClass(status);
        },
        orderStatusClassForTable: function (status) {
            return appService.orderStatusClassForTable(status);
        },
        textShortener: function (text, number = 30) {
            return appService.textShortener(text, number);
        },
        canPrint(order) {
            if (!order?.id) return false;
            const status = Number(order.status);
            return ![orderStatusEnum.CANCELED, orderStatusEnum.REJECTED].includes(status);
        },
        onSimplePrintReady() {
            this.silentPrintReady = isSilentPrintReady();
            this.printPreviewOn = false;
        },
        async quickPrint(row, mode = 'both') {
            if (!this.canPrint(row)) return;
            this.loading.isActive = true;
            this.printPreviewOn = isPrintPreviewOn();
            this.silentPrintReady = isSilentPrintReady();
            try {
                if (this.printPreviewOn) {
                    await this.onlineOrderStore.view(row.id);
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
                    res = await this.onlineOrderStore.printKot(row.id);
                } else if (mode === 'invoice') {
                    res = await this.onlineOrderStore.printInvoice(row.id);
                } else {
                    res = await this.onlineOrderStore.printBoth(row.id);
                }
                (res.data.warnings || []).forEach((w) => {
                    try { alertService.error(w); } catch (e) {}
                });
                await this.runPrintJobs(res.data.print_jobs || [], row.id);
                alertService.success(this.$t('message.bill_printed') || 'Print sent');
            } catch (err) {
                alertService.error(err.response?.data?.message || this.$t('message.something_wrong'));
            } finally {
                this.loading.isActive = false;
            }
        },
        buildKotPayloadFromOrder() {
            const order = this.onlineOrderStore.show || {};
            const items = Array.isArray(this.onlineOrderStore.orderItems)
                ? this.onlineOrderStore.orderItems
                : Object.values(this.onlineOrderStore.orderItems || {});
            return {
                order_serial_no: order.order_serial_no,
                order_type: order.order_type,
                table_no: order.table?.table_number || order.table?.name || '',
                customer_name: this.onlineOrderStore.orderUser?.name || order.customer_name || '',
                note: order.order_note || '',
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
            const order = this.onlineOrderStore.show || {};
            const items = Array.isArray(this.onlineOrderStore.orderItems)
                ? this.onlineOrderStore.orderItems
                : Object.values(this.onlineOrderStore.orderItems || {});
            await printBillIframe(order, {
                restaurant: this.onlineOrderStore.orderRestaurant || {},
                items: items.map((i) => ({
                    name: i.item_name || i.name,
                    quantity: i.quantity,
                    total_price: i.total_currency_price || i.total_price,
                    item_variations: i.item_variations,
                    item_extras: i.item_extras,
                    instruction: i.instruction,
                })),
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
                const agentInfo = await probeLocalAgentInfo(directJobs[0]?.bridge_port || 1811);
                if (!agentInfo.ok) {
                    alertService.error(this.$t('message.local_agent_required_auto_print'));
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

            if (orderId) {
                await this.onlineOrderStore.view(orderId);
            }
            const kotJobs = jobs.filter((j) => j.type === 'kot' && j.status !== 'skipped' && j.payload);
            for (const job of kotJobs) {
                try {
                    await printKotIframe(job.payload, job.printer || '');
                    await new Promise((r) => setTimeout(r, 400));
                } catch (e) {}
            }
            const wantsInvoice = jobs.some((j) => j.type === 'invoice') || jobs.length === 0;
            if (wantsInvoice || !kotJobs.length) {
                await this.printBrowserInvoice();
            }
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
        clear: function () {
            this.props.search.paginate        = 1;
            this.props.search.page            = 1;
            this.props.search.order_by        = "desc";
            this.props.search.order_serial_no = "";
            this.props.search.status          = null;
            this.props.search.channel         = 'online';
            this.props.search.excepts         = orderTypeEnum.POS + '|' + orderTypeEnum.DINING_TABLE;
            this.props.search.from_date       = "";
            this.props.search.to_date         = "";
            this.modelValue                   = null;
            this.list();
        },
        list: function (page = 1) {
            this.loading.isActive  = true;
            this.props.search.page = page;
            this.onlineOrderStore.fetch(this.props.search).then(res => {
                this.loading.isActive = false;
            }).catch((err) => {
                this.loading.isActive = false;
            });
        },
        xls: function () {
            this.loading.isActive = true;
            this.onlineOrderStore.export(this.props.search).then((res) => {
                this.loading.isActive = false;
                const blob            = new Blob([res.data], {
                    type: "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet",
                });
                const link            = document.createElement("a");
                link.href             = URL.createObjectURL(blob);
                link.download         = this.$t("menu.online_orders");
                link.click();
                URL.revokeObjectURL(link.href);
            }).catch((err) => {
                this.loading.isActive = false;
                alertService.error(err.response.data.message);
            });
        }
    }
}
</script>
