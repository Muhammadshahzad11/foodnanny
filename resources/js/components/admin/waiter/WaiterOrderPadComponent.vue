<template>
    <LoadingComponent :props="loading"/>
    <KitchenTicketPrintSheet :payload="printPayload"/>
    <div class="col-12 mb-3 flex flex-wrap items-center justify-between gap-3">
        <div>
            <router-link :to="{name: 'admin.waiter.tables'}" class="text-sm text-primary mb-1 inline-block">
                ← {{ $t('label.tables') }}
            </router-link>
            <h3 class="text-xl font-semibold text-heading" v-if="table">
                {{ table.table_number }} · {{ table.name }}
            </h3>
            <p class="text-sm text-[#6E7191]" v-if="orderMeta">
                {{ orderMeta }}
            </p>
        </div>
        <div class="flex flex-wrap gap-2">
            <button
                v-if="canPrintKot"
                type="button"
                @click.prevent="printKot"
                class="db-btn py-2 text-white bg-amber-600"
            >
                {{ $t('button.print_kot') }}
            </button>
            <router-link
                v-if="waiterOrderStore.context.orderId"
                :to="{name: 'admin.waiter.orders.show', params: {id: waiterOrderStore.context.orderId}}"
                class="db-btn py-2 text-white bg-slate-700"
            >
                {{ $t('button.view') }}
            </router-link>
        </div>
    </div>

    <div class="w-full md:w-[calc(100%-366px)]">
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
                <SwiperSlide class="!w-fit" v-for="(category, index) in categories" :key="category.id"
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

            <div class="flex flex-col gap-3 mb-3">
                <input class="db-field-control" type="text" v-model="orderNote"
                       :placeholder="$t('label.order_note')"/>
                <input v-on:keypress="onlyNumber($event)" class="db-field-control" type="number"
                       v-model="token" :placeholder="$t('label.token_no')"/>
            </div>
        </div>

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
                        <i class="lab-line-trash text-danger"></i>
                    </button>
                </td>
                <td class="px-3 py-3 align-top border-b border-[#EFF0F6]">
                    <p class="text-sm font-medium text-heading">{{ cart.name }}</p>
                    <p class="text-xs text-[#6E7191]" v-if="cart.instruction">{{ cart.instruction }}</p>
                </td>
                <td class="px-3 py-3 align-top border-b border-[#EFF0F6]">
                    <div class="flex items-center gap-1">
                        <button @click.prevent="cartQuantityDecrement(index)" class="w-6 h-6 rounded bg-[#EFF0F6]">-</button>
                        <span class="text-sm w-6 text-center">{{ cart.quantity }}</span>
                        <button @click.prevent="cartQuantityIncrement(index)" class="w-6 h-6 rounded bg-[#EFF0F6]">+</button>
                    </div>
                </td>
                <td class="px-3 py-3 align-top border-b border-[#EFF0F6] text-sm">
                    {{ currencyFormat(cart.total || 0, setting.site_digit_after_decimal_point, setting.site_default_currency_symbol, setting.site_currency_position) }}
                </td>
            </tr>
            </tbody>
        </table>

        <div class="p-4">
            <ul class="mb-4 space-y-2">
                <li class="flex items-center justify-between">
                    <span class="text-sm text-[#6E7191]">{{ $t('label.subtotal') }}</span>
                    <span class="text-sm">{{ currencyFormat(subtotal, setting.site_digit_after_decimal_point, setting.site_default_currency_symbol, setting.site_currency_position) }}</span>
                </li>
                <li class="flex items-center justify-between">
                    <span class="text-sm text-[#6E7191]">{{ $t('label.tax') }}</span>
                    <span class="text-sm">{{ currencyFormat(tax, setting.site_digit_after_decimal_point, setting.site_default_currency_symbol, setting.site_currency_position) }}</span>
                </li>
                <li class="flex items-center justify-between">
                    <span class="text-sm font-bold text-heading">{{ $t('label.total') }}</span>
                    <span class="text-sm font-medium">{{ currencyFormat(total, setting.site_digit_after_decimal_point, setting.site_default_currency_symbol, setting.site_currency_position) }}</span>
                </li>
            </ul>

            <div v-if="carts.length > 0" class="flex flex-col gap-2">
                <button @click.prevent="saveDraft" class="capitalize text-sm font-medium leading-6 w-full text-center rounded-3xl py-2 text-white bg-[#6E7191]">
                    {{ $t('button.save_draft') }}
                </button>
                <button @click.prevent="sendKitchen" class="capitalize text-sm font-medium leading-6 w-full text-center rounded-3xl py-2 text-white bg-[#1AB759]">
                    {{ $t('button.send_to_kitchen') }}
                </button>
                <button
                    v-if="canPrintKot"
                    @click.prevent="printKot"
                    class="capitalize text-sm font-medium leading-6 w-full text-center rounded-3xl py-2 text-white bg-amber-600"
                >
                    {{ $t('button.print_kot') }}
                </button>
                <button
                    v-if="waiterOrderStore.context.orderId && waiterOrderStore.context.isDraft"
                    @click.prevent="cancelDraft"
                    class="capitalize text-sm font-medium leading-6 w-full text-center rounded-3xl py-2 text-white bg-[#FB4E4E]"
                >
                    {{ $t('button.cancel_draft') }}
                </button>
            </div>
        </div>
    </div>

    <button @click="posOpen = true"
            class="fixed md:hidden bottom-0 left-0 z-10 w-full h-14 py-4 text-center flex items-center justify-center shadow-xl-top gap-3 bg-primary">
        <i class="lab lab-bag-2 lab-font-size-13 text-white"></i>
        <span class="text-base font-medium font-client text-white">
            {{ carts.length }} {{ $t('label.items') }} -
            {{ currencyFormat(total, setting.site_digit_after_decimal_point, setting.site_default_currency_symbol, setting.site_currency_position) }}
        </span>
    </button>
</template>

<script>
import {provide} from "vue";
import LoadingComponent from "../../common/LoadingComponent.vue";
import KitchenTicketPrintSheet from "../kitchen/KitchenTicketPrintSheet.vue";
import statusEnum from "../../../enums/modules/statusEnum.js";
import {useItemStore} from "../../../stores/item.js";
import {usePosCategoryStore} from "../../../stores/posCategory.js";
import {Swiper, SwiperSlide} from 'swiper/vue';
import ItemComponent from "../pos/ItemComponent.vue";
import DisplayModeEnum from "../../../enums/modules/displayModeEnum.js";
import {useCommonStore} from "../../../stores/common.js";
import {usePosOfferStore} from "../../../stores/posOffer.js";
import {useFrontendSettingStore} from "../../../stores/frontendSetting.js";
import {useWaiterCartStore} from "../../../stores/waiterCart.js";
import {useWaiterOrderStore} from "../../../stores/waiterOrder.js";
import {useWaiterTableStore} from "../../../stores/waiterTable.js";
import appService from "../../../services/appService.js";
import alertService from "../../../services/alertService.js";
import {apiErrorMessage} from "../../../services/apiError.js";
import _ from "lodash";

export default {
    name: "WaiterOrderPadComponent",
    components: {LoadingComponent, KitchenTicketPrintSheet, ItemComponent, Swiper, SwiperSlide},
    setup() {
        const waiterCartStore = useWaiterCartStore();
        provide('cartStore', waiterCartStore);

        return {
            itemStore: useItemStore(),
            commonStore: useCommonStore(),
            waiterCartStore,
            waiterOrderStore: useWaiterOrderStore(),
            waiterTableStore: useWaiterTableStore(),
            posOfferStore: usePosOfferStore(),
            posCategoryStore: usePosCategoryStore(),
            frontendSettingStore: useFrontendSettingStore(),
        };
    },
    data() {
        return {
            loading: {isActive: false},
            printPayload: null,
            posOpen: false,
            offer: {},
            table: null,
            orderNote: '',
            token: '',
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
        };
    },
    computed: {
        displayMode() {
            return this.commonStore.display_mode === DisplayModeEnum.LTR ? 'ltr' : 'rtl';
        },
        setting() {
            return this.frontendSettingStore.lists;
        },
        categories() {
            return this.posCategoryStore.lists;
        },
        items() {
            return this.itemStore.lists;
        },
        carts() {
            return this.waiterCartStore.lists;
        },
        subtotal() {
            return this.waiterCartStore.subtotal;
        },
        tax() {
            return this.waiterCartStore.tax;
        },
        total() {
            return this.waiterCartStore.total;
        },
        orderMeta() {
            const ctx = this.waiterOrderStore.context;
            if (!ctx.orderId) {
                return this.$t('label.new_table_order');
            }
            const serial = this.waiterOrderStore.show?.order_serial_no
                ? `#${this.waiterOrderStore.show.order_serial_no}`
                : `#${ctx.orderId}`;
            if (ctx.isDraft) {
                return `${serial} · ${this.$t('label.draft')}`;
            }
            return `${serial} · ${this.$t('message.waiter_order_sent_kitchen')}`;
        },
        canPrintKot() {
            return !!this.waiterOrderStore.context.orderId && !this.waiterOrderStore.context.isDraft;
        },
    },
    async mounted() {
        this.commonStore.update({top_sidebar: false});
        this.waiterCartStore.resetCart();
        this.waiterOrderStore.resetContext();
        await this.bootstrap();
    },
    beforeUnmount() {
        this.waiterCartStore.resetCart();
    },
    methods: {
        onlyNumber(e) {
            return appService.onlyNumber(e);
        },
        currencyFormat(amount, decimal, currency, position) {
            return appService.currencyFormat(amount, decimal, currency, position);
        },
        async bootstrap() {
            try {
                this.loading.isActive = true;
                const tableId = this.$route.params.id;
                const tableRes = await this.waiterTableStore.view(tableId);
                this.table = tableRes.data.data;

                const openOrder = this.table.open_order;
                if (openOrder?.id) {
                    await this.waiterOrderStore.view(openOrder.id);
                    this.hydrateCartFromOrder(this.waiterOrderStore.show);
                    this.orderNote = this.waiterOrderStore.context.orderNote || '';
                    this.token = this.waiterOrderStore.context.token || '';
                } else {
                    this.waiterOrderStore.setContext({tableId: Number(tableId)});
                }

                try {
                    await this.itemCategories();
                } catch (e) {
                    alertService.warning(apiErrorMessage(e, this.$t('message.failed_to_load_categories')));
                }
                try {
                    await this.itemList();
                } catch (e) {
                    alertService.warning(apiErrorMessage(e, this.$t('message.failed_to_load_menu_items')));
                }
                try {
                    const offerRes = await this.posOfferStore.fetch();
                    this.offer = offerRes.data.data || {};
                } catch (e) {
                    this.offer = {};
                }
                this.loading.isActive = false;
            } catch (err) {
                this.loading.isActive = false;
                alertService.error(apiErrorMessage(err, this.$t('message.failed_to_open_table_order')));
            }
        },
        hydrateCartFromOrder(order) {
            this.waiterCartStore.resetCart();
            if (!order?.order_items?.length) {
                return;
            }

            const payload = order.order_items.map((item) => {
                let variations = item.item_variations || {};
                let extras = item.item_extras || {};
                try {
                    if (typeof item.item_variations === 'string') {
                        variations = JSON.parse(item.item_variations || '{}');
                    }
                } catch (e) {
                    variations = {};
                }
                try {
                    if (typeof item.item_extras === 'string') {
                        extras = JSON.parse(item.item_extras || '{}');
                    }
                } catch (e) {
                    extras = {};
                }

                return {
                    discount: item.discount,
                    maximum_purchase_quantity: 99,
                    image: item.item?.thumb || item.item?.image || '',
                    instruction: item.instruction,
                    item_extra_total: item.item_extra_total,
                    item_extras: {
                        extras: Array.isArray(extras) ? extras.map((e) => e.id || e) : (extras.extras || []),
                        names: Array.isArray(extras) ? extras.map((e) => e.name || '') : (extras.names || []),
                    },
                    item_id: item.item_id,
                    item_variation_total: item.item_variation_total,
                    item_variations: {
                        variations: Array.isArray(variations)
                            ? Object.fromEntries(
                                variations
                                    .filter((v) => v && v.item_attribute_id != null && v.id != null)
                                    .map((v) => [v.item_attribute_id, v.id])
                            )
                            : (variations.variations || {}),
                        names: Array.isArray(variations)
                            ? Object.fromEntries(
                                variations
                                    .filter((v) => v && (v.variation_name || v.name))
                                    .map((v) => [v.variation_name || v.name, v.name])
                            )
                            : (variations.names || {}),
                    },
                    name: item.item_name || item.name || item.item?.name || `Item #${item.item_id}`,
                    currency_price: item.currency_price || '',
                    convert_price: parseFloat(item.price || item.item_price || 0),
                    quantity: item.quantity,
                    tax: item.tax_rate > 0 ? {
                        name: item.tax_name,
                        tax_convert_rate: item.tax_rate,
                        type: item.tax_type,
                    } : null,
                };
            });

            this.waiterCartStore.fetchCarts(payload);
            if (order.discount > 0) {
                this.waiterCartStore.callDiscount(parseFloat(order.discount));
            }
        },
        search() {
            this.itemList();
        },
        allCategory() {
            this.props.search.name = "";
            this.props.search.item_category_id = "";
            this.itemList();
        },
        itemCategories() {
            return this.posCategoryStore.fetch(this.categoryProps);
        },
        itemList() {
            return this.itemStore.fetch(this.props.search);
        },
        setCategory(id) {
            this.props.search.item_category_id = id;
            this.itemList();
        },
        cartQuantityIncrement(id) {
            this.waiterCartStore.quantity({id, status: "increment"});
        },
        cartQuantityDecrement(id) {
            this.waiterCartStore.quantity({id, status: "decrement"});
        },
        deleteCartItem(id) {
            this.waiterCartStore.deleteCartItem({id, status: "decrement"});
        },
        buildItemsPayload() {
            const items = [];
            _.forEach(this.carts, (item) => {
                let item_variations = [];
                if (item.item_variations?.variations && Object.keys(item.item_variations.variations).length > 0) {
                    _.forEach(item.item_variations.variations, (value, index) => {
                        item_variations.push({
                            id: value,
                            item_id: item.item_id,
                            item_attribute_id: index,
                        });
                    });
                }
                if (item.item_variations?.names && Object.keys(item.item_variations.names).length > 0) {
                    let i = 0;
                    _.forEach(item.item_variations.names, (value, index) => {
                        if (item_variations[i]) {
                            item_variations[i].variation_name = index;
                            item_variations[i].name = value;
                        }
                        i++;
                    });
                }

                let item_extras = [];
                if (item.item_extras?.extras?.length) {
                    _.forEach(item.item_extras.extras, (value) => {
                        item_extras.push({id: value, item_id: item.item_id});
                    });
                }
                if (item.item_extras?.names?.length) {
                    let i = 0;
                    _.forEach(item.item_extras.names, (value) => {
                        if (item_extras[i]) {
                            item_extras[i].name = value;
                        }
                        i++;
                    });
                }

                items.push({
                    item_id: item.item_id,
                    item_price: item.convert_price,
                    instruction: item.instruction,
                    quantity: item.quantity,
                    discount: item.discount,
                    total_price: item.total,
                    item_variation_total: item.item_variation_total,
                    item_extra_total: item.item_extra_total,
                    tax_name: item.tax_name,
                    tax_rate: item.tax_rate,
                    tax_type: item.tax_type,
                    tax_amount: item.tax_amount,
                    item_variations,
                    item_extras,
                });
            });
            return items;
        },
        buildPayload(sendToKitchen = false) {
            return {
                table_id: Number(this.$route.params.id),
                subtotal: this.subtotal,
                discount: this.waiterCartStore.discount || 0,
                tax: this.tax,
                total: this.total,
                token: this.token || null,
                order_note: this.orderNote || null,
                items: JSON.stringify(this.buildItemsPayload()),
                send_to_kitchen: sendToKitchen,
                updated_at: this.waiterOrderStore.context.updatedAt,
            };
        },
        async saveDraft() {
            try {
                this.loading.isActive = true;
                const payload = this.buildPayload(false);
                if (this.waiterOrderStore.context.orderId) {
                    await this.waiterOrderStore.update(this.waiterOrderStore.context.orderId, payload);
                } else {
                    await this.waiterOrderStore.create(payload);
                }
                this.loading.isActive = false;
                alertService.success(this.$t('message.waiter_draft_saved'));
            } catch (err) {
                this.loading.isActive = false;
                alertService.error(apiErrorMessage(err, this.$t('message.failed_to_save_draft')));
            }
        },
        async sendKitchen() {
            try {
                this.loading.isActive = true;
                if (this.carts.length === 0) {
                    this.loading.isActive = false;
                    alertService.warning(this.$t('message.waiter_order_requires_items'));
                    return;
                }
                if (!this.waiterOrderStore.context.orderId) {
                    await this.waiterOrderStore.create(this.buildPayload(true));
                } else {
                    await this.waiterOrderStore.update(
                        this.waiterOrderStore.context.orderId,
                        this.buildPayload(false)
                    );
                    await this.waiterOrderStore.sendKitchen(
                        this.waiterOrderStore.context.orderId,
                        this.waiterOrderStore.context.updatedAt
                    );
                }
                this.loading.isActive = false;
                alertService.success(this.$t('message.waiter_order_sent_kitchen'));
            } catch (err) {
                this.loading.isActive = false;
                alertService.error(apiErrorMessage(err, this.$t('message.failed_to_send_kitchen')));
            }
        },
        async cancelDraft() {
            try {
                this.loading.isActive = true;
                await this.waiterOrderStore.cancelDraft(this.waiterOrderStore.context.orderId);
                this.waiterCartStore.resetCart();
                this.orderNote = '';
                this.token = '';
                this.loading.isActive = false;
                alertService.success(this.$t('message.waiter_draft_canceled'));
                this.$router.push({name: 'admin.waiter.tables'});
            } catch (err) {
                this.loading.isActive = false;
                alertService.error(apiErrorMessage(err, this.$t('message.failed_to_cancel_draft')));
            }
        }
    }
}
</script>
