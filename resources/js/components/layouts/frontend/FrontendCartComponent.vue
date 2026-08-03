<template>
    <aside id="cart-canvas" @click="closeBackdrop"
           class="fixed inset-0 z-50 bg-black/50 duration-500 transition-all invisible opacity-0">
        <div
            class="ms-auto ltr:translate-x-full rtl:-translate-x-full max-w-sm w-full h-dvh overflow-x-hidden thin-scrolling bg-white">
            <div class="flex items-start justify-between gap-3 p-4 h-[78px]">
                <h4 v-if="carts.length > 0" class="font-medium overflow-hidden">
                    <span class="text-xs block mb-0.5 text-paragraph">{{ $t('label.your_cart_from') }}</span>
                    <span class="text-lg block capitalize whitespace-nowrap overflow-hidden text-ellipsis">
                        {{ restaurant.name }}
                    </span>
                </h4>
                <button @click.prevent="closeCanvas('cart-canvas')">
                    <i class="lab-line-circle-cross text-lg text-danger"></i>
                </button>
            </div>

            <div
                v-if="isDineIn"
                class="mx-4 mb-3 rounded-xl border border-primary/20 bg-primary/5 px-3 py-2 text-sm text-heading">
                {{ $t('message.dine_in_ordering_for_table') }}
                <span class="font-medium"> — {{ dineInTableLabel }}</span>
            </div>

            <div
                v-if="carts.length > 0 && !isDineIn && orderType !== null && restaurant.order_setup && (restaurant.order_setup.delivery === activityEnum.ENABLE || restaurant.order_setup.takeaway === activityEnum.ENABLE)"
                class="pb-4 flex items-center justify-center">
                <nav class="flex-shrink-0 w-fit flex items-center justify-center p-1 rounded-full bg-mate">
                    <button @click.prevent="changeOrderType(orderTypeEnum.DELIVERY)"
                            v-if="restaurant.order_setup.delivery === activityEnum.ENABLE"
                            :class="orderType === orderTypeEnum.DELIVERY ? 'text-white bg-secondary':''"
                            class="text-sm capitalize h-8 px-3 rounded-full">
                        {{ $t('label.delivery') }}
                    </button>
                    <button @click.prevent="changeOrderType(orderTypeEnum.TAKEAWAY)"
                            v-if="restaurant.order_setup.takeaway === activityEnum.ENABLE"
                            :class="orderType === orderTypeEnum.TAKEAWAY ? 'text-white bg-secondary':''"
                            class="text-sm capitalize h-8 px-3 rounded-full">
                        {{ $t('label.takeaway') }}
                    </button>
                </nav>
            </div>

            <div v-if="carts.length === 0"
                 class="h-[calc(100dvh-274px)] w-full py-10 flex flex-col items-center justify-center text-center">
                <img class="w-40 mb-3.5 text-gray-200" :src="setting.image_cart" alt="empty">
                <h3 class="text-sm capitalize text-gray-400">{{ $t('message.empty_cart') }}</h3>
            </div>

            <div v-if="(carts.length > 0 && orderType === null && !isDineIn && restaurant.order_setup && restaurant.order_setup.delivery !== activityEnum.ENABLE && restaurant.order_setup.takeaway !== activityEnum.ENABLE)"
                 class="h-[calc(100dvh-274px)] w-full py-10 flex flex-col items-center justify-center text-center">
                <i class="lab-line-warning text-7xl mb-3.5 text-gray-200"></i>
                <h3 class="text-sm capitalize text-gray-200">{{ $t('message.delivery_and_takeaway') }}</h3>
            </div>

            <ul v-if="carts.length > 0" class="px-4 h-[calc(100dvh-274px)] thin-scrolling">
                <li v-for="(cart, index) in carts" :key="index" class="py-4 border-t border-gray-100">
                    <div class="flex items-start gap-3">
                        <figure class="flex-shrink-0">
                            <img class="w-16 h-16 rounded-lg object-cover" :src="cart.image" alt="menu">
                        </figure>
                        <div class="flex-auto overflow-hidden">
                            <h3 class="text-sm capitalize whitespace-nowrap overflow-hidden text-ellipsis mb-0.5">
                                {{ cart.name }}
                            </h3>
                            <p v-if="Object.keys(cart.item_variations.variations).length !== 0"
                               class="text-xs capitalize text-paragraph mb-1.5">
                                <span v-for="(variation, variationName, index) in cart.item_variations.names"
                                      :key="variationName">
                                    {{ variationName }}: {{ variation }}<span
                                    v-if="index < Object.keys(cart.item_variations.names).length - 1">,&nbsp;</span>
                                </span>
                            </p>
                            <h4 class="text-sm font-medium">
                                {{
                                    currencyFormat(cart.total, setting.site_digit_after_decimal_point, setting.site_default_currency_symbol, setting.site_currency_position)
                                }}
                            </h4>
                        </div>
                        <div class="flex flex-col items-end gap-2 self-start flex-shrink-0">
                            <button @click.prevent="removeItem(index)" type="button"
                                    class="text-xs font-medium text-danger hover:underline"
                                    :title="$t('button.remove')">
                                {{ $t('button.remove') }}
                            </button>
                            <div class="flex items-center w-16 h-6 gap-1">
                                <button @click.prevent="quantityDecrement(index)" type="button"
                                        class="lab-line-minus-circle font-medium hover:text-primary"></button>
                                <input v-on:keypress="onlyNumber($event)" v-on:keyup="quantityUp(index, $event)"
                                       type="number" pattern="[1-9]*" :value="cart.quantity"
                                       class="appearance-none w-full h-full text-center text-sm">
                                <button @click.prevent="quantityIncrement(index)" type="button"
                                        class="lab-line-add-circle font-medium hover:text-primary"></button>
                            </div>
                        </div>
                    </div>
                    <div v-if="cart.item_extras.extras.length > 0 || cart.instruction !== ''" class="mt-3">
                        <div v-if="cart.item_extras.extras.length > 0" class="flex items-start gap-1 mb-1 last:mb-0">
                            <b class="text-xs font-medium capitalize whitespace-nowrap">{{ $t('label.extras') }}:</b>
                            <p class="text-xs capitalize text-paragraph">
                                {{ cart.item_extras.names.map(extra => extra).join(', ') }}</p>
                        </div>
                        <div v-if="cart.instruction !== ''" class="flex items-start gap-1 mb-1 last:mb-0">
                            <b class="text-xs font-medium capitalize whitespace-nowrap">
                                {{ $t('label.instruction') }}:
                            </b>
                            <p class="text-xs capitalize text-paragraph">{{ cart.instruction }}</p>
                        </div>
                    </div>
                </li>
            </ul>

            <div v-if="carts.length > 0" class="p-4 h-[140px]">
                <div
                    class="w-full h-12 rounded-xl px-3 mb-3 flex items-center justify-between gap-2 border border-gray-100">
                    <h5 class="text-sm capitalize whitespace-nowrap">{{ $t('label.subtotal') }}</h5>
                    <h6 class="text-sm font-medium text-green-500">
                        {{
                            currencyFormat(subtotal, setting.site_digit_after_decimal_point, setting.site_default_currency_symbol, setting.site_currency_position)
                        }}
                    </h6>
                </div>

                <router-link @click.prevent="closeCanvas('cart-canvas')" :to="{ name: 'frontend.checkout' }"
                             :class="restaurant.order_setup && restaurant.order_setup.minimum_order_limit > 0 && restaurant.order_setup.minimum_order_limit > subtotal ? 'bg-primary/50 pointer-events-disable text-sm' : ''"
                             class="w-full py-3 px-3 block font-medium text-white bg-primary rounded-3xl text-center">
                    {{ $t('button.proceed_checkout') }}
                    <strong
                        v-if="restaurant.order_setup && restaurant.order_setup.minimum_order_limit > 0 && restaurant.order_setup.minimum_order_limit > subtotal"
                        class="text-red-400">
                        ({{ $t('label.min_order') + ': ' + restaurant.order_setup.currency_minimum_order_limit }})
                    </strong>
                </router-link>
            </div>
        </div>
    </aside>
</template>

<script>
import {useCanvas} from "../../../composables/canvas.js";
import {useFrontendCartStore} from "../../../stores/frontendCart.js";
import {useDineInContextStore} from "../../../stores/dineInContext.js";
import orderTypeEnum from "../../../enums/modules/orderTypeEnum.js";
import activityEnum from "../../../enums/modules/activityEnum.js";
import {useFrontendSettingStore} from "../../../stores/frontendSetting.js";
import {useFrontendRestaurantStore} from "../../../stores/frontendRestaurant.js";
import appService from "../../../services/appService.js";
import alertService from "../../../services/alertService.js";


export default {
    name: "FrontendCartComponent",
    setup() {
        const {closeCanvas, closeBackdrop} = useCanvas();
        const frontendCartStore            = useFrontendCartStore();
        const frontendSettingStore         = useFrontendSettingStore();
        const dineInContextStore           = useDineInContextStore();
        const frontendRestaurantStore      = useFrontendRestaurantStore();

        return {
            closeCanvas,
            closeBackdrop,
            frontendCartStore,
            frontendSettingStore,
            dineInContextStore,
            frontendRestaurantStore
        }
    },
    data() {
        return {
            orderTypeEnum: orderTypeEnum,
            activityEnum: activityEnum,
            localOrderType: null
        }
    },
    computed: {
        setting: function () {
            return this.frontendSettingStore.lists;
        },
        carts: function () {
            return this.frontendCartStore.lists;
        },
        subtotal: function () {
            return this.frontendCartStore.subtotal;
        },
        restaurant: function () {
            return this.frontendCartStore.restaurant;
        },
        orderType: function () {
            return this.frontendCartStore.orderType;
        },
        isDineIn: function () {
            return this.orderType === orderTypeEnum.DINING_TABLE
                || (this.dineInContextStore.isActive
                    && this.dineInContextStore.matchesRestaurant(this.restaurant?.id || this.restaurant?.slug));
        },
        dineInTableLabel: function () {
            return this.dineInContextStore.tableLabel || this.$t('label.dining_table');
        }
    },
    watch: {
        carts: {
            immediate: true,
            handler() {
                this.syncCartOrderType();
            }
        }
    },
    methods: {
        syncCartOrderType: async function () {
            if (this.carts.length === 0) {
                return;
            }

            if (this.isDineIn && this.dineInContextStore.context) {
                this.frontendCartStore.applyDineInContext(this.dineInContextStore.context);
                return;
            }

            if (this.orderType !== null && this.orderType !== undefined) {
                return;
            }

            const restaurantId = this.restaurant?.id;
            if (!restaurantId) {
                return;
            }

            if (!this.restaurant.order_setup || this.restaurant.order_setup.delivery == null) {
                try {
                    const res = await this.frontendRestaurantStore.viewById({id: restaurantId});
                    const fresh = res.data.data;
                    if (fresh?.order_setup) {
                        this.frontendCartStore.restaurant = {
                            ...this.frontendCartStore.restaurant,
                            ...fresh,
                            order_setup: fresh.order_setup,
                        };
                    }
                } catch (e) {
                    // keep existing cart restaurant
                }
            }

            this.frontendCartStore.ensureOrderType();
        },
        onlyNumber: function (e) {
            return appService.onlyNumber(e);
        },
        currencyFormat(amount, decimal, currency, position) {
            return appService.currencyFormat(amount, decimal, currency, position);
        },
        quantityUp: function (id, e) {
            if (parseInt(e.target.value) > 0) {
                this.frontendCartStore.setQuantity({id: id, status: parseInt(e.target.value)}).catch(error => {
                    if (error === 'max_quantity_error') {
                        alertService.error(this.$t('message.already_added_max_quantity'));
                    }
                })
            } else {
                e.target.value = 1;
                this.frontendCartStore.setQuantity({id: id, status: 1});
            }
        },
        quantityIncrement: function (id) {
            this.frontendCartStore.setQuantity({id: id, status: "increment"}).catch(error => {
                if (error === 'max_quantity_error') {
                    alertService.error(this.$t('message.already_added_max_quantity'));
                }
            })
        },
        quantityDecrement: function (id) {
            this.frontendCartStore.setQuantity({id: id, status: "decrement"});
        },
        removeItem: function (id) {
            this.frontendCartStore.removeItem(id);
        },
        changeOrderType: function (e) {
            if (this.isDineIn) {
                return;
            }
            this.localOrderType = e;
            this.frontendCartStore.callUpdateOrderType(this.localOrderType);
        }
    }
}
</script>
