<template>
    <div class="mb-4 rounded-xl border border-[#EFF0F6] bg-white p-3">
        <div class="flex items-center justify-between gap-2 mb-2">
            <h4 class="text-sm font-semibold text-heading">
                {{ title || $t('label.connected_printers') }}
            </h4>
            <button
                type="button"
                class="db-btn py-1 px-2 text-xs text-white bg-sky-600"
                :disabled="loading"
                @click="fetchPrinters"
            >
                {{ loading ? $t('label.fetching') : $t('label.fetch_printers') }}
            </button>
        </div>

        <div v-if="loading && printers.length === 0" class="text-xs text-paragraph">
            {{ $t('message.fetching_printers') }}
        </div>

        <div v-else-if="printers.length === 0" class="text-xs text-paragraph">
            {{ $t('message.no_printers_configured') }}
        </div>

        <ul v-else class="flex flex-col gap-2">
            <li
                v-for="printer in printers"
                :key="printer.id"
                class="flex items-center justify-between gap-2 rounded-lg border border-[#EFF0F6] px-3 py-2"
            >
                <div class="min-w-0">
                    <p class="text-sm font-medium text-heading truncate">{{ printer.name }}</p>
                    <p class="text-[11px] text-paragraph">
                        {{ formatLabel(printer) }}
                        <span v-if="printer.ip_display"> · {{ printer.ip_display }}</span>
                    </p>
                </div>
                <span
                    class="shrink-0 text-[11px] font-semibold px-2 py-1 rounded-full"
                    :class="badgeClass(printer)"
                >
                    {{ statusText(printer) }}
                </span>
            </li>
        </ul>
    </div>
</template>

<script>
import axios from "axios";
import printFormatEnum from "../../../enums/modules/printFormatEnum.js";
import {probeLocalAgent} from "../../../services/localPrintBridge.js";

export default {
    name: "PrinterConnectionBar",
    props: {
        title: {type: String, default: ""},
        endpoint: {type: String, default: "admin/pos/printers"},
        format: {type: String, default: ""}, // kot|invoice|""
        autoFetch: {type: Boolean, default: true},
    },
    data() {
        return {
            loading: false,
            printers: [],
            agentOnline: false,
        };
    },
    mounted() {
        if (this.autoFetch) {
            this.fetchPrinters();
        }
    },
    methods: {
        fetchPrinters() {
            this.loading = true;
            let url = this.endpoint;
            if (this.format) {
                url += (url.includes("?") ? "&" : "?") + "format=" + encodeURIComponent(this.format);
            }
            axios.get(url).then(async (res) => {
                this.printers = res.data.data || [];
                await this.checkLocalAgent();
                this.loading = false;
                this.$emit("loaded", this.printers);
            }).catch(() => {
                this.printers = [];
                this.agentOnline = false;
                this.loading = false;
                this.$emit("loaded", []);
            });
        },
        formatLabel(printer) {
            if (Number(printer.print_format) === printFormatEnum.INVOICE) {
                return this.$t("label.invoice") + " / POS";
            }
            if (Number(printer.print_format) === printFormatEnum.KOT) {
                return this.$t("label.kot");
            }
            return this.$t("label.no_auto_kot");
        },
        statusText(printer) {
            if (printer.connection_status === "connected") {
                return this.$t("label.ip_connected");
            }
            if (printer.connection_status === "local_agent") {
                return this.agentOnline
                    ? this.$t("label.local_agent_ready")
                    : this.$t("label.local_agent_offline");
            }
            if (printer.connection_status === "browser") {
                return this.$t("label.browser_popup");
            }
            if (printer.connection_status === "offline") {
                return this.$t("label.ip_offline");
            }
            return this.$t("label.unknown");
        },
        badgeClass(printer) {
            if (printer.connection_status === "connected") {
                return "text-emerald-700 bg-emerald-100";
            }
            if (printer.connection_status === "local_agent") {
                return this.agentOnline
                    ? "text-emerald-700 bg-emerald-100"
                    : "text-amber-800 bg-amber-100";
            }
            if (printer.connection_status === "browser") {
                return "text-sky-700 bg-sky-100";
            }
            return "text-rose-700 bg-rose-100";
        },
        async checkLocalAgent() {
            try {
                this.agentOnline = await probeLocalAgent();
            } catch (e) {
                this.agentOnline = false;
            }
        },
    },
};
</script>
