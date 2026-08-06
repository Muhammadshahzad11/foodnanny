<template>
    <div class="kitchen-print-root hidden">
        <div v-if="payload" class="kot-sheet">
            <div class="kot-center kot-head">KITCHEN KOT</div>
            <div class="kot-center kot-meta">
                {{ payload.order_date }} {{ payload.order_time }}
            </div>
            <div class="kot-center kot-ticket">
                {{ payload.ticket_no || ('KOT - ' + payload.kot_no) }}
            </div>

            <!-- Order type + table — high-contrast thermal banner -->
            <div class="kot-banner">
                <div class="kot-banner-rule">************************</div>
                <div class="kot-banner-type">{{ displayOrderType }}</div>
                <div v-if="displayTable" class="kot-banner-table">
                    TABLE NO: {{ displayTable }}
                </div>
                <div v-else-if="payload.counter" class="kot-banner-table">
                    {{ payload.counter }} COUNTER
                </div>
                <div class="kot-banner-rule">************************</div>
            </div>

            <div class="kot-line">Biller: {{ payload.biller || 'Cashier' }}</div>
            <div v-if="payload.waiter" class="kot-line">Waiter: {{ payload.waiter }}</div>
            <div v-if="payload.reprint" class="kot-center kot-reprint">*** REPRINT ***</div>
            <div class="kot-dash"></div>

            <div class="kot-row kot-cols kot-col-head">
                <span class="c-no">No.</span>
                <span class="c-item">Item</span>
                <span class="c-qty">Qty.</span>
            </div>
            <div class="kot-dash"></div>

            <div v-for="(item, idx) in payload.items" :key="idx" class="kot-item-block">
                <div class="kot-row kot-cols">
                    <span class="c-no">{{ idx + 1 }}</span>
                    <span class="c-item">{{ item.name }}</span>
                    <span class="c-qty">{{ item.quantity }}</span>
                </div>
                <div v-for="(line, i) in (item.variation_lines || [])" :key="'v'+i" class="kot-mod">- {{ line }}</div>
                <div v-for="(line, i) in (item.extra_lines || [])" :key="'e'+i" class="kot-mod">+ {{ line }}</div>
                <div v-if="item.instruction" class="kot-mod">** {{ item.instruction }}</div>
            </div>

            <div class="kot-dash"></div>
            <div class="kot-row kot-cols">
                <span class="c-no"></span>
                <span class="c-item kot-strong">TOTAL QTY</span>
                <span class="c-qty kot-strong">{{ payload.total_qty }}</span>
            </div>
            <div class="kot-dash"></div>

            <div v-if="payload.special_note || payload.order_note" class="kot-note">
                NOTE: {{ payload.special_note || payload.order_note }}
            </div>
            <div v-if="payload.priority_label && payload.priority_label !== 'normal'" class="kot-center kot-priority">
                !! {{ String(payload.priority_label).toUpperCase() }} PRIORITY !!
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: "KitchenTicketPrintSheet",
    props: {
        payload: {
            type: Object,
            default: null,
        }
    },
    computed: {
        displayOrderType() {
            const raw = (this.payload?.order_type_label || 'POS').toString().trim();
            return raw.toUpperCase();
        },
        displayTable() {
            return this.payload?.table_no || this.payload?.table?.number || '';
        },
    },
}
</script>

<style>
@media print {
    body.printing-kot * {
        visibility: hidden !important;
    }
    body.printing-kot .kitchen-print-root,
    body.printing-kot .kitchen-print-root * {
        visibility: visible !important;
    }
    body.printing-kot .kitchen-print-root {
        display: block !important;
        position: absolute;
        left: 0;
        top: 0;
        width: 100%;
    }
    @page {
        margin: 2mm;
        size: 80mm auto;
    }
}

.kot-sheet {
    width: 72mm;
    max-width: 100%;
    margin: 0 auto;
    padding: 2mm;
    font-family: "Courier New", Courier, ui-monospace, monospace;
    color: #000;
    font-size: 11px;
    line-height: 1.3;
    word-wrap: break-word;
    overflow-wrap: anywhere;
}
.kot-center { text-align: center; }
.kot-head {
    font-size: 15px;
    font-weight: 900;
    letter-spacing: 1px;
    margin-bottom: 2px;
}
.kot-meta { font-size: 11px; }
.kot-ticket {
    font-size: 13px;
    font-weight: 800;
    margin: 3px 0 6px;
}
.kot-strong { font-weight: 800; }

/* High-visibility order type / table for kitchen staff */
.kot-banner {
    text-align: center;
    margin: 6px 0 8px;
    padding: 4px 0;
    border-top: 2px solid #000;
    border-bottom: 2px solid #000;
}
.kot-banner-rule {
    font-size: 10px;
    letter-spacing: 1px;
    line-height: 1.1;
    font-weight: 700;
}
.kot-banner-type {
    font-size: 18px;
    font-weight: 900;
    letter-spacing: 2px;
    line-height: 1.2;
    margin: 4px 0 2px;
    text-transform: uppercase;
}
.kot-banner-table {
    font-size: 16px;
    font-weight: 900;
    letter-spacing: 1px;
    line-height: 1.25;
    margin: 2px 0 4px;
    text-transform: uppercase;
}

.kot-dash {
    border-top: 1px dashed #000;
    margin: 5px 0;
}
.kot-line { margin: 1px 0; }
.kot-reprint {
    font-weight: 900;
    margin: 4px 0;
    font-size: 12px;
}
.kot-priority {
    font-weight: 900;
    font-size: 13px;
    margin-top: 6px;
    letter-spacing: 1px;
}
.kot-row {
    display: flex;
    width: 100%;
}
.kot-col-head { font-weight: 800; }
.kot-cols .c-no { width: 10%; flex-shrink: 0; }
.kot-cols .c-item { width: 72%; padding-right: 4px; }
.kot-cols .c-qty { width: 18%; text-align: right; flex-shrink: 0; }
.kot-item-block { margin: 4px 0; }
.kot-mod {
    margin-left: 10%;
    font-size: 10px;
}
.kot-note {
    margin-top: 6px;
    font-weight: 800;
    font-size: 12px;
    white-space: pre-wrap;
}
</style>
