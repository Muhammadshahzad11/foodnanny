<template>
    <div class="kitchen-print-root hidden">
        <div v-if="payload" class="kot-sheet">
            <div class="kot-center kot-head">{{ isModification ? 'ORDER CHANGE' : 'KOT' }}</div>
            <div v-if="payload.restaurant" class="kot-center kot-shop">{{ payload.restaurant }}</div>
            <div class="kot-rule"></div>

            <div class="kot-row kot-info">
                <span>{{ placeLabel }}</span>
                <span class="kot-right">{{ displayOrderType }}</span>
            </div>
            <div class="kot-row kot-info">
                <span>{{ payload.order_date }} {{ payload.order_time }}</span>
                <span class="kot-right">{{ payload.ticket_no || ('KOT - ' + payload.kot_no) }}</span>
            </div>
            <div v-if="payload.waiter" class="kot-row kot-info">
                <span>Waiter</span>
                <span class="kot-right">{{ payload.waiter }}</span>
            </div>
            <div v-if="payload.reprint" class="kot-center kot-reprint">*** REPRINT ***</div>

            <div class="kot-row kot-cols kot-col-head">
                <span class="c-item">ITEM</span>
                <span class="c-qty">QTY</span>
            </div>

            <div v-for="(item, idx) in payload.items" :key="idx" class="kot-item-block">
                <div class="kot-row kot-cols">
                    <span class="c-item">{{ idx + 1 }}. {{ item.name }}</span>
                    <span class="c-qty">{{ item.change_label || item.quantity }}</span>
                </div>
                <div v-for="(line, i) in (item.variation_lines || [])" :key="'v'+i" class="kot-mod">- {{ line }}</div>
                <div v-for="(line, i) in (item.extra_lines || [])" :key="'e'+i" class="kot-mod">+ {{ line }}</div>
                <div v-if="item.instruction" class="kot-mod">** {{ item.instruction }}</div>
            </div>

            <div class="kot-rule"></div>
            <div class="kot-right kot-strong kot-total">Total: {{ payload.total_qty }} items</div>

            <div v-if="payload.special_note || payload.order_note" class="kot-note">
                NOTE: {{ payload.special_note || payload.order_note }}
            </div>
            <div v-if="payload.priority_label && payload.priority_label !== 'normal'" class="kot-center kot-priority">
                !! {{ String(payload.priority_label).toUpperCase() }} PRIORITY !!
            </div>
            <div v-if="poweredBy" class="kot-center kot-powered">
                &mdash; {{ $t('label.powered_by') }} {{ poweredBy }}
            </div>
            <div class="kot-dash"></div>
        </div>
    </div>
</template>

<script>
import {useFrontendSettingStore} from "../../../stores/frontendSetting.js";

export default {
    name: "KitchenTicketPrintSheet",
    props: {
        payload: {
            type: Object,
            default: null,
        }
    },
    setup() {
        return {
            frontendSettingStore: useFrontendSettingStore(),
        };
    },
    computed: {
        poweredBy() {
            const fromPayload = String(this.payload?.powered_by || '').trim();
            if (fromPayload) return fromPayload;
            return String(this.frontendSettingStore.lists?.company_name || '').trim();
        },
        isModification() {
            return !!(this.payload?.is_modification || this.payload?.modification);
        },
        displayOrderType() {
            return (this.payload?.order_type_label || 'POS').toString().trim();
        },
        displayTable() {
            return this.payload?.table_no || this.payload?.table?.number || '';
        },
        placeLabel() {
            if (this.displayTable) return `Table: ${this.displayTable}`;
            return this.payload?.counter ? `${this.payload.counter} Counter` : 'Counter';
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
    font-family: "Helvetica Neue", Arial, "DejaVu Sans", sans-serif;
    color: #000;
    font-size: 11px;
    line-height: 1.3;
    word-wrap: break-word;
    overflow-wrap: anywhere;
}
.kot-center { text-align: center; }
.kot-right { text-align: right; }
.kot-head {
    font-size: 22px;
    font-weight: 700;
    letter-spacing: 4px;
}
.kot-shop {
    font-size: 14px;
    font-weight: 700;
}
.kot-strong { font-weight: 700; }
.kot-rule {
    border-top: 1px solid #000;
    margin: 4px 0;
}
.kot-dash {
    border-top: 1px dashed #000;
    margin: 4px 0;
}
.kot-info {
    justify-content: space-between;
    gap: 6px;
    margin: 1px 0;
    font-size: 12px;
}
.kot-reprint {
    font-weight: 800;
    margin: 4px 0;
    font-size: 12px;
}
.kot-priority {
    font-weight: 800;
    font-size: 13px;
    margin-top: 6px;
    letter-spacing: 1px;
}
.kot-row {
    display: flex;
    width: 100%;
}
.kot-col-head {
    font-weight: 700;
    margin-top: 5px;
    padding: 2px 0;
    border-top: 1px solid #000;
    border-bottom: 1px solid #000;
}
.kot-cols .c-item { width: 80%; padding-right: 4px; }
.kot-cols .c-qty { width: 20%; text-align: right; flex-shrink: 0; }
.kot-item-block {
    padding: 2px 0;
}
.kot-mod {
    margin-left: 12px;
    font-size: 10px;
}
.kot-total {
    margin-top: 5px;
    font-size: 12px;
}
.kot-note {
    margin-top: 6px;
    font-weight: 700;
    font-size: 12px;
    white-space: pre-wrap;
}
.kot-powered {
    margin-top: 6px;
    font-size: 9px;
}
</style>
