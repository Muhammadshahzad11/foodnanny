<template>
    <LoadingComponent :props="loading"/>

    <div class="db-card db-tab-div active">
        <div class="db-card-header border-none">
            <h3 class="db-card-title">{{ $t("menu.printer_settings") }}</h3>
            <div class="db-card-filter gap-2">
                <button type="button" class="db-btn py-2 text-white bg-sky-600" :disabled="loading.isActive" @click="fetchStatus">
                    {{ loading.isActive ? $t('label.fetching') : $t('label.fetch_printers') }}
                </button>
                <TableLimitComponent :method="list" :search="props.search" :page="paginationPage"/>
                <PrinterCreateComponent :props="props"/>
            </div>
        </div>

        <div
            v-if="needsLocalAgent && !agentOnline"
            class="mx-4 mb-3 rounded-lg border border-amber-200 bg-amber-50 px-3 py-2 text-xs text-amber-900 leading-relaxed"
        >
            {{ $t('message.network_direct_optional_hint') }}
        </div>

        <div class="db-table-responsive">
            <table class="db-table stripe">
                <thead class="db-table-head">
                <tr class="db-table-head-tr">
                    <th class="db-table-head-th">{{ $t("label.name") }}</th>
                    <th class="db-table-head-th">{{ $t("label.printer_ip") }}</th>
                    <th class="db-table-head-th">{{ $t("label.connection") }}</th>
                    <th class="db-table-head-th">{{ $t("label.print_format") }}</th>
                    <th class="db-table-head-th">{{ $t("label.printing_choice") }}</th>
                    <th class="db-table-head-th">{{ $t('label.status') }}</th>
                    <th class="db-table-head-th">{{ $t("label.action") }}</th>
                </tr>
                </thead>
                <tbody class="db-table-body" v-if="printers.length > 0">
                <tr class="db-table-body-tr" v-for="printer in printers" :key="printer.id">
                    <td class="db-table-body-td">{{ printer.name }}</td>
                    <td class="db-table-body-td">{{ printer.ip_display || '—' }}</td>
                    <td class="db-table-body-td">
                        <span class="text-xs font-semibold px-2 py-1 rounded-full" :class="connectionBadge(printer)">
                            {{ connectionText(printer) }}
                        </span>
                    </td>
                    <td class="db-table-body-td">{{ enums.printFormatArray[printer.print_format] }}</td>
                    <td class="db-table-body-td">{{ enums.printingChoiceArray[printer.printing_choice] }}</td>
                    <td class="db-table-body-td">
                        <span :class="statusClass(printer.status)">{{ enums.statusEnumArray[printer.status] }}</span>
                    </td>
                    <td class="db-table-body-td">
                        <div class="flex justify-start items-center gap-1.5">
                            <button type="button" class="db-btn py-1 px-2 text-xs text-white bg-sky-600" @click="testPrint(printer)">
                                {{ $t('label.test_print') }}
                            </button>
                            <SmModalEditComponent @click="edit(printer)"/>
                            <SmDeleteComponent @click="destroy(printer.id)"/>
                        </div>
                    </td>
                </tr>
                </tbody>
                <tbody class="db-table-body" v-else>
                <tr class="db-table-body-tr">
                    <td class="db-table-body-td" colspan="7">
                        <div class="p-4">
                            <img class="m-auto not-found max-w-[300px]" :src="setting.data_not_found" alt="Not Found">
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
</template>

<script>
import LoadingComponent from "../../../common/LoadingComponent.vue";
import PrinterCreateComponent from "./PrinterCreateComponent.vue";
import alertService from "../../../../services/alertService.js";
import PaginationTextComponent from "../../components/pagination/PaginationTextComponent.vue";
import PaginationBox from "../../components/pagination/PaginationBox.vue";
import PaginationSMBox from "../../components/pagination/PaginationSMBox.vue";
import appService from "../../../../services/appService.js";
import TableLimitComponent from "../../components/TableLimitComponent.vue";
import SmDeleteComponent from "../../components/buttons/SmDeleteComponent.vue";
import SmModalEditComponent from "../../components/buttons/SmModalEditComponent.vue";
import statusEnum from "../../../../enums/modules/statusEnum.js";
import askEnum from "../../../../enums/modules/askEnum.js";
import printingChoiceEnum from "../../../../enums/modules/printingChoiceEnum.js";
import printFormatEnum from "../../../../enums/modules/printFormatEnum.js";
import printerTypeEnum from "../../../../enums/modules/printerTypeEnum.js";
import {useModal} from "../../../../composables/modal.js";
import {usePrinterStore} from "../../../../stores/printer.js";
import {useFrontendSettingStore} from "../../../../stores/frontendSetting.js";
import VueSimpleAlert from "vue3-simple-alert";
import {
    probeLocalAgent,
    sendViaLocalBridge,
} from "../../../../services/localPrintBridge.js";
import {printKotIframe} from "../../../../services/thermalIframePrint.js";

export default {
    name: "PrinterListComponent",
    components: {
        TableLimitComponent,
        PaginationSMBox,
        PaginationBox,
        PaginationTextComponent,
        PrinterCreateComponent,
        LoadingComponent,
        SmDeleteComponent,
        SmModalEditComponent,
    },
    setup() {
        const printerStore = usePrinterStore();
        const frontendSettingStore = useFrontendSettingStore();
        return {printerStore, frontendSettingStore};
    },
    data() {
        return {
            loading: {isActive: false},
            agentOnline: false,
            enums: {
                statusEnum,
                statusEnumArray: {
                    [statusEnum.ACTIVE]: this.$t("label.active"),
                    [statusEnum.INACTIVE]: this.$t("label.inactive"),
                },
                printingChoiceArray: {
                    [printingChoiceEnum.BROWSER_POPUP]: this.$t("label.browser_popup"),
                    [printingChoiceEnum.DIRECT_PRINT]: this.$t("label.direct_print"),
                },
                printFormatArray: {
                    [printFormatEnum.INVOICE]: this.$t("label.invoice"),
                    [printFormatEnum.KOT]: this.$t("label.kot"),
                    [printFormatEnum.BOTH]: this.$t("label.both_kot_invoice") || "Both (KOT + Invoice)",
                    [printFormatEnum.NO_AUTO_KOT]: this.$t("label.no_auto_kot"),
                },
                printerTypeArray: {
                    [printerTypeEnum.WINDOWS_SHARED]: this.$t("label.windows_shared_printer"),
                    [printerTypeEnum.NETWORK]: this.$t("label.network_printer"),
                },
            },
            props: {
                form: {
                    name: "",
                    printing_choice: printingChoiceEnum.BROWSER_POPUP,
                    print_format: printFormatEnum.KOT,
                    printer_type: printerTypeEnum.NETWORK,
                    characters_per_line: 42,
                    open_cash_drawer: askEnum.NO,
                    invoice_qr_status: askEnum.NO,
                    computer_ipv4: "",
                    printer_ip: "",
                    printer_port: 9100,
                    windows_printer_name: "",
                    status: statusEnum.ACTIVE,
                },
                search: {
                    paginate: 1,
                    page: 1,
                    per_page: 10,
                    order_column: "id",
                    order_type: "desc",
                },
            },
        };
    },
    mounted() {
        this.list();
    },
    computed: {
        setting() {
            return this.frontendSettingStore.lists;
        },
        printers() {
            return this.printerStore.lists;
        },
        pagination() {
            return this.printerStore.pagination;
        },
        paginationPage() {
            return this.printerStore.page;
        },
        needsLocalAgent() {
            return (this.printers || []).some((p) =>
                Number(p.printing_choice) === printingChoiceEnum.DIRECT_PRINT
                && Number(p.printer_type) === printerTypeEnum.NETWORK
                && !!(p.printer_ip || '').trim()
            );
        },
    },
    methods: {
        statusClass(status) {
            return appService.statusClass(status);
        },
        async refreshAgentStatus() {
            try {
                this.agentOnline = await probeLocalAgent();
            } catch (e) {
                this.agentOnline = false;
            }
        },
        list(page = 1) {
            this.loading.isActive = true;
            this.props.search.page = page;
            this.props.search.with_connection = 1;
            this.printerStore.fetch(this.props.search).then(async () => {
                await this.refreshAgentStatus();
                this.loading.isActive = false;
            }).catch(() => {
                this.loading.isActive = false;
            });
        },
        fetchStatus() {
            this.loading.isActive = true;
            this.printerStore.fetchStatus().then(async (res) => {
                this.printerStore.lists = res.data.data || [];
                await this.refreshAgentStatus();
                this.loading.isActive = false;
                alertService.success(this.$t('message.printers_fetched'));
            }).catch((err) => {
                this.loading.isActive = false;
                alertService.error(err.response?.data?.message || this.$t('message.something_wrong'));
            });
        },
        connectionText(printer) {
            if (printer.connection_status === 'connected') return this.$t('label.ip_connected');
            if (printer.connection_status === 'local_agent') {
                return this.agentOnline
                    ? this.$t('label.local_agent_ready')
                    : this.$t('label.local_agent_offline');
            }
            if (printer.connection_status === 'browser') return this.$t('label.browser_popup');
            if (printer.connection_status === 'offline') return this.$t('label.ip_offline');
            return '—';
        },
        connectionBadge(printer) {
            if (printer.connection_status === 'connected') return 'text-emerald-700 bg-emerald-100';
            if (printer.connection_status === 'local_agent') {
                return this.agentOnline
                    ? 'text-emerald-700 bg-emerald-100'
                    : 'text-amber-800 bg-amber-100';
            }
            if (printer.connection_status === 'browser') return 'text-sky-700 bg-sky-100';
            if (printer.connection_status === 'offline') return 'text-rose-700 bg-rose-100';
            return 'text-slate-600 bg-slate-100';
        },
        edit(printer) {
            useModal().openModal('modal');
            this.printerStore.edit(printer.id);
            this.props.form = {
                name: printer.name,
                printing_choice: printer.printing_choice,
                print_format: printer.print_format,
                printer_type: printer.printer_type,
                characters_per_line: printer.characters_per_line,
                open_cash_drawer: printer.open_cash_drawer,
                invoice_qr_status: printer.invoice_qr_status,
                computer_ipv4: printer.computer_ipv4 || "",
                printer_ip: printer.printer_ip || "",
                printer_port: printer.printer_port || 9100,
                windows_printer_name: printer.windows_printer_name || "",
                status: printer.status,
            };
        },
        async testPrint(printer) {
            this.loading.isActive = true;
            try {
                // Browser Popup / Windows Shared → thermal iframe (no Local Agent page)
                const isBrowserPath = Number(printer.printing_choice) === printingChoiceEnum.BROWSER_POPUP
                    || Number(printer.printer_type) === printerTypeEnum.WINDOWS_SHARED
                    || !(printer.printer_ip || '').trim();

                if (isBrowserPath) {
                    this.loading.isActive = false;
                    alertService.success(this.$t('message.printer_test_pick_windows'));
                    await printKotIframe({
                        copy: 'TEST PRINT',
                        ticket_no: 'TEST - ' + (printer.name || 'TVS'),
                        order_date: new Date().toLocaleDateString(),
                        order_time: new Date().toLocaleTimeString(),
                        order_type_label: 'TEST',
                        biller: 'Cashier',
                        items: [{ name: 'Printer configuration OK', quantity: 1 }],
                        total_qty: 1,
                    }, printer.name || 'TVS', {force: true});
                    return;
                }

                await this.refreshAgentStatus();
                const res = await this.printerStore.testPrint(printer.id);
                const data = res.data?.data || {};

                if (data.mode === 'direct_print' && data.success) {
                    this.loading.isActive = false;
                    alertService.success(data.message || this.$t('message.printer_test_sent'));
                    return;
                }

                // Network Direct Print with agent if available; else browser fallback (no agent page)
                if (data.raw_base64 && this.agentOnline) {
                    try {
                        await sendViaLocalBridge(data);
                        this.loading.isActive = false;
                        alertService.success(this.$t('message.printer_test_sent'));
                        return;
                    } catch (bridgeErr) {
                        this.agentOnline = false;
                    }
                }

                this.loading.isActive = false;
                alertService.success(this.$t('message.printer_test_pick_windows'));
                await printKotIframe({
                    copy: 'TEST PRINT',
                    ticket_no: 'TEST - ' + (printer.name || 'TVS'),
                    order_date: new Date().toLocaleDateString(),
                    order_time: new Date().toLocaleTimeString(),
                    order_type_label: 'TEST',
                    biller: 'Cashier',
                    items: [{ name: 'Printer configuration OK', quantity: 1 }],
                    total_qty: 1,
                }, printer.name || 'TVS', {force: true});
            } catch (err) {
                this.loading.isActive = false;
                alertService.error(err.response?.data?.message || this.$t('message.something_wrong'));
            }
        },
        destroy(id) {
            return new VueSimpleAlert.confirm(
                this.$t("message.delete_record"),
                this.$t("message.are_you_sure"),
                "warning",
                {
                    confirmButtonText: this.$t("button.yes_delete"),
                    cancelButtonText: this.$t("button.no_cancel"),
                    confirmButtonColor: "#1AB759",
                    cancelButtonColor: "#E93C3C"
                }
            ).then(() => {
                this.loading.isActive = true;
                this.printerStore.destroy({id, search: this.props.search}).then(() => {
                    this.loading.isActive = false;
                    alertService.successFlip(null, this.$t("menu.printer_settings"));
                }).catch((err) => {
                    this.loading.isActive = false;
                    alertService.error(err.response?.data?.message);
                });
            }).catch(() => {});
        },
    },
};
</script>
