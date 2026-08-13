<template>
    <LoadingComponent :props="loading" />
    <section class="pt-8 pb-24 md:pb-16">
        <div class="container max-w-3xl">
            <router-link :to="{ name: 'frontend.myOrder' }" class="mb-6 inline-flex items-center gap-2 text-primary">
                <i class="lab-line-undo text-xl font-semibold"></i>
                <span class="text-base font-medium">{{ $t('label.back_to_my_orders') }}</span>
            </router-link>

            <div class="row">
                <div class="col-12 md:col-6">
                    <div class="p-4 mb-6 last:mb-0 rounded-2xl shadow-xs bg-white">
                        <h3 class="text-sm leading-6 mb-1 font-medium">
                            {{ $t('label.order_id') }}:
                            <span class="text-[#008BBA]">#{{ order.order_serial_no }}</span>
                        </h3>
                        <p class="text-xs font-light mb-2.5">{{ order.order_datetime }}</p>
                        <div class="flex flex-wrap items-center gap-2 mb-1.5">
                            <span class="text-sm capitalize text-paragraph">{{ $t('label.order_type') }}:</span>
                            <span class="text-sm capitalize">{{ enums.orderTypeEnumArray[order.order_type] }}</span>
                        </div>
                        <div class="flex flex-wrap items-center gap-2 mb-1.5" v-if="order.table">
                            <span class="text-sm capitalize text-paragraph">{{ $t('label.table') }}:</span>
                            <span class="text-sm capitalize">
                                {{ order.table.name || order.table.table_number }}
                                <span v-if="order.table.table_number && order.table.name">
                                    ({{ order.table.table_number }})
                                </span>
                            </span>
                        </div>
                        <div class="flex flex-wrap items-center gap-2 mb-6">
                            <span class="text-sm capitalize text-paragraph">{{ $t('label.order_from') }}:</span>
                            <span class="flex-auto text-sm font-medium capitalize">{{
                                textShortener(orderRestaurant.name, 18)
                            }}</span>
                            <button v-if="order.restaurant_review_status" @click="openModal('restaurant-review-modal')"
                                type="button"
                                class="flex items-center justify-center gap-1 px-3 py-2 rounded-lg text-primary bg-[#FFF8F2]">
                                <i class="lab-fill-list-star"></i>
                                <span class="text-xs font-medium capitalize">{{ $t('button.review') }}</span>
                            </button>
                        </div>
                        <OrderStatusComponent :props="order" />
                        <div
                            v-if="showDeliveryOtp"
                            class="mt-5 overflow-hidden rounded-2xl border-2 border-primary bg-gradient-to-br from-emerald-50 via-white to-amber-50"
                        >
                            <div class="p-4 text-center">
                                <p class="text-xs font-bold uppercase tracking-[0.14em] text-primary mb-1">
                                    {{ $t('label.your_delivery_otp') }}
                                </p>
                                <p class="text-3xl font-bold tracking-[0.35em] text-heading my-2">
                                    {{ order.delivery_otp }}
                                </p>
                                <p class="text-xs leading-5 text-paragraph mb-0">
                                    {{ $t('message.share_this_otp_with_rider') }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <div v-if="parseInt(order.status) === enums.orderStatusEnum.REJECTED"
                        class="p-4 mb-6 last:mb-0 rounded-2xl shadow-xs bg-white">
                        <h3 class="font-medium capitalize mb-2.5">{{ $t('label.reason') }}</h3>
                        <p class="text-sm text-heading mb-2">{{ order.reason }}</p>
                    </div>

                    <div v-if="orderDeliveryBoy && order.order_type === enums.orderTypeEnum.DELIVERY && parseInt(order.status) !== enums.orderStatusEnum.REJECTED && parseInt(order.status) !== enums.orderStatusEnum.CANCELED && parseInt(order.status) !== enums.orderStatusEnum.RETURNED"
                        class="p-4 mb-6 last:mb-0 rounded-2xl shadow-xs bg-white">
                        <h3 class="font-medium capitalize mb-3">{{ $t('label.delivery_man') }}</h3>
                        <div class="flex items-center gap-2">
                            <img class="w-10 h-10 object-cover rounded-full flex-shrink-0" :src="orderDeliveryBoy.image"
                                alt="avatar">
                            <dl class="flex-auto">
                                <dt class="text-sm font-medium capitalize mb-0.5">{{ orderDeliveryBoy.name }}</dt>
                                <dd class="text-xs text-paragraph" v-if="orderDeliveryBoy.phone">
                                    {{ orderDeliveryBoy.country_code + orderDeliveryBoy.phone }}
                                </dd>
                            </dl>

                            <div v-if="parseInt(order.status) !== enums.orderStatusEnum.REJECTED && parseInt(order.status) !== enums.orderStatusEnum.CANCELED && parseInt(order.status) !== enums.orderStatusEnum.RETURNED && parseInt(order.status) !== enums.orderStatusEnum.DELIVERED"
                                class="flex items-center gap-2">
                                <button @click="chatPopup = !chatPopup" type="button"
                                    class="w-10 h-10 flex-shrink-0 flex items-center justify-center rounded-full bg-primary/5">
                                    <i class="lab-fill-message text-primary"></i>
                                </button>

                                <a :href="'tel:' + orderDeliveryBoy.country_code + '' + orderDeliveryBoy.phone"
                                    v-if="orderDeliveryBoy.phone"
                                    class="w-10 h-10 flex-shrink-0 flex items-center justify-center rounded-full bg-primary/5">
                                    <i class="lab-fill-calling text-primary"></i>
                                </a>
                            </div>

                            <button v-if="order.delivery_boy_review_status"
                                @click="openModal('delivery-boy-review-modal')" type="button"
                                class="flex items-center justify-center gap-1 px-3 py-2 rounded-lg text-primary bg-[#FFF8F2]">
                                <i class="lab-fill-list-star"></i>
                                <span class="text-xs font-medium capitalize">{{ $t('button.review') }}</span>
                            </button>
                        </div>
                    </div>

                    <div v-if="orderAddress && order.order_type === enums.orderTypeEnum.DELIVERY"
                        class="p-4 mb-6 last:mb-0 rounded-2xl shadow-xs bg-white">
                        <h3 class="font-medium capitalize mb-2.5">{{ $t('label.delivery_address') }}</h3>
                        <div class="flex items-start gap-1.5">
                            <i class="lab-fill-location text-sm text-paragraph"></i>
                            <span class="text-sm">
                                {{ orderAddress.apartment ? orderAddress.apartment + ', ' : '' }}
                                {{ orderAddress.address }}
                            </span>
                        </div>
                    </div>

                    <div v-if="order.order_type === enums.orderTypeEnum.TAKEAWAY || isScanMenuOrder"
                        class="p-4 mb-6 last:mb-0 rounded-2xl shadow-xs bg-white">
                        <h3 class="font-medium capitalize mb-4"> {{ $t('label.restaurant_address') }}</h3>
                        <div class="flex items-start gap-3">
                            <i class="lab-line-restaurants flex-shrink-0 text-2xl"></i>
                            <dl class="flex-auto">
                                <dt class="text-sm mb-0.5">{{ order.restaurant.address }}</dt>
                                <dd class="text-xs">{{ order.restaurant.city }}, {{ order.restaurant.zip_code }},
                                    {{ order.restaurant.state }}
                                </dd>
                            </dl>
                            <OrderDetailsMapComponent v-if="order.order_type === enums.orderTypeEnum.TAKEAWAY" :order="order" />
                        </div>
                    </div>

                    <div v-if="order.order_note"
                        class="p-4 mb-6 last:mb-0 rounded-2xl shadow-xs bg-white">
                        <h3 class="font-medium capitalize mb-2.5">{{ $t('label.special_instructions') }}</h3>
                        <p class="text-sm text-heading leading-6">{{ order.order_note }}</p>
                    </div>

                    <div v-if="parseInt(order.status) !== enums.orderStatusEnum.REJECTED && parseInt(order.status) !== enums.orderStatusEnum.CANCELED"
                        class="p-4 mb-6 last:mb-0 rounded-2xl shadow-xs bg-white">
                        <h3 class="capitalize font-medium text-md leading-6 mb-2">{{ $t('label.payment_info') }}</h3>
                        <ul class="flex flex-col gap-2">
                            <li class="flex items-center gap-2">
                                <span class="capitalize text-sm leading-6 text-paragraph">
                                    {{ $t('label.method') }}:
                                </span>
                                <span class="capitalize text-sm leading-6 text-heading">
                                    {{ paymentMethodLabel }}
                                </span>
                            </li>
                            <li class="flex items-center gap-2">
                                <span class="capitalize text-sm leading-6 text-paragraph">
                                    {{ $t('label.status') }}:
                                </span>
                                <span class="capitalize text-sm leading-6"
                                    :class="enums.paymentStatusEnum.PAID === order.payment_status ? 'text-green-600' : 'text-[#FB4E4E]'">
                                    {{ enums.paymentStatusEnumArray[order.payment_status] }}
                                </span>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="col-12 md:col-6">
                    <div class="w-full rounded-2xl mb-6 shadow-xs bg-white">
                        <div class="p-4 border-b border-gray-100">
                            <h3 class="font-medium leading-6 capitalize mb-4">{{ $t('label.order_details') }}</h3>
                            <div class="pl-3">
                                <div v-if="orderItems.length > 0" v-for="item in orderItems" :key="item"
                                    class="mb-3 pb-3 border-b last:mb-0 last:pb-0 last:border-b-0 border-gray-100">
                                    <div class="flex items-start gap-3 relative">
                                        <h3
                                            class="absolute top-5 -left-3 text-sm w-[26px] h-[26px] leading-[26px] text-center rounded-full text-white bg-heading">
                                            {{ item.quantity }}</h3>
                                        <img class="w-16 h-16 rounded-lg object-cover flex-shrink-0"
                                            :src="item.item_image" alt="thumbnail">
                                        <div class="w-full">
                                            <a href="#" class="text-sm font-medium capitalize block mb-1">
                                                {{ item.item_name }}
                                            </a>
                                            <p v-if="item.item_variations.length > 0"
                                                class="capitalize text-xs text-paragraph mb-1.5">
                                                <span v-for="variation in item.item_variations" :key="variation">
                                                    <span class="capitalize text-xs w-fit whitespace-nowrap">
                                                        {{ variation.variation_name }}:&nbsp;
                                                    </span>
                                                    <span class="text-xs">
                                                        {{ variation.name }}
                                                    </span>
                                                </span>
                                            </p>
                                            <h3 class="text-xs font-medium">{{ item.total_currency_price }}</h3>
                                        </div>
                                    </div>
                                    <ul class="flex flex-col gap-1.5 mt-2">
                                        <li v-if="item.item_extras.length > 0" class="flex gap-1">
                                            <h3 class="capitalize text-xs w-fit whitespace-nowrap">{{
                                                $t('label.extras')
                                            }}:</h3>
                                            <p class="text-xs text-paragraph"
                                                v-for="(extra, index) in item.item_extras">
                                                {{ extra.name }}<span v-if="index + 1 < item.item_extras.length">,
                                                </span>
                                            </p>
                                        </li>
                                        <li v-if="item.instruction" class="flex gap-1">
                                            <h3 class="capitalize text-xs w-fit whitespace-nowrap">{{
                                                $t('label.instruction')
                                            }}:</h3>
                                            <p class="text-xs text-paragraph">{{ item.instruction }}</p>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="p-4">
                            <div class="rounded-xl border border-gray-100">
                                <ul class="flex flex-col gap-2 p-3 border-b border-dashed border-gray-100">
                                    <li class="flex items-center justify-between text-heading">
                                        <span class="text-sm leading-6 capitalize">{{ $t("label.subtotal") }}</span>
                                        <span class="text-sm leading-6 capitalize">{{
                                            order.subtotal_currency_price
                                        }}</span>
                                    </li>

                                    <li v-if="order.order_type === enums.orderTypeEnum.DELIVERY"
                                        class="flex items-center justify-between text-heading">
                                        <span class="text-sm leading-6">
                                            {{
                                                $t("label.delivery_fee")
                                            }} {{
                                                Object.keys(orderCoupon).length > 0 && orderCoupon.type ===
                                                    enums.discountEnum.FREE_DELIVERY ? '(' + $t('label.only_free') + ')' : ''
                                            }}</span>
                                        <span class="text-sm leading-6 capitalize">{{
                                            order.delivery_fee_currency_price
                                        }}</span>
                                    </li>
                                    <li v-if="order.service_fee > 0"
                                        class="flex items-center justify-between text-heading">
                                        <span class="text-sm leading-6">{{ $t("label.service_fee") }}</span>
                                        <span class="text-sm leading-6 capitalize">{{
                                            order.service_fee_currency_price
                                        }}</span>
                                    </li>
                                    <li v-if="order.order_type === enums.orderTypeEnum.DELIVERY && order.rider_tip > 0"
                                        class="flex items-center justify-between text-heading">
                                        <span class="text-sm leading-6">{{ $t("label.rider_tip") }}</span>
                                        <span class="text-sm leading-6 capitalize">{{
                                            order.rider_tip_currency_price
                                        }}</span>
                                    </li>
                                    <li v-if="order.total_tax > 0"
                                        class="flex items-center justify-between text-heading">
                                        <span class="text-sm leading-6 capitalize">{{ $t("label.tax") }}</span>
                                        <span class="text-sm leading-6 capitalize">{{
                                            order.total_tax_currency_price
                                        }}</span>
                                    </li>
                                    <li v-if="order.discount > 0"
                                        class="flex items-center justify-between text-heading">
                                        <span class="text-sm leading-6 capitalize">{{ $t("label.discount") }}</span>
                                        <span class="text-sm leading-6 capitalize">- {{
                                            order.discount_currency_price
                                        }}</span>
                                    </li>
                                </ul>
                                <div class="flex items-center justify-between p-3">
                                    <h4 class="text-sm leading-6 font-semibold capitalize">{{ $t('label.total') }}</h4>
                                    <h5 class="text-sm leading-6 font-semibold capitalize">{{
                                        order.total_currency_price
                                    }}</h5>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </section>

    <div v-if="orderDeliveryBoy && parseInt(order.status) !== enums.orderStatusEnum.REJECTED && parseInt(order.status) !== enums.orderStatusEnum.CANCELED && parseInt(order.status) !== enums.orderStatusEnum.RETURNED && parseInt(order.status) !== enums.orderStatusEnum.DELIVERED"
        :class="chatPopup ? 'scale-y-100' : 'scale-y-0'"
        class="fixed bottom-0 md:bottom-4 right-0 md:right-4 z-50 origin-bottom w-full md:w-[360px] md:rounded-xl shadow-[0px_8px_40px_0px_rgba(23,_31,_70,_0.08)] bg-white transition-all duration-300">
        <div class="flex items-start justify-between gap-2 p-4 md:rounded-t-xl bg-[#F7F7FC]">
            <img :src="orderDeliveryBoy.image" alt="avatar"
                class="w-10 aspect-square rounded-full object-cover flex-shrink-0" />
            <div class="flex-auto">
                <h3 class="text-sm font-medium capitalize mb-1">{{ orderDeliveryBoy.name }}</h3>
                <p v-if="orderDeliveryBoy.phone" class="text-xs text-paragraph">{{ orderDeliveryBoy.country_code + '' +
                    orderDeliveryBoy.phone }}</p>
            </div>
            <button @click="chatPopup = false" type="button"
                class="lab-fill-close-circle text-xl text-[#E93C3C]"></button>
        </div>

        <ul ref="mainChatBox" class="chat-list h-[calc(100dvh_-_145px)] md:h-80">
            <li v-for="(message, index) in messages" :key="index"
                :class="['chat-item', message.self ? 'chat-user' : 'chat-admin']">
                <img class="chat-avatar" :src="message.image" alt="avatar" />
                <div class="chat-group">
                    <div class="chat-group-text">
                        <p v-for="(text, i) in message.messages" :key="i" class="chat-text">
                            {{ text }}
                        </p>
                    </div>
                    <div class="chat-group-meta">
                        <span class="chat-meta">{{ message.timestamp }}</span>
                    </div>
                </div>
            </li>
        </ul>

        <form @submit.prevent="addMessage" class="flex items-center gap-3 px-4 py-3 border-t border-[#EFF0F6]">
            <input type="text" v-model="text" :placeholder="$t('label.type_message')"
                class="w-full h-12 py-3.5 px-4 rounded-full resize-none text-sm thin-scrolling placeholder:text-paragraph bg-[#F7F7FC]">
            <button type="submit"
                class="lab-fill-send text-3xl text-paragraph transition-all duration-300 hover:text-primary"></button>
        </form>
    </div>

    <div id="restaurant-review-modal" v-if="order.restaurant_review_status"
        class="fixed inset-0 z-50 p-3 w-screen h-dvh overflow-y-auto bg-black/50 transition-all duration-300 opacity-0 invisible">
        <div class="max-w-md w-full rounded-xl mx-auto bg-white transition-all duration-300">
            <div class="p-4">
                <div class="flex items-center justify-between gap-2 mb-4">
                    <h3 class="text-lg font-medium">{{ $t('message.write_review_for_restaurant') }}</h3>
                    <button @click="closeModal('restaurant-review-modal')" type="button"
                        class="lab-line-circle-cross text-xl text-danger"></button>
                </div>

                <div class="flex items-center gap-3 mb-6">
                    <img :src="orderRestaurant.logo" alt="logo"
                        class="w-11 h-11 object-cover rounded-full flex-shrink-0" />
                    <div class="flex-auto">
                        <span class="text-sm text-paragraph">{{ $t('label.restaurant') }}</span>
                        <h4 class="text-sm font-medium">{{ orderRestaurant.name }}</h4>
                    </div>
                </div>
                <form @submit.prevent="saveRestaurantReview">
                    <div class="mb-4">
                        <h5 class="text-sm mb-1">{{ $t('label.your_rating') }} ({{ restaurantReviewForm.star }})</h5>
                        <div class="flex items-center gap-1">
                            <button @click="restaurantReviewForm.star = rate" v-for="rate in 5" type="button"
                                :class="{ '!text-[#F6A609]': restaurantReviewForm.star >= rate }"
                                class="lab-fill-star text-xl leading-none text-[#D9DBE9]"></button>
                        </div>
                        <small class="db-field-alert" v-if="restaurantReviewErrors.star">
                            {{ restaurantReviewErrors.star[0] }}
                        </small>
                    </div>
                    <div class="mb-4">
                        <h5 class="text-sm mb-1">{{ $t('label.review_details') }}</h5>
                        <textarea v-model="restaurantReviewForm.review"
                            :class="restaurantReviewErrors.review ? 'invalid' : ''"
                            class="p-2 h-24 w-full resize-none rounded-lg border border-[#D9DBE9]"></textarea>
                        <small class="db-field-alert" v-if="restaurantReviewErrors.review">
                            {{ restaurantReviewErrors.review[0] }}
                        </small>
                    </div>
                    <button type="submit" class="w-full h-12 leading-12 font-medium rounded-full bg-primary text-white">
                        {{ $t('button.submit_review') }}
                    </button>
                </form>
            </div>
        </div>
    </div>

    <div id="delivery-boy-review-modal" v-if="order.delivery_boy_review_status"
        class="fixed inset-0 z-50 p-3 w-screen h-dvh overflow-y-auto bg-black/50 transition-all duration-300 opacity-0 invisible">
        <div class="max-w-md w-full rounded-xl mx-auto bg-white transition-all duration-300">
            <div class="p-4">
                <div class="flex items-center justify-between gap-2 mb-4">
                    <h3 class="text-lg font-medium">{{ $t('message.write_review_for_delivery_boy') }}</h3>
                    <button @click="closeModal('delivery-boy-review-modal')" type="button"
                        class="lab-line-circle-cross text-xl text-danger"></button>
                </div>

                <div class="flex items-center gap-3 mb-6">
                    <img :src="orderDeliveryBoy.image" alt="CheezoMania Burgers Logo"
                        class="w-11 h-11 object-cover rounded-full flex-shrink-0" />
                    <div class="flex-auto">
                        <span class="text-sm text-paragraph">{{ $t('label.delivery_boy') }}</span>
                        <h4 class="text-sm font-medium">{{ orderDeliveryBoy.name }}</h4>
                    </div>
                </div>
                <form @submit.prevent="saveDeliveryBoyReview">
                    <div class="mb-4">
                        <h5 class="text-sm mb-1">{{ $t('label.your_rating') }} ({{ deliveryBoyReviewForm.star }})</h5>
                        <div class="flex items-center gap-1">
                            <button @click="deliveryBoyReviewForm.star = rate" v-for="rate in 5" type="button"
                                :class="{ '!text-[#F6A609]': deliveryBoyReviewForm.star >= rate }"
                                class="lab-fill-star text-xl leading-none text-[#D9DBE9]"></button>
                        </div>
                        <small class="db-field-alert" v-if="deliveryBoyReviewErrors.star">
                            {{ deliveryBoyReviewErrors.star[0] }}
                        </small>
                    </div>
                    <div class="mb-4">
                        <h5 class="text-sm mb-1">{{ $t('label.review_details') }}</h5>
                        <textarea v-model="deliveryBoyReviewForm.review"
                            :class="deliveryBoyReviewErrors.review ? 'invalid' : ''"
                            class="p-2 h-24 w-full resize-none rounded-lg border border-[#D9DBE9]"></textarea>
                        <small class="db-field-alert" v-if="deliveryBoyReviewErrors.review">
                            {{ deliveryBoyReviewErrors.review[0] }}
                        </small>
                    </div>
                    <button type="submit" class="w-full h-12 leading-12 font-medium rounded-full bg-primary text-white">
                        {{ $t('button.submit_review') }}
                    </button>
                </form>
            </div>
        </div>
    </div>

</template>

<script>
import activityEnum from "../../../../enums/modules/activityEnum.js";
import orderTypeEnum from "../../../../enums/modules/orderTypeEnum.js";
import orderStatusEnum from "../../../../enums/modules/orderStatusEnum.js";
import discountEnum from "../../../../enums/modules/discountEnum.js";
import paymentStatusEnum from "../../../../enums/modules/paymentStatusEnum.js";
import paymentTypeEnum from "../../../../enums/modules/paymentTypeEnum.js";
import { useFrontendOrderStore } from "../../../../stores/frontendOrder.js";
import appService from "../../../../services/appService.js";
import alertService from "../../../../services/alertService.js";
import { useAuthStore } from "../../../../stores/auth.js";
import OrderDetailsMapComponent from "./OrderDetailsMapComponent.vue";
import OrderStatusComponent from "../../components/OrderStatusComponent.vue";
import { useModal } from "../../../../composables/modal.js";
import { useFrontendReviewStore } from "../../../../stores/frontendReview.js";
import { useFrontendMessageStore } from "../../../../stores/frontendMessage.js";
import messageChannelTypeEnum from "../../../../enums/modules/messageChannelTypeEnum.js";
import LoadingComponent from "../../../common/LoadingComponent.vue";
import ENV from "../../../../config/env.js";
import {isScanMenuOrder as orderIsScanMenu, resolvePaymentMethodLabel} from "../../../../utils/orderHelpers.js";
import {subscribeCustomerOrderRealtime} from "../../../../composables/useCustomerOrderRealtime.js";

export default {
    name: "OrderDetailsComponent",
    components: { LoadingComponent, OrderDetailsMapComponent, OrderStatusComponent },
    setup() {
        const { openModal, closeModal } = useModal();
        const authStore = useAuthStore();
        const frontendOrderStore = useFrontendOrderStore();
        const frontendReviewStore = useFrontendReviewStore();
        const frontendMessageStore = useFrontendMessageStore();

        return {
            openModal,
            closeModal,
            authStore,
            frontendOrderStore,
            frontendReviewStore,
            frontendMessageStore
        }
    },
    data() {
        return {
            loading: {
                isActive: false,
            },
            enums: {
                activityEnum: activityEnum,
                orderTypeEnum: orderTypeEnum,
                orderStatusEnum: orderStatusEnum,
                paymentStatusEnum: paymentStatusEnum,
                discountEnum: discountEnum,
                messageChannelTypeEnum: messageChannelTypeEnum,
                orderTypeEnumArray: {
                    [orderTypeEnum.DELIVERY]: this.$t("label.delivery"),
                    [orderTypeEnum.TAKEAWAY]: this.$t("label.takeaway"),
                    [orderTypeEnum.DINING_TABLE]: this.$t("label.dining_table")
                },
                paymentTypeEnumArray: {
                    [paymentTypeEnum.CASH_ON_DELIVERY]: this.$t("label.cash_on_delivery"),
                    [paymentTypeEnum.CREDIT]: this.$t("label.credit"),
                },
                paymentStatusEnumArray: {
                    [paymentStatusEnum.PAID]: this.$t("label.paid"),
                    [paymentStatusEnum.UNPAID]: this.$t("label.unpaid")
                }
            },
            chatPopup: false,
            imageUrl: Array(4).fill(null),
            restaurantReviewForm: {
                star: 0,
                review: ''
            },
            deliveryBoyReviewForm: {
                star: 0,
                review: ''
            },
            restaurantReviewErrors: {},
            deliveryBoyReviewErrors: {},
            details: "",
            images: {},
            text: "",
            unsubscribeOrderRealtime: null,
        }
    },
    computed: {
        order: function () {
            return this.frontendOrderStore.show;
        },
        isScanMenuOrder: function () {
            return orderIsScanMenu(this.order);
        },
        showDeliveryOtp: function () {
            return Number(this.order?.status) === this.enums.orderStatusEnum.OUT_FOR_DELIVERY
                && Number(this.order?.order_type) === this.enums.orderTypeEnum.DELIVERY
                && !!this.order?.delivery_otp;
        },
        paymentMethodLabel: function () {
            return resolvePaymentMethodLabel(this.order, (key) => this.$t(key));
        },
        orderRestaurant: function () {
            return this.frontendOrderStore.orderRestaurant;
        },
        orderAddress: function () {
            return this.frontendOrderStore.orderAddress;
        },
        orderItems: function () {
            return this.frontendOrderStore.orderItems;
        },
        orderDeliveryBoy: function () {
            return this.frontendOrderStore.orderDeliveryBoy;
        },
        orderCoupon: function () {
            return this.frontendOrderStore.orderCoupon;
        },
        messages: function () {
            const messageArrays = [];
            let children = {};
            const messages = this.frontendMessageStore.messages;

            messages.forEach((msg, index) => {
                if ((Object.keys(children).length > 0 && children.user_id !== msg.user_id) || index === 0) {
                    children = {
                        user_id: msg.user_id,
                        image: msg.image,
                        timestamp: msg.created_at,
                        self: msg.self,
                        messages: []
                    }
                    messageArrays.push(children);
                }
                children.timestamp = msg.created_at;
                children.messages.push(msg.text);
            })

            this.$nextTick(() => {
                const el = this.$refs.mainChatBox
                if (el) {
                    el.scrollTop = el.scrollHeight
                }
            })

            return messageArrays;
        }
    },
    async mounted() {
        if (this.$route.params.id) {
            this.loading.isActive = true;
            await this.frontendOrderStore.view(this.$route.params.id).then(async res => {

                if (Object.keys(res.data.data.delivery_boy).length > 0) {
                    const PUSHER_KEY = ENV.PUSHER_KEY;
                    const PUSHER_CLUSTER = ENV.PUSHER_CLUSTER;
                    if (PUSHER_KEY && PUSHER_CLUSTER) {
                        window.Echo.private(`chat.${res.data.data.id}.${this.enums.messageChannelTypeEnum.DELIVERY}.${res.data.data.delivery_boy?.id}`).listen('NewChatMessage', (e) => {
                            this.fetchMessages()
                        });
                    }
                }

                this.loading.isActive = false;
                this.bindCustomerOrderRealtime();
                if (res.data.data.restaurant_review_status) {
                    await this.frontendReviewStore.fetchRestaurantReview(this.$route.params.id).then(restaurantReviewRes => {
                        if (restaurantReviewRes.data?.data) {
                            this.restaurantReviewForm.star = restaurantReviewRes.data.data.star;
                            this.restaurantReviewForm.review = restaurantReviewRes.data.data.review;
                        }
                    }).catch(restaurantReviewErr => {
                        this.loading.isActive = false;
                    })
                }

                if (res.data.data.delivery_boy_review_status) {
                    await this.frontendReviewStore.fetchDeliveryBoyReview(this.$route.params.id).then(deliveryBoyReviewRes => {
                        if (deliveryBoyReviewRes.data?.data) {
                            this.deliveryBoyReviewForm.star = deliveryBoyReviewRes.data.data.star;
                            this.deliveryBoyReviewForm.review = deliveryBoyReviewRes.data.data.review;
                        }
                    }).catch(deliveryBoyReviewErr => {
                        this.loading.isActive = false;
                    })
                }
            }).catch((error) => {
                this.loading.isActive = false;
            })
        }
    },
    beforeUnmount() {
        if (typeof this.unsubscribeOrderRealtime === 'function') {
            this.unsubscribeOrderRealtime();
            this.unsubscribeOrderRealtime = null;
        }
    },
    methods: {
        bindCustomerOrderRealtime() {
            if (typeof this.unsubscribeOrderRealtime === 'function') {
                this.unsubscribeOrderRealtime();
                this.unsubscribeOrderRealtime = null;
            }
            const userId = this.authStore.info?.id;
            const orderId = this.$route.params.id;
            if (!userId || !orderId) return;

            this.unsubscribeOrderRealtime = subscribeCustomerOrderRealtime({
                userId,
                orderId,
                t: (key) => this.$t(key),
                onUpdate: () => {
                    this.frontendOrderStore.view(orderId).catch(() => {});
                },
            });
        },
        textShortener: function (text, number) {
            return appService.textShortener(text, number);
        },
        saveRestaurantReview: function () {
            try {
                this.loading.isActive = true;
                this.frontendReviewStore.saveRestaurantReview({
                    orderId: this.$route.params.id,
                    form: this.restaurantReviewForm
                }).then((res) => {
                    this.loading.isActive = false;
                    alertService.success(this.$t("message.review_successfully_submitted"));
                    this.restaurantReviewErrors = {};
                    this.closeModal('restaurant-review-modal');
                }).catch((err) => {
                    this.loading.isActive = false;
                    this.restaurantReviewErrors = err.response.data.errors;
                });
            } catch (err) {
                this.loading.isActive = false;
                alertService.error(err);
            }
        },
        saveDeliveryBoyReview: function () {
            try {
                this.loading.isActive = true;
                this.frontendReviewStore.saveDeliveryBoyReview({
                    orderId: this.$route.params.id,
                    form: this.deliveryBoyReviewForm
                }).then((res) => {
                    this.loading.isActive = false;
                    alertService.success(this.$t("message.review_successfully_submitted"));
                    this.deliveryBoyReviewErrors = {};
                    this.closeModal('delivery-boy-review-modal');
                }).catch((err) => {
                    this.loading.isActive = false;
                    this.deliveryBoyReviewErrors = err.response.data.errors;
                });
            } catch (err) {
                this.loading.isActive = false;
                alertService.error(err);
            }
        },
        addImage: function (event, index) {
            if (event.target.files && event.target.files[0]) {
                const reader = new FileReader();
                reader.onload = (e) => {
                    this.imageUrl[index] = URL.createObjectURL(event.target.files[0]);
                };
                reader.readAsDataURL(event.target.files[0]);
                this.images[index] = event.target.files[0];
            }
        },
        removeImage: function (index) {
            this.imageUrl[index] = null;
            this.images[index] = null;
        },
        loadImageFromUrls: async function (images) {
            for (const key in images) {
                const url = images[key];
                const response = await fetch(url);
                const blob = await response.blob();

                const filename = url.split('/').pop();
                const file = new File([blob], filename, { type: blob.type });

                this.images[key] = file;
                this.imageUrl[key] = URL.createObjectURL(file);
            }
        },
        fetchMessages: function () {
            if (this.$route.params.id) {
                this.frontendMessageStore.fetch(this.$route.params.id).then().catch()
            }
        },
        addMessage: function () {
            if (this.text) {
                this.frontendMessageStore.save({
                    order_id: this.$route.params.id,
                    text: this.text
                }).then(res => {
                    this.text = "";
                }).catch()
            }
        }
    }
}
</script>
