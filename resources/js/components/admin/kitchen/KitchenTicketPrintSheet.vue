<template>
    <div class="kitchen-print-root hidden print:block">
        <div v-if="payload" class="kot-sheet">
            <p class="kot-copy">{{ payload.copy || 'KITCHEN' }} COPY</p>
            <p v-if="payload.reprint" class="kot-reprint">*** REPRINT ***</p>
            <h1 class="kot-title">{{ payload.restaurant }}</h1>
            <p class="kot-line"><strong>Order:</strong> #{{ payload.order_serial_no }}</p>
            <p class="kot-line" v-if="payload.table">
                <strong>Table:</strong> {{ payload.table.number }} · {{ payload.table.name }}
            </p>
            <p class="kot-line" v-if="payload.waiter"><strong>Waiter:</strong> {{ payload.waiter }}</p>
            <p class="kot-line" v-if="payload.customer"><strong>Customer:</strong> {{ payload.customer }}</p>
            <p class="kot-line"><strong>Time:</strong> {{ payload.order_datetime }}</p>
            <p class="kot-line" v-if="payload.preparation_time">
                <strong>ETA:</strong> {{ payload.preparation_time }} min
            </p>
            <p class="kot-line" v-if="payload.priority_label">
                <strong>Priority:</strong> {{ payload.priority_label }}
            </p>
            <p class="kot-note" v-if="payload.order_note">NOTE: {{ payload.order_note }}</p>
            <hr/>
            <div v-for="(item, idx) in payload.items" :key="idx" class="kot-item">
                <p class="kot-item-name">{{ item.quantity }} × {{ item.name }}</p>
                <p v-for="(line, i) in (item.variation_lines || [])" :key="'v'+i" class="kot-mod">- {{ line }}</p>
                <p v-for="(line, i) in (item.extra_lines || [])" :key="'e'+i" class="kot-mod">+ {{ line }}</p>
                <p v-if="item.instruction" class="kot-item-note">** {{ item.instruction }}</p>
            </div>
            <hr/>
            <p class="kot-foot">Printed: {{ payload.printed_at }}</p>
            <p class="kot-foot">Kitchen Ticket — No prices</p>
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
    }
}
</script>

<style>
@media print {
    body * {
        visibility: hidden !important;
    }
    .kitchen-print-root,
    .kitchen-print-root * {
        visibility: visible !important;
    }
    .kitchen-print-root {
        display: block !important;
        position: absolute;
        left: 0;
        top: 0;
        width: 100%;
    }
}

.kot-sheet {
    width: 280px;
    margin: 0 auto;
    font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
    color: #000;
    font-size: 12px;
}
.kot-copy {
    text-align: center;
    font-weight: 700;
    letter-spacing: 1px;
}
.kot-reprint {
    text-align: center;
    font-weight: 700;
    margin: 4px 0;
}
.kot-title {
    text-align: center;
    font-size: 16px;
    margin: 8px 0;
}
.kot-line {
    margin: 2px 0;
}
.kot-note {
    margin: 8px 0;
    font-weight: 700;
}
.kot-item {
    margin: 8px 0;
}
.kot-item-name {
    font-weight: 700;
    font-size: 13px;
}
.kot-mod {
    margin: 1px 0 1px 8px;
}
.kot-item-note {
    font-weight: 700;
    margin-top: 2px;
}
.kot-foot {
    text-align: center;
    margin-top: 6px;
}
</style>
