<template>
    <!-- Always in DOM for reliable thermal print (not inside a closed modal) -->
    <div class="pos-receipt-print-root customer-receipt-sheet" aria-hidden="true">
        <div v-if="hasData" class="receipt-body bill-sheet" :dir="displayMode">
            <div class="bill-center">
                <img v-if="restaurant?.logo" :src="restaurant.logo" alt="" class="bill-logo"/>
                <div class="bill-name">{{ restaurant?.name || 'Restaurant' }}</div>
                <div v-if="restaurant?.address" class="bill-addr">{{ restaurant.address }}</div>
                <div v-if="cityLine" class="bill-addr">{{ cityLine }}</div>
                <div v-if="restaurant?.phone" class="bill-addr">
                    ph: {{ (restaurant.country_code || '') + restaurant.phone }}
                </div>
            </div>

            <div class="bill-line"></div>

            <div class="bill-row">
                <span>Name:</span>
                <span>{{ customerDisplay }}</span>
            </div>

            <div class="bill-grid">
                <div>
                    <div>Date: {{ orderDate }}</div>
                    <div>Time: {{ orderTime }}</div>
                    <div v-if="cashierName">Cashier: {{ cashierName }}</div>
                    <div v-if="order?.token">
                        Token No.: <strong>{{ order.token }}</strong>
                    </div>
                    <div v-if="tableLabel">
                        Table No: <strong>{{ tableLabel }}</strong>
                    </div>
                </div>
                <div class="bill-right">
                    <div class="bill-type-badge"><strong>{{ orderTypeLabel }}</strong></div>
                    <div>Bill No.: {{ order?.order_serial_no }}</div>
                </div>
            </div>

            <div class="bill-line"></div>

            <div class="bill-row bill-head">
                <span class="b-no">No.</span>
                <span class="b-item">Item</span>
                <span class="b-qty">Qty.</span>
                <span class="b-price">Price</span>
                <span class="b-amt">Amount</span>
            </div>
            <div class="bill-line"></div>

            <div v-for="(item, idx) in normalizedItems" :key="idx" class="bill-item">
                <div class="bill-row">
                    <span class="b-no">{{ idx + 1 }}</span>
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
                <span>Total Qty: {{ totalQty }}</span>
                <span>Sub Total {{ subtotalLabel }}</span>
            </div>
            <div v-if="hasDiscount" class="bill-row">
                <span></span>
                <span>Discount {{ discountLabel }}</span>
            </div>
            <div v-if="hasTax" class="bill-row">
                <span></span>
                <span>Tax {{ taxLabel }}</span>
            </div>
            <div class="bill-row bill-grand">
                <span></span>
                <span>Grand Total {{ totalLabel }}</span>
            </div>

            <div v-if="paymentLabel" class="bill-row" style="margin-top:4px">
                <span>Payment: {{ paymentLabel }}</span>
            </div>

            <div class="bill-line"></div>
            <div class="bill-center bill-thanks">Thank You | Visit Again...!!!</div>
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
.customer-receipt-sheet {
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
    font-family: "Courier New", Courier, ui-monospace, monospace;
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
    font-size: 13px;
    font-weight: 800;
    text-transform: uppercase;
}
.bill-addr { font-size: 10px; }
.bill-line {
    border-top: 1px solid #000;
    margin: 5px 0;
}
.bill-row {
    display: flex;
    justify-content: space-between;
    gap: 4px;
}
.bill-grid {
    display: flex;
    justify-content: space-between;
    gap: 8px;
    margin-top: 4px;
}
.bill-right { text-align: right; }
.bill-type-badge {
    display: inline-block;
    font-size: 13px;
    font-weight: 900;
    letter-spacing: 1px;
    text-transform: uppercase;
    border: 2px solid #000;
    padding: 2px 8px;
    margin-bottom: 3px;
}
.bill-head { font-weight: 700; }
.b-no { width: 8%; flex-shrink: 0; }
.b-item { width: 40%; }
.b-qty { width: 12%; text-align: right; }
.b-price { width: 18%; text-align: right; }
.b-amt { width: 22%; text-align: right; }
.bill-item { margin: 3px 0; }
.bill-mod {
    margin-left: 8%;
    font-size: 10px;
}
.bill-grand {
    font-weight: 800;
    font-size: 12px;
    margin-top: 2px;
}
.bill-thanks {
    margin-top: 6px;
    font-weight: 700;
}

@media print {
    body.printing-receipt * {
        visibility: hidden !important;
    }
    body.printing-receipt .pos-receipt-print-root,
    body.printing-receipt .pos-receipt-print-root * {
        visibility: visible !important;
    }
    /* Full page width like KOT root, then center the slip */
    body.printing-receipt .pos-receipt-print-root {
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
    body.printing-receipt .pos-receipt-print-root .bill-sheet {
        display: inline-block !important;
        width: 72mm !important;
        max-width: 100% !important;
        margin: 0 auto !important;
        text-align: left;
        vertical-align: top;
    }
    @page {
        margin: 2mm;
        size: 80mm auto;
    }
}
</style>
