<template>
    <div v-if="isMenuFixed && carts.length > 0" @click.prevent="openCanvas('cart-canvas')"
        class="fixed lg:block hidden top-1/2 -translate-y-1/2 ltr:right-0 rtl:left-0 ltr:rounded-l-xl rtl:rounded-r-xl z-30 overflow-hidden cursor-pointer">
        <div class="flex flex-col items-center justify-center text-center gap-1 py-2 px-3.5 bg-primary text-white">
            <i class="lab-fill-bag text-2xl leading-none"></i>
            <span class="text-sm font-medium">{{ carts.length }} {{ $t('label.items') }}</span>
        </div>
        <span class="text-sm font-medium py-2 px-3.5 bg-heading text-white w-full">
            {{
                currencyFormat(subtotal, setting.site_digit_after_decimal_point, setting.site_default_currency_symbol,
                    setting.site_currency_position)
            }}
        </span>
    </div>

    <section class="mb-24 md:mb-14">
        <div class="container">
            <DineInBannerComponent
                :visible="isDineInSession"
                :table-label="dineInContextStore.tableLabel"
                :zone="dineInContextStore.context?.zone || ''"
                :allow-exit="true"
                @exit="exitDineIn"/>
            <div class="relative pb-2">
                <img :src="restaurant.cover" alt="banner"
                    class="rounded-[0%_0%_30%_30%/_0%_0%_15%_15%] w-full h-[200px] md:h-[250px] object-cover">
                <img :src="restaurant.logo" alt="logo"
                    class="absolute -bottom-[3%] ltr:left-6 rtl:right-6 w-16 sm:w-20 h-16 sm:h-20 object-cover rounded-full border-2 border-white">
            </div>
            <div class="flex flex-col sm:flex-row items-start justify-between gap-y-6 gap-x-4 py-6">
                <div>
                    <h3 class="text-2xl sm:text-[40px] leading-10 font-semibold capitalize mb-4">
                        {{ restaurant.name }}
                    </h3>
                    <ul class="mb-2.5 flex flex-wrap gap-y-2.5 gap-x-1.5 w-full max-w-xl text-paragraph">
                        <li class="text-sm flex items-center gap-1.5">
                            {{ restaurant.cuisine }}
                        </li>
                        <li class="text-sm flex items-center gap-1.5 before:content-[''] before:w-1 before:h-1 before:rounded-full before:bg-paragraph">
                            {{ restaurant.distance }} {{ $t('label.km') }}
                        </li>
                        <li v-if="restaurant.rating_star > 0 && restaurant.rating_star_count > 0"
                            class="text-sm flex items-center gap-1.5 before:content-[''] before:w-1 before:h-1 before:rounded-full before:bg-paragraph">
                            <div class="flex items-center gap-1">
                                <i class="lab-fill-star-round -mt-0.5 text-amber-500"></i>
                                <b class="font-normal text-heading">
                                    {{ (restaurant.rating_star / restaurant.rating_star_count).toFixed(1) }}
                                </b>
                                <p>
                                    ({{ restaurant.rating_star_count }}
                                    {{ restaurant.rating_star_count > 1 ? $t('label.reviews') : $t('label.review') }})
                                </p>
                            </div>
                        </li>
                    </ul>
                    <ul class="flex flex-wrap gap-y-2.5 gap-x-1.5 w-full max-w-2xl text-paragraph">
                        <li
                            class="text-sm flex items-center gap-1.5">
                            <div class="flex items-center gap-1">
                                <i :class="restaurant.availability === enums.availabilityEnum.OPEN ? 'text-green-500' : 'text-red-500'"
                                    class="lab-line-clock -mt-0.5 "></i>
                                <p :class="restaurant.availability === enums.availabilityEnum.OPEN ? 'text-green-500' : 'text-red-500'"
                                    class="capitalize">{{
                                        restaurant.availability === enums.availabilityEnum.OPEN ? $t('label.open_now') :
                                            $t('label.close_now')
                                    }}</p>
                            </div>
                        </li>
                        <li
                            class="text-sm flex items-center gap-1.5 before:content-[''] before:w-1 before:h-1 before:rounded-full before:bg-paragraph">
                            {{ restaurant.today }}
                            <span v-if="restaurant.single_time_slots">, {{ restaurant.single_time_slots }}</span>
                        </li>
                        <li
                            class="text-sm flex items-center gap-1.5 before:content-[''] before:w-1 before:h-1 before:rounded-full before:bg-paragraph">
                            {{ $t('label.minimum_order_limit') }} : {{ minimumOrderLimit }}
                        </li>
                    </ul>
                </div>

                <nav class="flex items-center sm:self-end gap-3">
                    <button @click.prevent="openInfoModal"
                        class="flex items-center gap-2 h-11 px-4 rounded-3xl transition-all bg-gray-100 hover:text-primary text-heading">
                        <i class="lab-line-info-circle text-xl"></i>
                        <span class="capitalize text-sm font-medium whitespace-nowrap">
                            {{ $t('label.more_info') }}
                        </span>
                    </button>
                    <button @click.prevent="favorite(restaurant, restaurant.favorite = !restaurant.favorite)"
                        class="flex items-center justify-center h-11 w-11 rounded-full transition-all bg-gray-100 hover:text-primary">
                        <i :class="restaurant.favorite ? 'lab-fill-heart text-primary' : 'lab-line-heart'"
                            class="text-xl"></i>
                    </button>
                </nav>
            </div>

            <div
                class="mb-5 overflow-hidden rounded-2xl border-2 border-amber-400 bg-gradient-to-br from-amber-50 via-orange-50 to-rose-50 shadow-[0_8px_24px_rgba(245,158,11,0.18)]"
            >
                <div class="flex gap-3 p-4 sm:p-5">
                    <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full bg-amber-500 text-lg shadow-md">
                        ⚠️
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="text-sm font-bold tracking-wide text-amber-900 sm:text-base">
                            {{ $t('label.important_notice') }}
                        </p>
                        <p class="mt-1.5 text-sm leading-6 text-amber-950">
                            {{ $t('message.restaurant_platform_disclaimer') }}
                        </p>
                        <p class="mt-2 text-sm font-medium leading-6 text-amber-900">
                            {{ $t('message.restaurant_contact_disclaimer') }}
                        </p>
                    </div>
                </div>
            </div>

            <div v-if="coupons.length > 0" class="pt-3 sm:pt-4 pb-4 sm:pb-6 border-t border-gray-100">
                <h3 class="sm:text-xl font-medium capitalize mb-2.5">{{ $t('label.available_deals') }}</h3>
                <Swiper :dir="displayMode" :loop="false" :speed="1000" :navigation="true" :modules="modules" :breakpoints="couponBreakPoints" class="middle-navigate">
                    <SwiperSlide @click.prevent="openCouponModal(coupon.id)" v-for="coupon in coupons" class="mobile:!w-60 cursor-pointer">
                        <div class="w-full p-3 rounded-lg border border-gray-100">
                            <h3 class="flex items-center gap-1 mb-1.5">
                                <i v-if="coupon.type === enums.discountEnum.DEFAULT" :class="coupon.discount_type === enums.discountTypeEnum.PERCENTAGE ? 'lab-line-offers' : 'lab-line-coupon'" class="text-base leading-none -mt-[0.5px] text-primary"></i>
                                <i v-if="coupon.type === enums.discountEnum.FREE_DELIVERY"
                                    class="lab-line-bike text-base leading-none -mt-[0.5px] text-primary"></i>
                                <span v-if="coupon.type === enums.discountEnum.DEFAULT"
                                    class="text-sm font-medium text-primary">
                                    {{ coupon.discount_alt }} {{ $t('label.off') }}
                                </span>
                                <span v-if="coupon.type === enums.discountEnum.FREE_DELIVERY"
                                    class="text-sm font-medium text-primary">
                                    {{ textShortener(coupon.name, 14) }}
                                </span>
                                <span class="text-sm text-secondary uppercase">({{
                                    textShortener(coupon.code, 10)
                                }})</span>
                            </h3>
                            <h4 v-if="coupon.type === enums.discountEnum.DEFAULT" class="text-xs mb-1">
                                {{
                                    coupon.minimum_order > 0 ? $t('message.discount_off_above', {
                                        discount: coupon.discount_alt,
                                        min_order: coupon.minimum_order_currency_amount
                                    }) : $t('message.discount_off', { discount: coupon.discount_alt })
                                }}
                            </h4>

                            <h4 v-if="coupon.type === enums.discountEnum.FREE_DELIVERY" class="text-xs mb-1">
                                {{
                                    coupon.minimum_order > 0 ? textShortener($t('message.discount_delivery_off_above',
                                        { min_order: coupon.minimum_order_currency_amount }), 40) :
                                        $t('message.discount_delivery_off')
                                }}
                            </h4>
                            <p class="text-xs text-paragraph">{{ $t('label.use_in_checkout') }}</p>
                        </div>
                    </SwiperSlide>
                </Swiper>
            </div>

            <div :class="isMenuFixed ? 'fixed top-0 left-0 w-full z-40 px-3 py-3 shadow-xs bg-white' : 'py-4 border-y border-gray-100'" v-if="categoryWiseItems && categoryWiseItems.length">
                <div :class="isMenuFixed ? 'w-full max-w-6xl mx-auto' : 'w-full'">
                    <div :class="isMenuFixed ? 'mb-4' : 'mb-7'" class="flex items-center justify-between gap-3">
                        <h3 v-if="!isMenuFixed" class="sm:text-xl font-medium capitalize min-w-[150px] whitespace-nowrap overflow-hidden text-ellipsis">
                            {{ $t('label.restaurant_menu') }}
                        </h3>

                        <div v-if="isMenuFixed" class="flex items-center gap-2 overflow-hidden">
                            <router-link :to="{ name: 'frontend.restaurant' }">
                                <i class="lab-line-chevron-left text-lg font-bold"></i>
                            </router-link>
                            <h3 class="sm:text-xl font-medium capitalize min-w-[120px] whitespace-nowrap overflow-hidden text-ellipsis">
                                {{ restaurant.name }}
                            </h3>
                        </div>

                        <nav class="flex gap-4">
                            <button
                                @click.prevent="itemType === enums.itemTypeEnum.NON_VEG ? itemType = null : itemType = enums.itemTypeEnum.NON_VEG"
                                :class="itemType === enums.itemTypeEnum.NON_VEG ? 'shadow-filter bg-white' : 'bg-slate-100'"
                                class="flex-shrink-0 hidden sm:flex items-center gap-2 h-8 px-3 rounded-3xl transition-all duration-300">
                                <img :src="setting.image_non_vag" alt="food-type" class="h-4 drop-shadow-mealtype">
                                <span class="capitalize text-sm font-medium text-heading">{{
                                    $t('label.non_veg')
                                }}</span>
                                <i :class="itemType === enums.itemTypeEnum.NON_VEG ? 'ltr:me-0 rtl:ms-0' : 'ltr:-me-5 rtl:-ms-5 opacity-0'"
                                    class="lab-line-circle-cross text-sm text-red-500 transition-all duration-300"></i>
                            </button>
                            <button
                                @click.prevent="itemType === enums.itemTypeEnum.VEG ? itemType = null : itemType = enums.itemTypeEnum.VEG"
                                :class="itemType === enums.itemTypeEnum.VEG ? 'shadow-filter bg-white' : 'bg-slate-100'"
                                class="flex-shrink-0 hidden sm:flex items-center gap-2 h-8 px-3 rounded-3xl transition-all duration-300">
                                <img :src="setting.image_vag" alt="food-type" class="h-4 drop-shadow-mealtype">
                                <span class="capitalize text-sm font-medium text-heading">{{ $t('label.veg') }}</span>
                                <i :class="itemType === enums.itemTypeEnum.VEG ? 'ltr:me-0 rtl:ms-0' : 'ltr:-me-5 rtl:-ms-5 opacity-0'"
                                    class="lab-line-circle-cross text-sm text-red-500 transition-all duration-300"></i>
                            </button>
                            <form @submit.prevent="search"
                                class="group w-full max-w-[270px] h-8 rounded-3xl flex items-center gap-2 px-3 border border-slate-100 bg-slate-100 focus-within:border-secondary">
                                <button class="lab-line-search text-lg flex-shrink-0"></button>
                                <input type="search" @keyup="search" v-model="searchItem" :placeholder="$t('label.search_in_menu')"
                                    class="w-full placeholder:text-sm">
                                <button @click.prevent="searchReset"
                                    class="lab-fill-close-circle transition-all text-danger invisible group-focus-within:visible"></button>
                            </form>
                        </nav>
                    </div>

                    <Swiper :dir="displayMode" :speed="1000" :spaceBetween="0" :navigation="true" :modules="modules"
                        slidesPerView="auto" class="middle-navigate menu-categories">
                        <SwiperSlide v-for="(categoryWiseItem, categoryWiseItemIndex) in categoryWiseItems"
                            :key="categoryWiseItemIndex"
                            :class="{ '!bg-primary !text-white': currentSectionId === categoryWiseItem.slug }"
                            class="!w-fit text-sm whitespace-nowrap text-primary first-letter:capitalize px-3 h-8 leading-8 rounded-3xl hover:bg-primary/10 transition-all duration-500 cursor-pointer"
                            @click="handleMenuCategory($event, categoryWiseItem.slug)">
                            {{ categoryWiseItem.name }}
                        </SwiperSlide>
                    </Swiper>
                </div>
            </div>
            <div class="text-center text-gray-500 py-4" v-else>
                <h3 class="sm:text-xl font-medium capitalize mb-2.5">{{ $t('message.no_menu_available') }}</h3>
            </div>

            <dl v-if="searchItem.length > 0" class="mt-8">
                <dt class="mb-5 text-2xl font-semibold capitalize text-heading">
                    {{ $t("message.we_found", { length: searchItems.length, search: searchItem }) }}
                </dt>
                <dd class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6 bg-gray-100 rounded-lg p-5">
                    <ItemComponent :offer="checkOffer" :type="itemType" :itemIndex="100000" :items="searchItems" />
                </dd>
            </dl>

            <dl v-for="(categoryWiseItem, categoryWiseItemIndex) in categoryWiseItems" :key="categoryWiseItemIndex"
                :id="categoryWiseItem.slug" class="mt-8">
                <dt class="mb-5 text-2xl font-semibold capitalize text-heading">{{ categoryWiseItem.name }}</dt>
                <dd class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6">
                    <div v-if="categoryWiseItem.items.length === 0" class="text-lg font-normal">{{ $t('message.item_not_found') }}</div>
                    <div v-else-if="categoryWiseItem.items.filter(item => itemType == null || item.item_type === itemType).length === 0" class="text-lg font-normal">{{ $t('message.item_not_found') }}</div>
                    <ItemComponent v-if="categoryWiseItem.items.length > 0" :offer="checkOffer" :type="itemType" :itemIndex="categoryWiseItemIndex" :items="categoryWiseItem.items" />
                </dd>
            </dl>
        </div>
    </section>

    <div id="coupon-info-modal"
        class="fixed inset-0 z-50 p-3 w-screen h-dvh overflow-y-auto bg-black/50 transition-all duration-300 opacity-0 invisible">
        <div class="max-w-lg w-full rounded-xl mx-auto bg-white transition-all duration-300">
            <div class="flex items-center justify-between gap-4 py-4 px-6 border-b border-gray-100">
                <h3 class="text-lg font-semibold capitalize">{{ $t('label.offer_details') }}</h3>
                <button @click.prevent="closeCouponModal" class="lab-line-circle-cross text-lg text-danger"></button>
            </div>
            <div class="px-6 py-4">
                <h3 class="flex items-center gap-1.5 mb-4">
                    <i v-if="coupon.type === enums.discountEnum.DEFAULT" :class="coupon.discount_type === enums.discountTypeEnum.PERCENTAGE ? 'lab-line-offers' : 'lab-line-coupon'" class="text-xl text-primary"></i>
                    <i v-if="coupon.type === enums.discountEnum.FREE_DELIVERY" class="lab-line-bike text-xl text-primary"></i>
                    <span v-if="coupon.type === enums.discountEnum.DEFAULT" class="text-xl font-semibold">
                        {{ coupon.discount_alt }} {{ $t('label.off') }} ({{ coupon.code }})
                    </span>
                    <span v-if="coupon.type === enums.discountEnum.FREE_DELIVERY" class="text-xl font-semibold">
                        {{ coupon.name }} ({{ coupon.code }})
                    </span>
                </h3>
                <p class="text-sm mb-1 text-heading">{{ $t('message.new_and_existing_customers') }}</p>
                <p class="text-sm mb-4 text-heading">{{ $t('message.valid_for_all_items') }}</p>
                <p class="text-sm mb-4 text-heading">{{ $t('label.valid_from') }} {{ coupon.convert_start_date }} - {{ coupon.convert_end_date }}</p>
                <p v-if="coupon.minimum_order > 0" class="text-sm mb-1 text-heading">{{ $t('label.minimum_order') }} {{ coupon.minimum_order_currency_amount }}</p>
                <p v-if="coupon.maximum_discount > 0" class="text-sm mb-1 text-heading">{{$t('label.maximum_discount') }} {{ coupon.maximum_discount_currency_amount }}</p>
                <p class="text-sm mb-5 font-medium text-primary">{{ $t('label.use_in_checkout') }}</p>
                <button v-if="coupon.description" @click.prevent="handleSlide('coupon-terms-description-list')" class="flex items-center gap-2 transition-all duration-300 hover:text-primary">
                    <span class="text-base font-medium capitalize">{{ $t('label.terms_and_conditions') }}</span>
                    <i :class="toggleSlide ? 'rotate-180' : 'rotate-0'" class="lab-line-chevron-down font-semibold text-primary"></i>
                </button>
                <div v-if="coupon.description" id="coupon-terms-description-list"
                    class="h-0 overflow-hidden transition-all duration-300">
                    <div class="ql-ul-set editor-show-design px-2 py-1 overflow-hidden transition-all duration-300 text-xs"
                        v-html="coupon.description"></div>
                </div>
            </div>
        </div>
    </div>

    <div id="more-information"
        class="fixed inset-0 z-50 p-3 w-screen h-dvh overflow-y-auto bg-black/50 transition-all duration-300 opacity-0 invisible">
        <div class="w-full rounded-xl mx-auto bg-white transition-all duration-300 max-w-2xl">
            <div class="flex items-center justify-between gap-4 py-4 px-6">
                <h3 class="text-lg font-semibold capitalize">{{ restaurant.name }}</h3>
                <button @click.prevent="closeInfoModal" class="lab-line-circle-cross text-lg text-danger"></button>
            </div>

            <nav class="flex items-center px-6 py-2 border-b border-gray-100">
                <button @click.prevent="handleTab($event, 'information')"
                    class="tab-button tab-active text-sm capitalize rounded-2xl px-4 h-8 text-primary">
                    {{ $t('label.information') }}
                </button>
                <button @click.prevent="handleTab($event, 'reviews')"
                    class="tab-button text-sm capitalize rounded-2xl px-4 h-8 text-primary">
                    {{ $t('label.reviews') }}
                </button>
            </nav>

            <div id="information" class="tab-content tab-active p-6">
                <MapComponent v-if="mapShow" :location="{ lat: restaurant.latitude, lng: restaurant.longitude }"
                    :setting="{ autocomplete: false, mouseEvent: false, currentLocation: false }" />
                <ul class="w-full block">
                    <li class="flex items-center gap-3 py-3 border-b border-gray-100">
                        <i class="lab-fill-location text-2xl text-paragraph"></i>
                        <span>{{ restaurant.address }}</span>
                    </li>
                    <li class="flex items-center gap-3 py-3 border-b border-gray-100">
                        <i class="lab-fill-mail text-2xl text-paragraph"></i>
                        <span>{{ restaurant.email }}</span>
                    </li>
                    <li class="flex items-center gap-3 py-3 border-b border-gray-100">
                        <i class="lab-fill-call text-2xl text-paragraph"></i>
                        <span v-if="restaurant.phone" class="db-list-item-text w-full sm:w-1/2">{{
                            restaurant.country_code + "" + restaurant.phone
                        }}</span>
                    </li>
                    <li class="flex items-center gap-3 py-3 border-b border-gray-100">
                        <i class="lab-fill-clock text-2xl text-paragraph"></i>
                        <span>{{ $t('label.opening_time') }}</span>
                    </li>
                </ul>

                <ul class="w-full ltr:pl-9 rtl:pr-9 flex flex-col" v-if="enums.dayEnum.length > 0">
                    <li class="flex flex-col sm:flex-row" v-for="dayEnum in enums.dayEnum"
                    :key="dayEnum.id">
                        <p class="flex-shrink-0 w-24 mt-5 capitalize text-heading">
                            {{ dayEnum.name }}
                        </p>

                        <div class="flex items-center mr-4 flex-wrap">
                            <div class="flex items-center flex-wrap" v-if="restaurant.time_slots && restaurant.time_slots.filter(slot => slot.day === dayEnum.id).length > 0">
                                <div class="relative flex items-start gap-8 py-2 px-3 rounded-lg border border-[#EFF0F6] time-slot-gap"
                                    v-for="timeSlot in restaurant.time_slots.filter(slot => slot.day === dayEnum.id)"
                                    :key="timeSlot.id || (timeSlot.opening_time + '-' + timeSlot.closing_time)">
                                    <p class="text-paragraph">
                                        {{ timeSlot.opening_time + ' - ' + timeSlot.closing_time }}
                                    </p>
                                </div>
                            </div>
                            <div class="relative flex items-start gap-8 py-2 px-3 rounded-lg border border-[#EFF0F6] time-slot-gap" v-else>
                                <p class="text-paragraph">
                                    {{ $t('label.closed') }}
                                </p>
                            </div>
                        </div>
                    </li>
                </ul>
            </div>

            <div id="reviews" class="tab-content p-6">
                <h3 class="text-2xl font-medium capitalize mb-3">{{ $t('label.rating_and_reviews') }}</h3>

                <div v-if="restaurant.rating_star > 0 && restaurant.rating_star_count > 0"
                    class="flex items-baseline gap-1 mb-5">
                    <i class="lab-fill-star-round text-xl text-amber-500"></i>
                    <span class="text-lg font-medium">
                        {{ (restaurant.rating_star / restaurant.rating_star_count).toFixed(1) }}
                    </span>
                    <small class="text-sm capitalize text-paragraph">
                        ({{ restaurant.rating_star_count }}
                        {{ restaurant.rating_star_count > 1 ? $t('label.reviews') : $t('label.review') }})
                    </small>
                </div>
                <p class="text-sm text-heading mt-2" v-else>{{ $t('message.no_review_available') }}</p>

                <ul class="w-full flex flex-col gap-4">
                    <li v-for="(review, index) in restaurant.reviews" :key="index"
                        class="p-3 rounded-lg border border-gray-100">
                        <h4 class="text-lg font-medium capitalize mb-1">{{ review.name }}</h4>
                        <div class="flex items-center gap-0.5 mb-3">
                            <i :class="review.star >= 1 ? 'text-amber-500' : 'text-slate-500'"
                                class="lab-fill-star-round text-sm"></i>
                            <i :class="review.star >= 2 ? 'text-amber-500' : 'text-slate-500'"
                                class="lab-fill-star-round text-sm"></i>
                            <i :class="review.star >= 3 ? 'text-amber-500' : 'text-slate-500'"
                                class="lab-fill-star-round text-sm"></i>
                            <i :class="review.star >= 4 ? 'text-amber-500' : 'text-slate-500'"
                                class="lab-fill-star-round text-sm"></i>
                            <i :class="review.star >= 5 ? 'text-amber-500' : 'text-slate-500'"
                                class="lab-fill-star-round text-sm"></i>
                            <span class="text-xs text-paragraph">{{ review.created_at }}</span>
                        </div>
                        <p class="text-sm text-heading">{{ review.review }}</p>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</template>

<script>
import { Swiper, SwiperSlide } from 'swiper/vue';
import { useModal } from "../../../composables/modal.js";
import { useTab } from "../../../composables/tab.js";
import { useSlide } from "../../../composables/slide.js";
import { Navigation } from 'swiper/modules';
import dayEnum from "../../../enums/modules/dayEnum.js";
import availabilityEnum from "../../../enums/modules/availabilityEnum.js";
import appService from "../../../services/appService.js";
import { useFrontendSettingStore } from "../../../stores/frontendSetting.js";
import { useCommonStore } from "../../../stores/common.js";
import DisplayModeEnum from "../../../enums/modules/displayModeEnum.js";
import itemTypeEnum from "../../../enums/modules/itemTypeEnum.js";
import discountTypeEnum from "../../../enums/modules/discountTypeEnum.js";
import discountEnum from "../../../enums/modules/discountEnum.js";
import { useFrontendCouponStore } from "../../../stores/frontendCoupon.js";
import { useFrontendRestaurantStore } from "../../../stores/frontendRestaurant.js";
import { useFrontendItemCategoryStore } from "../../../stores/frontendItemCategory.js";
import ItemComponent from "../components/ItemComponent.vue";
import DineInBannerComponent from "../components/DineInBannerComponent.vue";
import { useFrontendItemStore } from "../../../stores/frontendItem.js";
import { useFrontendFavoriteStore } from "../../../stores/frontendFavorite.js";
import router from "../../../router/index.js";
import MapComponent from "../../common/MapComponent.vue";
import { useCanvas } from "../../../composables/canvas.js";
import { useFrontendCartStore } from "../../../stores/frontendCart.js";
import { useDineInContextStore } from "../../../stores/dineInContext.js";
import { useFrontendOfferStore } from "../../../stores/frontendOffer.js";


export default {
    name: "SingleRestaurantComponent",
    components: {
        MapComponent,
        ItemComponent,
        DineInBannerComponent,
        Swiper,
        SwiperSlide,
    },
    setup() {
        const { openModal, closeModal } = useModal();
        const { handleTab } = useTab();
        const { openCanvas } = useCanvas();
        const { handleSlide, toggleSlide } = useSlide(false);
        const commonStore = useCommonStore();
        const frontendCartStore = useFrontendCartStore();
        const dineInContextStore = useDineInContextStore();
        const frontendItemStore = useFrontendItemStore();
        const frontendOfferStore = useFrontendOfferStore();
        const frontendCouponStore = useFrontendCouponStore();
        const frontendSettingStore = useFrontendSettingStore();
        const frontendFavoriteStore = useFrontendFavoriteStore();
        const frontendRestaurantStore = useFrontendRestaurantStore();
        const frontendItemCategoryStore = useFrontendItemCategoryStore();

        return {
            handleTab,
            openModal,
            closeModal,
            openCanvas,
            handleSlide,
            toggleSlide,
            commonStore,
            frontendCartStore,
            dineInContextStore,
            frontendItemStore,
            frontendOfferStore,
            frontendCouponStore,
            frontendSettingStore,
            frontendFavoriteStore,
            frontendRestaurantStore,
            frontendItemCategoryStore,
            modules: [Navigation]
        }
    },
    data() {
        return {
            loading: {
                isActive: false,
            },
            enums: {
                dayEnum: dayEnum,
                availabilityEnum: availabilityEnum,
                itemTypeEnum: itemTypeEnum,
                discountTypeEnum: discountTypeEnum,
                discountEnum: discountEnum
            },
            minimumOrderLimit: 0,
            mapShow: false,
            isMenuFixed: false,
            itemType: null,
            searchItem: "",
            currentSectionId: null,
            couponBreakPoints: {
                0: { slidesPerView: 'auto', spaceBetween: 16 },
                640: { slidesPerView: 2, spaceBetween: 24 },
                768: { slidesPerView: 3, spaceBetween: 24 },
                1024: { slidesPerView: 4, spaceBetween: 24 }
            }
        }
    },
    computed: {
        setting: function () {
            return this.frontendSettingStore.lists;
        },
        restaurant: function () {
            return this.frontendRestaurantStore.show;
        },
        categoryWiseItems: function () {
            return this.frontendItemCategoryStore.categoryWiseItems;
        },
        searchItems: function () {
            return this.frontendItemStore.searchItems;
        },
        displayMode: function () {
            return this.commonStore.display_mode === DisplayModeEnum.LTR ? 'ltr' : 'rtl';
        },
        coupon: function () {
            return this.frontendCouponStore.show;
        },
        coupons: function () {
            return this.frontendCouponStore.lists;
        },
        carts: function () {
            return this.frontendCartStore.lists;
        },
        subtotal: function () {
            return this.frontendCartStore.subtotal;
        },
        checkOffer: function () {
            return this.frontendOfferStore.check;
        },
        isDineInSession: function () {
            return this.dineInContextStore.isActive
                && this.dineInContextStore.matchesRestaurant(this.$route.params.slug);
        }
    },
    mounted() {
        window.addEventListener('scroll', this.handleMenuFixed);
        this.syncDineInContext();
        if (typeof this.$route.params.slug !== "undefined") {
            this.loading.isActive = true;
            this.frontendRestaurantStore.view({
                slug: this.$route.params.slug,
                search: {
                    latitude: this.commonStore.latitude,
                    longitude: this.commonStore.longitude
                }
            }).then(res => {
                this.frontendOfferStore.fetchCheck({
                    id: res.data.data.id,
                    latitude: this.commonStore.latitude,
                    longitude: this.commonStore.longitude
                });
                this.frontendCouponStore.fetch(res.data.data.id);
                this.syncDineInContext();
                this.loading.isActive = false;
            }).catch((err) => {
                this.loading.isActive = false;
            });

            this.frontendItemCategoryStore.fetchCategoryWiseItems({
                slug: this.$route.params.slug,
                order_column: "sort",
                order_type: "asc"
            }).then(res => {
                this.loading.isActive = false;
            }).catch((err) => {
                this.loading.isActive = false;
            });
        }
    },
    methods: {
        syncDineInContext: function () {
            if (!this.dineInContextStore.isActive) {
                return;
            }
            if (!this.dineInContextStore.matchesRestaurant(this.$route.params.slug)) {
                this.dineInContextStore.clear();
                this.frontendCartStore.clearDineInContext();
                return;
            }
            this.frontendCartStore.applyDineInContext(this.dineInContextStore.context);
        },
        exitDineIn: function () {
            this.dineInContextStore.clear();
            this.frontendCartStore.clearDineInContext();
        },
        textShortener: function (text, number) {
            return appService.textShortener(text, number);
        },
        currencyFormat(amount, decimal, currency, position) {
            return appService.currencyFormat(amount, decimal, currency, position);
        },
        handleMenuFixed: function () {
            let scrollHeight = this.coupons.length > 0 ? 600 : 300;
            this.isMenuFixed = window.scrollY > scrollHeight;
            let section = document.querySelectorAll("dl");
            section.forEach((sec) => {
                let top = window.scrollY;
                let offset = sec.offsetTop - 150;
                let height = sec.offsetHeight;
                let id = sec.getAttribute("id");
                if (top >= offset && top < offset + height) {
                    this.currentSectionId = id;
                }
            });
        },
        openCouponModal: function (id) {
            this.loading.isActive = true;
            this.frontendCouponStore.view(id).then(res => {
                this.loading.isActive = false;
                this.openModal('coupon-info-modal');
            }).catch((err) => {
                this.loading.isActive = false;
            });
        },
        closeCouponModal: function () {
            this.closeModal('coupon-info-modal');
        },
        openInfoModal: function () {
            this.mapShow = true;
            this.openModal('more-information');
        },
        closeInfoModal: function () {
            this.mapShow = false;
            this.closeModal('more-information');
        },
        search: function () {
            let scrollHeight = this.coupons.length > 0 ? 500 : 350;
            window.scrollTo(0, scrollHeight);

            if (typeof this.$route.params.slug !== "undefined") {
                this.frontendItemStore.fetchSearchItems({
                    slug: this.$route.params.slug,
                    name: this.searchItem,
                }).then(res => {
                    this.loading.isActive = false;
                }).catch((err) => {
                    this.loading.isActive = false;
                })
            }
        },
        searchReset: function () {
            this.searchItem = "";
        },
        handleMenuCategory: function (event, id) {
            let scrollDiv = document.getElementById(id).offsetTop - 120 - (!this.isMenuFixed ? 200 : 0);
            window.scrollTo({ top: scrollDiv, behavior: 'smooth' });
        },
        favorite: function (restaurant, toggle) {
            this.frontendFavoriteStore.toggle({
                restaurant_id: restaurant.id,
                toggle: toggle
            }).then(res => {
            }).catch((err) => {
                if (err.response.status === 401) {
                    restaurant.favorite = false;
                    router.push({ name: "auth.login" });
                }
            });
        }
    },
    watch: {
        restaurant: {
            deep: true,
            handler(restaurant) {
                if (restaurant.order_setup != null && (typeof restaurant.order_setup === 'object' && Object.keys(restaurant.order_setup).length > 0)) {
                    if (typeof restaurant.order_setup.minimum_order_limit !== "undefined") {
                        this.minimumOrderLimit = restaurant.order_setup.currency_minimum_order_limit;
                    }
                }
            }
        }
    }
}
</script>
