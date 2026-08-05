<template>
    <div v-for="item in items" :key="item" v-show="type === null || type === item.item_type"
         class="group flex rounded-lg overflow-hidden border border-slate-100 bg-white transition-all duration-300 hover:shadow-hover">
        <figure class="flex-shrink-0 overflow-hidden relative">
            <label v-if="Object.keys(offer).length > 0"
                   class="absolute top-1.5 ltr:left-1.5 rtl:right-1.5 z-20 whitespace-nowrap text-xs py-0.5 px-1.5 rounded bg-primary text-white">
                {{ $t('message.percentage_off', {discount: offer.amount}) }}
            </label>
            <label v-else-if="item.discount > 0 || Object.keys(offer).length > 0"
                   class="absolute top-1.5 ltr:left-1.5 rtl:right-1.5 z-20 whitespace-nowrap text-xs py-0.5 px-1.5 rounded bg-primary text-white">
                {{ item.discount_option }} {{ $t('label.off') }}
            </label>

            <img :src="item.thumb" alt="menu"
                 @click.prevent="itemVariationModalShow('item-variation-modal-' + itemIndex, item)"
                 class="w-28 h-28 object-cover group-hover:rotate-3 group-hover:scale-110 transition-all duration-500 cursor-pointer">
            <div v-if="parseInt(item.halal) === parseInt(enums.askEnum.YES)"
                 class="absolute bottom-1.5 ltr:left-1.5 rtl:right-1.5 z-20 flex items-center gap-1.5">
                <img v-if="parseInt(item.halal) === parseInt(enums.askEnum.YES)" :src="setting.image_halal" alt="halal"
                     class="w-6 flex-shrink-0">
            </div>
        </figure>
        <div class="flex-auto flex flex-col justify-between px-3 py-2 overflow-hidden">
            <div>
                <div class="flex items-start gap-3 mb-2">
                    <h3 @click.prevent="itemVariationModalShow('item-variation-modal-' + itemIndex, item)"
                        class="cursor-pointer text-sm font-medium capitalize whitespace-nowrap overflow-hidden text-ellipsis transition-all duration-300 group-hover:text-primary">
                        {{ item.name }}
                    </h3>
                    <button :key="item" v-if="item.caution"
                            @click.prevent="itemInfoModalShow('item-info-modal-' + itemIndex, item.name, item.caution)"
                            class="lab-fill-info mt-0.5 text-paragraph"></button>
                </div>
                <p class="editor-show-design text-xs text-paragraph"
                   v-html="textShortener(item.description_alt, 65)"></p>
            </div>
            <div class="flex items-center justify-between">
                <div v-if="Object.keys(offer).length > 0" class="flex items-center gap-2">
                    <del class="text-xs font-medium text-paragraph">
                        {{ item.currency_price }}
                    </del>
                    <span class="text-sm font-medium text-heading">
                        {{
                            currencyFormat((item.convert_price - parseFloat((item.convert_price / 100) * offer.amount).toFixed(setting.site_digit_after_decimal_point)), setting.site_digit_after_decimal_point, setting.site_default_currency_symbol, setting.site_currency_position)
                        }}
                    </span>
                </div>
                <div v-else class="flex items-center gap-2">
                    <del v-if="item.discount > 0" class="text-xs font-medium text-paragraph">
                        {{ item.currency_price }}
                    </del>
                    <span class="text-sm font-medium text-heading">
                        {{ item.discount > 0 ? item.currency_discounted_price : item.currency_price }}
                    </span>
                </div>
                <button @click.prevent="itemVariationModalShow('item-variation-modal-' + itemIndex, item)"
                        class="w-fit flex items-center gap-1 h-6 px-2 rounded-3xl shadow text-primary bg-white">
                    <i class="lab-fill-bag text-sm"></i>
                    <span class="text-xs capitalize">{{ $t('button.add') }}</span>
                </button>
            </div>
        </div>
    </div>

    <div :id="'item-info-modal-' + itemIndex"
         class="fixed inset-0 z-60 p-3 w-screen h-dvh overflow-y-auto bg-black/50 transition-all duration-300 opacity-0 invisible">
        <div class="max-w-lg w-full rounded-xl mx-auto bg-white transition-all duration-300">
            <div class="flex items-start justify-between gap-4 py-4 px-6">
                <h3 class="text-lg font-semibold capitalize">{{ itemInfo.name }}</h3>
                <button @click.prevent="closeModal('item-info-modal-' + itemIndex)"
                        class="lab-line-circle-cross text-lg text-danger"></button>
            </div>
            <div v-html="itemInfo.caution" class="p-6 ql-ul-set editor-show-design"></div>
        </div>
    </div>

    <div v-if="item" :id="'item-variation-modal-' + itemIndex"
         class="fixed inset-0 z-50 p-3 w-screen h-dvh overflow-y-auto bg-black/50 transition-all duration-300 opacity-0 invisible">
        <div class="max-w-2xl  w-full rounded-xl mx-auto bg-white transition-all duration-300">
            <div v-if="typeof item === 'object' && Object.keys(item).length > 0" class="p-6">
                <LoadingContentComponent :props="loading"/>
                <div class="flex gap-4 mb-4">
                    <img class="w-28 h-28 object-cover rounded-lg flex-shrink-0" :src="item.thumb" alt="menu">
                    <div class="flex-auto overflow-hidden">
                        <div class="flex items-center gap-3 mb-2">
                            <h3 class="text-lg font-medium capitalize whitespace-nowrap overflow-hidden text-ellipsis">
                                {{ item.name }}
                            </h3>
                            <button v-if="item.caution" restaurants
                                    @click.prevent="itemInfoModalShow('item-info-modal-' + itemIndex, item.name, item.caution)"
                                    class="lab-fill-info text-paragraph"></button>
                        </div>
                        <p v-if="item.description_alt" class="text-sm text-paragraph text-justify mb-3">
                            {{ item.description_alt }}
                        </p>

                        <div v-if="Object.keys(offer).length > 0" class="flex items-center gap-2">
                            <del class="font-medium leading-none text-paragraph">
                                {{ item.currency_price }}
                            </del>
                            <span class="font-medium leading-none text-heading">
                                {{
                                    currencyFormat((item.convert_price - parseFloat((item.convert_price / 100) * offer.amount).toFixed(setting.site_digit_after_decimal_point)), setting.site_digit_after_decimal_point, setting.site_default_currency_symbol, setting.site_currency_position)
                                }}
                            </span>
                        </div>
                        <div v-else class="flex items-center gap-2">
                            <del v-if="item.discount > 0" class="font-medium leading-none text-paragraph">
                                {{ item.currency_price }}
                            </del>
                            <span class="font-medium leading-none text-heading">
                                {{ item.discount > 0 ? item.currency_discounted_price : item.currency_price }}
                            </span>
                        </div>
                    </div>
                    <button @click.prevent="itemVariationModalHide('item-variation-modal-' + itemIndex)"
                            class="flex-shrink-0 self-start">
                        <i class="lab-line-circle-cross text-danger"></i>
                    </button>
                </div>

                <div class="flex items-center gap-2 mb-4">
                    <h3 class="text-sm font-medium capitalize">{{ $t('label.quantity') }}:</h3>
                    <div class="flex items-center w-20 h-6 gap-1 p-1 rounded-full bg-gray-100">
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
                    <Swiper :dir="displayMode" :speed="1000" :spaceBetween="10" slidesPerView="auto">
                        <SwiperSlide :for="variation.item_attribute_id + '-' + variation.name"
                                     v-for="variation in item.variations[item.item_attributes[0].id]" :key="variation"
                                     @click="changeVariation(variation.item_attribute_id, variation.id, variation.name)"
                                     :class="temp.item_variations.variations[variation.item_attribute_id] === variation.id ? 'border-primary/50 bg-primary/10' : 'border-gray-100 bg-gray-100'"
                                     class="!flex items-center gap-2 !w-fit !h-12 px-2.5 rounded-lg cursor-pointer border">
                            <input type="radio" :id="variation.item_attribute_id + '-' + variation.name"
                                   :value="variation.id"
                                   v-model="temp.item_variations.variations[variation.item_attribute_id]"
                                   class="cs-custom-radio">
                            <dl class="flex-auto overflow-hidden">
                                <dt class="text-xs capitalize whitespace-nowrap text-heading">{{
                                        textShortener(variation.name, 15)
                                    }}
                                </dt>
                                <dd class="text-xs font-medium mt-0.5 text-heading">+{{ variation.currency_price }}</dd>
                            </dl>
                        </SwiperSlide>
                    </Swiper>
                </div>

                <div class="mb-4" v-else-if="item.item_attributes.length > 1">
                    <div class="row">
                        <div v-for="item_attribute in item.item_attributes" class="col-12 sm:col-6">
                            <label class="text-sm leading-6 block font-medium capitalize mb-1.5 text-heading">
                                {{ item_attribute.name }}:
                            </label>
                            <div class="relative">
                                <i class="lab lab-line-chevron-down text-sm absolute top-1/2 ltr:right-2.5 rtl:left-2.5 -translate-y-1/2 lab-font-size-16"></i>
                                <select
                                    @change.prevent="changeVariationAdjust(item_attribute.id, temp.item_variations.variations[item_attribute.id])"
                                    v-model="temp.item_variations.variations[item_attribute.id]"
                                    class="text-xs capitalize rounded-lg h-10 w-full py-1.5 px-2.5 appearance-none transition border border-[#EFF0F6] text-heading hover:border-primary/30">
                                    <option :value="variation.id"
                                            v-for="variation in item.variations[item_attribute.id]" :key="variation">
                                        {{ variation.name }} +{{ variation.currency_price }}
                                    </option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mb-4" v-if="item.extras.length > 0">
                    <h3 class="text-sm font-medium capitalize mb-2">{{ $t('label.extras') }}:</h3>
                    <Swiper :dir="displayMode" :speed="1000" :spaceBetween="16" slidesPerView="auto">
                        <SwiperSlide v-for="extra in item.extras" :key="extra"
                                     :class="extraCheckClass(extra.id) ? 'border-primary/50 bg-primary/10' : 'border-gray-100 bg-gray-100'"
                                     class="!flex !w-fit !h-12 rounded-lg border">
                            <label :for="extra.id + '-' + extra.name"
                                   class="!flex items-center gap-2 !w-fit !h-12 px-2.5 rounded-lg cursor-pointer ">
                                <input type="checkbox" class="cs-custom-checkbox"
                                       @change="changeExtra($event, extra.id, extra.name)"
                                       :id="extra.id + '-' + extra.name" :value="extra.id">
                                <dl class="flex-auto overflow-hidden">
                                    <dt class="text-xs capitalize whitespace-nowrap">{{
                                            textShortener(extra.name, 15)
                                        }}
                                    </dt>
                                    <dd class="text-xs font-medium mt-0.5">
                                        +{{ extra.currency_price }}
                                    </dd>
                                </dl>
                            </label>
                        </SwiperSlide>
                    </Swiper>
                </div>

                <div class="mb-4" v-if="item.addons.length > 0">
                    <h3 class="text-sm font-medium capitalize mb-2">{{ $t('label.addons') }}:</h3>
                    <Swiper :dir="displayMode" :speed="1000" :spaceBetween="10" slidesPerView="auto">
                        <SwiperSlide v-for="addon in item.addons" :key="addon" class="!w-fit">
                            <div
                                :class="addons[addon.id] ? 'border-primary/50 bg-primary/10' : 'border-gray-100 bg-gray-100'"
                                class="!flex !w-fit rounded-lg overflow-hidden cursor-pointer border border-gray-100 bg-gray-100">
                                <img class="w-[72px] h-[72px] object-cover flex-shrink-0" :src="addon.thumb"
                                     @click.prevent="changeAddon(addon)" alt="addon">
                                <div class="flex-auto flex flex-col justify-between p-2">
                                    <div class="flex items-center gap-1">
                                        <h5 class="text-xs capitalize whitespace-nowrap overflow-hidden text-ellipsis max-w-[120px]">
                                            {{ addon.addon_item_name }}
                                        </h5>
                                        <button v-if="addon.caution"
                                                @click.prevent="itemInfoModalShow('item-info-modal-' + itemIndex, addon.addon_item_name, addon.caution)"
                                                class="lab-fill-info text-sm leading-none text-paragraph"></button>
                                    </div>
                                    <p v-if="addon.variation_names.length > 0" class="inline-flex gap-0.5 mt-1">
                                        <span v-for="(variation, key) in addon.variation_names"
                                              class="text-[10px] leading-none capitalize text-paragraph">
                                            {{
                                                textShortener(variation.name, 8)
                                            }}{{ addon.variation_names.length !== key + 1 ? ', ' : '' }}
                                        </span>
                                    </p>

                                    <div class="flex items-center justify-between gap-2 mt-2">
                                        <h6 v-if="Object.keys(offer).length > 0" class="text-xs font-medium">
                                            {{
                                                currencyFormat(((addon.addon_item_convert_price + addon.variation_total_convert_price) - ((addon.addon_item_convert_price / 100) * offer.amount)), setting.site_digit_after_decimal_point, setting.site_default_currency_symbol, setting.site_currency_position)
                                            }}
                                        </h6>
                                        <h6 v-else class="text-xs font-medium">
                                            {{ addon.total_currency_price }}
                                        </h6>
                                        <button v-if="!addons[addon.id]" @click.prevent="changeAddon(addon)"
                                                class="w-fit flex items-center gap-1 h-[18px] px-1.5 rounded-3xl shadow-filter text-primary bg-white">
                                            <i class="lab-fill-bag text-xs"></i>
                                            <span class="text-[10px] leading-none capitalize">
                                                {{ $t('button.add') }}
                                            </span>
                                        </button>
                                        <div v-if="addons[addon.id]" class="flex items-center w-12 h-3 gap-0.5">
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
                        </SwiperSlide>
                    </Swiper>
                </div>

                <h3 class="text-sm font-medium capitalize mb-2">{{ $t('label.special_instructions') }}:</h3>
                <textarea v-model="temp.instruction" :placeholder="$t('message.add_note')" class="w-full h-14 p-2 mb-4 rounded-lg resize-none  placeholder:text-xs border border-gray-200"></textarea>
                <button :disabled="temp.total_price <= 0" @click.prevent="addToCart('item-variation-modal-' + itemIndex)" class="w-full h-12 rounded-3xl text-center flex items-center justify-center gap-3 bg-primary text-white">
                    <i class="lab-fill-bag-check text-lg leading-none"></i>
                    <span>
                        {{ $t('button.add_to_cart') }} - {{ currencyFormat(temp.total_price, setting.site_digit_after_decimal_point, setting.site_default_currency_symbol, setting.site_currency_position) }}
                    </span>
                </button>
            </div>
        </div>
    </div>
</template>
<script>
import {useModal} from "../../../composables/modal";
import AskEnum from "../../../enums/modules/askEnum";
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
