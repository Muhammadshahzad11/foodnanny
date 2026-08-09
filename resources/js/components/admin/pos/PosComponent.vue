<template>
    <LoadingComponent :props="loading"/>
    <div class="w-full md:w-[calc(100%-366px)]">
        <PrinterConnectionBar
            class="mb-4"
            endpoint="admin/pos/printers"
            :title="$t('label.connected_printers') + ' (KOT & POS)'"
            @loaded="onPrintersLoaded"
        />
        <form @submit.prevent="search"
              class="flex items-center w-full h-10 mb-4 rounded-lg overflow-hidden border border-[#EFF0F6] bg-white">
            <input v-model="props.search.name" type="text" :placeholder="$t('label.search_by_menu_item')"
                   class="w-full px-4 placeholder:text-xs placeholder:text-[#A0A3BD]">
            <button type="submit" class="flex-shrink-0 w-10 h-full text-center bg-primary">
                <i class="lab-line-search text-xl text-white"></i>
            </button>
        </form>

        <div class="swiper pos-menu-swiper mb-6" v-if="categories.length > 1">
            <Swiper :dir="displayMode" :speed="1000" slidesPerView="auto" :spaceBetween="16" class="menu-slides">
                <SwiperSlide class="!w-fit" v-for="(category, index) in categories" :key="category"
                             :class="category.id === props.search.item_category_id || (category.id === 0 && props.search.item_category_id === '') ? 'pos-group' : ''">
                    <router-link v-if="index === 0" to="#" @click.prevent="allCategory"
                                 class="w-28 flex flex-col items-center text-center gap-4 py-4 px-3 rounded-lg border-b-2 border-transparent transition hover:bg-primary/10 hover:border-primary bg-white">
                        <img class="h-7 drop-shadow-category" :src="category.thumb" alt="all-category">
                        <h3 class="text-xs font-medium text-center w-full whitespace-nowrap overflow-hidden text-ellipsis transition-all">
                            {{ category.name }}</h3>
                    </router-link>
                    <router-link v-else to="#" @click.prevent="setCategory(category.id)"
                                 class="w-28 flex flex-col items-center text-center gap-4 py-4 px-3 rounded-lg border-b-2 border-transparent transition hover:bg-primary/10 hover:border-primary bg-white">
                        <img class="h-7 drop-shadow-category" :src="category.thumb" alt="category">
                        <h3 class="text-xs font-medium text-center w-full whitespace-nowrap overflow-hidden text-ellipsis transition-all">
                            {{ category.name }}</h3>
                    </router-link>
                </SwiperSlide>
            </Swiper>
        </div>

        <ItemComponent v-if="items.length > 0" :offer="offer" :items="items"/>

        <div class="mt-12" v-else>
            <div class="max-w-[250px] mx-auto">
                <img class="w-full mb-8" :src="setting.data_not_found" alt="image_order_not_found">
            </div>
            <span class="w-full mb-4 text-center text-black">{{ $t('message.no_items_found') }}</span>
        </div>
    </div>

    <div id="pos-cart"
         :class="posOpen ? 'max-md:translate-x-0' : 'max-md:ltr:translate-x-full max-md:rtl:-translate-x-full'"
         class="w-full h-dvh md:w-[350px] md:h-[calc(100dvh-96px)] md:rounded-xl fixed z-40 md:z-20 top-0 ltr:right-0 rtl:left-0 md:top-20 md:ltr:right-4 md:rtl:left-4 thin-scrolling bg-white">
        <div class="p-4">
            <div class="md:hidden ltr:text-right rtl:text-left mb-3">
                <button @click.prevent="posOpen = false">
                    <i class="lab lab-line-circle-cross text-danger text-xl"></i>
                </button>
            </div>

            <div class="flex items-center w-full gap-4 mb-3">
                <input v-on:keypress="onlyNumber($event)" class="db-field-control" type="number" id="token"
                       v-model="checkoutProps.form.token" :placeholder="$t('label.token_no')"/>
                <div v-if="Object.keys(mainOffer).length > 0"
                     class="w-full h-10 rounded-md ps-3 p-[1px] border border-[#E5E7EB] flex items-center justify-between gap-3">
                    <span class="text-[#6E7191] text-sm font-normal flex gap-1 items-center">{{ $t('label.offer') }}
                        <div v-bind="$attrs" type="button" class="group relative">
                            <i class="lab-line-info-circle text-base text-[#6E7191]"></i>
                            <span class="inline-block absolute min-w-[200px] max-w-[300px] w-auto z-[999] -top-1 left-1/2 -translate-x-1/2 translate-y-10 first-letter:capitalize text-xs rounded-md py-1 px-2 bg-gray-800 text-white before:absolute before:w-2 before:h-2 before:bg-gray-800 before:rotate-45 before:left-1/2 before:-top-1 before:-translate-x-1/2 group-hover:opacity-100 group-hover:visible group-hover:-top-2 opacity-0 invisible transition-all duration-300">
                            {{ $t('message.this_is_an_online_offer') }}
                            </span>
                        </div>
                    </span>
                    <nav class="w-fit h-full flex items-center justify-center p-0.5 rounded-md bg-[#FFF8F2]">
                        <button @click.prevent="offerApply(enums.switchEnum.OFF)"
                                :class="enums.switchEnum.OFF === offerStatus ? 'text-white bg-[#6E7191]' : 'text-[#6E7191]'"
                                class="text-sm font-medium uppercase h-full px-2 rounded">
                            {{ $t('label.off') }}
                        </button>
                        <button @click.prevent="offerApply(enums.switchEnum.ON)"
                                :class="enums.switchEnum.ON === offerStatus ? 'text-white bg-primary' : 'text-[#6E7191]'"
                                class="text-sm font-medium uppercase h-full px-2 rounded">
                            {{ $t('label.on') }}
                        </button>
                    </nav>
                </div>
            </div>

            <div class="mb-3">
                <p class="text-xs font-semibold uppercase tracking-wide text-[#6E7191] mb-2">{{ $t('label.order_type') }}</p>
                <div class="grid grid-cols-3 gap-1.5">
                    <button
                        type="button"
                        class="rounded-xl py-2.5 px-1 text-xs font-bold border-2 transition active:scale-[0.98]"
                        :class="Number(checkoutProps.form.order_type) === enums.orderTypeEnum.TAKEAWAY
                            ? 'bg-primary border-primary text-white shadow-sm'
                            : 'bg-white border-[#EFF0F6] text-heading hover:border-primary/40'"
                        @click.prevent="setPosService(enums.orderTypeEnum.TAKEAWAY)"
                    >
                        {{ $t('label.takeaway') }}
                    </button>
                    <button
                        type="button"
                        class="rounded-xl py-2.5 px-1 text-xs font-bold border-2 transition active:scale-[0.98]"
                        :class="Number(checkoutProps.form.order_type) === enums.orderTypeEnum.DINING_TABLE
                            ? 'bg-primary border-primary text-white shadow-sm'
                            : 'bg-white border-[#EFF0F6] text-heading hover:border-primary/40'"
                        @click.prevent="setPosService(enums.orderTypeEnum.DINING_TABLE)"
                    >
                        {{ $t('label.dine_in') }}
                    </button>
                    <button
                        type="button"
                        class="rounded-xl py-2.5 px-1 text-xs font-bold border-2 transition active:scale-[0.98]"
                        :class="Number(checkoutProps.form.order_type) === enums.orderTypeEnum.DELIVERY
                            ? 'bg-primary border-primary text-white shadow-sm'
                            : 'bg-white border-[#EFF0F6] text-heading hover:border-primary/40'"
                        @click.prevent="setPosService(enums.orderTypeEnum.DELIVERY)"
                    >
                        {{ $t('label.delivery') }}
                    </button>
                </div>
            </div>

            <div v-if="Number(checkoutProps.form.order_type) === enums.orderTypeEnum.DINING_TABLE" class="mb-3">
                <label class="text-xs font-medium text-[#6E7191] mb-1.5 block">{{ $t('label.table') }}</label>
                <select v-model="checkoutProps.form.table_id" class="db-field-control w-full">
                    <option value="">{{ $t('label.select_table') || 'Select table' }}</option>
                    <option v-for="t in diningTables" :key="t.id" :value="t.id">
                        {{ t.table_number }} · {{ t.name }}
                    </option>
                </select>
            </div>

            <div class="mb-3">
                <input
                    v-model="checkoutProps.form.order_note"
                    type="text"
                    class="db-field-control w-full"
                    :placeholder="$t('label.order_note')"
                />
            </div>

            <div class="mb-3">
                <div class="w-full h-10 rounded-md ps-3 p-[1px] border border-[#E5E7EB] flex items-center justify-between gap-3">
                    <span class="text-[#6E7191] text-sm font-normal flex gap-1 items-center min-w-0">
                        {{ $t('label.print_preview') }}
                        <div class="group relative shrink-0">
                            <i class="lab-line-info-circle text-base text-[#6E7191]"></i>
                            <span class="inline-block absolute min-w-[220px] max-w-[280px] w-auto z-[999] -top-1 left-0 translate-y-10 text-xs rounded-md py-1.5 px-2 bg-gray-800 text-white before:absolute before:w-2 before:h-2 before:bg-gray-800 before:rotate-45 before:left-4 before:-top-1 group-hover:opacity-100 group-hover:visible group-hover:-top-2 opacity-0 invisible transition-all duration-300">
                                {{ $t('message.print_preview_help') }}
                            </span>
                        </div>
                    </span>
                    <nav class="w-fit h-full flex items-center justify-center p-0.5 rounded-md bg-[#FFF8F2] shrink-0">
                        <button
                            type="button"
                            @click.prevent="setPrintPreview(false)"
                            :class="!printPreviewOn ? 'text-white bg-[#6E7191]' : 'text-[#6E7191]'"
                            class="text-sm font-medium uppercase h-full px-2 rounded"
                        >
                            {{ $t('label.off') }}
                        </button>
                        <button
                            type="button"
                            @click.prevent="setPrintPreview(true)"
                            :class="printPreviewOn ? 'text-white bg-primary' : 'text-[#6E7191]'"
                            class="text-sm font-medium uppercase h-full px-2 rounded"
                        >
                            {{ $t('label.on') }}
                        </button>
                    </nav>
                </div>
                <p v-if="hasDirectNetworkPrinter" class="mt-1.5 text-[11px] leading-4 text-amber-700">
                    {{ $t('message.local_print_agent_pos_hint') }}
                    <a class="underline font-semibold" href="/local-print-agent/" target="_blank" rel="noopener">
                        {{ $t('label.setup_local_print_agent') }}
                    </a>
                </p>
                <p v-else class="mt-1.5 text-[11px] leading-4 text-sky-800">
                    {{ $t('message.browser_popup_pos_hint') }}
                </p>
            </div>
        </div>

        <SimplePrintSetupModal v-model="showSimplePrintSetup" @ready="onSimplePrintReady"/>

        <table class="w-full">
            <thead class="bg-primary/10">
            <tr class="h-9">
                <th class="capitalize text-xs font-normal ltr:text-left rtl:text-right pl-3 text-heading"></th>
                <th class="capitalize text-xs font-normal ltr:text-left rtl:text-right px-3 text-heading">
                    {{ $t('label.item') }}
                </th>
                <th class="capitalize text-xs font-normal ltr:text-left rtl:text-right px-3 text-heading">
                    {{ $t('label.qty') }}
                </th>
                <th class="capitalize text-xs font-normal ltr:text-left rtl:text-right px-3 text-heading">
                    {{ $t('label.price') }}
                </th>
            </tr>
            </thead>
            <tbody>
            <tr v-for="(cart, index) in carts" :key="index">
                <td class="ltr:pl-3 rtl:pr-3 py-3 ltr:last:pr-3 rtl:last:pl-3 align-top border-b border-[#EFF0F6]">
                    <button @click.prevent="deleteCartItem(index)">
                        <i class="lab lab-line-trash-2 text-danger font-semibold"></i>
                    </button>
                </td>
                <td class="ltr:pl-3 rtl:pr-3 py-3 ltr:last:pr-3 rtl:last:pl-3 align-top border-b border-[#EFF0F6]">
                    <h3 class="capitalize text-xs font-semibold">{{ cart.name }}</h3>
                    <p v-if="Object.keys(cart.item_variations.variations).length !== 0">
                        <span class="block" v-for="(variation, variationName, index) in cart.item_variations.names">
                            <span class="capitalize text-[10px] leading-4 font-rubik text-heading">
                                {{ variationName }}:&nbsp;
                            </span>
                            <span class="capitalize text-[10px] leading-4 font-rubik">
                                {{ variation }}
                                <span v-if="index + 1 < cart.item_variations.names">, &nbsp;</span>
                            </span>
                        </span>
                    </p>
                    <ul v-if="cart.item_extras.extras.length > 0 || cart.instruction !== ''">
                        <li v-if="cart.item_extras.extras.length > 0" class="leading-4">
                            <span class="capitalize text-[10px] leading-4 text-heading">
                                {{ $t('label.extras') }}:&nbsp;
                            </span>
                            <span class="capitalize text-[10px] leading-4">
                                 {{ cart.item_extras.names.map(extra => extra).join(', ') }}
                            </span>
                        </li>
                        <li class="leading-4" v-if="cart.instruction">
                            <span class="capitalize text-[10px] leading-4 text-heading"> {{
                                    $t('label.instruction')
                                }}:</span>
                            <span class="capitalize text-[10px] leading-4">{{ cart.instruction }}</span>
                        </li>
                    </ul>
                </td>
                <td class="ltr:pl-3 rtl:pr-3 py-3 ltr:last:pr-3 rtl:last:pl-3 align-top border-b border-[#EFF0F6]">
                    <div class="flex items-center">
                        <button @click.prevent="cartQuantityDecrement(index)"
                                :class="cart.quantity === 1 ? 'lab-line-trash' : 'lab-line-minus'"
                                class="text-[10px] w-4 h-4 leading-3 text-center rounded-full border transition text-primary border-primary hover:bg-primary hover:text-white"></button>
                        <input v-on:keypress="onlyNumber($event)" v-on:keyup="cartQuantityUp(index, $event)"
                               type="number" :value="cart.quantity"
                               class="flex-shrink-0 text-center w-6 text-xs font-semibold text-heading"
                               :id="'pos-quantity-value-' +index">
                        <button @click.prevent="cartQuantityIncrement(index)"
                                class="lab-line-plus text-[10px] w-4 h-4 leading-3 text-center rounded-full border transition text-primary border-primary hover:bg-primary hover:text-white"></button>
                    </div>
                </td>
                <td class="ltr:pl-3 rtl:pr-3 py-3 ltr:last:pr-3 rtl:last:pl-3 align-top border-b border-[#EFF0F6] text-xs text-heading">
                    {{
                        currencyFormat(cart.total, setting.site_digit_after_decimal_point, setting.site_default_currency_symbol, setting.site_currency_position)
                    }}
                </td>
            </tr>
            </tbody>
        </table>

        <div class="p-4">
            <div v-if="carts.length > 0" class="flex h-[38px]">
                <div class="db-field-down-arrow">
                    <select v-model="discountType"
                            class="w-[120px] h-full text-sm ltr:rounded-tl ltr:rounded-bl rtl:rounded-tr rtl:rounded-br appearance-none border px-3 cursor-pointer text-heading border-[#EFF0F6] flex-shrink-0 whitespace-nowrap overflow-hidden text-ellipsis">
                        <option :value="enums.discountTypeEnum.PERCENTAGE">{{ $t("label.percentage") }}</option>
                        <option :value="enums.discountTypeEnum.FIXED">{{ $t("label.fixed") }}</option>
                    </select>
                </div>
                <input @keyup.enter="applyDiscount" v-on:keypress="floatNumber($event)" v-model="discount" type="text"
                       :placeholder="$t('label.add_discount')"
                       class="w-full h-full border-t border-b px-3 placeholder:text-sm border-[#EFF0F6]">
                <button @click.prevent="applyDiscount" type="submit"
                        class="flex-shrink-0 w-16 h-full text-sm font-medium capitalize ltr:rounded-tr ltr:rounded-br rtl:rounded-tl rtl:rounded-bl text-white bg-primary">
                    {{ $t('button.apply') }}
                </button>
            </div>
            <small class="db-field-alert" v-if="discountErrorMessage">{{ discountErrorMessage }}</small>
            <ul class="flex flex-col gap-1.5 mb-4 mt-4">
                <li class="flex items-center justify-between">
                    <span class="text-sm capitalize leading-6 text-[#2E2F38]"> {{ $t("label.sub_total") }}</span>
                    <span class="text-sm capitalize leading-6 text-[#2E2F38]">
                        {{
                            currencyFormat(subtotal, setting.site_digit_after_decimal_point, setting.site_default_currency_symbol, setting.site_currency_position)
                        }}
                    </span>
                </li>
                <li class="flex items-center justify-between">
                    <span class="text-sm capitalize leading-6 text-[#2E2F38]">{{ $t("label.tax") }}</span>
                    <span class="text-sm capitalize leading-6 text-[#2E2F38]">
                        {{
                            currencyFormat(tax, setting.site_digit_after_decimal_point, setting.site_default_currency_symbol, setting.site_currency_position)
                        }}
                    </span>
                </li>
                <li v-if="discountAmount > 0" class="flex items-center justify-between">
                    <span class="text-sm capitalize leading-6 text-[#2E2F38]">{{ $t("label.discount") }}</span>
                    <span class="text-sm capitalize leading-6 text-[#2E2F38]">
                        - {{
                            currencyFormat(discountAmount, setting.site_digit_after_decimal_point, setting.site_default_currency_symbol, setting.site_currency_position)
                        }}
                    </span>
                </li>
                <li class="flex items-center justify-between">
                        <span class="text-sm font-bold capitalize leading-6 text-[#2E2F38]">{{
                                $t("label.total")
                            }}</span>
                    <span class="text-sm font-medium capitalize leading-6 text-[#2E2F38]">
                        {{
                            currencyFormat(total, setting.site_digit_after_decimal_point, setting.site_default_currency_symbol, setting.site_currency_position)
                        }}
                    </span>
                </li>
            </ul>
            <div v-if="carts.length > 0" class="flex items-center justify-center gap-6">
                <button @click.prevent="resetCart"
                        class="capitalize text-sm font-medium leading-6 w-full text-center rounded-3xl py-2 text-white bg-[#FB4E4E]">
                    {{ $t('button.cancel') }}
                </button>
                <button @click.prevent="orderSubmit"
                        class="capitalize text-sm font-medium leading-6 w-full text-center rounded-3xl py-2 text-white bg-[#1AB759]">
                    {{ $t('button.order') }}
                </button>
            </div>
        </div>
    </div>

    <button @click="posOpen = true"
            class="fixed md:hidden bottom-0 left-0 z-10 w-full h-14 py-4 text-center flex items-center justify-center shadow-xl-top gap-3 bg-primary">
        <i class="lab lab-bag-2 lab-font-size-13 text-white"></i>
        <span class="text-base font-medium font-client text-white">
            {{ carts.length }} {{
                $t('label.items')
            }} - {{
                currencyFormat(total, setting.site_digit_after_decimal_point, setting.site_default_currency_symbol, setting.site_currency_position)
            }}
        </span>
    </button>

    <PaymentComponent ref="paymentRef" :method="submitReset" :props="checkoutProps"/>
</template>

<script>
import LoadingComponent from "../../common/LoadingComponent.vue";
import PrinterConnectionBar from "../components/PrinterConnectionBar.vue";
import statusEnum from "../../../enums/modules/statusEnum.js";
import {useItemStore} from "../../../stores/item.js";
import {usePosCategoryStore} from "../../../stores/posCategory.js";
import {Swiper, SwiperSlide} from 'swiper/vue';
import ItemComponent from "./ItemComponent.vue";
import DisplayModeEnum from "../../../enums/modules/displayModeEnum.js";
import {useCommonStore} from "../../../stores/common.js";
import {useDefaultAccessStore} from "../../../stores/defaultAccess.js";
import {usePosOfferStore} from "../../../stores/posOffer.js";
import {useFrontendSettingStore} from "../../../stores/frontendSetting.js";
import appService from "../../../services/appService.js";
import {useModal} from "../../../composables/modal.js";
import switchEnum from "../../../enums/modules/switchEnum.js";
import {usePosCartStore} from "../../../stores/posCart.js";
import discountTypeEnum from "../../../enums/modules/discountTypeEnum.js";
import _ from "lodash";
import PaymentComponent from "./PaymentComponent.vue";
import SimplePrintSetupModal from "./SimplePrintSetupModal.vue";
import posPaymentMethodEnum from "../../../enums/modules/posPaymentMethodEnum.js";
import orderTypeEnum from "../../../enums/modules/orderTypeEnum.js";
import {usePosOrderStore} from "../../../stores/posOrder.js";
import alertService from "../../../services/alertService.js";
import {
    isPrintPreviewOn,
    isSilentPrintReady,
    setPrintPreviewOn,
    setSilentPrintReady,
    syncSilentPrintFromUrl,
} from "../../../services/printPreference.js";

export default {
    name: "PosComponent",
    components: {
        PaymentComponent,
        LoadingComponent,
        PrinterConnectionBar,
        ItemComponent,
        Swiper,
        SwiperSlide,
        SimplePrintSetupModal,
    },
    setup() {
        const {openModal, closeModal} = useModal();
        const itemStore               = useItemStore();
        const commonStore             = useCommonStore();
        const posCartStore            = usePosCartStore();
        const posOfferStore           = usePosOfferStore();
        const posCategoryStore        = usePosCategoryStore();
        const defaultAccessStore      = useDefaultAccessStore();
        const frontendSettingStore    = useFrontendSettingStore();
        const posOrderStore           = usePosOrderStore();

        return {
            openModal,
            closeModal,
            itemStore,
            commonStore,
            posCartStore,
            posOfferStore,
            posCategoryStore,
            defaultAccessStore,
            frontendSettingStore,
            posOrderStore,
        }
    },
    data() {
        return {
            loading: {
                isActive: false,
            },
            posOpen: false,
            printPreviewOn: false,
            silentPrintReady: false,
            showSimplePrintSetup: false,
            hasDirectNetworkPrinter: false,
            offerStatus: switchEnum.ON,
            mainOffer: {},
            offer: {},
            discount: null,
            diningTables: [],
            errors: {},
            enums: {
                switchEnum: switchEnum,
                discountTypeEnum: discountTypeEnum,
                orderTypeEnum: orderTypeEnum,
            },
            checkoutProps: {
                form: {
                    subtotal: 0,
                    token: "",
                    discount: 0,
                    tax: 0,
                    total: 0,
                    items: [],
                    payment_method: posPaymentMethodEnum.CASH,
                    payment_note: null,
                    received_amount: null,
                    order_type: orderTypeEnum.TAKEAWAY,
                    table_id: '',
                    order_note: '',
                }
            },
            props: {
                search: {
                    paginate: 0,
                    order_column: "id",
                    order_type: "asc",
                    name: "",
                    item_category_id: "",
                    status: statusEnum.ACTIVE
                },
            },
            categoryProps: {
                paginate: 0,
                order_column: "id",
                order_type: "asc",
                status: statusEnum.ACTIVE
            },
            discountType: discountTypeEnum.PERCENTAGE,
            discountErrorMessage: "",
        }
    },
    computed: {
        displayMode: function () {
            return this.commonStore.display_mode === DisplayModeEnum.LTR ? 'ltr' : 'rtl';
        },
        setting: function () {
            return this.frontendSettingStore.lists;
        },
        categories: function () {
            return this.posCategoryStore.lists;
        },
        items: function () {
            return this.itemStore.lists;
        },
        carts: function () {
            return this.posCartStore.lists;
        },
        subtotal: function () {
            return this.posCartStore.subtotal;
        },
        tax: function () {
            return this.posCartStore.tax;
        },
        discountAmount: function () {
            return this.posCartStore.discount;
        },
        total: function () {
            return this.posCartStore.total;
        }
    },
    async mounted() {
        try {
            syncSilentPrintFromUrl();
            this.printPreviewOn = isPrintPreviewOn();
            this.silentPrintReady = isSilentPrintReady();
            this._onPrintPreviewChanged = (e) => {
                this.printPreviewOn = !!(e?.detail?.on ?? isPrintPreviewOn());
                this.silentPrintReady = isSilentPrintReady();
            };
            window.addEventListener('fn-print-preview-changed', this._onPrintPreviewChanged);

            this.closeSidebar();
            this.itemCategories();
            this.itemList();
            this.loadDiningTables();

            this.loading.isActive = true;
            await this.posOfferStore.fetch().then(res => {
                this.mainOffer        = res.data.data;
                this.offer            = this.mainOffer;
                this.loading.isActive = false;
            }).catch(err => {
                this.offerStatus      = switchEnum.OFF;
                this.loading.isActive = false;
            })
        } catch (err) {
            this.loading.isActive = false;
        }
    },
    beforeUnmount() {
        if (this._onPrintPreviewChanged) {
            window.removeEventListener('fn-print-preview-changed', this._onPrintPreviewChanged);
        }
    },
    methods: {
        setPrintPreview(on) {
            setPrintPreviewOn(!!on);
            this.printPreviewOn = !!on;
            this.silentPrintReady = isSilentPrintReady();
            // Network Direct Print uses Local Print Agent — don't push Silent Print setup
            if (!on && !this.silentPrintReady && !this.hasDirectNetworkPrinter) {
                this.openSimplePrintSetup();
            }
        },
        onPrintersLoaded(printers) {
            const list = Array.isArray(printers) ? printers : [];
            this.hasDirectNetworkPrinter = list.some((p) =>
                Number(p.printing_choice) === 10 && !!(p.printer_ip || '').trim()
            );
        },
        openSimplePrintSetup() {
            this.showSimplePrintSetup = true;
        },
        onSimplePrintReady() {
            this.silentPrintReady = isSilentPrintReady();
            this.printPreviewOn = false;
        },
        resetSilentPrint() {
            setSilentPrintReady(false);
            this.silentPrintReady = false;
            setPrintPreviewOn(false);
            this.printPreviewOn = false;
            alertService.success(this.$t('message.direct_print_reset'));
        },
        onlyNumber: function (e) {
            return appService.onlyNumber(e);
        },
        floatNumber: function (e) {
            return appService.floatNumber(e);
        },
        currencyFormat: function (amount, decimal, currency, position) {
            return appService.currencyFormat(amount, decimal, currency, position);
        },
        offerApply: function (status) {
            this.offerStatus = status;
            if (status === switchEnum.ON) {
                this.offer = this.mainOffer;
            } else if (status === switchEnum.OFF) {
                this.offer = {};
            }
        },
        closeSidebar: function () {
            this.commonStore.update({top_sidebar: false});
        },
        search: function () {
            this.itemList();
        },
        allCategory: function () {
            this.props.search.name             = "";
            this.props.search.item_category_id = "";
            this.itemList();
        },
        itemCategories: function (page = 1) {
            this.loading.isActive  = true;
            this.props.search.page = page;
            this.posCategoryStore.fetch(this.categoryProps).then((res) => {
                this.loading.isActive = false;
            }).catch((err) => {
                this.loading.isActive = false;
            });
        },
        itemList: function () {
            this.loading.isActive = true;
            this.itemStore.fetch(this.props.search).then((res) => {
                this.loading.isActive = false;
            }).catch((err) => {
                this.loading.isActive = false;
            });
        },
        setCategory: function (id) {
            this.props.search.item_category_id = id;
            this.itemList();
        },
        cartQuantityUp: function (id, e) {
            if (e.target.value > 0) {
                this.posCartStore.quantity({id: id, status: e.target.value})
            } else {
                e.target.value = 1;
                this.posCartStore.quantity({id: id, status: 1});
            }
        },
        cartQuantityIncrement: function (id) {
            this.posCartStore.quantity({id: id, status: "increment"})
        },
        cartQuantityDecrement: function (id) {
            this.posCartStore.quantity({id: id, status: "decrement"});
        },
        deleteCartItem: function (id) {
            this.posCartStore.deleteCartItem({id: id, status: "decrement"});
        },
        applyDiscount: async function () {
            this.discountErrorMessage = "";
            if (this.discountType === discountTypeEnum.FIXED) {
                if (this.subtotal < this.discount) {
                    this.discountErrorMessage = this.$t('message.discount_fixed_error_message');
                } else {
                    this.checkoutProps.form.discount = parseFloat(+this.discount).toFixed(this.setting.site_digit_after_decimal_point);
                    await this.posCartStore.callDiscount(this.checkoutProps.form.discount);
                }
            } else {
                if (this.discount > 100) {
                    this.discountErrorMessage = this.$t('message.discount_error_message');
                } else {
                    this.checkoutProps.form.discount = parseFloat((this.subtotal / 100) * this.discount).toFixed(this.setting.site_digit_after_decimal_point);
                    await this.posCartStore.callDiscount(this.checkoutProps.form.discount);
                }
            }
            this.discount = null;
        },
        resetCart: function () {
            this.posCartStore.resetCart();
        },
        setPosService(type) {
            this.checkoutProps.form.order_type = type;
            if (Number(type) !== orderTypeEnum.DINING_TABLE) {
                this.checkoutProps.form.table_id = '';
            } else if (!this.diningTables.length) {
                this.loadDiningTables();
            }
        },
        async loadDiningTables() {
            try {
                const res = await this.posOrderStore.fetchTables();
                this.diningTables = res.data.data || [];
            } catch (e) {
                this.diningTables = [];
            }
        },
        orderSubmit: function () {
            if (Number(this.checkoutProps.form.order_type) === orderTypeEnum.DINING_TABLE
                && !this.checkoutProps.form.table_id) {
                alertService.error(this.$t('message.table_required_for_dine_in') || 'Please select a table for dine-in.');
                return;
            }

            this.checkoutProps.form.subtotal = this.subtotal;
            this.checkoutProps.form.tax      = this.tax;
            this.checkoutProps.form.total    = this.total;
            this.checkoutProps.form.items    = [];

            _.forEach(this.carts, (item, index) => {
                let item_variations = [];
                if (Object.keys(item.item_variations.variations).length > 0) {
                    _.forEach(item.item_variations.variations, (value, index) => {
                        item_variations.push({
                            "id": value,
                            "item_id": item.item_id,
                            "item_attribute_id": index,
                        });
                    });
                }

                if (Object.keys(item.item_variations.names).length > 0) {
                    let i = 0;
                    _.forEach(item.item_variations.names, (value, index) => {
                        item_variations[i].variation_name = index;
                        item_variations[i].name           = value;
                        i++;
                    });
                }

                let item_extras = [];
                if (item.item_extras.extras.length) {
                    _.forEach(item.item_extras.extras, (value) => {
                        item_extras.push({
                            id: value,
                            item_id: item.item_id,
                        });
                    });
                }

                if (item.item_extras.names.length) {
                    let i = 0;
                    _.forEach(item.item_extras.names, (value) => {
                        item_extras[i].name = value;
                        i++;
                    });
                }

                this.checkoutProps.form.items.push({
                    item_id: item.item_id,
                    item_price: item.convert_price,
                    instruction: item.instruction,
                    quantity: item.quantity,
                    discount: item.discount,
                    total_price: item.total,
                    item_variation_total: item.item_variation_total,
                    item_extra_total: item.item_extra_total,
                    item_variations: item_variations,
                    item_extras: item_extras,
                    tax_name: item.tax_name,
                    tax_rate: item.tax_rate,
                    tax_type: item.tax_type,
                    tax_amount: item.tax_amount
                });
            });

            this.checkoutProps.form.items = JSON.stringify(this.checkoutProps.form.items);

            this.openModal('order-payment-modal');
            this.$nextTick(() => {
                this.$refs.paymentRef?.prefillCashAmount?.();
            });
        },
        submitReset: function (objects) {
            if (objects.hasOwnProperty('mainOffer')) {
                this.mainOffer = objects.mainOffer;
            }

            if (objects.hasOwnProperty('offer')) {
                this.offer = objects.offer;
            }

            if (objects.hasOwnProperty('discount')) {
                this.discount = objects.discount;
            }

            if (objects.hasOwnProperty('discountType')) {
                this.discountType = objects.discountType
            }
        }
    }
}
</script>
