<template>
    <LoadingComponent :props="loading"/>
    <section class="pb-14">
        <div class="container max-w-5xl">
            <div class="row">
                <div class="col-12">
                    <router-link :to="{ name: 'frontend.singleRestaurant', params: { slug: restaurant.slug } }"
                                 class="pt-7 flex items-center gap-2">
                        <i :class="displayMode === 'ltr' ? 'lab-line-undo' : 'lab-line-redo'"
                           class="-mt-0.5 text-lg leading-none font-semibold text-primary"></i>
                        <span class="font-medium whitespace-nowrap text-primary">{{ $t('button.back_to_store') }}</span>
                    </router-link>
                </div>

                <div class="col-12 md:col-7">
                    <div class="mb-6 rounded-2xl shadow-xs bg-white">
                        <div v-if="isDineIn"
                             class="px-4 pt-4 pb-5 border-b border-gray-100">
                            <h3 class="mb-2 text-lg font-medium capitalize">{{ $t('label.dining_table') }}</h3>
                            <p class="mb-1 text-sm text-heading">
                                {{ $t('label.table') }}:
                                <span class="font-medium">{{ dineInTableLabel }}</span>
                            </p>
                            <p class="text-xs text-paragraph">{{ $t('message.dine_in_checkout_note') }}</p>
                        </div>

                        <div v-if="!isDineIn && checkoutProps.form.order_type === orderTypeEnum.TAKEAWAY"
                             class="px-4 pt-4 pb-5 border-b border-gray-100">
                            <MapComponent :key="mapKey" v-if="mapShow"
                                          :location="{lat: restaurant.latitude, lng: restaurant.longitude}"
                                          :position="restaurantPosition"
                                          :setting="{ autocomplete: false, mouseEvent: false, currentLocation: false }"/>
                            <div class="flex items-center gap-2">
                                <i class="lab-line-branches flex-shrink-0 text-2xl"></i>
                                <span class="text-sm text-heading">{{ restaurant.address }}</span>
                            </div>
                        </div>

                        <div v-if="!isDineIn && checkoutProps.form.order_type === orderTypeEnum.DELIVERY"
                             class="px-4 pt-4 pb-5 border-b border-gray-100">
                            <div class="flex items-center gap-3 mb-5">
                                <h3 class="flex-auto text-lg font-medium capitalize">
                                    {{ $t('label.delivery_address') }}
                                </h3>
                                <button v-if="Object.keys(localAddress).length !== 0" @click.prevent="editAddress"
                                        class="flex items-center gap-1.5 px-2.5 h-8 rounded-3xl text-cyan-600 bg-cyan-50">
                                    <i class="lab-fill-edit"></i>
                                    <span class="text-sm font-medium capitalize whitespace-nowrap">
                                        {{ $t('button.edit_address') }}
                                    </span>
                                </button>
                                <AddressComponent :getLocation="updateAddress" :props="addressProps"/>
                            </div>

                            <Swiper v-if="addressShow" :dir="displayMode" :speed="1000" :spaceBetween="16"
                                    slidesPerView="auto">
                                <SwiperSlide :id="'delivery-address-'+address.id" v-for="address in addresses"
                                             @click="changeAddress(address)"
                                             :class="checkoutProps.form.address_id === address.id ? 'bg-primary/5 border-primary/30' : 'bg-gray-100 border-gray-100'"
                                             class="!w-56 p-3 rounded-lg !flex items-start gap-2 cursor-pointer border transition-all duration-300">
                                    <div class="flex-auto">
                                        <div class="flex items-center gap-2 mb-2">
                                            <i :class="locationIcon(address.label)" class="-mt-0.5 text-cyan-600"></i>
                                            <span class="text-sm font-medium capitalize text-cyan-600">
                                                {{ address.label }}
                                            </span>
                                        </div>
                                        <div class="flex items-start gap-2">
                                            <i class="lab-fill-location mt-1 text-paragraph"></i>
                                            <span class="text-sm leading-6">
                                                {{
                                                    address.apartment ? address.apartment + ', ' : ''
                                                }}{{ address.address }}
                                            </span>
                                        </div>
                                    </div>
                                    <input type="radio" v-model="checkoutProps.form.address_id" disabled="disabled"
                                           :value="address.id" :id="'delivery-address-'+address.id"
                                           class="cs-custom-radio flex-shrink-0">
                                </SwiperSlide>
                            </Swiper>
                        </div>

                        <div class="p-4" v-if="!isDineIn">
                            <h3 class="mb-5 text-lg font-medium capitalize">
                                {{
                                    checkoutProps.form.order_type === orderTypeEnum.DELIVERY ? $t('label.delivery') : $t('label.takeaway')
                                }} {{ $t('label.time') }}
                            </h3>
                            <div class="flex flex-wrap items-start gap-4">
                                <label v-if="Object.keys(nowTimeSlot).length > 0" :for="scheduleEnum.NOW"
                                       @click="selectNowDeliveryTime(nowTimeSlot)"
                                       :class="schedule === scheduleEnum.NOW ? 'bg-primary/5 border-primary/30' : 'bg-white border-gray-100'"
                                       class="w-fit py-2 px-3 rounded-lg flex items-start gap-5 cursor-pointer border transition-all duration-300">
                                    <dl class="flex-auto">
                                        <dt class="text-sm font-medium whitespace-nowrap mb-1.5">
                                            {{ $t('label.now') }}
                                        </dt>
                                        <dd class="text-sm whitespace-nowrap">
                                            {{ restaurant.order_setup.food_preparation_time }} {{ $t('label.minute') }}
                                        </dd>
                                    </dl>
                                    <input class="cs-custom-radio flex-shrink-0" type="radio" :id="scheduleEnum.NOW"
                                           :value="scheduleEnum.NOW" v-model="schedule">
                                </label>
                                <label @click="openModal('time-schedule-modal')" :for="scheduleEnum.TOMORROW"
                                       :class="schedule === scheduleEnum.TOMORROW ? 'bg-primary/5 border-primary/30' : 'bg-white border-gray-100'"
                                       class="w-fit py-2 px-3 rounded-lg flex items-start gap-5 cursor-pointer border transition-all duration-300">
                                    <dl class="flex-auto">
                                        <dt class="text-sm font-medium whitespace-nowrap mb-1.5">
                                            {{ $t('label.schedule_for_later') }}
                                        </dt>
                                        <dd class="text-sm whitespace-nowrap">
                                            {{ localDeliveryTimeLabel || $t('label.choose_a_time') }}
                                        </dd>
                                    </dl>
                                    <input class="cs-custom-radio flex-shrink-0" type="radio"
                                           :id="scheduleEnum.TOMORROW"
                                           :value="scheduleEnum.TOMORROW" v-model="schedule">
                                </label>
                            </div>
                        </div>
                    </div>

                    <div
                        v-if="setting.site_rider_tip === activityEnum.ENABLE && checkoutProps.form.order_type === orderTypeEnum.DELIVERY && riderTips.length > 0"
                        class="mb-6 p-4 rounded-2xl shadow-xs bg-white">
                        <h3 class="mb-1 text-lg font-medium capitalize">{{ $t("message.tip_your_rider") }} </h3>
                        <p class="text-xs font-light mb-5">{{ $t('message.full_tips_for_rider') }}</p>
                        <Swiper :dir="displayMode" :speed="1000" :spaceBetween="12" slidesPerView="auto">
                            <SwiperSlide v-for="riderTip in riderTips" @click="selectRiderTip(riderTip)"
                                         :class="Object.keys(riderTipMethod).length > 0 && riderTip.id === riderTipMethod.id ? 'bg-primary/5 border-primary/30' : 'bg-gray-100 border-gray-100'"
                                         class="!w-fit text-sm py-2 px-4 rounded-full cursor-pointer text-secondary border transition-all duration-300">
                                {{ riderTip.label }}
                            </SwiperSlide>
                        </Swiper>
                    </div>

                    <div class="p-4 rounded-2xl shadow-xs bg-white">
                        <h3 class="mb-5 text-lg font-medium capitalize">{{ $t('label.payment_method') }}</h3>
                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-3 sm:gap-4">
                            <div
                                v-if="Object.keys(cashOnDelivery).length > 0 && setting.site_cash_on_delivery === activityEnum.ENABLE"
                                @click.prevent="selectPaymentMethod(cashOnDelivery)"
                                :class="Object.keys(paymentMethod).length > 0 && cashOnDelivery.id === paymentMethod.id ? 'bg-primary/5 border-primary/30' : 'bg-white border-white'"
                                class="w-full min-w-0 px-2 sm:px-3 py-4 text-center rounded-lg shadow-xs cursor-pointer border transition-all duration-300">
                                <img class="w-10 mx-auto mb-2" :src="cashOnDelivery.image" alt="payment">
                                <span class="block text-[11px] sm:text-xs leading-snug capitalize whitespace-normal break-words">
                                    {{ cashPaymentLabel }}
                                </span>
                            </div>

                            <div v-if="profile.balance >= total + checkoutProps.form.delivery_fee"
                                 @click.prevent="selectPaymentMethod(credit)"
                                 :class="Object.keys(paymentMethod).length > 0 && credit.id === paymentMethod.id ? 'bg-primary/5 border-primary/30' : 'bg-white border-white'"
                                 class="w-full min-w-0 px-2 sm:px-3 py-4 text-center rounded-lg shadow-xs cursor-pointer border transition-all duration-300">
                                <img class="w-10 mx-auto mb-2" :src="credit.image" alt="payment">
                                <span class="block text-[11px] sm:text-xs leading-snug capitalize whitespace-normal break-words">
                                    {{ credit.name }} ({{ profile.balance }})
                                </span>
                            </div>

                            <div v-if="setting.site_online_payment_gateway === activityEnum.ENABLE"
                                 v-for="paymentGateway in paymentGateways"
                                 :key="paymentGateway.id"
                                 @click.prevent="selectPaymentMethod(paymentGateway)"
                                 :class="Object.keys(paymentMethod).length > 0 && paymentGateway.id === paymentMethod.id ? 'bg-primary/5 border-primary/30' : 'bg-white border-white'"
                                 class="w-full min-w-0 px-2 sm:px-3 py-4 text-center rounded-lg shadow-xs cursor-pointer border transition-all duration-300">
                                <img class="w-10 mx-auto mb-2" :src="paymentGateway.image" alt="payment">
                                <span class="block text-[11px] sm:text-xs leading-snug capitalize whitespace-normal break-words">
                                    {{ paymentGateway.name }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12 md:col-5">
                    <div class="p-4 rounded-2xl shadow-xs bg-white">
                        <h4 class="font-medium overflow-hidden mb-5">
                            <span class="text-xs block mb-1 text-paragraph">{{ $t('label.order_summary_from') }}</span>
                            <span class="text-xl block capitalize whitespace-nowrap overflow-hidden text-ellipsis">
                                {{ restaurant.name }}
                            </span>
                        </h4>
                        <nav
                            v-if="!isDineIn && carts.length > 0 && orderType !== null && restaurant.order_setup && (restaurant.order_setup.delivery === activityEnum.ENABLE || restaurant.order_setup.takeaway === activityEnum.ENABLE)"
                            class="w-fit mx-auto mb-4 flex items-center justify-center p-1 rounded-full bg-mate">
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
                        <div
                            v-if="isDineIn"
                            class="mb-4 rounded-xl border border-primary/20 bg-primary/5 px-3 py-2 text-sm text-heading">
                            {{ $t('label.dining_table') }}: <span class="font-medium">{{ dineInTableLabel }}</span>
                        </div>

                        <ul v-if="carts.length > 0" class="mb-4">
                            <li v-for="(cart, index) in carts" :key="index" class="py-4 border-t last:border-b border-gray-100">
                                <div class="flex items-start gap-3">
                                    <div
                                        class="flex-shrink-0 w-6 h-6 leading-6 text-center rounded-full text-xs mt-5 ltr:-mr-6 rtl:-ml-6 relative text-white bg-secondary">
                                        {{ cart.quantity }}
                                    </div>
                                    <figure class="flex-shrink-0">
                                        <img class="w-16 h-16 rounded-lg object-cover" :src="cart.image" alt="menu">
                                    </figure>
                                    <div class="flex-auto overflow-hidden">
                                        <h3 class="text-sm capitalize whitespace-nowrap overflow-hidden text-ellipsis mb-0.5">
                                            {{ cart.name }}
                                        </h3>
                                        <p v-if="Object.keys(cart.item_variations.variations).length !== 0"
                                           class="text-xs capitalize text-paragraph mb-1.5">
                                            <span
                                                v-for="(variation, variationName, index) in cart.item_variations.names"
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
                                    <div class="flex flex-col items-end gap-2 flex-shrink-0">
                                        <button @click.prevent="removeItem(index)" type="button"
                                                class="text-xs font-medium text-danger hover:underline">
                                            {{ $t('button.remove') }}
                                        </button>
                                        <div class="flex items-center w-16 h-6 gap-1">
                                            <button @click.prevent="quantityDecrement(index)" type="button"
                                                    class="lab-line-minus-circle font-medium hover:text-primary"></button>
                                            <span class="w-full text-center text-sm">{{ cart.quantity }}</span>
                                            <button @click.prevent="quantityIncrement(index)" type="button"
                                                    class="lab-line-add-circle font-medium hover:text-primary"></button>
                                        </div>
                                    </div>
                                </div>

                                <div v-if="cart.item_extras.extras.length > 0 || cart.instruction !== ''"
                                     class="mt-3 ltr:ml-3 rtl:mr-3">
                                    <div v-if="cart.item_extras.extras.length > 0"
                                         class="flex items-start gap-1 mb-1 last:mb-0">
                                        <b class="text-xs font-medium capitalize whitespace-nowrap">
                                            {{ $t('label.extras') }}:
                                        </b>
                                        <p class="text-xs capitalize text-paragraph">
                                            {{ cart.item_extras.names.map(extra => extra).join(', ') }}
                                        </p>
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

                        <div v-if="setting.site_cutlery === activityEnum.ENABLE" class="flex items-center gap-3 mb-4">
                            <i class="lab-fill-cutlery flex-shrink-0 text-2xl text-primary"></i>
                            <dl class="flex-auto">
                                <dt class="text-sm font-medium capitalize mb-1">{{ $t('label.cutlery') }}</dt>
                                <dd class="text-xs text-secondary">
                                    {{ $t('message.cutlery_will_provide_with_your_order') }}
                                </dd>
                            </dl>
                            <label for="switcher" class="cs-custom-switcher">
                                <input type="checkbox" @click="changeCutlery($event)" v-model="cutleryModel"
                                       id="switcher" class="peer">
                            </label>
                        </div>

                        <CouponComponent :props="{ total: parseFloat(subtotal) }" :coupon="coupon"/>

                        <div class="rounded-xl mb-6 border border-gray-100">
                            <ul class="flex flex-col gap-2 p-3 border-b border-dashed border-gray-100">
                                <li class="flex items-center justify-between text-heading">
                                    <span class="text-sm leading-6 capitalize">{{ $t('label.subtotal') }}</span>
                                    <span class="text-sm leading-6 capitalize">
                                        {{
                                            currencyFormat(subtotal, setting.site_digit_after_decimal_point, setting.site_default_currency_symbol, setting.site_currency_position)
                                        }}
                                    </span>
                                </li>


                                <li v-if="checkoutProps.form.order_type === orderTypeEnum.DELIVERY"
                                    class="flex items-center justify-between text-heading">
                                    <span class="text-sm leading-6">
                                        {{
                                            $t('label.delivery_fee')
                                        }} {{
                                            Object.keys(cartCoupon).length > 0 && cartCoupon.type === discountEnum.FREE_DELIVERY ? '(' + $t('label.only_free') + ')' : ''
                                        }}
                                    </span>
                                    <span class="text-sm leading-6 capitalize">
                                        {{
                                            currencyFormat(checkoutProps.form.delivery_fee, setting.site_digit_after_decimal_point, setting.site_default_currency_symbol, setting.site_currency_position)
                                        }}
                                    </span>
                                </li>
                                <li v-if="serviceFee > 0" class="flex items-center justify-between text-heading">
                                    <span class="text-sm leading-6">{{ $t('label.service_fee') }}</span>
                                    <span class="text-sm leading-6 capitalize">
                                        {{
                                            currencyFormat(serviceFee, setting.site_digit_after_decimal_point, setting.site_default_currency_symbol, setting.site_currency_position)
                                        }}
                                    </span>
                                </li>
                                <li v-if="setting.site_rider_tip === activityEnum.ENABLE && checkoutProps.form.order_type === orderTypeEnum.DELIVERY"
                                    class="flex items-center justify-between text-heading">
                                    <span class="text-sm leading-6">{{ $t('label.rider_tip') }}</span>
                                    <span class="text-sm leading-6 capitalize">
                                        {{
                                            currencyFormat(Object.keys(riderTipMethod).length > 0 ? riderTipMethod.amount : 0, setting.site_digit_after_decimal_point, setting.site_default_currency_symbol, setting.site_currency_position)
                                        }}
                                    </span>
                                </li>
                                <li v-if="tax > 0" class="flex items-center justify-between text-heading">
                                    <span class="text-sm leading-6 capitalize">{{ $t('label.tax') }}</span>
                                    <span class="text-sm leading-6 capitalize">
                                        {{
                                            currencyFormat(tax, setting.site_digit_after_decimal_point, setting.site_default_currency_symbol, setting.site_currency_position)
                                        }}
                                    </span>
                                </li>
                                <li v-if="Object.keys(cartCoupon).length > 0 && cartCoupon.type !== discountEnum.FREE_DELIVERY"
                                    class="flex items-center justify-between text-heading">
                                    <span class="text-sm leading-6">{{ $t('label.discount') }}</span>
                                    <span class="text-sm leading-6 capitalize">
                                        - {{
                                            currencyFormat(discount, setting.site_digit_after_decimal_point, setting.site_default_currency_symbol, setting.site_currency_position)
                                        }}
                                    </span>
                                </li>

                            </ul>
                            <div class="flex items-center justify-between p-3">
                                <h4 class="text-sm leading-6 font-semibold">{{ $t('label.total') }} <span
                                    class="font-normal text-[10px] text-gray-500 leading-6">({{
                                        $t('label.include_fess_and_tax')
                                    }})</span></h4>
                                <h5 class="text-sm leading-6 font-semibold capitalize text-primary">
                                    {{
                                        currencyFormat(total + checkoutProps.form.delivery_fee, setting.site_digit_after_decimal_point, setting.site_default_currency_symbol, setting.site_currency_position)
                                    }}
                                </h5>
                            </div>
                        </div>

                        <button @click="orderSubmit"
                                class="w-full h-12 leading-12 text-center rounded-3xl capitalize font-medium text-white bg-primary">
                            {{ $t('button.place_order') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div id="time-schedule-modal"
         class="fixed inset-0 z-50 p-3 w-screen h-dvh overflow-y-auto bg-black/50 transition-all duration-300 opacity-0 invisible">
        <div class="max-w-lg w-full rounded-xl mx-auto bg-white transition-all duration-300">
            <div class="flex items-center justify-between gap-4 py-3.5 px-4 border-b border-slate-100">
                <h3 class="text-lg font-semibold capitalize">{{ $t('label.select_time_schedule') }}</h3>
                <button @click.prevent="closeModal('time-schedule-modal')"
                        class="lab-line-circle-cross text-lg text-danger"></button>
            </div>

            <div v-if="todayTimeSlots.length > 0 || tomorrowTimeSlots.length > 0" class="p-4 border-b border-gray-100">
                <nav class="w-fit flex items-center rounded-full bg-primary/10">
                    <button v-if="todayTimeSlots.length > 0"
                            :class="scheduleTab === scheduleEnum.TODAY && todayTimeSlots.length > 0 ? 'text-white bg-primary' : ''"
                            @click.prevent="scheduleTabHandel($event, 'time-slot-today-tab', scheduleEnum.TODAY)"
                            class="text-sm font-medium capitalize h-10 px-4 rounded-full">
                        {{ $t('label.today') }}
                    </button>
                    <button v-if="tomorrowTimeSlots.length > 0"
                            :class="scheduleTab === scheduleEnum.TOMORROW || (todayTimeSlots.length === 0 && tomorrowTimeSlots.length > 0) ? 'text-white bg-primary' : ''"
                            @click.prevent="scheduleTabHandel($event, 'time-slot-tomorrow-tab', scheduleEnum.TOMORROW)"
                            class="text-sm font-medium capitalize h-10 px-4 rounded-full">
                        {{ $t('label.tomorrow') }}
                    </button>
                </nav>
            </div>

            <div v-if="todayTimeSlots.length > 0"
                 :class="todayTimeSlots.length > 0 && scheduleTab === scheduleEnum.TODAY ? 'tab-active' : ''"
                 id="time-slot-today-tab" class="tab-content">
                <ul v-if="todayTimeSlots.length > 0" class="p-4 grid grid-cols-2 gap-y-4 gap-6">
                    <li v-for="todayTimeSlot in todayTimeSlots"
                        @click.prevent="()=> {selectDeliveryTime(todayTimeSlot); closeModal('time-schedule-modal');}"
                        class="w-full h-10 leading-10 rounded-3xl text-center text-sm cursor-pointer border"
                        :class="checkoutProps.form.is_advance_order === isAdvanceOrderEnum.NO && checkoutProps.form.delivery_time === todayTimeSlot.time ? 'bg-primary/5 border-primary/40' : 'border-gray-100 bg-gray-100'">
                        {{ todayTimeSlot.label }}
                    </li>
                </ul>
            </div>

            <div v-if="tomorrowTimeSlots.length > 0" id="time-slot-tomorrow-tab"
                 :class="(todayTimeSlots.length === 0 && tomorrowTimeSlots.length > 0) || scheduleTab === scheduleEnum.TOMORROW ? 'tab-active' : ''"
                 class="tab-content">
                <ul v-if="tomorrowTimeSlots.length > 0" class="p-4 grid grid-cols-2 gap-y-4 gap-6">
                    <li v-for="tomorrowTimeSlot in tomorrowTimeSlots"
                        @click.prevent="()=>{selectDeliveryTime(tomorrowTimeSlot, isAdvanceOrderEnum.YES);closeModal('time-schedule-modal');}"
                        class="w-full h-10 leading-10 rounded-3xl text-center text-sm cursor-pointer border"
                        :class="checkoutProps.form.is_advance_order === isAdvanceOrderEnum.YES && checkoutProps.form.delivery_time === tomorrowTimeSlot.time ? 'bg-primary/5 border-primary/40' : 'border-gray-100 bg-gray-100'">
                        {{ tomorrowTimeSlot.label }}
                    </li>
                </ul>
            </div>

            <div v-if="todayTimeSlots.length === 0 && tomorrowTimeSlots.length === 0" class="tab-content tab-active">
                <div class="p-4 grid grid-cols-2 gap-y-4 gap-6">
                    {{ $t('message.no_schedule_found') }}
                </div>
            </div>
        </div>
    </div>
</template>
<script>
import {useModal} from "../../../composables/modal.js";
import {useTab} from "../../../composables/tab.js";
import discountEnum from "../../../enums/modules/discountEnum.js";
import labelEnum from "../../../enums/modules/labelEnum.js";
import statusEnum from "../../../enums/modules/statusEnum.js";
import activityEnum from "../../../enums/modules/activityEnum.js";
import orderTypeEnum from "../../../enums/modules/orderTypeEnum.js";
import sourceEnum from "../../../enums/modules/sourceEnum.js";
import scheduleEnum from "../../../enums/modules/scheduleEnum.js";
import isAdvanceOrderEnum from "../../../enums/modules/isAdvanceOrderEnum.js";
import askEnum from "../../../enums/modules/askEnum.js";
import {useFrontendAddressStore} from "../../../stores/frontendAddress.js";
import {useFrontendTimeSlotStore} from "../../../stores/frontendTimeSlot.js";
import {useFrontendPaymentGatewayStore} from "../../../stores/frontendPaymentGateway.js";
import _ from "lodash";
import {useFrontendCartStore} from "../../../stores/frontendCart.js";
import {useDineInContextStore} from "../../../stores/dineInContext.js";
import {useFrontendRiderTipStore} from "../../../stores/frontendRiderTip.js";
import {useCommonStore} from "../../../stores/common.js";
import DisplayModeEnum from "../../../enums/modules/displayModeEnum.js";
import {useFrontendSettingStore} from "../../../stores/frontendSetting.js";
import {useAuthStore} from "../../../stores/auth.js";
import alertService from "../../../services/alertService.js";
import {Swiper, SwiperSlide} from 'swiper/vue';
import appService from "../../../services/appService.js";
import MapComponent from "../../common/MapComponent.vue";
import CouponComponent from "./CouponComponent.vue";
import AddressComponent from "./AddressComponent.vue";
import {useFrontendOrderStore} from "../../../stores/frontendOrder.js";
import ENV from "../../../config/env.js";
import LoadingComponent from "../../common/LoadingComponent.vue";

export default {
    name: "CheckoutComponent",
    components: {
        LoadingComponent,
        CouponComponent,
        Swiper,
        SwiperSlide,
        AddressComponent,
        MapComponent
    },
    setup() {
        const {closeModal, openModal}     = useModal();
        const {handleTab}                 = useTab();
        const authStore                   = useAuthStore();
        const commonStore                 = useCommonStore();
        const frontendCartStore           = useFrontendCartStore();
        const dineInContextStore          = useDineInContextStore();
        const frontendOrderStore          = useFrontendOrderStore();
        const frontendSettingStore        = useFrontendSettingStore();
        const frontendAddressStore        = useFrontendAddressStore();
        const frontendTimeSlotStore       = useFrontendTimeSlotStore();
        const frontendRiderTipStore       = useFrontendRiderTipStore();
        const frontendPaymentGatewayStore = useFrontendPaymentGatewayStore();

        return {
            openModal,
            closeModal,
            handleTab,
            authStore,
            commonStore,
            frontendCartStore,
            dineInContextStore,
            frontendOrderStore,
            frontendSettingStore,
            frontendAddressStore,
            frontendTimeSlotStore,
            frontendRiderTipStore,
            frontendPaymentGatewayStore
        }
    },
    data() {
        return {
            loading: {
                isActive: false,
            },
            mapShow: false,
            addressShow: true,
            cutleryModel: false,
            mapKey: "restaurant",
            schedule: scheduleEnum.TODAY,
            scheduleTab: scheduleEnum.TODAY,
            localDeliveryTimeLabel: null,
            localOrderType: null,
            localAddress: {},
            paymentGateways: [],
            credit: {},
            cashOnDelivery: {},
            orderTypeEnum: orderTypeEnum,
            activityEnum: activityEnum,
            statusEnum: statusEnum,
            labelEnum: labelEnum,
            sourceEnum: sourceEnum,
            isAdvanceOrderEnum: isAdvanceOrderEnum,
            scheduleEnum: scheduleEnum,
            discountEnum: discountEnum,
            checkoutProps: {
                form: {
                    restaurant_id: 0,
                    subtotal: 0,
                    discount: 0,
                    delivery_fee: 0,
                    delivery_time: null,
                    total: 0,
                    tax: 0,
                    order_type: null,
                    is_advance_order: isAdvanceOrderEnum.NO,
                    source: sourceEnum.WEB,
                    address_id: null,
                    cutlery: askEnum.NO,
                    coupon_id: null,
                    payment_method: null,
                    service_fee: 0,
                    rider_tip: 0,
                    extra_delivery_fee: null,
                    table_id: null,
                    qr_token: null,
                    items: []
                }
            },
            addressProps: {
                form: {
                    address: "",
                    apartment: "",
                    latitude: "",
                    longitude: "",
                    label: "",
                },
                search: {
                    paginate: 0,
                    order_column: 'id',
                    order_type: 'asc'
                },
                status: false,
                switchLabel: "",
                isMap: false,
            },
        }
    },
    computed: {
        displayMode: function () {
            return this.commonStore.display_mode === DisplayModeEnum.LTR ? 'ltr' : 'rtl';
        },
        setting: function () {
            return this.frontendSettingStore.lists;
        },
        profile: function () {
            return this.authStore.info;
        },
        restaurant: function () {
            return this.frontendCartStore.restaurant;
        },
        orderType: function () {
            return this.frontendCartStore.orderType;
        },
        isDineIn: function () {
            const orderType = Number(this.orderType ?? this.checkoutProps?.form?.order_type);
            const tableId = Number(
                this.frontendCartStore.tableId
                || this.checkoutProps?.form?.table_id
                || this.dineInContextStore.context?.table_id
                || 0
            );
            if (orderType === orderTypeEnum.DINING_TABLE || tableId > 0) {
                return true;
            }
            return this.dineInContextStore.isActive
                && this.dineInContextStore.matchesRestaurant(this.restaurant?.id || this.restaurant?.slug);
        },
        cashPaymentLabel: function () {
            return this.$t('label.pay_at_counter');
        },
        dineInTableLabel: function () {
            return this.dineInContextStore.tableLabel || this.$t('label.dining_table');
        },
        carts: function () {
            return this.frontendCartStore.lists;
        },
        subtotal: function () {
            return this.frontendCartStore.subtotal;
        },
        tax: function () {
            return this.frontendCartStore.tax;
        },
        total: function () {
            return this.frontendCartStore.total;
        },
        addresses: function () {
            return this.frontendAddressStore.lists;
        },
        nowTimeSlot: function () {
            return this.frontendTimeSlotStore.now;
        },
        todayTimeSlots: function () {
            return this.frontendTimeSlotStore.today;
        },
        tomorrowTimeSlots: function () {
            return this.frontendTimeSlotStore.tomorrow;
        },
        serviceFee: function () {
            return this.frontendCartStore.serviceFee;
        },
        cutlery: function () {
            return this.frontendCartStore.cutlery;
        },
        paymentMethod: function () {
            return this.frontendCartStore.paymentMethod;
        },
        riderTipMethod: function () {
            return this.frontendCartStore.riderTip
        },
        riderTips: function () {
            return this.frontendRiderTipStore.lists;
        },
        discount: function () {
            return this.frontendCartStore.discount;
        },
        cartCoupon: function () {
            return this.frontendCartStore.coupon;
        },
        address: function () {
            return this.frontendCartStore.address;
        },
        timeSlot: function () {
            return this.frontendCartStore.timeSlot;
        }
    },
    mounted() {
        if (this.isDineIn) {
            this.frontendCartStore.applyDineInContext(
                this.dineInContextStore.context || {
                    table_id: this.frontendCartStore.tableId,
                    qr_token: this.frontendCartStore.qrToken,
                }
            );
            this.checkoutProps.form.order_type = orderTypeEnum.DINING_TABLE;
            this.checkoutProps.form.table_id = this.frontendCartStore.tableId || this.dineInContextStore.context?.table_id;
            this.checkoutProps.form.qr_token = this.frontendCartStore.qrToken || this.dineInContextStore.context?.qr_token;
            this.checkoutProps.form.delivery_fee = 0;
            this.checkoutProps.form.address_id = null;
            this.checkoutProps.form.is_advance_order = isAdvanceOrderEnum.NO;
            this.checkoutProps.form.delivery_time = null;
        } else {
            this.checkoutProps.form.order_type = this.orderType;
        }
        this.loading.isActive              = true;
        this.frontendAddressStore.fetch(this.addressProps).then(res => {
            this.loading.isActive = false;
            if (!this.isDineIn && res.data.data.length > 0) {
                if (Object.keys(this.address).length > 0) {
                    this.checkoutProps.form.address_id = this.address.id
                    this.localAddress                  = this.address;
                    this.deliveryChargeCalculation();
                }
            }
        }).catch((err) => {
            this.loading.isActive = false;
        });
        if (!this.isDineIn) {
            this.loading.isActive = true;
            this.frontendTimeSlotStore.fetchToday(this.restaurant.id).then(res => {
                this.loading.isActive = false;
            }).catch((err) => {
                this.loading.isActive = false;
            });

            this.loading.isActive = true;
            this.frontendTimeSlotStore.fetchTomorrow(this.restaurant.id).then(res => {
                this.loading.isActive = false;
            }).catch((err) => {
                this.loading.isActive = false;
            });
        }

        this.loading.isActive = true;
        this.frontendPaymentGatewayStore.fetch({status: this.statusEnum.ACTIVE}).then(res => {
            if (res.data.data.length > 0) {
                _.forEach(res.data.data, (gateway) => {
                    if (gateway.slug === "credit") {
                        this.credit = gateway;
                    } else if (gateway.slug === "cashondelivery") {
                        this.cashOnDelivery = {
                            ...gateway,
                            // Always use contextual UI label — ignore DB "Cash On Delivery" name
                            name: this.cashPaymentLabel,
                        };
                    } else {
                        this.paymentGateways.push(gateway);
                    }
                });
            }
            this.loading.isActive = false;
        }).catch((err) => {
            this.loading.isActive = false;
        });

        this.loading.isActive = true;
        this.frontendRiderTipStore.fetch({
            order_column: "id",
            order_type: "asc"
        }).then(res => {
            this.loading.isActive = false;
        }).catch((err) => {
            this.loading.isActive = false;
        });

        if (!this.isDineIn && Object.keys(this.timeSlot).length > 0) {
            this.localDeliveryTimeLabel              = this.timeSlot.label;
            this.checkoutProps.form.delivery_time    = this.timeSlot.delivery_time;
            this.checkoutProps.form.is_advance_order = this.timeSlot.is_advance_order;
            this.schedule                            = this.timeSlot.schedule;
            this.scheduleTab                         = this.timeSlot.scheduleTab;
        }

        this.cutleryModel = this.cutlery;
        window.setTimeout(() => {
            this.mapShow = true;
        }, 1000);
    },
    methods: {
        restaurantPosition: function (e) {
            window.setTimeout(() => {
                this.deliveryChargeCalculation();
            }, 1000);
        },
        locationIcon: function (label) {
            let icon = ''
            if (label === 'Home') {
                icon = 'lab-fill-home';
            } else if (label === 'Work') {
                icon = 'lab-fill-briefcase';
            } else {
                icon = 'lab-fill-box';
            }
            return icon;
        },
        scheduleTabHandel: function (e, id, schedule) {
            this.scheduleTab = schedule;
            this.handleTab(e, id);
        },
        changeOrderType: function (e) {
            if (this.isDineIn) {
                return;
            }
            this.localOrderType = e;
            this.frontendCartStore.callUpdateOrderType(this.localOrderType);
            this.checkoutProps.form.order_type = e;
        },
        quantityIncrement: function (id) {
            this.frontendCartStore.setQuantity({id: id, status: "increment"}).catch(error => {
                if (error === 'max_quantity_error') {
                    alertService.error(this.$t('message.already_added_max_quantity'));
                }
            });
        },
        quantityDecrement: function (id) {
            const slug = this.restaurant?.slug;
            this.frontendCartStore.setQuantity({id: id, status: "decrement"});
            if (this.frontendCartStore.lists.length === 0) {
                if (slug) {
                    this.$router.push({
                        name: 'frontend.singleRestaurant',
                        params: {slug},
                    });
                } else {
                    this.$router.push({name: 'frontend.home'});
                }
            }
        },
        removeItem: function (id) {
            const slug = this.restaurant?.slug;
            this.frontendCartStore.removeItem(id);
            if (this.frontendCartStore.lists.length === 0) {
                if (slug) {
                    this.$router.push({
                        name: 'frontend.singleRestaurant',
                        params: {slug},
                    });
                } else {
                    this.$router.push({name: 'frontend.home'});
                }
            }
        },
        currencyFormat: function (amount, decimal, currency, position) {
            return appService.currencyFormat(amount, decimal, currency, position);
        },
        coupon: function (e) {
            if (Object.keys(e).length !== 0) {
                this.checkoutProps.form.coupon_id = e.id;
            } else {
                this.checkoutProps.form.coupon_id = null;
            }

            window.setTimeout(() => {
                this.deliveryChargeCalculation();
            }, 1000);
        },
        editAddress: async function () {
            if (typeof this.localAddress === "object" && this.checkoutProps.form.address_id !== null) {
                this.loading.isActive = true;
                this.frontendAddressStore.edit(this.checkoutProps.form.address_id)
                this.loading.isActive = false;

                this.addressProps.form.address   = this.localAddress.address;
                this.addressProps.form.apartment = this.localAddress.apartment;
                this.addressProps.form.latitude  = this.localAddress.latitude;
                this.addressProps.form.longitude = this.localAddress.longitude;
                this.addressProps.form.label     = this.localAddress.label;

                if (this.addressProps.form.label !== labelEnum.HOME && this.addressProps.form.label !== labelEnum.WORK) {
                    this.addressProps.status      = true;
                    this.addressProps.switchLabel = labelEnum.OTHER;
                } else {
                    this.addressProps.switchLabel = this.localAddress.label;
                }

                this.addressProps.isMap = false;
                this.openModal('new-address-modal');
            }
        },
        changeAddress: function (address) {
            const addressLat = parseFloat(address.latitude);
            const addressLng = parseFloat(address.longitude);
            const restaurantLat = parseFloat(this.restaurant.latitude);
            const restaurantLng = parseFloat(this.restaurant.longitude);
            const hasCoords = !Number.isNaN(addressLat) && !Number.isNaN(addressLng)
                && !Number.isNaN(restaurantLat) && !Number.isNaN(restaurantLng);

            // Allow address when coords are incomplete (manual testing without maps)
            const withinRadius = !hasCoords
                || appService.distance(addressLat, addressLng, restaurantLat, restaurantLng)
                    <= this.setting.site_delivery_boy_order_radius;

            if (withinRadius) {
                this.localAddress                  = address;
                this.checkoutProps.form.address_id = address.id;
                this.deliveryChargeCalculation();
                this.frontendCartStore.setAddress(this.localAddress);
            } else {
                this.localAddress                  = {};
                this.checkoutProps.form.address_id = null;
                this.deliveryChargeCalculation();
                alertService.error(this.$t('message.restaurant_does_not_deliver'));
                this.frontendCartStore.setAddress(this.localAddress);
            }
        },
        updateAddress: function (address) {
            this.changeAddress(address);
        },
        selectDeliveryTime: function (timeSlot, advance = isAdvanceOrderEnum.NO) {
            this.localDeliveryTimeLabel              = timeSlot.label;
            this.checkoutProps.form.delivery_time    = timeSlot.time;
            this.checkoutProps.form.is_advance_order = advance;

            this.frontendCartStore.setTimeSlot({
                scheduleTab: this.scheduleTab,
                schedule: scheduleEnum.TOMORROW,
                label: this.localDeliveryTimeLabel,
                delivery_time: this.checkoutProps.form.delivery_time,
                is_advance_order: this.checkoutProps.form.is_advance_order
            });
        },
        selectNowDeliveryTime: function (timeSlot) {
            this.localDeliveryTimeLabel              = null
            this.checkoutProps.form.delivery_time    = timeSlot.time;
            this.checkoutProps.form.is_advance_order = isAdvanceOrderEnum.NO;
            this.frontendCartStore.setTimeSlot({
                scheduleTab: scheduleEnum.TODAY,
                schedule: scheduleEnum.NOW,
                label: this.localDeliveryTimeLabel,
                delivery_time: this.checkoutProps.form.delivery_time,
                is_advance_order: this.checkoutProps.form.is_advance_order
            });
        },
        changeCutlery: function (e) {
            this.frontendCartStore.setCutlery(e.target.checked);
        },
        selectRiderTip: function (riderTip) {
            this.frontendCartStore.setRiderTip(riderTip);
        },
        selectPaymentMethod: function (paymentMethod) {
            this.frontendCartStore.setPaymentMethod(paymentMethod);
        },
        deliveryChargeCalculation: function () {
            if (this.checkoutProps.form.order_type === orderTypeEnum.DELIVERY) {
                if ((typeof this.localAddress.latitude !== 'undefined' && this.localAddress.latitude !== '') && (typeof this.localAddress.longitude !== 'undefined' && this.localAddress.longitude !== '') && (typeof this.restaurant.latitude !== 'undefined' && this.restaurant.latitude !== '') && (typeof this.restaurant.longitude !== 'undefined' && this.restaurant.longitude !== '')) {
                    const distance = appService.distance(parseFloat(this.localAddress.latitude), parseFloat(this.localAddress.longitude), parseFloat(this.restaurant.latitude), parseFloat(this.restaurant.longitude));
                    if (distance > this.setting.delivery_setup_free_delivery_kilometer) {
                        let extraDistance                    = distance - parseFloat(this.setting.delivery_setup_free_delivery_kilometer);
                        this.checkoutProps.form.delivery_fee = (extraDistance * parseFloat(this.setting.delivery_setup_charge_per_kilo) + parseFloat(this.setting.delivery_setup_basic_delivery_fee));
                    } else {
                        this.checkoutProps.form.delivery_fee = parseFloat(this.setting.delivery_setup_basic_delivery_fee);
                    }
                } else {
                    this.checkoutProps.form.delivery_fee = 0;
                }

                this.checkoutProps.form.extra_delivery_fee = null;
                if (Object.keys(this.cartCoupon).length > 0 && this.cartCoupon.type === discountEnum.FREE_DELIVERY) {
                    this.checkoutProps.form.extra_delivery_fee = this.checkoutProps.form.delivery_fee;
                    this.checkoutProps.form.delivery_fee       = 0;
                }
            }
        },
        orderSubmit: function () {
            this.loading.isActive                  = true;
            this.checkoutProps.form.restaurant_id  = Object.keys(this.restaurant).length > 0 ? this.restaurant.id : 0;
            this.checkoutProps.form.subtotal       = this.subtotal;
            this.checkoutProps.form.total          = parseFloat(this.total + this.checkoutProps.form.delivery_fee);
            this.checkoutProps.form.tax            = this.tax;
            this.checkoutProps.form.discount       = this.discount;
            this.checkoutProps.form.coupon_id      = Object.keys(this.cartCoupon).length > 0 ? this.cartCoupon.id : 0;
            this.checkoutProps.form.payment_method = Object.keys(this.paymentMethod).length > 0 ? this.paymentMethod.id : 0;
            this.checkoutProps.form.cutlery        = this.cutleryModel ? askEnum.YES : askEnum.NO;
            this.checkoutProps.form.service_fee    = this.serviceFee;
            this.checkoutProps.form.rider_tip      = Object.keys(this.riderTipMethod).length > 0 ? this.riderTipMethod.amount : 0;
            this.checkoutProps.form.items          = [];

            if (this.isDineIn) {
                this.checkoutProps.form.order_type = orderTypeEnum.DINING_TABLE;
                this.checkoutProps.form.table_id = this.frontendCartStore.tableId || this.dineInContextStore.context?.table_id;
                this.checkoutProps.form.qr_token = this.frontendCartStore.qrToken || this.dineInContextStore.context?.qr_token;
                this.checkoutProps.form.delivery_fee = 0;
                this.checkoutProps.form.rider_tip = 0;
                this.checkoutProps.form.address_id = null;
                this.checkoutProps.form.delivery_time = null;
                this.checkoutProps.form.is_advance_order = isAdvanceOrderEnum.NO;
                this.checkoutProps.form.total = parseFloat(this.total);
            } else {
                this.checkoutProps.form.table_id = null;
                this.checkoutProps.form.qr_token = null;
            }

            _.forEach(this.carts, (item) => {
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

            this.frontendOrderStore.save(this.checkoutProps.form).then(orderResponse => {
                this.loading.isActive = false;
                const wasDineIn = this.isDineIn;
                const paymentSlug = Object.keys(this.paymentMethod).length > 0 ? this.paymentMethod.slug : '';
                this.frontendCartStore.callResetCart();
                if (wasDineIn) {
                    this.dineInContextStore.clear();
                }
                if (paymentSlug) {
                    window.location.href = ENV.API_URL + "/payment/" + paymentSlug + "/pay/" + orderResponse.data.data.id;
                } else {
                    alertService.error(this.$t('message.payment_method_required'));
                }
            }).catch((err) => {
                this.loading.isActive = false;
                if (typeof err.response.data.errors === 'object') {
                    _.forEach(err.response.data.errors, (error) => {
                        alertService.error(error[0]);
                    });
                }
            })
        }
    },
    watch: {
        orderType: {
            deep: true,
            handler(orderTypeObject) {
                this.checkoutProps.form.order_type = orderTypeObject;
                if (orderTypeObject === orderTypeEnum.TAKEAWAY) {
                    this.mapShow                         = true;
                    this.checkoutProps.form.delivery_fee = 0;
                } else {
                    this.deliveryChargeCalculation();
                }
            }
        }
    }
}
</script>
