<template>
    <div id="receipt-modal" class="modal">
        <div class="modal-dialog max-w-[340px] rounded-none pos-receipt-print-root" id="print" :dir="displayMode">
            <div class="modal-header hidden-print">
                <button type="button" @click="reset" class="modal-close flex items-center justify-center gap-1.5 py-2 px-4 rounded bg-[#FB4E4E]">
                    <i class="lab lab-fill-back-circle text-base text-white"></i>
                    <span class="text-xs leading-5 capitalize text-white">{{ $t('button.close') }}</span>
                </button>
                <button type="button" @click="manualPrint" class="flex items-center justify-center gap-1.5 py-2 px-4 rounded bg-[#1AB759]">
                    <i class="lab lab-fill-printer text-base text-white"></i>
                    <span class="text-xs leading-5 capitalize text-white">{{ $t('button.print_invoice') }}</span>
                </button>
            </div>
            <div class="modal-body receipt-body">
                <div class="text-center pb-3.5 border-b border-dashed border-gray-400">
                    <img
                        v-if="restaurant.logo"
                        :src="restaurant.logo"
                        alt=""
                        class="receipt-logo mx-auto mb-2"
                    />
                    <h3 class="text-2xl font-bold mb-1">{{ restaurant.name }}</h3>
                    <h4 class="text-sm font-normal">{{ restaurant.address }}</h4>
                    <h5 v-if="restaurant.phone" class="text-sm font-normal">{{ $t('label.tel') }}:
                        {{ restaurant.country_code + restaurant.phone }}</h5>
                </div>

                <table class="w-full my-1.5">
                    <tbody>
                    <tr>
                        <td class="text-xs text-left py-0.5 text-heading">
                            {{ $t('label.invoice') || 'Invoice' }} #{{ order.order_serial_no }}
                        </td>
                    </tr>
                    <tr>
                        <td class="text-xs text-left py-0.5 text-heading">{{ $t('label.order') }}
                            #{{ order.order_serial_no }}
                        </td>
                    </tr>
                    <tr v-if="resolvedCashier">
                        <td class="text-xs text-left py-0.5 text-heading" colspan="2">
                            {{ $t('label.cashier') }}: {{ resolvedCashier }}
                        </td>
                    </tr>
                    <tr>
                        <td class="text-xs text-left py-0.5 text-heading">{{ order.order_date }}</td>
                        <td class="text-xs text-right py-0.5 text-heading">{{ order.order_time }}</td>
                    </tr>
                    </tbody>
                </table>

                <table class="w-full">
                    <thead class="border-t border-b border-dashed border-gray-400">
                    <tr>
                        <th scope="col" class="py-1 font-normal text-xs capitalize text-left text-heading w-8">
                            {{ $t('label.qty') }}
                        </th>
                        <th scope="col"
                            class="py-1 font-normal text-xs capitalize flex items-center justify-between text-heading">
                            <span>{{ $t('label.item_description') }}</span>
                            <span>{{ $t('label.price') }}</span>
                        </th>
                    </tr>
                    </thead>
                    <tbody class="border-b border-dashed border-gray-400">
                    <tr v-if="orderItems.length > 0" v-for="item in orderItems" :key="item">
                        <td class="text-left font-normal align-top py-1">
                            <p class="text-xs leading-5 text-heading">{{ item.quantity }}</p>
                        </td>
                        <td class="text-left font-normal align-top py-1">
                            <div class="flex items-center justify-between gap-2">
                                <h4 class="text-sm font-normal capitalize receipt-item-name">{{ item.item_name }}</h4>
                                <p class="text-xs leading-5 text-heading whitespace-nowrap">{{ item.total_currency_price }} </p>
                            </div>
                            <p v-if="Object.keys(item.item_variations).length !== 0"
                               class="text-xs leading-5 font-normal text-heading max-w-[200px]">
                                <span v-for="(variation, index) in item.item_variations">
                                    {{ variation.variation_name }}: {{ variation.name }}
                                    <span
                                        v-if="index + 1 < Object.keys(item.item_variations).length">, </span>
                                </span>
                            </p>
                            <p v-if="item.item_extras.length > 0"
                               class="text-xs leading-5 font-normal text-heading max-w-[200px]">
                                {{ $t('label.extras') }}:
                                <span v-for="(extra, index) in item.item_extras">
                                    {{ extra.name }}
                                    <span v-if="index + 1 < item.item_extras.length">, </span>
                                </span>
                            </p>
                            <p v-if="item.instruction" class="text-xs leading-5 font-normal text-heading max-w-[200px]">
                                {{ $t('label.instruction') }}: {{ item.instruction }}
                            </p>

                            <div class="flex items-center justify-between" v-if="item.tax_rate > 0">
                                <p class="text-xs leading-5 font-normal text-heading">
                                    {{ item.tax_name }} ({{ item.tax_currency_rate }} {{ item.tax_type }})</p>
                                <p class="text-xs leading-5 font-normal text-heading">
                                    {{ item.tax_currency_amount }}
                                </p>
                            </div>
                        </td>
                    </tr>
                    </tbody>
                </table>

                <div class="py-2 pl-7">
                    <table class="w-full">
                        <tbody>
                        <tr>
                            <td class="text-xs text-left py-0.5 uppercase text-heading">
                                {{ $t('label.subtotal') }}:
                            </td>
                            <td class="text-xs text-right py-0.5 text-heading">
                                {{ order.subtotal_currency_price }}
                            </td>
                        </tr>
                        <tr>
                            <td class="text-xs text-left py-0.5 uppercase text-heading">
                                {{ $t('label.total_tax') }}:
                            </td>
                            <td class="text-xs text-right py-0.5 text-heading">
                                {{ order.total_tax_currency_price }}
                            </td>
                        </tr>
                        <tr>
                            <td class="text-xs text-left py-0.5 uppercase text-heading">
                                {{ $t('label.discount') }}:
                            </td>
                            <td class="text-xs text-right py-0.5 text-heading">
                                {{ order.discount_currency_price }}
                            </td>
                        </tr>
                        <tr>
                            <td class="text-xs text-left py-0.5 font-bold uppercase text-heading">
                                {{ $t('label.total') }}:
                            </td>
                            <td class="text-xs text-right py-0.5 font-bold text-heading">
                                {{ order.total_currency_price }}
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
                <div class="text-xs py-2 border-t border-b border-dashed border-gray-400 text-heading">
                    <table class="w-full">
                        <tbody>
                        <tr>
                            <td class="pt-1 pb-1 pr-1 align-top text-start">
                                {{ $t('label.payment_type') }}:
                                {{ enums.posPaymentMethodEnumArray[posDetail.payment_method] }}
                            </td>
                            <td class="pt-1 pb-1 text-end" v-if="order.cash_back_amount > 0">
                                <div>{{ $t('label.cash') }}: {{ posDetail.received_currency_amount }}</div>
                                <span>{{ $t('label.change') }} : {{ order.cash_back_currency_amount }}</span>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
                <h4 v-if="order.token"
                    class="py-2 capitalize text-xl font-bold text-center border-b border-dashed border-gray-400">
                    {{ $t('label.token') }} #{{ order.token }}
                </h4>
                <div class="text-center pt-2 pb-4">
                    <p class="text-[11px] leading-[14px] capitalize text-heading">
                        {{ $t('message.thank_you') }}
                    </p>
                    <p class="text-[11px] leading-[14px] capitalize text-heading">
                        {{ $t('message.please_come_again') }}
                    </p>
                </div>
                <div class="flex flex-col items-end">
                    <h5 class="text-[8px] font-normal text-left w-[46px] leading-[10px]">
                        {{ $t('label.powered_by') }}
                    </h5>
                    <h6 class="text-xs font-normal leading-4">{{ setting.company_name }}</h6>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import {useModal} from "../../../../composables/modal.js";
import {useCommonStore} from "../../../../stores/common.js";
import {usePosOrderStore} from "../../../../stores/posOrder.js";
import {useFrontendSettingStore} from "../../../../stores/frontendSetting.js";
import {useAuthStore} from "../../../../stores/auth.js";
import posPaymentMethodEnum from "../../../../enums/modules/posPaymentMethodEnum.js";
import DisplayModeEnum from "../../../../enums/modules/displayModeEnum.js";
import {printReceipt} from "../../../../services/printService.js";
import alertService from "../../../../services/alertService.js";

export default {
    name: "PosReceiptComponent",
    props: {
        order: Object,
        cashierName: {
            type: String,
            default: '',
        },
    },
    setup() {
        const {closeModal}         = useModal();
        const commonStore          = useCommonStore();
        const posOrderStore        = usePosOrderStore();
        const frontendSettingStore = useFrontendSettingStore();
        const authStore            = useAuthStore();
        return {
            closeModal,
            commonStore,
            posOrderStore,
            frontendSettingStore,
            authStore,
        }
    },
    data() {
        return {
            enums: {
                posPaymentMethodEnumArray: {
                    [posPaymentMethodEnum.CASH]: this.$t("label.cash"),
                    [posPaymentMethodEnum.CARD]: this.$t("label.card"),
                    [posPaymentMethodEnum.MOBILE_BANKING]: this.$t("label.mfs"),
                    [posPaymentMethodEnum.OTHER]: this.$t("label.other")
                }
            }
        }
    },
    computed: {
        setting: function () {
            return this.frontendSettingStore.lists;
        },
        orderItems: function () {
            return this.posOrderStore.orderItems;
        },
        restaurant: function () {
            return this.posOrderStore.restaurant;
        },
        posDetail: function () {
            return this.posOrderStore.posDetail;
        },
        displayMode: function () {
            return this.commonStore.display_mode === DisplayModeEnum.LTR ? 'ltr' : 'rtl';
        },
        resolvedCashier() {
            return this.cashierName || this.authStore.info?.name || '';
        }
    },
    methods: {
        reset: function () {
            this.closeModal('receipt-modal');
        },
        async manualPrint() {
            try {
                await printReceipt();
            } catch (err) {
                alertService.error(this.$t('message.receipt_print_failed'));
            }
        },
        /** Called by PaymentComponent after checkout for auto-print. */
        async autoPrint() {
            return printReceipt();
        },
    },
}
</script>

<style>
.receipt-logo {
    max-height: 48px;
    max-width: 120px;
    object-fit: contain;
}
.receipt-item-name {
    word-break: break-word;
}

@media print {
    body.printing-receipt * {
        visibility: hidden !important;
    }
    body.printing-receipt .pos-receipt-print-root,
    body.printing-receipt .pos-receipt-print-root * {
        visibility: visible !important;
    }
    body.printing-receipt .pos-receipt-print-root .hidden-print {
        display: none !important;
        visibility: hidden !important;
    }
    body.printing-receipt .pos-receipt-print-root {
        display: block !important;
        position: absolute;
        left: 0;
        top: 0;
        width: 72mm;
        max-width: 100%;
        margin: 0;
        box-shadow: none !important;
        border: none !important;
        background: #fff !important;
    }
    body.printing-receipt .pos-receipt-print-root .receipt-body {
        padding: 0 !important;
    }
}
</style>
