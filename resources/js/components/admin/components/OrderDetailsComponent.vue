<template>
    <div class="col-12">
        <div class="row">
            <div class="col-12" :class="!fullStyle ? 'sm:col-8' : ''">
                <div class="db-card p-4">
                    <div class="flex flex-wrap gap-y-5 items-end justify-between">
                        <div>
                            <div class="flex flex-wrap items-start gap-y-2 gap-x-6 mb-5">
                                <p class="text-2xl font-medium">{{ $t('label.order_id') }}:
                                    <span class="text-heading">#{{ order.order_serial_no }}</span>
                                </p>
                                <div class="flex items-center gap-2 mt-1.5">
                                    <span :class="statusClass(order.payment_status)" class="!text-xs !rounded-md">
                                        {{ enums.paymentStatusEnumArray[order.payment_status] }}
                                    </span>
                                    <span :class="orderStatusClass(order.status)" class="!text-xs">
                                        {{ enums.orderStatusEnumArray[order.status] }}
                                    </span>
                                    <span v-if="order.is_advance_order === enums.isAdvanceOrderEnum.YES"
                                          class="text-xs bg-[#1AB759] text-white capitalize px-2 py-1 rounded-md">
                                        {{ $t('label.advance') }}
                                    </span>
                                </div>
                            </div>
                            <ul class="flex flex-col gap-2">
                                <li class="flex items-center gap-2">
                                    <i class="lab lab-line-calendar lab-font-size-16"></i>
                                    <span class="text-xs">{{ order.order_datetime }}</span>
                                </li>
                                <li class="text-xs">
                                    {{ $t('label.payment_type') }}:
                                    <span class="text-heading" v-if="order.order_type === enums.orderTypeEnum.POS">
                                        {{ enums.posPaymentMethodArray[order.pos_detail?.payment_method] }}
                                    </span>
                                    <span v-else>
                                        <span class="text-heading" v-if="order.transaction">
                                            {{ order.transaction.payment_method }}
                                        </span>
                                        <span v-else class="text-heading">
                                            {{ enums.paymentTypeEnumArray[order.payment_method] }}
                                        </span>
                                    </span>
                                </li>
                                <li class="text-xs">
                                    {{ $t('label.order_type') }}:
                                    <span class="text-heading">
                                        {{ enums.orderTypeEnumArray[order.order_type] }}
                                    </span>
                                </li>
                                <li class="text-xs" v-if="order.table">
                                    {{ $t('label.table') }}:
                                    <span class="text-heading">
                                        {{ order.table.name || order.table.table_number }}
                                        <span v-if="order.table.table_number && order.table.name">
                                            ({{ order.table.table_number }})
                                        </span>
                                    </span>
                                </li>
                                <li class="text-xs">
                                    {{ $t('label.delivery_time') }}:
                                    <span
                                        :class="order.is_advance_order === enums.isAdvanceOrderEnum.YES ? 'text-primary' : ''"
                                        class="text-heading">
                                        {{ order.delivery_date }} {{ order.delivery_time }}
                                    </span>
                                </li>
                                <li v-if="order.is_received === enums.askEnum.YES && order.order_type === enums.orderTypeEnum.DELIVERY"
                                    class="text-xs">
                                    {{ $t('label.order_collected') }}:
                                    <span class="text-heading">
                                        {{ $t('label.yes') }}
                                    </span>
                                </li>
                                <li v-if="order.is_received === enums.askEnum.NO && order.order_type === enums.orderTypeEnum.DELIVERY"
                                    class="text-xs">
                                    {{ $t('label.order_collected') }}:
                                    <span class="text-heading">
                                        {{ $t('label.no') }}
                                    </span>
                                </li>

                                <li v-if="order.order_type !== enums.orderTypeEnum.POS"
                                    class="text-xs">
                                    {{ $t('label.cutlery') }}:
                                    <span class="text-heading">
                                        {{ enums.cutleryArray[order.cutlery] }}
                                    </span>
                                </li>
                                <li v-if="order.token" class="text-xs">
                                    {{ $t('label.token_no') }}:
                                    <span class="text-heading">
                                        #{{ order.token }}
                                    </span>
                                </li>
                            </ul>
                        </div>
                        <slot v-if="fullStyle"></slot>
                    </div>
                </div>
            </div>

            <div v-if="!fullStyle" class="col-12 sm:col-4">
                <slot></slot>
            </div>
        </div>
    </div>

    <div class="col-12 sm:col-8">
        <div class="row">
            <div class="col-12">
                <div class="db-card">
                    <div class="db-card-header">
                        <h3 class="db-card-title">{{ $t('label.order_details') }}</h3>
                    </div>
                    <div class="db-card-body">
                        <div class="pl-3">
                            <div class="mb-3 pb-3 border-b last:mb-0 last:pb-0 last:border-b-0 border-gray-2"
                                 v-if="orderItems.length > 0" v-for="item in orderItems" :key="item">
                                <div class="flex items-center gap-3 relative">
                                    <h3 class="absolute top-5 -left-3 -right-3 text-sm w-[26px] h-[26px] leading-[26px] text-center rounded-full text-white bg-heading">
                                        {{ item.quantity }}</h3>
                                    <img class="w-16 h-16 rounded-lg flex-shrink-0" :src="item.item_image"
                                         alt="thumbnail">

                                    <div class="w-full">
                                        <a href="#"
                                           class="text-sm font-medium capitalize transition text-heading hover:underline">
                                            {{ item.item_name }}
                                        </a>
                                        <p v-if="item.item_variations.length !== 0" class="capitalize text-xs mb-1.5">
                                            <span v-for="(variation, index) in item.item_variations">
                                                {{ variation.variation_name }}: {{ variation.name }}<span
                                                v-if="index + 1 < item.item_variations.length">,&nbsp;</span>
                                            </span>
                                        </p>
                                        <h3 class="text-xs font-semibold">{{ item.total_currency_price }}</h3>
                                    </div>
                                </div>

                                <ul v-if="item.item_extras.length > 0 || item.instruction !== ''"
                                    class="flex flex-col gap-1.5 mt-2">
                                    <li class="flex gap-1" v-if="item.item_extras.length > 0">
                                        <h3 class="capitalize text-xs w-fit whitespace-nowrap">{{
                                                $t('label.extras')
                                            }}:</h3>
                                        <p class="text-xs">
                                            <span v-for="(extra, index) in item.item_extras">
                                                {{ extra.name }}<span
                                                v-if="index + 1 < item.item_extras.length">,&nbsp;</span>
                                            </span>
                                        </p>
                                    </li>
                                    <li class="flex gap-1" v-if="item.instruction !== ''">
                                        <h3 class="capitalize text-xs w-fit whitespace-nowrap">
                                            {{ $t('label.instruction') }}:
                                        </h3>
                                        <p class="text-xs">{{ item.instruction }}</p>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12"
                 v-if="order.status === enums.orderStatusEnum.REJECTED || order.status === enums.orderStatusEnum.RETURNED">
                <div class="db-card">
                    <div class="db-card-header">
                        <h3 class="db-card-title">{{ $t('label.reason') }}</h3>
                    </div>
                    <div class="db-card-body">
                        <p>{{ order.reason }}</p>
                        <div v-if="order.return_images && order.return_images.length > 0"
                             class="flex flex-wrap gap-3 items-center mt-4">
                            <p v-for="(image, index) in order.return_images" :key="index">
                                <img :src="image" @click="showReturnImageModal(image)" alt="Return Image"
                                     class="w-24 h-24 object-cover rounded-lg shadow-md border"/>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-12 sm:col-4">
        <div class="row">
            <div class="col-12">
                <div class="db-card p-1">
                    <ul class="flex flex-col gap-2 p-3 border-b border-dashed border-[#EFF0F6]">
                        <li class="flex items-center justify-between text-heading">
                            <span class="text-sm leading-6 capitalize">{{ $t('label.subtotal') }}</span>
                            <span class="text-sm leading-6 capitalize font-semibold">{{
                                    order.subtotal_currency_price
                                }}</span>
                        </li>
                        <li v-if="order.order_type === enums.orderTypeEnum.DELIVERY"
                            class="flex items-center justify-between text-heading">
                            <span class="text-sm leading-6">{{
                                    $t('label.delivery_fee')
                                }} {{
                                    order.delivery_fee <= 0 ? '(' + $t('label.free').toLowerCase() + ')' : ''
                                }}</span>
                            <span class="text-sm leading-6 capitalize font-semibold">
                                {{ order.delivery_fee_currency_price }}
                            </span>
                        </li>
                        <li v-if="order.service_fee > 0" class="flex items-center justify-between text-heading">
                            <span class="text-sm leading-6">{{ $t('label.service_fee') }}</span>
                            <span class="text-sm leading-6 capitalize font-semibold">
                                {{ order.service_fee_currency_price }}
                            </span>
                        </li>
                        <li v-if="order.order_type === enums.orderTypeEnum.DELIVERY && order.rider_tip > 0"
                            class="flex items-center justify-between text-heading">
                            <span class="text-sm leading-6">{{ $t('label.rider_tip') }}</span>
                            <span class="text-sm leading-6 capitalize font-semibold">
                                {{ order.rider_tip_currency_price }}
                            </span>
                        </li>
                        <li v-if="order.total_tax > 0" class="flex items-center justify-between text-heading">
                            <span class="text-sm leading-6">{{ $t('label.tax') }}</span>
                            <span class="text-sm leading-6 capitalize font-semibold">
                                {{ order.total_tax_currency_price }}
                            </span>
                        </li>
                        <li v-if="order.discount > 0" class="flex items-center justify-between text-heading">
                            <span class="text-sm leading-6 capitalize">{{ $t('label.discount') }}</span>
                            <span class="text-sm leading-6 capitalize font-semibold">- {{
                                    order.discount_currency_price
                                }}</span>
                        </li>
                    </ul>
                    <div class="flex items-center justify-between p-3">
                        <h4 class="text-sm leading-6 font-bold capitalize">{{ $t('label.total') }}</h4>
                        <h5 class="text-sm leading-6 font-bold capitalize">
                            {{ order.total_currency_price }}
                        </h5>
                    </div>
                </div>
            </div>

            <div v-if="orderUser" class="col-12">
                <div class="db-card">
                    <div class="db-card-header">
                        <h3 class="db-card-title">{{ $t('label.customer_information') }}</h3>
                    </div>
                    <div class="db-card-body">
                        <div class="flex items-center gap-3 mb-4">
                            <img class="w-8 rounded-full aspect-square object-cover flex-shrink-0"
                                 :src="orderUser.image" alt="avatar">
                            <h4 class="font-semibold text-sm capitalize text-[#374151]">
                                {{ textShortener(orderUser.name, 20) }}
                            </h4>
                        </div>
                        <ul class="flex flex-col gap-3 py-4 border-t border-[#EFF0F6]">
                            <li v-if="orderUser.email" class="flex items-center gap-2.5">
                                <i class="lab lab-line-mail lab-font-size-14"></i>
                                <span class="text-xs">{{ orderUser.email }}</span>
                            </li>
                            <li v-if="orderUser.phone" class="flex items-center gap-2.5">
                                <i class="lab lab-line-calling lab-font-size-14"></i>
                                <span class="text-xs">{{ orderUser.country_code + '' + orderUser.phone }}</span>
                            </li>
                            <li class="flex items-center gap-2.5"
                                v-if="order.order_type === enums.orderTypeEnum.DELIVERY">
                                <i class="lab lab-line-location lab-font-size-14"></i>
                                <span class="text-xs w-full max-w-[300px]">
                                    {{ orderAddress?.apartment ? orderAddress?.apartment + ', ' : '' }}
                                    {{ orderAddress?.address }}
                                </span>
                                <MapComponent v-if="orderAddress" :orderAddress="orderAddress"/>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="col-12" v-if="(authInfo.role_id === enums.roleEnum.DELIVERY_BOY || authInfo.role_id === enums.roleEnum.ADMIN || routeName === 'admin.returnOrders.show' || routeName === 'admin.orderTracker.list') && orderRestaurant && Object.keys(orderRestaurant).length > 0">
                <div class="db-card">
                    <div class="db-card-header">
                        <h3 class="db-card-title">{{ $t('label.restaurant_information') }}</h3>
                    </div>
                    <div class="db-card-body">
                        <div class="flex items-center gap-3 mb-4">
                            <img class="w-8 h-8 object-cover rounded-full" :src="orderRestaurant.logo" alt="avatar">
                            <h4 class="font-semibold text-sm capitalize text-[#374151]">
                                {{ textShortener(orderRestaurant.name, 20) }}
                            </h4>
                        </div>
                        <ul class="flex flex-col gap-3 py-4 border-t border-[#EFF0F6]">
                            <li v-if="orderRestaurant.email" class="flex items-center gap-2.5">
                                <i class="lab lab-line-mail lab-font-size-14"></i>
                                <span class="text-xs">{{ orderRestaurant.email }}</span>
                            </li>
                            <li v-if="orderRestaurant.phone" class="flex items-center gap-2.5">
                                <i class="lab lab-line-calling lab-font-size-14"></i>
                                <span class="text-xs">{{
                                        orderRestaurant.country_code + '' + orderRestaurant.phone
                                    }}</span>
                            </li>
                            <li class="flex items-center gap-2.5"
                                v-if="order.order_type === enums.orderTypeEnum.DELIVERY">
                                <i class="lab lab-line-location lab-font-size-14"></i>
                                <span class="text-xs w-full max-w-[300px]">
                                    {{ orderRestaurant?.apartment ? orderRestaurant?.apartment + ', ' : '' }}
                                    {{ orderRestaurant?.address }}
                                </span>
                                <RestaurantMapComponent v-if="orderRestaurant" :orderRestaurant="orderRestaurant"/>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="col-12"
                 v-if="(authInfo.role_id === enums.roleEnum.ADMIN || defaultAccess.restaurant_id > 0 || routeName === 'admin.returnOrders.show' || routeName === 'admin.orderTracker.list') && orderDeliveryBoy && Object.keys(orderDeliveryBoy).length > 0">
                <div class="db-card">
                    <div class="db-card-header">
                        <h3 class="db-card-title">{{ $t('label.delivery_boy_information') }}</h3>
                    </div>
                    <div class="db-card-body">
                        <div class="flex items-center gap-3 mb-4">
                            <img class="w-8 h-8 object-cover rounded-full" :src="orderDeliveryBoy.image" alt="avatar">
                            <h4 class="font-semibold text-sm capitalize text-[#374151]">
                                {{ textShortener(orderDeliveryBoy.name, 20) }}
                            </h4>
                        </div>
                        <ul class="flex flex-col gap-3 py-4 border-t border-[#EFF0F6]">
                            <li v-if="orderDeliveryBoy.email" class="flex items-center gap-2.5">
                                <i class="lab lab-line-mail lab-font-size-14"></i>
                                <span class="text-xs">{{ orderDeliveryBoy.email }}</span>
                            </li>
                            <li v-if="orderDeliveryBoy.phone" class="flex items-center gap-2.5">
                                <i class="lab lab-line-calling lab-font-size-14"></i>
                                <span class="text-xs">{{
                                        orderDeliveryBoy.country_code + '' + orderDeliveryBoy.phone
                                    }}</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="returnImage" class="modal">
        <div class="modal-dialog max-w-lg">
            <div class="modal-header">
                <h3 class="modal-title">{{ $t("label.return_order_image") }}</h3>
                <button class="modal-close lab-line-close font-bold text-base text-slate-400 hover:text-red-500"
                        @click="hideReturnImageModal"></button>
            </div>
            <div class="modal-body" v-if="returnPreviewImage">
                <img :src="returnPreviewImage" class="w-full object-fill" alt="return-order-image"/>
            </div>
        </div>
    </div>

</template>
<script>
import MapComponent from "./order/MapComponent.vue";
import LoadingComponent from "../../common/LoadingComponent.vue";
import {useOnlineOrderStore} from "../../../stores/onlineOrder.js";
import isAdvanceOrderEnum from "../../../enums/modules/isAdvanceOrderEnum.js";
import paymentStatusEnum from "../../../enums/modules/paymentStatusEnum.js";
import orderStatusEnum from "../../../enums/modules/orderStatusEnum.js";
import paymentTypeEnum from "../../../enums/modules/paymentTypeEnum.js";
import orderTypeEnum from "../../../enums/modules/orderTypeEnum.js";
import appService from "../../../services/appService.js";
import {useAuthStore} from "../../../stores/auth.js";
import roleEnum from "../../../enums/modules/roleEnum.js";
import RestaurantMapComponent from "./order/RestaurantMapComponent.vue";
import posPaymentMethodEnum from "../../../enums/modules/posPaymentMethodEnum.js";
import activityEnum from "../../../enums/modules/activityEnum.js";
import {useDefaultAccessStore} from "../../../stores/defaultAccess.js";
import askEnum from "../../../enums/modules/askEnum.js";
import {useModal} from "../../../composables/modal.js";

export default {
    name: "OrderDetailsComponent",
    props: {
        order: {
            type: Object,
            required: true,
            default: null
        },
        orderItems: {
            type: Object,
            required: true,
            default: null
        },
        orderUser: {
            type: Object,
            required: false,
            default: null
        },
        orderRestaurant: {
            type: Object,
            required: false,
            default: null
        },
        orderDeliveryBoy: {
            type: Object,
            required: false,
            default: null
        },
        orderAddress: {
            type: Object,
            required: false,
            default: null
        },
        fullStyle: {
            type: Boolean,
            require: false,
            default: true
        },
    },
    components: {MapComponent, RestaurantMapComponent, LoadingComponent},
    setup() {
        const onlineOrderStore        = useOnlineOrderStore();
        const authStore               = useAuthStore();
        const defaultAccessStore      = useDefaultAccessStore();
        const {openModal, closeModal} = useModal();

        return {
            onlineOrderStore,
            authStore,
            defaultAccessStore,
            openModal,
            closeModal
        }
    },
    data() {
        return {
            enums: {
                askEnum: askEnum,
                roleEnum: roleEnum,
                isAdvanceOrderEnum: isAdvanceOrderEnum,
                paymentStatusEnum: paymentStatusEnum,
                activityEnum: activityEnum,
                paymentStatusEnumArray: {
                    [paymentStatusEnum.PAID]: this.$t("label.paid"),
                    [paymentStatusEnum.UNPAID]: this.$t("label.unpaid")
                },
                orderStatusEnum: orderStatusEnum,
                orderStatusEnumArray: {
                    [orderStatusEnum.PENDING]: this.$t("label.pending"),
                    [orderStatusEnum.ACCEPT]: this.$t("label.accept"),
                    [orderStatusEnum.PREPARING]: this.$t("label.preparing"),
                    [orderStatusEnum.PREPARED]: this.$t("label.prepared"),
                    [orderStatusEnum.OUT_FOR_DELIVERY]: this.$t("label.out_for_delivery"),
                    [orderStatusEnum.DELIVERED]: this.$t("label.delivered"),
                    [orderStatusEnum.CANCELED]: this.$t("label.canceled"),
                    [orderStatusEnum.REJECTED]: this.$t("label.rejected"),
                    [orderStatusEnum.RETURNED]: this.$t("label.returned")
                },
                paymentTypeEnum: paymentTypeEnum,
                paymentTypeEnumArray: {
                    [paymentTypeEnum.CASH_ON_DELIVERY]: this.$t("label.cash_on_delivery"),
                },
                posPaymentMethodArray: {
                    [posPaymentMethodEnum.CASH]: this.$t("label.cash"),
                    [posPaymentMethodEnum.CARD]: this.$t("label.card"),
                    [posPaymentMethodEnum.MOBILE_BANKING]: this.$t("label.mfs"),
                    [posPaymentMethodEnum.OTHER]: this.$t("label.other"),
                },
                orderTypeEnum: orderTypeEnum,
                orderTypeEnumArray: {
                    [orderTypeEnum.DELIVERY]: this.$t("label.delivery"),
                    [orderTypeEnum.TAKEAWAY]: this.$t("label.takeaway"),
                    [orderTypeEnum.POS]: this.$t("label.pos"),
                    [orderTypeEnum.DINING_TABLE]: this.$t("label.dining_table")
                },
                cutleryArray: {
                    [activityEnum.ENABLE]: this.$t('label.included'),
                    [activityEnum.DISABLE]: this.$t('label.not_included')
                }
            },
            routeName: "",
            returnPreviewImage: null
        }
    },
    computed: {
        authInfo: function () {
            return this.authStore.info;
        },
        defaultAccess: function () {
            return this.defaultAccessStore.lists;
        }
    },
    mounted() {
        this.routeName = this.$route.name;
    },
    methods: {
        showReturnImageModal: function (image) {
            this.returnPreviewImage = image;
            this.openModal("returnImage");
        },
        hideReturnImageModal: function () {
            this.returnPreviewImage = null;
            this.closeModal('returnImage');
        },
        statusClass: function (status) {
            return appService.statusClass(status);
        },
        orderStatusClass: function (status) {
            return appService.orderStatusClass(status);
        },
        textShortener: function (text, number = 30) {
            return appService.textShortener(text, number);
        }
    }
}
</script>
