<template>
    <div v-for="item in items" :key="item.id || item.name"
         v-show="type === null || type === item.item_type"
         class="group restaurant-item flex h-full min-h-[8.5rem] overflow-hidden bg-white transition-all duration-300">
        <figure class="restaurant-item__media relative flex-shrink-0 overflow-hidden">
            <label v-if="Object.keys(offer).length > 0 && parseFloat(offer.amount || 0) > 0"
                   class="absolute top-2 ltr:left-2 rtl:right-2 z-20 whitespace-nowrap text-[11px] font-semibold py-1 px-2 bg-primary text-white rounded">
                {{ $t('message.percentage_off', {discount: offer.amount}) }}
            </label>
            <label v-else-if="item.discount > 0 || Object.keys(offer).length > 0"
                   class="absolute top-2 ltr:left-2 rtl:right-2 z-20 whitespace-nowrap text-[11px] font-semibold py-1 px-2 bg-primary text-white rounded">
                {{ item.discount_option }} {{ $t('label.off') }}
            </label>

            <img :src="item.thumb" alt="menu"
                 @click.prevent="itemVariationModalShow('item-variation-modal-' + itemIndex, item)"
                 class="restaurant-item__image object-cover group-hover:scale-105 transition-transform duration-500 cursor-pointer">
            <div v-if="parseInt(item.item_type) === parseInt(enums.itemTypeEnum.VEG)"
                 class="restaurant-item__veg absolute z-20">
                <img :src="setting.image_vag" alt="veg" class="w-4 h-4 object-contain">
            </div>
            <div v-if="parseInt(item.halal) === parseInt(enums.askEnum.YES)"
                 class="restaurant-item__halal absolute z-20 flex items-center">
                <img :src="setting.image_halal" alt="halal" class="w-5 h-5 sm:w-6 sm:h-6 object-contain drop-shadow">
            </div>
        </figure>
        <div class="flex min-w-0 flex-auto flex-col justify-between gap-2 px-3 py-2.5 sm:px-3.5 sm:py-3 overflow-hidden">
            <div class="min-w-0">
                <div class="flex items-start gap-2 mb-1">
                    <h3 @click.prevent="itemVariationModalShow('item-variation-modal-' + itemIndex, item)"
                        class="min-w-0 flex-1 cursor-pointer text-sm font-semibold capitalize leading-5 line-clamp-1 transition-colors duration-300 group-hover:text-primary">
                        {{ item.name }}
                    </h3>
                    <button v-if="item.caution"
                            @click.prevent="itemInfoModalShow('item-info-modal-' + itemIndex, item.name, item.caution)"
                            class="lab-fill-info mt-0.5 shrink-0 text-paragraph"></button>
                </div>
                <p class="editor-show-design restaurant-item__desc text-xs leading-5 text-paragraph line-clamp-2"
                   v-html="textShortener(item.description_alt, 72)"></p>
            </div>
            <div class="flex items-center justify-between gap-2 mt-auto">
                <div v-if="Object.keys(offer).length > 0" class="flex min-w-0 items-center gap-2">
                    <del class="text-xs font-medium text-paragraph">
                        {{ item.currency_price }}
                    </del>
                    <span class="text-sm font-semibold text-primary truncate">
                        {{
                            currencyFormat((item.convert_price - parseFloat((item.convert_price / 100) * offer.amount).toFixed(setting.site_digit_after_decimal_point)), setting.site_digit_after_decimal_point, setting.site_default_currency_symbol, setting.site_currency_position)
                        }}
                    </span>
                </div>
                <div v-else class="flex min-w-0 items-center gap-2">
                    <del v-if="item.discount > 0" class="text-xs font-medium text-paragraph">
                        {{ item.currency_price }}
                    </del>
                    <span class="text-sm font-semibold text-primary truncate">
                        {{ item.discount > 0 ? item.currency_discounted_price : item.currency_price }}
                    </span>
                </div>
                <button @click.prevent="itemVariationModalShow('item-variation-modal-' + itemIndex, item)"
                        class="restaurant-item__add shrink-0 inline-flex items-center gap-1 h-8 px-3 text-white bg-primary hover:bg-heading transition">
                    <i class="lab-line-add-circle text-sm"></i>
                    <span class="text-xs font-semibold capitalize">{{ $t('button.add') }}</span>
                </button>
            </div>
        </div>
    </div>

    <Teleport to="body">
        <div :id="'item-info-modal-' + itemIndex"
             class="fixed inset-0 z-[120] p-3 w-screen h-dvh overflow-y-auto bg-black/55 transition-all duration-300 opacity-0 invisible">
            <div class="max-w-lg w-full rounded-2xl mx-auto bg-white transition-all duration-300 shadow-2xl">
                <div class="flex items-start justify-between gap-4 py-4 px-5 sm:px-6 border-b border-gray-100">
                    <h3 class="text-lg font-semibold capitalize">{{ itemInfo.name }}</h3>
                    <button @click.prevent="closeModal('item-info-modal-' + itemIndex)"
                            class="lab-line-circle-cross text-lg text-danger"></button>
                </div>
                <div v-html="itemInfo.caution" class="p-5 sm:p-6 ql-ul-set editor-show-design"></div>
            </div>
        </div>
    </Teleport>

    <Teleport to="body">
        <div v-if="item" :id="'item-variation-modal-' + itemIndex"
             class="fixed inset-0 z-[120] flex items-end sm:items-center justify-center p-0 sm:p-4 w-screen h-dvh overflow-hidden bg-black/55 transition-all duration-300 opacity-0 invisible">
            <div class="item-customize-modal w-full max-w-2xl bg-white transition-all duration-300 flex flex-col overflow-hidden">
                <div v-if="typeof item === 'object' && Object.keys(item).length > 0" class="flex min-h-0 flex-1 flex-col">
                    <LoadingContentComponent :props="loading"/>

                    <div class="shrink-0 flex gap-3 sm:gap-4 p-4 sm:p-5 border-b border-gray-100">
                        <img class="w-20 h-20 sm:w-24 sm:h-24 object-cover rounded-xl flex-shrink-0" :src="item.thumb" alt="menu">
                        <div class="flex-auto overflow-hidden min-w-0">
                            <div class="flex items-start gap-2 mb-1">
                                <h3 class="text-base sm:text-lg font-semibold capitalize leading-snug">
                                    {{ item.name }}
                                </h3>
                                <button v-if="item.caution"
                                        @click.prevent="itemInfoModalShow('item-info-modal-' + itemIndex, item.name, item.caution)"
                                        class="lab-fill-info text-paragraph shrink-0 mt-1"></button>
                            </div>
                            <p v-if="item.description_alt" class="text-xs sm:text-sm text-paragraph line-clamp-2 mb-2">
                                {{ item.description_alt }}
                            </p>
                            <div v-if="Object.keys(offer).length > 0" class="flex items-center gap-2">
                                <del class="text-sm font-medium text-paragraph">{{ item.currency_price }}</del>
                                <span class="text-sm font-semibold text-heading">
                                    {{
                                        currencyFormat((item.convert_price - parseFloat((item.convert_price / 100) * offer.amount).toFixed(setting.site_digit_after_decimal_point)), setting.site_digit_after_decimal_point, setting.site_default_currency_symbol, setting.site_currency_position)
                                    }}
                                </span>
                            </div>
                            <div v-else class="flex items-center gap-2">
                                <del v-if="item.discount > 0" class="text-sm font-medium text-paragraph">{{ item.currency_price }}</del>
                                <span class="text-sm font-semibold text-heading">
                                    {{ item.discount > 0 ? item.currency_discounted_price : item.currency_price }}
                                </span>
                            </div>
                        </div>
                        <button @click.prevent="itemVariationModalHide('item-variation-modal-' + itemIndex)"
                                class="flex-shrink-0 self-start h-8 w-8 inline-flex items-center justify-center rounded-full bg-gray-100 hover:bg-gray-200">
                            <i class="lab-line-circle-cross text-danger"></i>
                        </button>
                    </div>

                    <div class="flex-1 min-h-0 overflow-y-auto overscroll-contain px-4 sm:px-5 py-4">
                        <div class="flex items-center gap-2 mb-4">
                            <h3 class="text-sm font-medium capitalize">{{ $t('label.quantity') }}:</h3>
                            <div class="flex items-center w-24 h-8 gap-1 p-1 rounded-lg bg-gray-100">
                                <button @click.prevent="quantityDecrement"
                                        class="flex-shrink-0 lab-line-minus-circle font-medium hover:text-primary"></button>
                                <input v-model="temp.quantity" v-on:keypress="onlyNumber($event)" v-on:keyup="quantityUp"
                                       type="number" class="appearance-none w-full h-full text-center text-sm">
                                <button @click.prevent="quantityIncrement"
                                        class="flex-shrink-0 lab-line-add-circle font-medium hover:text-primary"></button>
                            </div>
                        </div>

                        <div class="mb-4" v-if="item.item_attributes.length === 1">
                            <h3 class="text-sm font-medium capitalize mb-2">{{ item.item_attributes[0].name }}:</h3>
                            <div class="flex flex-wrap gap-2">
                                <button type="button"
                                     v-for="variation in item.variations[item.item_attributes[0].id]" :key="variation.id"
                                     @click="changeVariation(variation.item_attribute_id, variation.id, variation.name)"
                                     :class="temp.item_variations.variations[variation.item_attribute_id] === variation.id ? 'border-primary bg-primary/10' : 'border-gray-200 bg-gray-50'"
                                     class="inline-flex items-center gap-2 min-h-12 px-3 rounded-xl cursor-pointer border transition">
                                    <input type="radio" :id="'var-' + itemIndex + '-' + variation.id"
                                           :value="variation.id"
                                           v-model="temp.item_variations.variations[variation.item_attribute_id]"
                                           class="cs-custom-radio">
                                    <span class="text-left">
                                        <span class="block text-xs capitalize font-medium text-heading">{{ variation.name }}</span>
                                        <span class="block text-xs font-semibold mt-0.5 text-heading">+{{ variation.currency_price }}</span>
                                    </span>
                                </button>
                            </div>
                        </div>

                        <div class="mb-4" v-else-if="item.item_attributes.length > 1">
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <div v-for="item_attribute in item.item_attributes" :key="item_attribute.id">
                                    <label class="text-sm leading-6 block font-medium capitalize mb-1.5 text-heading">
                                        {{ item_attribute.name }}:
                                    </label>
                                    <div class="relative">
                                        <i class="lab lab-line-chevron-down text-sm absolute top-1/2 ltr:right-2.5 rtl:left-2.5 -translate-y-1/2"></i>
                                        <select
                                            @change.prevent="changeVariationAdjust(item_attribute.id, temp.item_variations.variations[item_attribute.id])"
                                            v-model="temp.item_variations.variations[item_attribute.id]"
                                            class="text-xs capitalize rounded-xl h-11 w-full py-1.5 px-2.5 appearance-none transition border border-[#EFF0F6] text-heading hover:border-primary/30">
                                            <option :value="variation.id"
                                                    v-for="variation in item.variations[item_attribute.id]" :key="variation.id">
                                                {{ variation.name }} +{{ variation.currency_price }}
                                            </option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mb-4" v-if="item.extras.length > 0">
                            <h3 class="text-sm font-medium capitalize mb-2">{{ $t('label.extras') }}:</h3>
                            <div class="flex flex-wrap gap-2">
                                <label v-for="extra in item.extras" :key="extra.id"
                                     :for="'extra-' + itemIndex + '-' + extra.id"
                                     :class="extraCheckClass(extra.id) ? 'border-primary bg-primary/10' : 'border-gray-200 bg-gray-50'"
                                     class="inline-flex items-center gap-2 min-h-12 px-3 rounded-xl cursor-pointer border transition">
                                    <input type="checkbox" class="cs-custom-checkbox"
                                           @change="changeExtra($event, extra.id, extra.name)"
                                           :id="'extra-' + itemIndex + '-' + extra.id" :value="extra.id">
                                    <span class="text-left">
                                        <span class="block text-xs capitalize font-medium">{{ extra.name }}</span>
                                        <span class="block text-xs font-semibold mt-0.5">+{{ extra.currency_price }}</span>
                                    </span>
                                </label>
                            </div>
                        </div>

                        <div class="mb-4" v-if="item.addons.length > 0">
                            <h3 class="text-sm font-medium capitalize mb-2">{{ $t('label.addons') }}:</h3>
                            <div class="flex flex-col gap-2">
                                <div v-for="addon in item.addons" :key="addon.id"
                                    :class="addons[addon.id] ? 'border-primary bg-primary/10' : 'border-gray-200 bg-gray-50'"
                                    class="flex rounded-xl overflow-hidden border">
                                    <img class="w-[72px] h-[72px] object-cover flex-shrink-0" :src="addon.thumb"
                                         @click.prevent="changeAddon(addon)" alt="addon">
                                    <div class="flex-auto flex flex-col justify-between p-2.5 min-w-0">
                                        <div class="flex items-center gap-1">
                                            <h5 class="text-xs capitalize font-medium truncate">{{ addon.addon_item_name }}</h5>
                                            <button v-if="addon.caution"
                                                    @click.prevent="itemInfoModalShow('item-info-modal-' + itemIndex, addon.addon_item_name, addon.caution)"
                                                    class="lab-fill-info text-sm leading-none text-paragraph"></button>
                                        </div>
                                        <p v-if="addon.variation_names.length > 0" class="inline-flex flex-wrap gap-0.5 mt-1">
                                            <span v-for="(variation, key) in addon.variation_names" :key="key"
                                                  class="text-[10px] leading-none capitalize text-paragraph">
                                                {{ textShortener(variation.name, 8) }}{{ addon.variation_names.length !== key + 1 ? ', ' : '' }}
                                            </span>
                                        </p>
                                        <div class="flex items-center justify-between gap-2 mt-2">
                                            <h6 v-if="Object.keys(offer).length > 0" class="text-xs font-semibold">
                                                {{
                                                    currencyFormat(((addon.addon_item_convert_price + addon.variation_total_convert_price) - ((addon.addon_item_convert_price / 100) * offer.amount)), setting.site_digit_after_decimal_point, setting.site_default_currency_symbol, setting.site_currency_position)
                                                }}
                                            </h6>
                                            <h6 v-else class="text-xs font-semibold">{{ addon.total_currency_price }}</h6>
                                            <button v-if="!addons[addon.id]" @click.prevent="changeAddon(addon)"
                                                    class="inline-flex items-center gap-1 h-7 px-2 rounded-lg text-white bg-primary">
                                                <i class="lab-fill-bag text-xs"></i>
                                                <span class="text-[10px] leading-none capitalize">{{ $t('button.add') }}</span>
                                            </button>
                                            <div v-if="addons[addon.id]" class="flex items-center w-16 h-6 gap-0.5">
                                                <button @click.prevent="addonQuantityDecrement(addon.id)"
                                                        :class="addonQuantity[addon.id] === 1 ? 'lab-line-trash text-danger' : 'lab-line-minus-circle'"
                                                        class="flex-shrink-0 text-xs leading-none font-medium hover:text-primary"></button>
                                                <input v-on:keypress="onlyNumber($event)"
                                                       v-on:keyup="addonQuantityUp(addon.id)"
                                                       v-model="addonQuantity[addon.id]"
                                                       type="number"
                                                       class="appearance-none w-full h-full text-center text-xs leading-none">
                                                <button @click.prevent="addonQuantityIncrement(addon.id)"
                                                        class="flex-shrink-0 lab-line-add-circle text-xs leading-none font-medium hover:text-primary"></button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <h3 class="text-sm font-medium capitalize mb-2">{{ $t('label.special_instructions') }}:</h3>
                        <textarea v-model="temp.instruction" :placeholder="$t('message.add_note')"
                                  class="w-full h-16 p-3 mb-1 rounded-xl resize-none placeholder:text-xs border border-gray-200 focus:border-primary/40 outline-none"></textarea>
                    </div>

                    <div class="shrink-0 border-t border-gray-100 p-3 sm:p-4 bg-white">
                        <button :disabled="temp.total_price <= 0"
                                @click.prevent="addToCart('item-variation-modal-' + itemIndex)"
                                class="w-full h-12 rounded-xl text-center flex items-center justify-center gap-3 bg-primary text-white font-semibold disabled:opacity-50">
                            <i class="lab-fill-bag-check text-lg leading-none"></i>
                            <span>
                                {{ $t('button.add_to_cart') }} - {{ currencyFormat(temp.total_price, setting.site_digit_after_decimal_point, setting.site_default_currency_symbol, setting.site_currency_position) }}
                            </span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </Teleport>
</template>
<script>
import {useModal} from "../../../composables/modal";
import AskEnum from "../../../enums/modules/askEnum";
import itemTypeEnum from "../../../enums/modules/itemTypeEnum.js";
import {useFrontendSettingStore} from "../../../stores/frontendSetting.js";
import appService from "../../../services/appService.js";
import {useCommonStore} from "../../../stores/common.js";
import DisplayModeEnum from "../../../enums/modules/displayModeEnum.js";
import {useFrontendItemStore} from "../../../stores/frontendItem.js";
import _ from "lodash";
import LoadingContentComponent from "../../common/LoadingContentComponent.vue";
import {Swiper, SwiperSlide} from 'swiper/vue';
import {useFrontendRestaurantStore} from "../../../stores/frontendRestaurant.js";
import {useFrontendCartStore} from "../../../stores/frontendCart.js";
import {useDineInContextStore} from "../../../stores/dineInContext.js";
import alertService from "../../../services/alertService.js";

export default {
    name: "ItemComponent",
    props: {
        items: Object,
        itemIndex: Number,
        type: Number,
        offer: {
            type: Object,
            required: false
        }
    },
    components: {
        LoadingContentComponent,
        Swiper,
        SwiperSlide
    },
    setup() {
        const {openModal, closeModal} = useModal();
        const commonStore             = useCommonStore();
        const frontendItemStore       = useFrontendItemStore();
        const frontendCartStore       = useFrontendCartStore();
        const dineInContextStore      = useDineInContextStore();
        const frontendSettingStore    = useFrontendSettingStore();
        const frontendRestaurantStore = useFrontendRestaurantStore();

        return {
            openModal,
            closeModal,
            commonStore,
            frontendItemStore,
            frontendCartStore,
            dineInContextStore,
            frontendSettingStore,
            frontendRestaurantStore
        }
    },
    data() {
        return {
            item: {},
            itemInfo: {},
            addons: {},
            addonQuantity: {},
            mainItemPrice: 0,
            mainAddonPrice: 0,
            cartModel: this.frontendCartStore,
            loading: {
                isActive: false,
            },
            enums: {
                askEnum: AskEnum,
                itemTypeEnum: itemTypeEnum,
                displayModeEnum: DisplayModeEnum,
            },
            temp: {
                name: "",
                image: "",
                item_id: 0,
                quantity: 0,
                tax: {},
                discount: 0,
                maximum_purchase_quantity: 0,
                currency_price: 0,
                convert_price: 0,
                item_variations: {
                    variations: {},
                    names: {}
                },
                item_extras: {
                    extras: [],
                    names: []
                },
                item_variation_total: 0,
                item_extra_total: 0,
                total_price: 0,
                instruction: "",
            }
        }
    },
    computed: {
        displayMode: function () {
            return this.commonStore.display_mode === this.enums.displayModeEnum.LTR ? 'ltr' : 'rtl';
        },
        setting: function () {
            return this.frontendSettingStore.lists;
        },
        restaurant: function () {
            if (this.$route.name === 'frontend.singleRestaurant') {
                this.cartModel = this.frontendCartStore;
            }
            return this.cartModel.restaurant;
        }
    },
    methods: {
        itemInfoModalShow: function (id, name, caution) {
            this.itemInfo = {
                name: name,
                caution: caution
            };
            this.openModal(id);
        },
        textShortener: function (text, number) {
            return appService.textShortener(text, number);
        },
        onlyNumber: function (e) {
            return appService.onlyNumber(e);
        },
        currencyFormat: function (amount, decimal, currency, position) {
            return appService.currencyFormat(amount, decimal, currency, position);
        },
        itemVariationModalShow: function (id, item) {
            this.loading.isActive = true;
            this.frontendItemStore.view(item.slug).then(res => {
                this.loading.isActive = false;
                this.item             = res.data.data;
                this.mainItemPrice    = Object.keys(this.$props.offer).length > 0 ? (item.convert_price - parseFloat((item.convert_price / 100) * this.$props.offer.amount).toFixed(this.setting.site_digit_after_decimal_point)) : (item.discount > 0 ? item.convert_discounted_price : item.convert_price);

                if (this.item.item_attributes.length > 0) {
                    _.forEach(this.item.item_attributes, (element) => {
                        if (typeof this.item.variations[element.id][0] !== "undefined") {
                            this.temp.item_variations.variations[this.item.variations[element.id][0].item_attribute_id] = this.item.variations[element.id][0].id;
                            this.temp.item_variations.names[element.name]                                               = this.item.variations[element.id][0].name;
                            this.temp.item_variation_total += this.item.variations[element.id][0].convert_price;
                        }
                    });
                }

                if (this.item.addons.length > 0) {
                    _.forEach(this.item.addons, (addon) => {
                        this.addonQuantity[addon.id] = 1;
                    });
                }

                this.temp.name                      = this.item.name;
                this.temp.image                     = this.item.thumb;
                this.temp.item_id                   = this.item.id;
                this.temp.quantity                  = 1;
                this.temp.tax                       = this.item.tax;
                this.temp.discount                  = 0;
                this.temp.maximum_purchase_quantity = this.item.maximum_purchase_quantity;
                this.temp.convert_price             = this.mainItemPrice
                this.temp.currency_price            = this.currencyFormat(this.mainItemPrice, this.setting.site_digit_after_decimal_point, this.setting.site_default_currency_symbol, this.setting.site_currency_position)
                this.temp.total_price               = this.mainItemPrice + this.temp.item_variation_total;

                this.openModal(id);
            }).catch((err) => {
                this.loading.isActive = false;
            })
        },
        itemVariationModalHide: function (id) {
            this.item                           = {};
            this.temp.name                      = "";
            this.temp.image                     = "";
            this.temp.item_id                   = 0;
            this.temp.quantity                  = 0;
            this.temp.tax                       = {};
            this.temp.discount                  = 0;
            this.temp.maximum_purchase_quantity = 0;
            this.temp.currency_price            = 0;
            this.temp.convert_price             = 0;
            this.temp.item_variations           = {
                variations: {},
                names: {}
            };
            this.temp.item_extras               = {
                extras: [],
                names: []
            };
            this.temp.item_variation_total      = 0;
            this.temp.item_extra_total          = 0;
            this.temp.total_price               = 0;
            this.temp.instruction               = "";
            this.mainItemPrice                  = 0;
            this.closeModal(id);
        },
        quantityUp: function () {
            if (this.temp.quantity === 0) {
                this.temp.quantity = 1;
            } else if (this.temp.quantity > this.temp.maximum_purchase_quantity) {
                this.temp.quantity = this.temp.maximum_purchase_quantity;
            }
            this.totalPriceSetup();
        },
        quantityIncrement: function () {
            this.temp.quantity++;
            if (this.temp.quantity <= 0) {
                this.temp.quantity = 1;
            } else if (this.temp.quantity > this.temp.maximum_purchase_quantity) {
                this.temp.quantity = this.temp.maximum_purchase_quantity;
            }
            this.totalPriceSetup();
        },
        quantityDecrement: function () {
            this.temp.quantity--;
            if (this.temp.quantity <= 0) {
                this.temp.quantity = 1;
            } else if (this.temp.quantity > this.temp.maximum_purchase_quantity) {
                this.temp.quantity = this.temp.maximum_purchase_quantity;
            }
            this.totalPriceSetup();
        },
        changeVariation: function (attributeId, variationId, variationName) {
            this.temp.item_variations.variations[attributeId] = variationId;
            _.forEach(this.item.item_attributes, (element) => {
                if (element.id === attributeId) {
                    this.temp.item_variations.names[element.name] = variationName;
                }
            });
            this.totalPriceSetup();
        },
        changeVariationAdjust: function (attributeId, variationId) {
            _.forEach(this.item.variations[attributeId], (variation) => {
                if (variation.id === variationId) {
                    this.changeVariation(attributeId, variationId, variation.name);
                }
            });
        },
        changeExtra: function (e, id, name) {
            if (e.target.checked) {
                this.temp.item_extras.extras.push(id);
                this.temp.item_extras.names.push(name);
            } else {
                for (let i = 0; i < this.temp.item_extras.extras.length; i++) {
                    if (this.temp.item_extras.extras[i] === id) {
                        this.temp.item_extras.extras.splice(i, 1);
                    }
                }
                for (let i = 0; i < this.temp.item_extras.names.length; i++) {
                    if (this.temp.item_extras.names[i] === name) {
                        this.temp.item_extras.names.splice(i, 1);
                    }
                }
            }
            this.totalPriceSetup();
        },
        extraCheckClass: function (id) {
            for (let i = 0; i < this.temp.item_extras.extras.length; i++) {
                if (this.temp.item_extras.extras[i] === id) {
                    return true;
                }
            }
            return false;
        },
        changeAddon: function (addon) {
            if (typeof this.addons[addon.id] === "undefined") {
                this.mainAddonPrice = (Object.keys(this.$props.offer).length > 0 ? ((addon.addon_item_convert_price) - (parseFloat(((addon.addon_item_convert_price) / 100) * this.$props.offer.amount).toFixed(this.setting.site_digit_after_decimal_point))) : (addon.addon_item_discount > 0 ? addon.addon_item_convert_discounted_price : addon.addon_item_convert_price));
                this.addons[addon.id] = {
                    name: addon.addon_item_name,
                    image: addon.thumb,
                    item_id: addon.item_addon_id,
                    quantity: this.addonQuantity[addon.id],
                    tax: addon.addon_item_tax,
                    discount: 0,
                    maximum_purchase_quantity: addon.addon_item_maximum_purchase_quantity,
                    currency_price: this.currencyFormat(this.mainAddonPrice, this.setting.site_digit_after_decimal_point, this.setting.site_default_currency_symbol, this.setting.site_currency_position),
                    convert_price: this.mainAddonPrice,
                    item_variations: {
                        variations: {},
                        names: {}
                    },
                    item_extras: {
                        extras: [],
                        names: []
                    },
                    item_variation_total: addon.variation_total_convert_price,
                    item_extra_total: 0,
                    total_price: this.mainAddonPrice,
                    instruction: "",
                };
                if (addon.variations !== "undefined" && Object.keys(addon.variations).length !== 0) {
                    _.forEach(addon.variations, (variationId, attributeId) => {
                        this.addons[addon.id].item_variations.variations[attributeId] = variationId;
                    });
                }
                if (addon.variation_names.length > 0) {
                    _.forEach(addon.variation_names, (variation) => {
                        this.addons[addon.id].item_variations.names[variation.attribute_name] = variation.name;
                    });
                }
            } else {
                delete this.addons[addon.id];
            }
            this.totalPriceSetup();
        },
        addonQuantityUp: function (id) {
            if (typeof this.addonQuantity[id] !== "undefined") {
                if (this.addonQuantity[id] === 0) {
                    this.addonQuantity[id] = 1;
                }
            }
            if (typeof this.addons[id] !== "undefined") {
                if (this.addonQuantity[id] > this.addons[id].maximum_purchase_quantity) {
                    this.addonQuantity[id] = this.addons[id].maximum_purchase_quantity;
                }
                this.addons[id].quantity = this.addonQuantity[id];
            }

            this.totalPriceSetup();
        },
        addonQuantityIncrement: function (id) {
            if (typeof this.addonQuantity[id] !== "undefined") {
                this.addonQuantity[id]++;
                if (this.addonQuantity[id] <= 0) {
                    this.addonQuantity[id] = 1;
                }
                if (typeof this.addons[id] !== "undefined") {
                    if (this.addonQuantity[id] > this.addons[id].maximum_purchase_quantity) {
                        this.addonQuantity[id] = this.addons[id].maximum_purchase_quantity;
                    }
                    this.addons[id].quantity = this.addonQuantity[id];
                }
                this.totalPriceSetup();
            }
        },
        addonQuantityDecrement: function (id) {
            if (typeof this.addonQuantity[id] !== "undefined") {
                this.addonQuantity[id]--;
                if (this.addonQuantity[id] <= 0) {
                    this.addonQuantity[id] = 1;
                    delete this.addons[id];
                }
                if (typeof this.addons[id] !== "undefined") {
                    if (this.addonQuantity[id] > this.addons[id].maximum_purchase_quantity) {
                        this.addonQuantity[id] = this.addons[id].maximum_purchase_quantity;
                    }
                    this.addons[id].quantity = this.addonQuantity[id];
                }
                this.totalPriceSetup();
            }
        },
        totalPriceSetup: function () {
            let item_variation_total = 0;
            let item_extra_total     = 0;
            let item_addon_total     = 0;
            _.forEach(this.temp.item_variations.variations, (variationId, attributeId) => {
                _.forEach(this.item.variations[attributeId], (itemVariation) => {
                    if (variationId === itemVariation.id) {
                        item_variation_total += itemVariation.convert_price;
                    }
                });
            });

            _.forEach(this.temp.item_extras.extras, (extraId) => {
                _.forEach(this.item.extras, (itemExtra) => {
                    if (extraId === itemExtra.id) {
                        item_extra_total += itemExtra.convert_price;
                    }
                });
            });

            _.forEach(this.addons, (addon) => {
                item_addon_total += ((addon.total_price + addon.item_variation_total) * addon.quantity);
            });

            this.temp.item_variation_total = item_variation_total;
            this.temp.item_extra_total     = item_extra_total;
            this.temp.total_price          = parseFloat((((this.mainItemPrice) + this.temp.item_variation_total + this.temp.item_extra_total) * this.temp.quantity) + item_addon_total);
        },
        addToCart: function (id) {
            this.itemArrays = [
                {
                    name: this.temp.name,
                    image: this.temp.image,
                    item_id: this.temp.item_id,
                    quantity: this.temp.quantity,
                    tax: this.temp.tax,
                    discount: this.temp.discount,
                    maximum_purchase_quantity: this.temp.maximum_purchase_quantity,
                    currency_price: this.temp.currency_price,
                    convert_price: this.temp.convert_price,
                    item_variations: this.temp.item_variations,
                    item_extras: this.temp.item_extras,
                    item_variation_total: this.temp.item_variation_total,
                    item_extra_total: this.temp.item_extra_total,
                    instruction: this.temp.instruction
                }
            ];

            if (this.addons !== "undefined" && Object.keys(this.addons).length !== 0) {
                _.forEach(this.addons, (addon) => {
                    this.itemArrays.push({
                        name: addon.name,
                        image: addon.image,
                        item_id: addon.item_id,
                        quantity: addon.quantity,
                        tax: addon.tax,
                        discount: addon.discount,
                        maximum_purchase_quantity: addon.maximum_purchase_quantity,
                        currency_price: addon.currency_price,
                        convert_price: addon.convert_price,
                        item_variations: addon.item_variations,
                        item_extras: addon.item_extras,
                        item_variation_total: addon.item_variation_total,
                        item_extra_total: addon.item_extra_total,
                        instruction: addon.instruction
                    });
                });
            }

            if (Object.keys(this.restaurant).length > 0) {
                if (this.restaurant.id !== this.item.restaurant_id) {
                    if (this.$route.name === 'frontend.singleRestaurant') {
                        this.cartModel = this.frontendCartStore;
                    }
                    this.cartModel.callResetCart();
                }
            }

            if (this.itemArrays.length > 0) {
                this.loading.isActive = true;
                this.frontendRestaurantStore.viewById({
                    id: this.item.restaurant_id,
                }).then(async res => {
                    this.loading.isActive = false;

                    if (this.$route.name === 'frontend.singleRestaurant') {
                        this.cartModel = this.frontendCartStore;
                    }
                    await this.cartModel.fetchCarts({
                        restaurant: res.data.data,
                        items: this.itemArrays,
                        dineIn: this.dineInPayloadForRestaurant(res.data.data)
                    }).then(res => {
                        this.item                           = {};
                        this.temp.name                      = "";
                        this.temp.image                     = "";
                        this.temp.item_id                   = 0;
                        this.temp.quantity                  = 0;
                        this.temp.tax                       = {};
                        this.temp.discount                  = 0;
                        this.temp.maximum_purchase_quantity = 0;
                        this.temp.currency_price            = 0;
                        this.temp.convert_price             = 0;
                        this.temp.item_variations           = {
                            variations: {},
                            names: {}
                        };
                        this.temp.item_extras               = {
                            extras: [],
                            names: []
                        };
                        this.temp.item_variation_total      = 0;
                        this.temp.item_extra_total          = 0;
                        this.temp.total_price               = 0;
                        this.temp.instruction               = "";
                        this.addons                         = {};
                        this.itemArrays                     = [];

                        alertService.success(this.$t('message.add_to_cart'));
                        this.closeModal(id);
                    }).catch((err) => {
                        if (err === 'max_quantity_error') {
                            alertService.error(this.$t('message.already_added_max_quantity'));
                            this.closeModal(id);
                        } else {
                            alert(err);
                        }
                    })
                }).catch((err) => {
                    this.loading.isActive = false;
                })
            }
        },
        dineInPayloadForRestaurant: function (restaurant) {
            if (!this.dineInContextStore.isActive) {
                return null;
            }
            if (!this.dineInContextStore.matchesRestaurant(restaurant?.id)
                && !this.dineInContextStore.matchesRestaurant(restaurant?.slug)) {
                return null;
            }
            return {
                table_id: this.dineInContextStore.context.table_id,
                qr_token: this.dineInContextStore.context.qr_token,
            };
        }
    }
}
</script>

<style scoped>
.restaurant-item {
    border: 1px solid rgb(10 61 40 / 0.08);
    border-radius: 1rem;
    box-shadow: 0 1px 0 rgb(10 61 40 / 0.03);
    align-items: stretch;
}
.restaurant-item:hover {
    border-color: rgb(var(--primary) / 0.22);
    box-shadow: 0 12px 28px rgb(10 61 40 / 0.08);
}
.restaurant-item__media {
    width: 7.25rem;
    min-width: 7.25rem;
    align-self: stretch;
    min-height: 8.5rem;
    border-radius: 1rem 0 0 1rem;
    background: #f3f4f6;
}
.restaurant-item__image {
    width: 100%;
    height: 100%;
    min-height: 8.5rem;
    display: block;
}
.restaurant-item__halal {
    left: 0.4rem;
    bottom: 0.4rem;
    padding: 0.15rem;
    border-radius: 999px;
    background: rgb(255 255 255 / 0.92);
}
.restaurant-item__veg {
    left: 0.4rem;
    bottom: 0.4rem;
    display: flex;
    align-items: center;
    justify-content: center;
    width: 1.25rem;
    height: 1.25rem;
    border-radius: 999px;
    background: rgb(255 255 255 / 0.95);
    box-shadow: 0 1px 4px rgb(0 0 0 / 0.12);
}
.restaurant-item__halal {
    left: auto;
    right: 0.4rem;
}
.restaurant-item__add {
    border-radius: 0.45rem;
}
.restaurant-item__desc :deep(p),
.restaurant-item__desc :deep(*) {
    display: inline;
    margin: 0;
    padding: 0;
}
.restaurant-item button.bg-primary {
    border-radius: 0.55rem;
}
@media (min-width: 640px) {
    .restaurant-item__media {
        width: 8rem;
        min-width: 8rem;
    }
}
</style>

<style>
.item-customize-modal {
    max-height: min(92dvh, 920px);
    border-radius: 1.25rem 1.25rem 0 0;
    box-shadow: 0 24px 64px rgb(10 61 40 / 0.28);
}
@media (min-width: 640px) {
    .item-customize-modal {
        border-radius: 1.25rem;
    }
}
</style>
