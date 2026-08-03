<template>
    <div id="table-qr-print" class="hidden print:block">
        <div class="qr-print-sheet">
            <div class="qr-print-card" v-for="item in items" :key="item.uuid || item.id">
                <h2 class="qr-print-restaurant">{{ item.restaurant }}</h2>
                <p class="qr-print-table">{{ item.name }}</p>
                <p class="qr-print-number">{{ $t('label.table_number') }}: {{ item.table_number }}</p>
                <img :src="item.qr_image_base64" alt="QR" class="qr-print-image"/>
                <p class="qr-print-instruction">{{ instruction || $t('message.table_qr_scan_instruction') }}</p>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: "TableQrPrintSheetComponent",
    props: {
        items: {type: Array, default: () => []},
        instruction: {type: String, default: ""},
    }
};
</script>

<style>
@media print {
    body * {
        visibility: hidden !important;
    }

    #table-qr-print,
    #table-qr-print * {
        visibility: visible !important;
    }

    #table-qr-print {
        position: absolute;
        left: 0;
        top: 0;
        width: 100%;
        display: block !important;
    }

    .qr-print-sheet {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 24px;
        padding: 24px;
    }

    .qr-print-card {
        border: 1px solid #d1d5db;
        border-radius: 12px;
        padding: 20px;
        text-align: center;
        page-break-inside: avoid;
        break-inside: avoid;
    }

    .qr-print-restaurant {
        font-size: 18px;
        font-weight: 700;
        margin-bottom: 4px;
    }

    .qr-print-table {
        font-size: 16px;
        font-weight: 600;
    }

    .qr-print-number {
        font-size: 13px;
        color: #4b5563;
        margin-bottom: 12px;
    }

    .qr-print-image {
        width: 180px;
        height: 180px;
        margin: 0 auto 12px;
        object-fit: contain;
    }

    .qr-print-instruction {
        font-size: 12px;
        color: #6b7280;
    }
}
</style>
