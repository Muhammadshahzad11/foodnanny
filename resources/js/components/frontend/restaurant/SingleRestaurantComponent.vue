<template>
    <div v-if="isMenuFixed && carts.length > 0" @click.prevent="openCanvas('cart-canvas')"
        class="fixed lg:block hidden top-1/2 -translate-y-1/2 ltr:right-0 rtl:left-0 z-30 overflow-hidden cursor-pointer restaurant-float-cart">
        <div class="flex flex-col items-center justify-center text-center gap-1 py-2.5 px-4 bg-primary text-white">
            <i class="lab-fill-bag text-2xl leading-none"></i>
            <span class="text-sm font-medium">{{ carts.length }} {{ $t('label.items') }}</span>
        </div>
        <span class="text-sm font-semibold py-2.5 px-4 bg-heading text-white w-full block text-center">
            {{
                currencyFormat(subtotal, setting.site_digit_after_decimal_point, setting.site_default_currency_symbol,
                    setting.site_currency_position)
            }}
        </span>
    </div>

    <section class="restaurant-page mb-24 md:mb-14">
        <div class="container">
            <DineInBannerComponent
                :visible="isDineInSession"
                :table-label="dineInContextStore.tableLabel"
                :zone="dineInContextStore.context?.zone || ''"
                :allow-exit="true"
                @exit="exitDineIn"/>

            <div class="restaurant-hero relative overflow-hidden mb-5 sm:mb-8">
                <img :src="restaurant.cover" alt="banner"
                    class="restaurant-hero__media absolute inset-0 w-full h-full object-cover">
                <div class="restaurant-hero__veil absolute inset-0"></div>
                <div class="relative z-[1] flex min-h-[220px] sm:min-h-[300px] lg:min-h-[340px] flex-col justify-end p-4 sm:p-8 lg:p-10">
                    <div class="flex flex-col gap-4 sm:gap-5 sm:flex-row sm:items-end sm:justify-between">
                        <div class="restaurant-hero__copy max-w-3xl">
                            <div class="mb-3 sm:mb-4 flex flex-wrap items-end gap-3 sm:gap-4">
                                <img :src="restaurant.logo" alt="logo"
                                    class="restaurant-hero__logo h-14 w-14 sm:h-20 sm:w-20 object-cover ring-2 ring-white/80">
                                <div
                                    :class="restaurant.availability === enums.availabilityEnum.OPEN
                                        ? 'bg-primary/90 text-white'
                                        : 'bg-rose-600/90 text-white'"
                                    class="inline-flex items-center gap-1.5 px-3 py-1 text-[11px] sm:text-xs font-semibold uppercase tracking-wide backdrop-blur-sm">
                                    <span class="h-1.5 w-1.5 rounded-full bg-white animate-pulse"></span>
                                    {{
                                        restaurant.availability === enums.availabilityEnum.OPEN
                                            ? $t('label.open_now')
                                            : $t('label.close_now')
                                    }}
                                </div>
                            </div>
                            <h1 class="text-2xl sm:text-4xl lg:text-5xl font-semibold leading-tight text-white capitalize mb-2 sm:mb-3">
                                {{ restaurant.name }}
                            </h1>
                            <div class="flex flex-wrap items-center gap-x-2 sm:gap-x-3 gap-y-1.5 text-xs sm:text-sm text-white/85">
                                <span>{{ restaurant.cuisine }}</span>
                                <span class="opacity-40">·</span>
                                <span>{{ restaurant.distance }} {{ $t('label.km') }}</span>
                                <template v-if="restaurant.rating_star > 0 && restaurant.rating_star_count > 0">
                                    <span class="opacity-40">·</span>
                                    <span class="inline-flex items-center gap-1">
                                        <i class="lab-fill-star-round text-amber-300"></i>
                                        <strong class="font-semibold text-white">
                                            {{ (restaurant.rating_star / restaurant.rating_star_count).toFixed(1) }}
                                        </strong>
                                        <span class="text-white/70">
                                            ({{ restaurant.rating_star_count }}
                                            {{ restaurant.rating_star_count > 1 ? $t('label.reviews') : $t('label.review') }})
                                        </span>
                                    </span>
                                </template>
                            </div>
                            <div class="mt-2.5 sm:mt-3 flex flex-wrap gap-2 text-[11px] sm:text-sm text-white/80">
                                <span class="restaurant-meta-chip">
                                    <i class="lab-line-clock"></i>
                                    {{ restaurant.today }}
                                    <template v-if="restaurant.single_time_slots"> · {{ restaurant.single_time_slots }}</template>
                                </span>
                                <span class="restaurant-meta-chip">
                                    {{ $t('label.minimum_order_limit') }}: {{ minimumOrderLimit }}
                                </span>
                            </div>
                        </div>

                        <nav class="flex items-center gap-2 shrink-0">
                            <button @click.prevent="openInfoModal"
                                class="inline-flex items-center gap-2 h-10 sm:h-11 px-3.5 sm:px-4 bg-white/95 text-heading hover:bg-white transition">
                                <i class="lab-line-info-circle text-xl text-primary"></i>
                                <span class="capitalize text-sm font-medium whitespace-nowrap">
                                    {{ $t('label.more_info') }}
                                </span>
                            </button>
                            <button @click.prevent="favorite(restaurant, restaurant.favorite = !restaurant.favorite)"
                                class="inline-flex items-center justify-center h-10 w-10 sm:h-11 sm:w-11 bg-white/95 text-heading hover:bg-white transition">
                                <i :class="restaurant.favorite ? 'lab-fill-heart text-primary' : 'lab-line-heart'"
                                    class="text-xl"></i>
                            </button>
                        </nav>
                    </div>
                </div>
            </div>

            <aside class="restaurant-notice mb-5 sm:mb-8">
                <div class="flex gap-3 sm:gap-4">
                    <div class="restaurant-notice__icon shrink-0">
                        <i class="lab-line-info-circle text-xl"></i>
                    </div>
                    <div class="min-w-0">
                        <p class="restaurant-notice__title text-xs sm:text-sm font-bold tracking-wide uppercase">
                            {{ $t('label.important_notice') }}
                        </p>
                        <p class="restaurant-notice__body mt-1.5 text-xs sm:text-sm leading-6">
                            {{ $t('message.restaurant_platform_disclaimer') }}
                        </p>
                        <p class="restaurant-notice__emphasis mt-2 text-xs sm:text-sm font-semibold leading-6">
                            {{ $t('message.restaurant_contact_disclaimer') }}
                        </p>
                    </div>
                </div>
            </aside>

            <div v-if="coupons.length > 0" class="mb-7 sm:mb-10 overflow-hidden">
                <div class="mb-4">
                    <p class="text-xs font-semibold uppercase tracking-[0.14em] text-primary mb-1">{{ $t('label.offers') }}</p>
                    <h2 class="text-xl sm:text-2xl font-semibold text-heading capitalize">{{ $t('label.available_deals') }}</h2>
                </div>
                <Swiper :dir="displayMode" :loop="false" :speed="800" :navigation="true" :modules="modules" :breakpoints="couponBreakPoints" class="middle-navigate restaurant-deals !overflow-hidden">
                    <SwiperSlide @click.prevent="openCouponModal(coupon.id)" v-for="coupon in coupons" :key="coupon.id" class="!w-[85%] xs:!w-72 sm:!w-auto mobile:!w-64 cursor-pointer">
                        <div class="restaurant-deal h-full p-4 transition duration-300">
                            <div class="mb-3 flex items-center gap-2">
                                <span class="inline-flex h-9 w-9 items-center justify-center bg-primary/10 text-primary rounded-lg">
                                    <i v-if="coupon.type === enums.discountEnum.DEFAULT" :class="coupon.discount_type === enums.discountTypeEnum.PERCENTAGE ? 'lab-line-offers' : 'lab-line-coupon'" class="text-lg"></i>
                                    <i v-if="coupon.type === enums.discountEnum.FREE_DELIVERY" class="lab-line-bike text-lg"></i>
                                </span>
                                <div class="min-w-0">
                                    <p v-if="coupon.type === enums.discountEnum.DEFAULT" class="text-sm font-semibold text-primary truncate">
                                        {{ coupon.discount_alt }} {{ $t('label.off') }}
                                    </p>
                                    <p v-if="coupon.type === enums.discountEnum.FREE_DELIVERY" class="text-sm font-semibold text-primary truncate">
                                        {{ textShortener(coupon.name, 18) }}
                                    </p>
                                    <p class="text-[11px] font-medium uppercase tracking-wider text-secondary">
                                        {{ textShortener(coupon.code, 12) }}
                                    </p>
                                </div>
                            </div>
                            <p v-if="coupon.type === enums.discountEnum.DEFAULT" class="text-xs leading-5 text-paragraph mb-2">
                                {{
                                    coupon.minimum_order > 0 ? $t('message.discount_off_above', {
                                        discount: coupon.discount_alt,
                                        min_order: coupon.minimum_order_currency_amount
                                    }) : $t('message.discount_off', { discount: coupon.discount_alt })
                                }}
                            </p>
                            <p v-if="coupon.type === enums.discountEnum.FREE_DELIVERY" class="text-xs leading-5 text-paragraph mb-2">
                                {{
                                    coupon.minimum_order > 0 ? textShortener($t('message.discount_delivery_off_above',
                                        { min_order: coupon.minimum_order_currency_amount }), 48) :
                                        $t('message.discount_delivery_off')
                                }}
                            </p>
                            <p class="text-[11px] font-medium text-heading/70">{{ $t('label.use_in_checkout') }}</p>
                        </div>
                    </SwiperSlide>
                </Swiper>
            </div>

            <div
                :class="isMenuFixed ? 'restaurant-menu-bar--fixed' : 'restaurant-menu-bar'"
                v-if="categoryWiseItems && categoryWiseItems.length"
            >
                <div :class="isMenuFixed ? 'container' : 'w-full'">
                    <div :class="isMenuFixed ? 'mb-3' : 'mb-5'" class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                        <div class="flex items-center gap-2 min-w-0">
                            <router-link v-if="isMenuFixed" :to="{ name: 'frontend.restaurant' }" class="text-heading hover:text-primary">
                                <i class="lab-line-chevron-left text-lg font-bold"></i>
                            </router-link>
                            <div class="min-w-0">
                                <p v-if="!isMenuFixed" class="text-xs font-semibold uppercase tracking-[0.14em] text-primary mb-1">Menu</p>
                                <h2 class="text-xl sm:text-2xl font-semibold capitalize text-heading truncate">
                                    {{ isMenuFixed ? restaurant.name : $t('label.restaurant_menu') }}
                                </h2>
                            </div>
                        </div>

                        <nav class="flex flex-wrap items-center gap-2 sm:gap-3 w-full sm:w-auto">
                            <button
                                @click.prevent="itemType === enums.itemTypeEnum.NON_VEG ? itemType = null : itemType = enums.itemTypeEnum.NON_VEG"
                                :class="itemType === enums.itemTypeEnum.NON_VEG ? 'restaurant-filter--on' : 'restaurant-filter'"
                                class="inline-flex shrink-0 items-center gap-2 h-9 px-3 transition">
                                <img :src="setting.image_non_vag" alt="food-type" class="h-4 drop-shadow-mealtype">
                                <span class="capitalize text-sm font-medium">{{ $t('label.non_veg') }}</span>
                            </button>
                            <button
                                @click.prevent="itemType === enums.itemTypeEnum.VEG ? itemType = null : itemType = enums.itemTypeEnum.VEG"
                                :class="itemType === enums.itemTypeEnum.VEG ? 'restaurant-filter--on' : 'restaurant-filter'"
                                class="inline-flex shrink-0 items-center gap-2 h-9 px-3 transition">
                                <img :src="setting.image_vag" alt="food-type" class="h-4 drop-shadow-mealtype">
                                <span class="capitalize text-sm font-medium">{{ $t('label.veg') }}</span>
                            </button>
                            <form @submit.prevent="search"
                                class="group flex h-9 w-full sm:w-auto sm:min-w-[180px] sm:max-w-[260px] flex-1 items-center gap-2 px-3 restaurant-search focus-within:ring-1 focus-within:ring-primary/40">
                                <button type="submit" class="lab-line-search text-lg shrink-0 text-paragraph"></button>
                                <input type="search" @keyup="search" v-model="searchItem" :placeholder="$t('label.search_in_menu')"
                                    class="w-full bg-transparent text-sm placeholder:text-paragraph/70 outline-none">
                                <button type="button" @click.prevent="searchReset"
                                    class="lab-fill-close-circle text-danger opacity-0 group-focus-within:opacity-100 transition"></button>
                            </form>
                        </nav>
                    </div>

                    <Swiper :dir="displayMode" :speed="700" :spaceBetween="8" :navigation="true" :modules="modules"
                        slidesPerView="auto" class="middle-navigate menu-categories restaurant-cats">
                        <SwiperSlide v-for="(categoryWiseItem, categoryWiseItemIndex) in categoryWiseItems"
                            :key="categoryWiseItemIndex"
                            :class="{ 'restaurant-cat--active': currentSectionId === categoryWiseItem.slug }"
                            class="restaurant-cat !w-fit"
                            @click="handleMenuCategory($event, categoryWiseItem.slug)">
                            {{ categoryWiseItem.name }}
                        </SwiperSlide>
                    </Swiper>
                </div>
            </div>
            <div class="text-center text-paragraph py-10" v-else>
                <h3 class="sm:text-xl font-medium capitalize">{{ $t('message.no_menu_available') }}</h3>
            </div>

            <dl v-if="searchItem.length > 0" class="mt-8 restaurant-section">
                <dt class="mb-5 text-2xl font-semibold capitalize text-heading">
                    {{ $t("message.we_found", { length: searchItems.length, search: searchItem }) }}
                </dt>
                <dd class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-5 items-stretch restaurant-menu-grid restaurant-menu-grid--search">
                    <ItemComponent :offer="checkOffer" :type="itemType" :itemIndex="100000" :items="searchItems" />
                </dd>
            </dl>

            <dl v-for="(categoryWiseItem, categoryWiseItemIndex) in categoryWiseItems" :key="categoryWiseItemIndex"
                :id="categoryWiseItem.slug" class="mt-10 restaurant-section">
                <dt class="mb-5 flex items-center gap-3">
                    <span class="h-6 w-1 bg-primary"></span>
                    <span class="text-2xl font-semibold capitalize text-heading">{{ categoryWiseItem.name }}</span>
                </dt>
                <dd class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-5 items-stretch restaurant-menu-grid">
                    <div v-if="categoryWiseItem.items.length === 0" class="text-lg font-normal text-paragraph">{{ $t('message.item_not_found') }}</div>
                    <div v-else-if="categoryWiseItem.items.filter(item => itemType == null || item.item_type === itemType).length === 0" class="text-lg font-normal text-paragraph">{{ $t('message.item_not_found') }}</div>
                    <ItemComponent v-if="categoryWiseItem.items.length > 0" :offer="checkOffer" :type="itemType" :itemIndex="categoryWiseItemIndex" :items="categoryWiseItem.items" />
                </dd>
            </dl>
        </div>
    </section>

    <Teleport to="body">
    <div id="coupon-info-modal"
        class="fixed inset-0 z-[120] p-3 w-screen h-dvh overflow-y-auto bg-black/55 transition-all duration-300 opacity-0 invisible">
        <div class="max-w-lg w-full rounded-2xl mx-auto bg-white transition-all duration-300 shadow-2xl">
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
    </Teleport>

    <Teleport to="body">
    <div id="more-information"
        class="fixed inset-0 z-[120] p-3 w-screen h-dvh overflow-y-auto bg-black/55 transition-all duration-300 opacity-0 invisible">
        <div class="w-full rounded-2xl mx-auto bg-white transition-all duration-300 max-w-2xl shadow-2xl">
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
    </Teleport>
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
            let scrollHeight = this.coupons.length > 0 ? 720 : 420;
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

<style scoped>
.restaurant-page {
    position: relative;
}
.restaurant-page::before {
    content: "";
    position: absolute;
    inset: 0 0 auto 0;
    height: 480px;
    z-index: 0;
    pointer-events: none;
    background:
        radial-gradient(ellipse 80% 55% at 10% -10%, rgb(var(--primary) / 0.12), transparent 55%),
        linear-gradient(180deg, #f3faf5 0%, transparent 100%);
}
.restaurant-hero {
    position: relative;
    z-index: 1;
    border-radius: 1rem;
    box-shadow: 0 16px 36px rgb(10 61 40 / 0.12);
    animation: restaurant-fade 600ms ease both;
}
@media (min-width: 640px) {
    .restaurant-hero { border-radius: 1.25rem; }
}
.restaurant-hero__media {
    /* no persistent transform — avoids trapping fixed modals */
}
.restaurant-hero__veil {
    background:
        linear-gradient(180deg, rgb(10 61 40 / 0.18) 0%, rgb(10 61 40 / 0.58) 48%, rgb(10 61 40 / 0.9) 100%),
        linear-gradient(90deg, rgb(10 61 40 / 0.3), transparent 60%);
}
.restaurant-hero__logo {
    border-radius: 0.85rem;
    background: #fff;
}
.restaurant-meta-chip {
    display: inline-flex;
    align-items: center;
    gap: 0.35rem;
    padding: 0.3rem 0.65rem;
    background: rgb(255 255 255 / 0.12);
    border: 1px solid rgb(255 255 255 / 0.18);
    backdrop-filter: blur(8px);
    border-radius: 0.45rem;
}
.restaurant-notice {
    position: relative;
    z-index: 1;
    padding: 0.9rem 1rem;
    border: 1px solid rgb(220 38 38 / 0.35);
    border-left: 4px solid #dc2626;
    background: linear-gradient(135deg, #fff5f5 0%, #fee2e2 55%, #fff7ed 100%);
    border-radius: 0.85rem;
    box-shadow: 0 8px 24px rgb(220 38 38 / 0.12);
}
.restaurant-notice__icon {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 2.35rem;
    height: 2.35rem;
    color: #fff;
    background: #dc2626;
    border-radius: 0.7rem;
    box-shadow: 0 4px 12px rgb(220 38 38 / 0.35);
}
.restaurant-notice__title {
    color: #991b1b;
}
.restaurant-notice__body {
    color: #7f1d1d;
}
.restaurant-notice__emphasis {
    color: #b91c1c;
}
.restaurant-deal {
    border: 1px solid rgb(var(--primary) / 0.12);
    border-radius: 1rem;
    background: linear-gradient(145deg, #ffffff 0%, #f5fbf7 100%);
}
.restaurant-deals {
    overflow: hidden;
}
.restaurant-menu-bar {
    position: relative;
    z-index: 1;
    padding: 1rem 0 0.75rem;
    border-top: 1px solid rgb(10 61 40 / 0.06);
}
.restaurant-menu-bar--fixed {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    z-index: 45;
    padding: 0.75rem 0.75rem;
    background: rgb(255 255 255 / 0.96);
    backdrop-filter: blur(12px);
    border-bottom: 1px solid rgb(10 61 40 / 0.08);
    box-shadow: 0 8px 24px rgb(10 61 40 / 0.06);
}
.restaurant-filter {
    color: rgb(var(--heading));
    background: #eef6f0;
    border-radius: 0.55rem;
}
.restaurant-filter--on {
    color: #fff;
    background: rgb(var(--primary));
    border-radius: 0.55rem;
}
.restaurant-search {
    background: #eef6f0;
    border: 1px solid transparent;
    border-radius: 0.65rem;
}
.restaurant-cat {
    cursor: pointer;
    height: 2.15rem;
    line-height: 2.15rem;
    padding: 0 0.95rem;
    font-size: 0.875rem;
    white-space: nowrap;
    text-transform: capitalize;
    color: rgb(var(--heading));
    background: #fff;
    border: 1px solid rgb(10 61 40 / 0.1);
    border-radius: 999px;
    transition: color 0.2s ease, background 0.2s ease, border-color 0.2s ease;
}
.restaurant-cat:hover {
    border-color: rgb(var(--primary) / 0.35);
    color: rgb(var(--primary));
}
.restaurant-cat--active {
    color: #fff !important;
    background: rgb(var(--primary)) !important;
    border-color: rgb(var(--primary)) !important;
}
.restaurant-float-cart {
    border-radius: 0.85rem 0 0 0.85rem;
    box-shadow: 0 12px 28px rgb(10 61 40 / 0.2);
    z-index: 50;
}
.restaurant-menu-grid--search {
    padding: 1rem;
    background: #f3faf5;
    border-radius: 1rem;
}
.restaurant-menu-grid {
    position: relative;
    z-index: 1;
}
.restaurant-section {
    position: relative;
    z-index: 1;
}
@keyframes restaurant-fade {
    from { opacity: 0; }
    to { opacity: 1; }
}
@media (max-width: 640px) {
    .restaurant-menu-bar--fixed .container {
        padding-left: 0.25rem;
        padding-right: 0.25rem;
    }
}
@media (prefers-reduced-motion: reduce) {
    .restaurant-hero { animation: none !important; }
}
</style>

