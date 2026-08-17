<template>
    <!-- Always in DOM for reliable thermal print (not inside a closed modal) -->
    <div class="customer-bill-print-root" aria-hidden="true">
        <div v-if="hasData" class="receipt-body bill-sheet" :dir="displayMode">
            <div class="bill-center">
                <img v-if="restaurant?.logo" :src="restaurant.logo" alt="" class="bill-logo"/>
                <div class="bill-name">{{ restaurant?.name || 'Restaurant' }}</div>
                <div class="bill-doc-title">TAX INVOICE</div>
            </div>

            <div class="bill-line"></div>

            <div class="bill-nameline">Name: <strong>{{ customerDisplay }}</strong></div>

            <div class="bill-dash"></div>

            <div class="bill-row bill-meta">
                <span>Date: {{ orderDate }}</span>
                <span class="bill-right">Bill#: {{ order?.order_serial_no }}</span>
            </div>
            <div class="bill-row bill-meta">
                <span>Time: {{ orderTime }}</span>
                <span class="bill-right">{{ tableLabel ? 'Table: ' + tableLabel : orderTypeLabel }}</span>
            </div>
            <div v-if="cashierName" class="bill-row bill-meta">
                <span>Cashier: {{ cashierName }}</span>
                <span class="bill-right">{{ tableLabel ? orderTypeLabel : '' }}</span>
            </div>

            <div class="bill-line"></div>

            <div class="bill-row bill-head">
                <span class="b-item">Item</span>
                <span class="b-qty">Qty</span>
                <span class="b-price">Price</span>
                <span class="b-amt">Amt</span>
            </div>
            <div class="bill-line"></div>

            <div v-for="(item, idx) in normalizedItems" :key="idx" class="bill-item">
                <div class="bill-row">
                    <span class="b-item">{{ item.name }}</span>
                    <span class="b-qty">{{ item.quantity }}</span>
                    <span class="b-price">{{ item.unitPrice }}</span>
                    <span class="b-amt">{{ item.priceLabel }}</span>
                </div>
                <div v-if="item.variationText" class="bill-mod">{{ item.variationText }}</div>
                <div v-if="item.extrasText" class="bill-mod">+ {{ item.extrasText }}</div>
                <div v-if="item.instruction" class="bill-mod">** {{ item.instruction }}</div>
            </div>

            <div class="bill-line"></div>

            <div class="bill-row">
                <span>Total Qty {{ totalQty }}</span>
                <span class="bill-right">Sub Total. {{ subtotalLabel }}</span>
            </div>
            <div v-if="hasDiscount" class="bill-row">
                <span>Discount</span>
                <span class="bill-right">- {{ discountLabel }}</span>
            </div>
            <template v-if="hasTax">
                <div class="bill-row">
                    <span>CGST{{ gstHalfLabel }}</span>
                    <span class="bill-right">{{ halfTaxLabel }}</span>
                </div>
                <div class="bill-row">
                    <span>SGST{{ gstHalfLabel }}</span>
                    <span class="bill-right">{{ halfTaxLabel }}</span>
                </div>
            </template>
            <div v-if="hasRoundOff" class="bill-row">
                <span>Round Off</span>
                <span class="bill-right">{{ roundOffLabel }}</span>
            </div>

            <div class="bill-line"></div>

            <div class="bill-row bill-grand">
                <span>Grand Total</span>
                <span class="bill-right">{{ totalLabel }}</span>
            </div>
            <div v-if="paymentLabel" class="bill-row">
                <span>Payment</span>
                <span class="bill-right">{{ paymentLabelUpper }}</span>
            </div>

            <div class="bill-line"></div>
            <div class="bill-center bill-thanks">
                Thank you for dining with us!<br>Visit again.
            </div>
            <div v-if="poweredBy" class="bill-center bill-powered">
                {{ $t('label.powered_by') }} {{ poweredBy }}
            </div>
            <div class="bill-dash"></div>
        </div>
    </div>
</template>

<script>
import {useCommonStore} from "../../../../stores/common.js";
import DisplayModeEnum from "../../../../enums/modules/displayModeEnum.js";
import appService from "../../../../services/appService.js";
import {useFrontendSettingStore} from "../../../../stores/frontendSetting.js";
import orderTypeEnum from "../../../../enums/modules/orderTypeEnum.js";

export default {
    name: "CustomerReceiptPrintSheet",
    props: {
        order: {type: Object, default: null},
        restaurant: {type: Object, default: () => ({})},
        items: {type: Array, default: () => []},
        cashierName: {type: String, default: ''},
        waiterName: {type: String, default: ''},
        paymentLabel: {type: String, default: ''},
        tableLabel: {type: String, default: ''},
        orderTypeOverride: {type: String, default: ''},
    },
    setup() {
        return {
            commonStore: useCommonStore(),
            frontendSettingStore: useFrontendSettingStore(),
        };
    },
    computed: {
        hasData() {
            return !!(this.order && (this.order.order_serial_no || this.order.id));
        },
        displayMode() {
            return this.commonStore.display_mode === DisplayModeEnum.LTR ? 'ltr' : 'rtl';
        },
        setting() {
            return this.frontendSettingStore.lists || {};
        },
        orderDate() {
            return this.order?.order_date || this.order?.order_datetime || '';
        },
        orderTime() {
            return this.order?.order_time || '';
        },
        cityLine() {
            const r = this.restaurant || {};
            return [r.city, r.state, r.zip_code].filter(Boolean).join(', ');
        },
        customerDisplay() {
            const name = (this.order?.user?.name || this.order?.customer_name || '').toString().trim();
            // POS guest / placeholder customer → show Walk-in on the bill
            if (!name || /walking\s*customer/i.test(name) || /^guest$/i.test(name)) {
                return 'Walk-in';
            }
            return name;
        },
        orderTypeLabel() {
            if (this.orderTypeOverride) return this.orderTypeOverride;
            const t = Number(this.order?.order_type);
            if (t === orderTypeEnum.DINING_TABLE) return 'Dine In';
            if (t === orderTypeEnum.DELIVERY) return 'Delivery';
            if (t === orderTypeEnum.TAKEAWAY) return 'Take Away';
            if (t === orderTypeEnum.POS) return 'Take Away';
            return 'POS';
        },
        subtotalLabel() {
            return this.order?.subtotal_currency_price || this.money(this.order?.subtotal);
        },
        taxLabel() {
            return this.order?.total_tax_currency_price || this.money(this.order?.total_tax);
        },
        discountLabel() {
            return this.order?.discount_currency_price || this.money(this.order?.discount);
        },
        totalLabel() {
            return this.order?.total_currency_price || this.money(this.order?.total);
        },
        hasDiscount() {
            return parseFloat(this.order?.discount || 0) > 0;
        },
        hasTax() {
            return parseFloat(this.order?.total_tax || 0) > 0;
        },
        paymentLabelUpper() {
            return String(this.paymentLabel || '').toUpperCase();
        },
        poweredBy() {
            return String(this.setting?.company_name || '').trim();
        },
        /** Tax is stored as one GST figure; the bill shows it as CGST + SGST halves. */
        gstRate() {
            const rates = (this.items || [])
                .map((i) => parseFloat(i.tax_rate || 0))
                .filter((r) => r > 0);
            return rates.length ? rates[0] : 0;
        },
        gstHalfLabel() {
            if (!(this.gstRate > 0)) return '';
            const half = (this.gstRate / 2).toFixed(2).replace(/\.?0+$/, '');
            return ` (${half}%)`;
        },
        halfTaxLabel() {
            return this.money(parseFloat(this.order?.total_tax || 0) / 2);
        },
        roundOffAmount() {
            const num = (v) => parseFloat(v || 0) || 0;
            const o = this.order || {};
            const computed = num(o.subtotal) - num(o.discount) + num(o.total_tax)
                + num(o.delivery_fee) + num(o.service_fee) + num(o.rider_tip);
            return Math.round((num(o.total) - computed) * 100) / 100;
        },
        hasRoundOff() {
            return Math.abs(this.roundOffAmount) >= 0.01;
        },
        roundOffLabel() {
            const value = this.roundOffAmount;
            return (value < 0 ? '-' : '') + this.money(Math.abs(value));
        },
        totalQty() {
            return (this.items || []).reduce((s, i) => s + parseFloat(i.quantity || 0), 0);
        },
        normalizedItems() {
            return (this.items || []).map((item) => {
                const variations = item.item_variations;
                let variationText = '';
                if (Array.isArray(variations)) {
                    variationText = variations.map((v) => {
                        if (typeof v === 'string') return v;
                        return [v.variation_name, v.name].filter(Boolean).join(': ');
                    }).filter(Boolean).join(', ');
                } else if (variations && typeof variations === 'object') {
                    if (variations.names) {
                        const names = variations.names;
                        variationText = Array.isArray(names)
                            ? names.join(', ')
                            : Object.entries(names || {}).map(([k, v]) => `${k}: ${v}`).join(', ');
                    }
                }

                let extras = item.item_extras;
                let extrasText = '';
                if (Array.isArray(extras)) {
                    extrasText = extras.map((e) => e.name || e).filter(Boolean).join(', ');
                } else if (extras && typeof extras === 'object' && extras.names) {
                    extrasText = (extras.names || []).join(', ');
                }

                const qty = parseFloat(item.quantity || 1) || 1;
                const totalNum = parseFloat(item.total_convert_price || item.total_price || item.total || 0);
                const unitNum = totalNum / qty;

                return {
                    quantity: item.quantity,
                    name: item.item_name || item.name,
                    unitPrice: (typeof item.price === 'string' && item.price) ? item.price : this.money(unitNum),
                    priceLabel: item.total_currency_price || this.money(totalNum),
                    variationText,
                    extrasText,
                    instruction: item.instruction || '',
                };
            });
        },
    },
    methods: {
        money(amount) {
            const s = this.setting;
            return appService.currencyFormat(
                amount || 0,
                s.site_digit_after_decimal_point,
                s.site_default_currency_symbol,
                s.site_currency_position
            );
        },
    },
};
</script>

<style>
.customer-bill-print-root {
    /* Keep off-screen until print — must not appear inside the POS UI */
    position: fixed !important;
    left: -100vw !important;
    top: 0 !important;
    width: 72mm;
    height: 0;
    overflow: hidden;
    opacity: 0;
    pointer-events: none;
    z-index: -1;
}
.bill-sheet {
    width: 72mm;
    max-width: 100%;
    margin: 0 auto;
    padding: 2mm;
    font-family: "Helvetica Neue", Arial, "DejaVu Sans", sans-serif;
    color: #000;
    font-size: 11px;
    line-height: 1.3;
    word-wrap: break-word;
}
.bill-center { text-align: center; }
.bill-logo {
    max-height: 42px;
    max-width: 100px;
    object-fit: contain;
    margin: 0 auto 4px;
    display: block;
}
.bill-name {
    font-size: 14px;
    font-weight: 600;
}
.bill-doc-title {
    font-size: 18px;
    font-weight: 700;
    letter-spacing: .3px;
}
.bill-addr { font-size: 10px; }
.bill-line {
    border-top: 1px solid #000;
    margin: 4px 0;
}
.bill-dash {
    border-top: 1px dashed #000;
    margin: 4px 0;
}
.bill-nameline { font-size: 12.5px; }
.bill-row {
    display: flex;
    justify-content: space-between;
    gap: 4px;
    font-size: 12px;
}
.bill-meta { padding: 1px 0; }
.bill-right { text-align: right; }
.bill-head { font-weight: 700; }
.b-item { width: 40%; }
.b-qty { width: 12%; text-align: right; }
.b-price { width: 24%; text-align: right; }
.b-amt { width: 24%; text-align: right; }
.bill-item { margin: 2px 0; }
.bill-mod {
    margin-left: 8px;
    font-size: 10.5px;
}
.bill-grand {
    font-weight: 700;
    font-size: 16px;
    padding: 2px 0 1px;
}
.bill-thanks {
    margin-top: 4px;
    font-size: 12.5px;
    font-weight: 700;
    line-height: 1.35;
}
.bill-powered {
    margin-top: 4px;
    font-size: 11px;
    font-weight: 400;
}

@media print {
    body.printing-receipt * {
        visibility: hidden !important;
    }
    body.printing-receipt .customer-bill-print-root,
    body.printing-receipt .customer-bill-print-root * {
        visibility: visible !important;
    }
    body.printing-receipt .customer-bill-print-root {
        display: block !important;
        position: absolute !important;
        left: 0 !important;
        top: 0 !important;
        right: 0 !important;
        width: 100% !important;
        height: auto !important;
        margin: 0 auto !important;
        overflow: visible !important;
        opacity: 1 !important;
        pointer-events: auto !important;
        z-index: 1 !important;
        background: #fff !important;
        color: #000 !important;
        text-align: center;
    }
    body.printing-receipt .customer-bill-print-root .bill-sheet {
        display: inline-block !important;
        width: 72mm !important;
        max-width: 100% !important;
        margin: 0 auto !important;
        text-align: left;
        vertical-align: top;
    }
    @page {
        margin: 0;
        size: 80mm auto;
    }
}
</style>
